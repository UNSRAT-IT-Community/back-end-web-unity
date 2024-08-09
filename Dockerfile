FROM back-end-webunity-base:latest

WORKDIR /app

COPY . /app

COPY start.sh /start.sh
RUN chmod +x /start.sh

RUN /app/.env.example /app/.env

RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

CMD ["/start.sh"]