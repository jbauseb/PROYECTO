@echo off
title Instalador de ALM System
color 0A

echo.
echo ================================================
echo    Instalador de ALM System en Sistemas Windows
echo    Jose Alfredo Bautista Sebastiao
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

REM Paso 1: Clona el repositorio
echo Clonando el repositorio...
git clone https://github.com/jbauseb/PROYECTO.git

REM Paso 2: Mueve el proyecto a la carpeta htdocs de XAMPP
echo Copiando archivos a htdocs...
move /Y PROYECTO "C:\xampp\htdocs"

REM Paso 3: Instala dependencias con Composer
echo Instalando dependencias con Composer...
cd /d C:\xampp\htdocs\PROYECTO\Tarea03_4
composer install


REM Iniciar Apache
echo Iniciando Apache...
start "" "C:\xampp\apache_start.bat"

REM Esperar 2 segundos
timeout /t 2 >nul

REM Iniciar MySQL
echo Iniciando MySQL...
start "" "C:\xampp\mysql_start.bat"

REM Esperar otros 2 segundos para asegurarse
timeout /t 2 >nul

REM Paso 5: Crea la base de datos e inserta datos de muestra
echo Creando base de datos...
"C:\xampp\mysql\bin\mysql.exe" -u root < "C:\xampp\htdocs\PROYECTO\Tarea03_4\basedatos\BaseDatos.sql"

REM Paso 6: Abre la aplicación en el navegador
echo Abriendo la aplicacion en el navegador...
start http://localhost/PROYECTO/Tarea03_4

echo.
echo Instalacion finalizada correctamente.
pause
exit

