# PWA İkonları

Bu klasöre aşağıdaki PNG dosyalarını koymanız gerekmektedir.

## Gereken Dosyalar

| Dosya | Boyut | Kullanım |
|---|---|---|
| icon-72.png | 72×72 | Android legacy |
| icon-96.png | 96×96 | Android / shortcut |
| icon-128.png | 128×128 | iOS 120px @2x |
| icon-144.png | 144×144 | Windows tile / iOS |
| icon-152.png | 152×152 | iOS iPad |
| icon-192.png | 192×192 | Android Ana ekran |
| icon-384.png | 384×384 | Android Splash |
| icon-512.png | 512×512 | Android Play Store / Splash |
| shortcut-randevu.png | 96×96 | Kısayol ikonu |
| shortcut-iletisim.png | 96×96 | Kısayol ikonu |

## Ücretsiz Oluşturma Araçları

### Yöntem 1 — realfavicongenerator.net (En Kolay)
1. https://realfavicongenerator.net adresine git
2. 512×512 PNG logoyu yükle
3. "Generate" → ZIP indir
4. İçindeki dosyaları bu klasöre koy

### Yöntem 2 — PWABuilder
1. https://www.pwabuilder.com/imageGenerator adresine git
2. Logonu yükle
3. Tüm boyutları tek seferde indir

### Yöntem 3 — Maskable.app (Maskable ikon için)
1. https://maskable.app/editor adresine git
2. icon-192.png ve icon-512.png için "maskable" versiyon üret
3. Logonun etrafında güvenli alan bırak

## Maskable İkon Nedir?

Android'de "Uyarlanabilir İkon" özelliği için gereklidir.
Logonun daire/kare içinde kırpılmadan gösterilmesini sağlar.
manifest.json'da "purpose": "any maskable" olarak işaretlenmiştir.

## Hızlı Test

PWA kurulumunu test etmek için:
1. Chrome DevTools → Application → Manifest
2. Lighthouse → PWA kategorisi
3. https://web.dev/measure

## iOS Splash Screen (Opsiyonel)

iOS splash screen için her iPhone boyutuna özel PNG gerekir.
Şimdilik temel kurulum yeterli, splash screen daha sonra eklenebilir.
