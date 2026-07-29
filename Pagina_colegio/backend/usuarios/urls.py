from __future__ import annotations

from django.urls import path

from . import views

urlpatterns = [
    path("login/", views.login_view, name="login"),
    path("logout/", views.logout_view, name="logout"),
    path("dashboard/admin/", views.dashboard_admin, name="dashboard_admin"),
    path("dashboard/profesor/", views.dashboard_profesor, name="dashboard_profesor"),
    path("dashboard/alumno/", views.dashboard_alumno, name="dashboard_alumno"),
]
