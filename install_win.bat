@echo off
title Instalador de ALM System
color 0A

echo.
echo ================================================
echo    Instalador de ALM System
echo ================================================
echo.

REM Verificar si Git está instalado
where git >nul 2>nul
if errorlevel 1 (
    echo ERROR: Git no esta instalado. Instale Git antes de continuar.
    pause
    exit /b
)

REM Verificar si Composer está instalado
where composer >nul 2>nul
if errorlevel 1 (
    echo ERROR: Composer no esta instalado. Instale Composer antes de continuar.
    pause
    exit /b
)

REM Paso 1: Clonar el repositorio
echo Clonando el repositorio...
git clone https://github.com/jbauseb/PROYECTO.git

REM Paso 2: Mover el proyecto a la carpeta htdocs de XAMPP
echo Copiando archivos a htdocs...
move /Y PROYECTO "C:\xampp\htdocs\PROYECTO"

REM Paso 3: Instalar dependencias con Composer
echo Instalando dependencias con Composer...
cd /d C:\xampp\htdocs\PROYECTO\Tarea03_4
composer install

REM Paso 4: Crear la base de datos e insertar datos de muestra
echo Creando base de datos...
mysql -u root < C:\xampp\htdocs\PROYECTO\Tarea03_4\basedatos\BaseDatos.sql

REM Paso 5: Iniciar XAMPP
echo Iniciando XAMPP...
start "" "C:\xampp\xampp-control.exe"

REM Paso 6: Abrir la aplicación en el navegador
echo Abriendo la aplicacion en el navegador...
start http://localhost/PROYECTO/Tarea03_4

echo.
echo Instalacion finalizada correctamente.
pause
exit

