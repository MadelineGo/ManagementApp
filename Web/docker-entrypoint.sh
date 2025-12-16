#!/bin/sh
set -e

echo "Generating nginx.conf from environment variables..."

# Set default values if environment variables are not provided
AUTH_SERVICE_HOST=${AUTH_SERVICE_HOST:-auth-service}
AUTH_SERVICE_PORT=${AUTH_SERVICE_PORT:-8080}
CLIENT_ORDER_SERVICE_HOST=${CLIENT_ORDER_SERVICE_HOST:-client-order-service}
CLIENT_ORDER_SERVICE_PORT=${CLIENT_ORDER_SERVICE_PORT:-8000}

# Generate nginx.conf
cat > /etc/nginx/nginx.conf << EOF
events {
    worker_connections 1024;
}

http {
    include /etc/nginx/mime.types;
    default_type application/octet-stream;

    # Logging
    access_log /var/log/nginx/access.log;
    error_log /var/log/nginx/error.log;

    # Performance optimizations
    sendfile on;
    tcp_nopush on;
    tcp_nodelay on;
    keepalive_timeout 65;
    types_hash_max_size 2048;

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss application/json application/javascript;

    server {
        listen 80;
        server_name localhost;
        root /usr/share/nginx/html;
        index index.html;

        # Security headers
        add_header X-Frame-Options "SAMEORIGIN" always;
        add_header X-Content-Type-Options "nosniff" always;
        add_header X-XSS-Protection "1; mode=block" always;

        # Serve static files
        location / {
            try_files \$uri \$uri/ /index.html;
        }

        # Proxy API requests to auth service
        location /api/auth {
            proxy_pass http://${AUTH_SERVICE_HOST}:${AUTH_SERVICE_PORT};
            proxy_http_version 1.1;
            proxy_set_header Upgrade \$http_upgrade;
            proxy_set_header Connection 'upgrade';
            proxy_set_header Host \$host;
            proxy_set_header X-Real-IP \$remote_addr;
            proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
            proxy_set_header X-Forwarded-Proto \$scheme;
            proxy_cache_bypass \$http_upgrade;
        }

        # Proxy API requests to client-order-service
        location /api {
            proxy_pass http://${CLIENT_ORDER_SERVICE_HOST}:${CLIENT_ORDER_SERVICE_PORT};
            proxy_http_version 1.1;
            proxy_set_header Upgrade \$http_upgrade;
            proxy_set_header Connection 'upgrade';
            proxy_set_header Host \$host;
            proxy_set_header X-Real-IP \$remote_addr;
            proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
            proxy_set_header X-Forwarded-Proto \$scheme;
            proxy_cache_bypass \$http_upgrade;
        }

        # Cache static assets
        location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
            expires 1y;
            add_header Cache-Control "public, immutable";
        }

        # Error pages
        error_page 404 /index.html;
    }
}
EOF

echo "nginx.conf generated successfully!"
echo "Auth Service: ${AUTH_SERVICE_HOST}:${AUTH_SERVICE_PORT}"
echo "Client Order Service: ${CLIENT_ORDER_SERVICE_HOST}:${CLIENT_ORDER_SERVICE_PORT}"

# Start nginx
exec nginx -g "daemon off;"
