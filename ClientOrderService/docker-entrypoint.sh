#!/bin/bash
set -e

echo "Starting ClientOrderService..."

# Wait for SQL Server to be available
echo "Waiting for SQL Server to be available..."
until /opt/mssql-tools18/bin/sqlcmd -S ${DB_HOST} -U ${DB_USERNAME} -P ${DB_PASSWORD} -C -Q "SELECT 1" > /dev/null 2>&1; do
  echo "SQL Server is unavailable - sleeping"
  sleep 3
done

echo "SQL Server is up!"

# Create database if it doesn't exist
echo "Creating database ${DB_DATABASE} if it doesn't exist..."
/opt/mssql-tools18/bin/sqlcmd -S ${DB_HOST} -U ${DB_USERNAME} -P ${DB_PASSWORD} -C -Q "
IF NOT EXISTS (SELECT name FROM sys.databases WHERE name = '${DB_DATABASE}')
BEGIN
    CREATE DATABASE [${DB_DATABASE}];
    PRINT 'Database ${DB_DATABASE} created successfully';
END
ELSE
BEGIN
    PRINT 'Database ${DB_DATABASE} already exists';
END
"

echo "Database ${DB_DATABASE} is ready!"

# Wait for Laravel to connect to the database
echo "Verifying Laravel database connection..."
until php -r "
    require __DIR__ . '/vendor/autoload.php';
    \$app = require_once __DIR__ . '/bootstrap/app.php';
    \$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class);
    \$kernel->bootstrap();
    
    // Attempt DB connection
    try {
        \$db = \$app->make('db');
        \$pdo = \$db->connection()->getPdo();
        exit(0);
    } catch (\Exception \$e) {
        echo 'Connection failed: ' . \$e->getMessage() . PHP_EOL;
        exit(1);
    }
"; do
  echo "Laravel database connection unavailable - sleeping"
  sleep 3
done

echo "Laravel connected to database successfully!"

# Run Migrations
echo "Running migrations..."
php artisan migrate --force

# Start Application
echo "Starting server on port 8000..."
exec php artisan serve --host=0.0.0.0 --port=8000
