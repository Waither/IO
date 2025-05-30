FROM nginx:1.17.8-alpine

# Copy the public directory
# COPY ./public/ /app/public/
COPY . /app/

# Copy the nginx config file
COPY ./docker/nginx/nginx.conf /etc/nginx/conf.d/default.conf

# Ustaw katalog roboczy
WORKDIR /app

# (opcjonalnie) expose 80
EXPOSE 80