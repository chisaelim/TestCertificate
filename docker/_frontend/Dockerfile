FROM node:24.12.0-alpine

WORKDIR /app

COPY docker/_frontend/*.sh /usr/local/bin/

COPY _frontend-app /app

RUN chmod +x /usr/local/bin/*.sh

EXPOSE 5173
