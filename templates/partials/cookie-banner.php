<?php
/**
 * templates/partials/cookie-banner.php
 *
 * Basitleştirilmiş çerez bildirimi.
 * "Siteyi kullanmaya devam ederek kabul ediyorsunuz" yaklaşımı.
 * Banner localStorage'a kaydedilir, bir kez kapandığında tekrar çıkmaz.
 */
?>
<div class="cookie-bar" id="cookie-bar" role="dialog"
     aria-label="Çerez bildirimi" aria-live="polite" hidden>

    <div class="cookie-bar__inner">
        <div class="cookie-bar__icon">
            <i class="uil uil-shield-check" aria-hidden="true"></i>
        </div>

        <p class="cookie-bar__text">
            Bu siteyi kullanmaya devam ederek
            <a href="<?= ROOT_URL ?>cerez-politikasi" target="_blank" rel="noopener">çerez politikamızı</a>
            kabul etmiş olursunuz. Oturum işlevleri için zorunlu çerezler kullanılmaktadır.
        </p>

        <div class="cookie-bar__actions">
            <button class="cookie-bar__btn cookie-bar__btn--accept"
                    id="cookie-accept"
                    type="button">
                Anladım
            </button>
            <button class="cookie-bar__btn cookie-bar__btn--close"
                    id="cookie-close"
                    type="button"
                    aria-label="Kapat">
                <i class="uil uil-times"></i>
            </button>
        </div>
    </div>

</div><!-- /.cookie-bar -->

<script>
(function () {
    'use strict';

    var STORAGE_KEY = 'cookie_consent_v2';
    var bar = document.getElementById('cookie-bar');

    if (!bar) return;

    try {
        if (localStorage.getItem(STORAGE_KEY)) {
            return; /* Daha önce kabul edilmiş */
        }
    } catch (e) {}

    /* 800ms gecikmeyle göster — sayfa yüklenmesini bekle
       removeAttribute('hidden') → display:block → reflow → RAF → animasyon başlar
       Tek setTimeout'ta yapılırsa browser reflow atlıyor, transform animasyonu çalışmıyor */
    setTimeout(function () {
        bar.removeAttribute('hidden');
        /* Çift RAF: ilk RAF layout'u flush eder, ikinci RAF animasyonu tetikler */
        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                bar.classList.add('cookie-bar--visible');
            });
        });
    }, 800);

    function accept() {
        try { localStorage.setItem(STORAGE_KEY, '1'); } catch (e) {}
        bar.classList.remove('cookie-bar--visible');
        bar.classList.add('cookie-bar--hiding');
        setTimeout(function () { bar.setAttribute('hidden', ''); }, 400);
    }

    document.getElementById('cookie-accept').addEventListener('click', accept);
    document.getElementById('cookie-close').addEventListener('click', accept);
})();
</script>
