<?php
/**
 * app/helpers/image.php
 *
 * Görsel optimizasyon ve yardımcı fonksiyonlar.
 *
 * processUploadedImage($tmpPath, $destDir, $filename, $opts)
 *   - EXIF orientation fix
 *   - max-width resize (1600px)
 *   - thumbnail üretimi (400x225 — 16:9)
 *   - WebP üretimi (GD varsa)
 *
 * getImageTag($path, $alt, $opts)
 *   - <picture> + WebP fallback
 *
 * getImageUrl($path, $size)
 *   - 'original' | 'thumb' | 'webp' | 'thumb_webp'
 */

if (!function_exists('processUploadedImage')) {

    /**
     * Yüklenen görseli optimize et.
     *
     * @param string $tmpPath  Geçici dosya yolu ($_FILES['x']['tmp_name'])
     * @param string $destDir  Hedef dizin (trailing slash yok)
     * @param string $filename Kaydedilecek dosya adı (uzantısız de olabilir)
     * @param array  $opts     maxWidth, thumbW, thumbH, quality, makeWebp
     * @return array|false  ['original'=>'...', 'thumb'=>'...', 'webp'=>'...', 'thumb_webp'=>'...']
     */
    function processUploadedImage(
        string $tmpPath,
        string $destDir,
        string $filename,
        array  $opts = []
    ): array|false {
        $maxWidth = (int) ($opts['maxWidth'] ?? 1600);
        $thumbW   = (int) ($opts['thumbW']  ?? 400);
        $thumbH   = (int) ($opts['thumbH']  ?? 225);  // 16:9
        $quality  = (int) ($opts['quality'] ?? 85);
        $makeWebp = isset($opts['makeWebp']) ? (bool)$opts['makeWebp'] : function_exists('imagewebp');

        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png'], true)) {
            return false;
        }

        // GD kontrolü
        if (!extension_loaded('gd')) {
            // GD yoksa sadece taşı
            $dest = $destDir . '/' . $filename;
            move_uploaded_file($tmpPath, $dest);
            return ['original' => basename($dest), 'thumb' => null, 'webp' => null, 'thumb_webp' => null];
        }

        // Görsel yükle
        $src = _imgLoad($tmpPath, $ext);
        if (!$src) return false;

        // EXIF orientation düzelt
        $src = _fixOrientation($src, $tmpPath, $ext);

        $origW = imagesx($src);
        $origH = imagesy($src);

        // ── Orijinal — max-width resize ───────────────────────
        if ($origW > $maxWidth) {
            $ratio  = $maxWidth / $origW;
            $newH   = (int) round($origH * $ratio);
            $resized = imagecreatetruecolor($maxWidth, $newH);
            _preserveAlpha($resized, $ext);
            imagecopyresampled($resized, $src, 0, 0, 0, 0, $maxWidth, $newH, $origW, $origH);
            imagedestroy($src);
            $src = $resized;
        }

        $origPath = $destDir . '/' . $filename;
        _imgSave($src, $origPath, $ext, $quality);

        $result = ['original' => $filename, 'thumb' => null, 'webp' => null, 'thumb_webp' => null];

        // ── Thumbnail — 16:9 centre crop ──────────────────────
        $thumbName = _addSuffix($filename, '_thumb');
        $thumbPath = $destDir . '/' . $thumbName;
        $thumb = _centerCrop($src, $thumbW, $thumbH);
        if ($thumb) {
            _imgSave($thumb, $thumbPath, $ext, $quality);
            $result['thumb'] = $thumbName;
            imagedestroy($thumb);
        }

        // ── WebP ──────────────────────────────────────────────
        if ($makeWebp && function_exists('imagewebp')) {
            $webpName = _addSuffix($filename, '', 'webp');
            $webpPath = $destDir . '/' . $webpName;
            imagewebp($src, $webpPath, $quality);
            $result['webp'] = $webpName;

            if ($thumb && function_exists('imagewebp')) {
                // thumb için de webp
                $thumbSrc = _imgLoad($thumbPath, $ext);
                if ($thumbSrc) {
                    $twName = _addSuffix($filename, '_thumb', 'webp');
                    imagewebp($thumbSrc, $destDir . '/' . $twName, $quality);
                    $result['thumb_webp'] = $twName;
                    imagedestroy($thumbSrc);
                }
            }
        }

        imagedestroy($src);
        return $result;
    }

    /**
     * <picture> tag ile WebP fallback üret.
     */
    function getImageTag(
        string  $path,
        string  $alt,
        array   $opts = []
    ): string {
        $class  = $opts['class']   ?? '';
        $width  = $opts['width']   ?? '';
        $height = $opts['height']  ?? '';
        $lazy   = ($opts['lazy']   ?? true) ? 'loading="lazy"' : 'loading="eager"';
        $sizes  = $opts['sizes']   ?? '';

        $webpPath = _toWebpPath($path);
        $hasWebp  = $webpPath && file_exists(PUBLIC_PATH . '/' . ltrim($webpPath, '/'));

        $wAttr = $width  ? "width=\"{$width}\""  : '';
        $hAttr = $height ? "height=\"{$height}\"" : '';
        $sAttr = $sizes  ? "sizes=\"{$sizes}\""   : '';
        $cAttr = $class  ? "class=\"{$class}\""   : '';

        $imgTag = "<img src=\"" . e($path) . "\" alt=\"" . e($alt) . "\""
            . " {$wAttr} {$hAttr} {$lazy} {$cAttr}>";

        if (!$hasWebp) {
            return $imgTag;
        }

        return "<picture>"
            . "<source srcset=\"" . e($webpPath) . "\" {$sAttr} type=\"image/webp\">"
            . $imgTag
            . "</picture>";
    }

    /**
     * Görsel URL'si — size: original | thumb | webp | thumb_webp
     */
    function getImageUrl(string $filename, string $size = 'original'): string
    {
        if ($filename === '') return SITE_URL . '/images/og-default.png';

        $map = [
            'original'  => $filename,
            'thumb'     => _addSuffix($filename, '_thumb'),
            'webp'      => _addSuffix($filename, '', 'webp'),
            'thumb_webp'=> _addSuffix($filename, '_thumb', 'webp'),
        ];

        $name = $map[$size] ?? $filename;
        $absPath = PUBLIC_PATH . '/images/uploads/' . $name;

        // Dosya yoksa orijinale düş
        if ($size !== 'original' && !file_exists($absPath)) {
            $name = $filename;
        }

        return SITE_URL . '/images/uploads/' . $name;
    }

    // ── Dahili yardımcılar ──────────────────────────────────────

    function _imgLoad(string $path, string $ext) {
        return match($ext) {
            'jpg','jpeg' => @imagecreatefromjpeg($path),
            'png'        => @imagecreatefrompng($path),
            default      => false,
        };
    }

    function _imgSave($img, string $path, string $ext, int $q): void {
        match($ext) {
            'jpg','jpeg' => imagejpeg($img, $path, $q),
            'png'        => imagepng($img, $path, (int)round((100-$q)/10)),
            default      => null,
        };
    }

    function _preserveAlpha($img, string $ext): void {
        if ($ext === 'png') {
            imagealphablending($img, false);
            imagesavealpha($img, true);
        }
    }

    function _fixOrientation($img, string $path, string $ext) {
        if ($ext !== 'jpg' && $ext !== 'jpeg') return $img;
        if (!function_exists('exif_read_data')) return $img;
        $exif = @exif_read_data($path);
        $ori  = $exif['Orientation'] ?? 1;
        return match((int)$ori) {
            3  => imagerotate($img, 180, 0),
            6  => imagerotate($img, -90, 0),
            8  => imagerotate($img, 90, 0),
            default => $img,
        };
    }

    function _centerCrop($src, int $tw, int $th) {
        $sw = imagesx($src);
        $sh = imagesy($src);

        $srcRatio  = $sw / $sh;
        $destRatio = $tw / $th;

        if ($srcRatio > $destRatio) {
            $cropH = $sh;
            $cropW = (int) round($sh * $destRatio);
            $cropX = (int) round(($sw - $cropW) / 2);
            $cropY = 0;
        } else {
            $cropW = $sw;
            $cropH = (int) round($sw / $destRatio);
            $cropX = 0;
            $cropY = (int) round(($sh - $cropH) / 3); // üstten 1/3 — yüzler için
        }

        $thumb = imagecreatetruecolor($tw, $th);
        imagecopyresampled($thumb, $src, 0, 0, $cropX, $cropY, $tw, $th, $cropW, $cropH);
        return $thumb;
    }

    function _addSuffix(string $filename, string $suffix, string $newExt = ''): string {
        $base = pathinfo($filename, PATHINFO_FILENAME);
        $ext  = $newExt ?: pathinfo($filename, PATHINFO_EXTENSION);
        return $base . $suffix . '.' . $ext;
    }

    function _toWebpPath(?string $path): ?string {
        if (!$path) return null;
        return preg_replace('/\.(jpe?g|png)$/i', '.webp', $path);
    }

    /**
     * Bir görselin tüm varyantlarını siler.
     * original + _thumb + .webp + _thumb.webp
     *
     * @param string $filename  Orijinal dosya adı (ör: "1774238140_foto.jpg")
     * @param string $uploadDir Uploads dizini (varsayılan: BASE_PATH/public/images/uploads)
     * @return int   Silinen dosya sayısı
     */
    function deleteImageVariants(string $filename, string $uploadDir = ''): int
    {
        if ($filename === '') return 0;

        if ($uploadDir === '') {
           $uploadDir = defined('PUBLIC_PATH')
           ? PUBLIC_PATH . '/images/uploads'
           : ((defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 2)) . '/public/images/uploads');
        }

        $base = pathinfo($filename, PATHINFO_FILENAME);
        $ext  = pathinfo($filename, PATHINFO_EXTENSION);

        $variants = [
            $filename,                        // original
            $base . '_thumb.' . $ext,         // thumbnail
            $base . '.webp',                  // webp
            $base . '_thumb.webp',            // thumb webp
        ];

        $deleted = 0;
        foreach ($variants as $file) {
            $path = rtrim($uploadDir, '/') . '/' . $file;
            if (file_exists($path)) {
                if (@unlink($path)) {
                    $deleted++;
                }
            }
        }

        return $deleted;
    }
}
