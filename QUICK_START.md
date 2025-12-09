# 🚀 Quick Start - Image Upload

## ⚡ Setup (One Time)

```bash
php artisan storage:setup
```

That's it! ✅

## 📸 Upload Images

1. Admin Dashboard → Projects → Add Project
2. Fill details + Upload image
3. Save
4. Image is now on your server!

## ✅ Test It Works

1. Upload a project with image
2. Delete original image from your desktop
3. Refresh projects page
4. Image still shows = **SUCCESS!** 🎉

## 🌐 Production Deploy

```bash
# On your server
php artisan storage:setup
chmod -R 755 storage
```

Done! Images work everywhere.

## 📁 Where Are Images?

```
storage/app/public/projects/
```

Accessible via:
```
yourdomain.com/storage/projects/image.jpg
```

## ❓ Problems?

```bash
php artisan storage:setup
```

Still issues? Check `IMAGE_UPLOAD_README.md`

---

**That's all you need to know!** 🎯
