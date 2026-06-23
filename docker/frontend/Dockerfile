FROM node:24-alpine AS frontend-build

WORKDIR /app/frontend

COPY frontend/package.json frontend/package-lock.json ./
RUN npm ci

COPY frontend/ ./
RUN npm run build

FROM nginx:1.27-alpine

RUN apk add --no-cache curl

COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY --from=frontend-build /app/backend/public/spa /var/www/html/public/spa
COPY backend/public /var/www/html/public

WORKDIR /var/www/html

HEALTHCHECK --interval=30s --timeout=5s --retries=3 \
    CMD curl -f http://localhost/api/health || exit 1

EXPOSE 80
