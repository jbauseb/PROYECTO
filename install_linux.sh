#!/bin/bash
# Instalador automático para ALM System en Linux
# Se asume que este script tiene permisos de ejecución: chmod +x install_linux.sh

echo
echo "==================================================="
echo "      Instalador de ALM System en Sistemas Linux"
echo "	    Jose Alfredo Bautista Sebastiao"
echo "==================================================="
echo

# Colores para mensajes
GREEN='\033[0;32m'
RED='\033[0;31m'
NC='\033[0m' # Sin color

# Verificar si XAMPP está instalado
if [ ! -d "/opt/lampp" ]; then
    echo -e "${RED}ERROR:${NC} XAMPP no está instalado en /opt/lampp"
    exit 1
fi

# Verificar si Git está instalado
if ! command -v git &> /dev/null
then
    echo -e "${RED}ERROR:${NC} Git no está instalado. Instale Git antes de continuar --> sudo apt install git"
    exit 1
fi

# Verificar si Composer está instalado
#if ! command -v composer &> /dev/null
#then
#    echo -e "${RED}ERROR:${NC} Composer no está instalado. Instale Composer antes de continuar --> sudo apt install composer"
#    exit 1
#fi

# Paso 1: Clonar el repositorio
echo -e "${GREEN}Clonando el repositorio...${NC}"
git clone https://github.com/jbauseb/PROYECTO.git

# Paso 2: Mover el proyecto a la carpeta htdocs de XAMPP
echo -e "${GREEN}Moviendo archivos a htdocs...${NC}"

# Eliminar si ya existe el directorio destino
if [ -d "/opt/lampp/htdocs/PROYECTO" ]; then
    sudo rm -rf /opt/lampp/htdocs/PROYECTO
fi

# Mover
sudo mv PROYECTO /opt/lampp/htdocs

# Paso 3: Dar permisos
echo -e "${GREEN}Asignando permisos...${NC}"
sudo chmod -R 777 /opt/lampp/htdocs/PROYECTO

# Paso 4: Iniciar Apache y MySQL de XAMPP
echo -e "${GREEN}Iniciando Apache y MySQL de XAMPP...${NC}"
sudo /opt/lampp/lampp start

# Espera hasta que arranque MySQL
echo -e "${GREEN}Esperando que MySQL esté disponible...${NC}"
while ! sudo /opt/lampp/bin/mysqladmin ping -u root --silent; do
    sleep 1
done

# Paso 5: Crear la base de datos e insertar datos de muestra
echo -e "${GREEN}Creando la base de datos...${NC}"
sudo /opt/lampp/bin/mysql -u root < /opt/lampp/htdocs/PROYECTO/Tarea03_4/basedatos/BaseDatos.sql


# Paso 6: Abrir la aplicación en el navegador
echo -e "${GREEN}Abriendo la aplicación en el navegador...${NC}"
xdg-open "http://localhost/PROYECTO/Tarea03_4" &> /dev/null

echo
echo -e "${GREEN}Instalación finalizada correctamente.${NC}"
echo
exit 0

