FROM mysql:8.0

# Ustawienia środowiskowe
ENV MYSQL_ROOT_PASSWORD=rootpassword
ENV MYSQL_DATABASE=to_projekt
ENV MYSQL_USER=user
ENV MYSQL_PASSWORD=password

# Kopiowanie pliku konfiguracyjnego MySQL (opcjonalne)
COPY mysql.conf /etc/mysql/conf.d/mysql.conf
COPY init-scripts/ /docker-entrypoint-initdb.d/