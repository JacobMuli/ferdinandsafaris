#!/bin/bash

# Terminate on error
set -e

echo "🚀 Starting Ferdinand Safaris Deployment..."

# 1. Update Code (Optional: uncomment if using git)
# git pull origin main

# 2. Install/Update PHP Dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

# 3. Install/Update Frontend Dependencies & Build Assets
echo "🎨 Building assets..."
npm install
npm run build

# 4. Run Transformations / Optimizations
echo "⚡ Optimizing Laravel..."
php artisan optimize
php artisan view:cache
php artisan event:cache

# 5. Database Migrations
echo "🗄️ Running migrations..."
php artisan migrate --force

# 6. Seed CMS Data (Baseline)
echo "📝 Refreshing CMS content..."
php artisan db:seed --class=CmsSeeder --force

# 7. Setup Background Processes (Supervisor should handle persistent ones)
# This script assumes Supervisor is configured to manage Reverb and Queue Workers.
# However, we can send a signal to restart them if needed.
echo "🔄 Restarting queue workers..."
php artisan queue:restart

echo "✅ Deployment Successful!"
