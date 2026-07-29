from __future__ import annotations

from functools import wraps
from typing import Any, Callable, TypeVar, cast

from django.http import HttpRequest, HttpResponse, HttpResponseForbidden
from django.shortcuts import redirect
from django.urls import reverse

from .models import Usuario

F = TypeVar("F", bound=Callable[..., HttpResponse])


def login_required(view_func: F) -> F:
    @wraps(view_func)
    def _wrapped(request: HttpRequest, *args: Any, **kwargs: Any) -> HttpResponse:
        usuario_id = request.session.get("usuario_id")
        if not usuario_id:
            return redirect(reverse("login"))
        try:
            request.usuario = Usuario.objects.select_related("rol").get(id=usuario_id)  # type: ignore[attr-defined]
        except Usuario.DoesNotExist:
            request.session.flush()
            return redirect(reverse("login"))
        return view_func(request, *args, **kwargs)

    return cast(F, _wrapped)


def role_required(*roles: str) -> Callable[[F], F]:
    def decorator(view_func: F) -> F:
        @wraps(view_func)
        def _wrapped(request: HttpRequest, *args: Any, **kwargs: Any) -> HttpResponse:
            usuario_id = request.session.get("usuario_id")
            if not usuario_id:
                return redirect(reverse("login"))
            try:
                usuario = Usuario.objects.select_related("rol").get(id=usuario_id)
            except Usuario.DoesNotExist:
                request.session.flush()
                return redirect(reverse("login"))

            if usuario.rol.nombre not in roles:
                return HttpResponseForbidden("No tienes permiso para acceder a esta sección.")

            request.usuario = usuario  # type: ignore[attr-defined]
            return view_func(request, *args, **kwargs)

        return cast(F, _wrapped)

    return decorator
