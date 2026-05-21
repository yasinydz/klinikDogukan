<?php
/**
 * templates/pages/offline.php
 * URL: /offline
 * Service Worker tarafından internet yokken gösterilir.
 */
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>İnternet Bağlantısı Yok | Psikolog Doğukan Kopuk</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #111827;
            color: #e2e8f0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            text-align: center;
        }

        .offline-wrap {
            max-width: 400px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.5rem;
        }

        .offline-icon {
            width: 80px;
            height: 80px;
            background: rgba(28, 104, 82, 0.15);
            border: 2px solid rgba(28, 104, 82, 0.4);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
        }

        h1 {
            font-size: 1.5rem;
            font-weight: 800;
            color: #f7fafc;
        }

        p {
            color: #a0aec0;
            font-size: 0.95rem;
            line-height: 1.7;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.75rem;
            background: #1C6852;
            color: #fff;
            border: none;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            font-family: inherit;
            transition: background 0.2s;
        }
        .btn:hover { background: #155C45; }

        .contact-info {
            padding: 1.25rem 1.5rem;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 12px;
            width: 100%;
        }

        .contact-info p {
            font-size: 0.85rem;
            margin-bottom: 0.75rem;
            color: #718096;
        }

        .contact-info a {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            color: #68D391;
            font-weight: 600;
            text-decoration: none;
            font-size: 1rem;
        }
    </style>
</head>
<body>

<div class="offline-wrap">
    <div class="offline-icon">📶</div>

    <div>
        <h1>İnternet Bağlantısı Yok</h1>
        <p style="margin-top:0.75rem;">
            Sayfayı görüntülemek için internet bağlantısına ihtiyacınız var.
            Bağlantınızı kontrol edip tekrar deneyin.
        </p>
    </div>

    <button class="btn" onclick="window.location.reload()">
        ↺ Tekrar Dene
    </button>

    <div class="contact-info">
        <p>Acil randevu için bize doğrudan ulaşabilirsiniz:</p>
        <a href="tel:+90 (532) 276 86 02">
            📞 +90 (532) 276 86 02
        </a>
    </div>
</div>

</body>
</html>
