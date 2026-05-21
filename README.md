# dogukankopuk.com

Kişisel/profesyonel web sitesi — vanilla PHP, MySQL.

## Yapı

```
.
├── .htaccess              ← kök, tüm istekleri public/'e yönlendirir
├── .env.example           ← şablon (.env sunucuda elle oluşturulur)
├── .gitignore
├── app/                   ← helpers, middleware, services (web'e kapalı)
├── config/                ← app, database, mail (web'e kapalı)
├── routes/                ← web ve admin rotaları (web'e kapalı)
├── storage/               ← loglar (web'e kapalı)
├── templates/             ← view dosyaları (web'e kapalı)
└── public/                ← document root içeriği
    ├── .htaccess
    ├── index.php          ← front controller
    ├── css/, js/, images/, icons/
    └── robots.txt
```

## Hostinger'a İlk Deploy

1. **Database hazırla** (hPanel > Veritabanları > MySQL):
   - DB oluştur, kullanıcı oluştur, tüm yetkileri ver
   - Adı, kullanıcı adı, şifreyi not al

2. **Git ile deploy** (hPanel > Websites > Manage > Git):
   - "Continue with GitHub" → repoyu seç
   - Branch: `master` (veya `main`)
   - Deploy path: `public_html` (boş bırak veya `/`)
   - Auto deployment'ı aç

3. **.env dosyasını oluştur** (File Manager veya SSH):
   - `public_html/` içinde `.env.example`'ı `.env` olarak kopyala
   - Database bilgilerini, SMTP, kişisel bilgileri doldur

4. **Klasör izinleri** (SSH):
   ```bash
   chmod -R 755 public_html
   chmod -R 775 public_html/storage public_html/public/images/uploads
   ```

5. **Test**: `https://www.dogukankopuk.com`

## Sonraki Deployment

`git push` yeterli — Hostinger Auto Deploy aktifse otomatik çeker.

`.env` ve `public/images/uploads/` repo dışıdır, push'tan etkilenmez.
