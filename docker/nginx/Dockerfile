FROM node:20-alpine AS frontend

WORKDIR /app

COPY package.json package-lock.json* ./
RUN npm ci

COPY vite.config.js ./
COPY public/         public/
COPY resources/      resources/

RUN npm run build

FROM nginx:1.27-alpine

# Remove default config
RUN rm /etc/nginx/conf.d/default.conf

# Copy nginx configs from origin
COPY docker/nginx/nginx.conf          /etc/nginx/nginx.conf
COPY docker/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf

# Bake static files into the image (no host volume mount needed)
COPY public/                          /var/www/html/public/
COPY --from=frontend /app/public/build /var/www/html/public/build

HEALTHCHECK --interval=30s --timeout=3s --start-period=10s \
    CMD wget -q --spider http://localhost/health || exit 1

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
