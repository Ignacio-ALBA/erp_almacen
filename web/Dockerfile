# Usar una imagen base de Ubuntu
FROM ubuntu:22.04

# Variables de entorno
ARG XAMPP_URL

# Establece la zona horaria
ENV TZ=America/Mexico_City

RUN apt-get update && \
    apt-get install -y tzdata wget unzip net-tools && \
    ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && \
    echo $TZ > /etc/timezone && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* && \
    echo "La zona horaria está configurada como:" && date
    
# Instalar dependencias necesarias y descargar XAMPP
RUN apt-get update && apt-get install -y \
    wget \
    unzip \
    net-tools \
    wkhtmltopdf \
    && rm -rf /var/lib/apt/lists/* \
    && wget -O /tmp/xampp-installer.run $XAMPP_URL \
    && chmod +x /tmp/xampp-installer.run \
    && /tmp/xampp-installer.run --mode unattended \
    && rm /tmp/xampp-installer.run \
    && wget -O /opt/lampp/phpmyadmin.zip https://www.phpmyadmin.net/downloads/phpMyAdmin-latest-all-languages.zip \
    && unzip /opt/lampp/phpmyadmin.zip -d /opt/lampp/htdocs/ \
    && mv /opt/lampp/htdocs/phpMyAdmin-* /opt/lampp/htdocs/phpmyadmin \
    && rm /opt/lampp/phpmyadmin.zip \
    && mkdir -p /opt/lampp/htdocs/dashboard/tmp \
    && chown -R root:root /opt/lampp/htdocs/dashboard/tmp \
    && chmod -R 755 /opt/lampp/htdocs/dashboard/tmp


# Modificar el archivo de configuración de Apache para phpMyAdmin
RUN sed -i 's/Require local/Require all granted/' /opt/lampp/etc/extra/httpd-xampp.conf
#RUN sed -i 's/Require local/Require ip 127.0.0.1/' /opt/lampp/etc/extra/httpd-xampp.conf

# Ajustar los permisos de config.inc.php
RUN chmod 644 /opt/lampp/phpmyadmin/config.inc.php && \
    chown root:root /opt/lampp/phpmyadmin/config.inc.php

# Exponer puertos
EXPOSE 80 3306

# Cambiar al usuario root para ejecutar XAMPP
USER root

# Comando para iniciar XAMPP
CMD ["/bin/bash", "-c", "/opt/lampp/lampp start && tail -f /dev/null"]