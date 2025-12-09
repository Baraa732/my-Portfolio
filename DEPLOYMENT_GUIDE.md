# Project Image Upload - Deployment Guide

## How It Works

When you upload a project image:
1. **Image is copied to server storage** (`storage/app/public/projects/`)
2. **Original file location doesn't matter** - the image is permanently stored on your server
3. **Accessible via URL** through the storage symbolic link

## Local Development Setup

### 1. Create Storage Link
```bash
php artisan storage:setup
```
Or manually:
```bash
php artisan storage:link
```

### 2. Verify Setup
- Check that `public/storage` folder exists (it's a symbolic link)
- Upload a test project with an image
- Image should be accessible at: `http://localhost:8000/storage/projects/filename.jpg`

## Production Server Deployment

### Option 1: Shared Hosting (cPanel, Plesk, etc.)

#### Step 1: Upload Files
```
- Upload all files via FTP/SFTP
- Place Laravel files in root or subdirectory
- Point domain to /public folder
```

#### Step 2: Setup Storage (SSH Access)
```bash
cd /path/to/your/project
php artisan storage:setup
php artisan config:cache
php artisan route:cache
```

#### Step 3: Setup Storage (No SSH - Manual)
If you don't have SSH access:
1. Create folder: `public/storage`
2. In your hosting file manager, create a symbolic link:
   - From: `public/storage`
   - To: `../storage/app/public`

Or add this to your `public/index.php` (before anything else):
```php
// Fallback for servers without symlink support
if (!file_exists(__DIR__.'/storage')) {
    $storagePath = realpath(__DIR__.'/../storage/app/public');
    if ($storagePath) {
        symlink($storagePath, __DIR__.'/storage');
    }
}
```

#### Step 4: Set Permissions
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod -R 755 public/storage
```

### Option 2: VPS/Cloud Server (AWS, DigitalOcean, etc.)

#### Complete Setup Script
```bash
# Navigate to project
cd /var/www/your-project

# Install dependencies
composer install --optimize-autoloader --no-dev

# Setup environment
cp .env.example .env
php artisan key:generate

# Setup storage
php artisan storage:setup

# Set permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Setup database
php artisan migrate --force
```

#### Nginx Configuration
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/your-project/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Increase upload size for images
    client_max_body_size 10M;
}
```

#### Apache Configuration (.htaccess already included)
The `.htaccess` file in `public/` folder handles everything automatically.

### Option 3: Platform as a Service (Heroku, Railway, etc.)

#### Heroku Setup
```bash
# Add buildpack
heroku buildpacks:add heroku/php

# Set environment
heroku config:set APP_KEY=$(php artisan key:generate --show)
heroku config:set APP_ENV=production
heroku config:set APP_DEBUG=false

# Deploy
git push heroku main

# Run migrations and setup
heroku run php artisan migrate --force
heroku run php artisan storage:setup
```

## Environment Variables

### Required in .env
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

FILESYSTEM_DISK=public

# Increase upload limits if needed
UPLOAD_MAX_FILESIZE=10M
POST_MAX_SIZE=10M
```

## Troubleshooting

### Images Not Showing

**Problem**: Images upload but don't display

**Solutions**:
1. Check storage link exists:
   ```bash
   ls -la public/storage
   ```

2. Recreate storage link:
   ```bash
   rm public/storage
   php artisan storage:setup
   ```

3. Check permissions:
   ```bash
   chmod -R 755 storage/app/public
   ```

4. Verify .env setting:
   ```env
   FILESYSTEM_DISK=public
   APP_URL=https://yourdomain.com
   ```

### Upload Fails

**Problem**: "File too large" error

**Solutions**:
1. Update `php.ini`:
   ```ini
   upload_max_filesize = 10M
   post_max_size = 10M
   ```

2. Update `.htaccess` (already included):
   ```apache
   php_value upload_max_filesize 10M
   php_value post_max_size 10M
   ```

3. For Nginx, update server block:
   ```nginx
   client_max_body_size 10M;
   ```

### Symlink Not Working

**Problem**: Server doesn't support symlinks

**Solution**: Use direct path in views
Update `config/filesystems.php`:
```php
'public' => [
    'driver' => 'local',
    'root' => storage_path('app/public'),
    'url' => env('APP_URL').'/storage',
    'visibility' => 'public',
    'serve' => true, // Enable direct serving
],
```

## Security Best Practices

1. **Never commit uploaded images to Git**
   - Already in `.gitignore`: `/storage/*.jpg`, `/storage/*.png`

2. **Validate file types**
   - Already implemented in controller: `mimes:jpeg,png,jpg,gif,webp`

3. **Limit file size**
   - Already set to 5MB: `max:5120`

4. **Use unique filenames**
   - Already implemented: `time() . '_' . uniqid()`

5. **Set proper permissions**
   - Storage: `755`
   - Files: `644`

## Testing Checklist

- [ ] Upload image in development
- [ ] Image displays on projects page
- [ ] Delete original file from desktop
- [ ] Image still displays (proves it's on server)
- [ ] Deploy to production
- [ ] Run `php artisan storage:setup`
- [ ] Upload image in production
- [ ] Image displays correctly
- [ ] Check image URL is accessible directly

## Support

If images still don't work after following this guide:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Check web server error logs
3. Verify file permissions
4. Ensure storage link exists
5. Check APP_URL in .env matches your domain
