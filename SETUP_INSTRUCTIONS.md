# Quick Setup Instructions

## Local Development

### 1. Run Setup Command
```bash
php artisan storage:setup
```

This will:
- Create `storage/app/public/projects` folder
- Create symbolic link from `public/storage` to `storage/app/public`
- Set proper permissions

### 2. Test Upload
1. Go to admin dashboard
2. Add a new project with an image
3. Image will be stored in `storage/app/public/projects/`
4. Image will be accessible at `http://localhost:8000/storage/projects/filename.jpg`

### 3. Verify
- Delete the original image from your desktop/folder
- Refresh the projects page
- Image should still display (it's now on your server!)

## Production Deployment

### Quick Deploy (SSH Access)
```bash
cd /path/to/project
composer install --no-dev --optimize-autoloader
php artisan storage:setup
php artisan config:cache
php artisan route:cache
chmod -R 755 storage bootstrap/cache
```

### Without SSH (cPanel/Shared Hosting)
1. Upload all files via FTP
2. In File Manager, create symbolic link:
   - From: `public/storage`
   - To: `../storage/app/public`
3. Set folder permissions to 755:
   - `storage/`
   - `bootstrap/cache/`

## How It Works

```
User uploads image → Stored in storage/app/public/projects/
                  ↓
            Symbolic link (public/storage → storage/app/public)
                  ↓
            Accessible via URL: yourdomain.com/storage/projects/image.jpg
```

**Key Points:**
- Images are COPIED to server storage (not linked to original location)
- Original file can be deleted - image stays on server
- Works on any server with proper setup
- Secure and follows Laravel best practices

## Troubleshooting

**Images not showing?**
```bash
php artisan storage:setup
```

**Permission denied?**
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

**Still not working?**
Check `storage/logs/laravel.log` for errors.
