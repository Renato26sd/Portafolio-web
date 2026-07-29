from __future__ import annotations

from django.contrib import messages
from django.contrib.auth.hashers import check_password
from django.http import HttpRequest, HttpResponse
from django.shortcuts import redirect, render
from django.urls import reverse

from .decorators import login_required, role_required
from .models import Usuario


def login_view(request: HttpRequest) -> HttpResponse:
    if request.method == "POST":
        username = request.POST.get("username", "").strip()
        password = request.POST.get("password", "")

        try:
            usuario = Usuario.objects.select_related("rol").get(username=username, activo=True)
        except Usuario.DoesNotExist:
            messages.error(request, "Usuario o contraseña incorrectos.")
            return render(request, "auth/login.html")

        # Se asume que password_hash fue generado con los mismos algoritmos de Django
        if not check_password(password, usuario.password_hash):
            messages.error(request, "Usuario o contraseña incorrectos.")
            return render(request, "auth/login.html")

        # Login manual: guardamos el id del usuario en la sesión
        request.session["usuario_id"] = usuario.id
        request.session["usuario_nombre"] = usuario.nombre_completo
        request.session["usuario_rol"] = usuario.rol.nombre

        # Redirigir según rol
        if usuario.rol.nombre == "ADMIN":
            return redirect(reverse("dashboard_admin"))
        if usuario.rol.nombre == "PROFESOR":
            return redirect(reverse("dashboard_profesor"))
        if usuario.rol.nombre == "ALUMNO":
            return redirect(reverse("dashboard_alumno"))

        # Si el rol no es reconocido, forzar logout
        messages.error(request, "Rol de usuario no válido.")
        return redirect(reverse("logout"))

    return render(request, "auth/login.html")


def logout_view(request: HttpRequest) -> HttpResponse:
    request.session.flush()
    return redirect(reverse("login"))


@login_required
@role_required("ADMIN")
def dashboard_admin(request: HttpRequest) -> HttpResponse:
    return render(request, "dashboards/admin.html", {"usuario": request.usuario})  # type: ignore[attr-defined]


@login_required
@role_required("PROFESOR")
def dashboard_profesor(request: HttpRequest) -> HttpResponse:
    return render(request, "dashboards/profesor.html", {"usuario": request.usuario})  # type: ignore[attr-defined]


@login_required
@role_required("ALUMNO")
def dashboard_alumno(request: HttpRequest) -> HttpResponse:
    return render(request, "dashboards/alumno.html", {"usuario": request.usuario})  # type: ignore[attr-defined]
