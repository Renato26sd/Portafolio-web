from __future__ import annotations

from django.db import models


class Rol(models.Model):
    id = models.AutoField(primary_key=True)
    nombre = models.CharField(max_length=50, unique=True)

    class Meta:
        managed = False
        db_table = 'rol'

    def __str__(self) -> str:  # type: ignore[override]
        return self.nombre


class Usuario(models.Model):
    id = models.AutoField(primary_key=True)
    username = models.CharField(max_length=50, unique=True)
    password_hash = models.CharField(max_length=255)
    nombre_completo = models.CharField(max_length=150)
    email = models.CharField(max_length=150, blank=True, null=True)
    activo = models.BooleanField(default=True)
    rol = models.ForeignKey(Rol, on_delete=models.RESTRICT, db_column='rol_id')
    fecha_creacion = models.DateTimeField()

    class Meta:
        managed = False
        db_table = 'usuario'

    def __str__(self) -> str:  # type: ignore[override]
        return f"{self.nombre_completo} ({self.username})"


class Alumno(models.Model):
    id = models.OneToOneField(Usuario, on_delete=models.CASCADE, primary_key=True, db_column='id')
    codigo_alumno = models.CharField(max_length=50, blank=True, null=True)
    grado = models.CharField(max_length=50, blank=True, null=True)
    seccion = models.CharField(max_length=20, blank=True, null=True)

    class Meta:
        managed = False
        db_table = 'alumno'

    def __str__(self) -> str:  # type: ignore[override]
        return self.id.nombre_completo


class Profesor(models.Model):
    id = models.OneToOneField(Usuario, on_delete=models.CASCADE, primary_key=True, db_column='id')
    especialidad = models.CharField(max_length=100, blank=True, null=True)

    class Meta:
        managed = False
        db_table = 'profesor'

    def __str__(self) -> str:  # type: ignore[override]
        return self.id.nombre_completo
