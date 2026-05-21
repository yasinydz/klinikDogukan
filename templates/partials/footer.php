<?php
/**
 * templates/partials/footer.php
 *
 * Site footer ve mobil FAB navigasyon.
 * Her public sayfada include edilir.
 */
?>
</main><!-- /#main-content -->

<footer class="site-footer" role="contentinfo">

    <!-- Sosyal Medya -->
    <div class="footer__socials">
        <?php if (SOCIAL_YOUTUBE !== ''): ?>
        <a href="<?= e(SOCIAL_YOUTUBE) ?>"
           target="_blank" rel="noopener noreferrer"
           aria-label="YouTube">
            <i class="uil uil-youtube" aria-hidden="true"></i>
        </a>
        <?php endif; ?>

        <?php if (SOCIAL_INSTAGRAM !== ''): ?>
        <a href="<?= e(SOCIAL_INSTAGRAM) ?>"
           target="_blank" rel="noopener noreferrer"
           aria-label="Instagram">
            <i class="uil uil-instagram-alt" aria-hidden="true"></i>
        </a>
        <?php endif; ?>

        <?php if (SOCIAL_LINKEDIN !== ''): ?>
        <a href="<?= e(SOCIAL_LINKEDIN) ?>"
           target="_blank" rel="noopener noreferrer"
           aria-label="LinkedIn">
            <i class="uil uil-linkedin" aria-hidden="true"></i>
        </a>
        <?php endif; ?>

        <?php if (SOCIAL_FACEBOOK !== ''): ?>
        <a href="<?= e(SOCIAL_FACEBOOK) ?>"
           target="_blank" rel="noopener noreferrer"
           aria-label="Facebook">
            <i class="uil uil-facebook-f" aria-hidden="true"></i>
        </a>
        <?php endif; ?>
    </div>

    <div class="container footer__container">

        <!-- Marka & İletişim — NAP -->
        <article class="footer__brand">
            <h4><?= e(PSYCHOLOGIST_NAME) ?></h4>
            <p class="footer__brand-tagline">Klinik Psikolog</p>
            <p class="footer__brand-desc">
                Güvenli, bilimsel ve bireysel terapi ile hayatınıza
                yeni bir bakış açısı kazandırmak için yanınızdayım.
            </p>
            <a href="<?= ROOT_URL ?>randevu" class="btn btn--sm footer__brand-cta">
                <i class="uil uil-calendar-alt" aria-hidden="true"></i>
                Randevu Al
            </a>
            <ul class="footer__contact-list">
                <li>
                    <i class="uil uil-map-marker" aria-hidden="true"></i>
                    <?= e(siteSetting('address_district', ADDRESS_DISTRICT)) ?>, <?= e(siteSetting('address_city', ADDRESS_CITY)) ?>, Türkiye
                </li>
                <li>
                    <i class="uil uil-phone" aria-hidden="true"></i>
                    <a href="tel:<?= e(siteSetting('contact_phone_href', CONTACT_PHONE_HREF)) ?>">
                        <?= e(siteSetting('contact_phone', CONTACT_PHONE)) ?>
                    </a>
                </li>
                <li>
                    <i class="uil uil-envelope" aria-hidden="true"></i>
                    <a href="mailto:<?= e(siteSetting('contact_email', CONTACT_EMAIL)) ?>">
                        <?= e(siteSetting('contact_email', CONTACT_EMAIL)) ?>
                    </a>
                </li>
                <li>
                    <i class="uil uil-clock" aria-hidden="true"></i>
                    <?= e(siteSetting('work_hours_weekday', WORK_HOURS_WEEKDAY)) ?>
                </li>
                <li>
                    <i class="uil uil-clock" aria-hidden="true"></i>
                    <?= e(siteSetting('work_hours_saturday', WORK_HOURS_SATURDAY)) ?>
                </li>
                <?php
                $footerMapsDir  = siteSetting('maps_directions_url', '');
                $footerShowMaps = siteSetting('maps_show_footer', '0') === '1';
                if ($footerShowMaps && $footerMapsDir !== ''):
                ?>
                <li>
                    <i class="uil uil-directions" aria-hidden="true"></i>
                    <a href="<?= e($footerMapsDir) ?>" target="_blank" rel="noopener noreferrer">
                        Yol Tarifi Al →
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </article>

        <!-- Hizmetler -->
        <article>
            <h4>Hizmetler</h4>
            <ul>
                <li><a href="<?= ROOT_URL ?>hizmetler/anksiyete-terapisi">Anksiyete Terapisi</a></li>
                <li><a href="<?= ROOT_URL ?>hizmetler/depresyon-terapisi">Depresyon Terapisi</a></li>
                <li><a href="<?= ROOT_URL ?>hizmetler/okb-terapisi">OKB Terapisi</a></li>
                <li><a href="<?= ROOT_URL ?>hizmetler/travma-terapisi">Travma Terapisi</a></li>
                <li><a href="<?= ROOT_URL ?>hizmetler/iliskisel-sorunlar">İlişkisel Sorunlar</a></li>
                <li><a href="<?= ROOT_URL ?>hizmetler/online-terapi">Online Terapi</a></li>
            </ul>
        </article>

        <!-- Blog Kategorileri — Dinamik -->
        <article>
            <h4>Kategoriler</h4>
            <ul>
                <?php
                $footerCats = mysqli_query(
                    $connection,
                    "SELECT title, slug FROM post_categories ORDER BY title ASC LIMIT 6"
                );
                if ($footerCats && mysqli_num_rows($footerCats) > 0):
                    while ($fc = mysqli_fetch_assoc($footerCats)):
                ?>
                <li>
                    <a href="<?= ROOT_URL ?>kategori/<?= urlencode($fc['slug']) ?>">
                        <?= e($fc['title']) ?>
                    </a>
                </li>
                <?php endwhile;
                else: ?>
                <li><a href="<?= ROOT_URL ?>blog">Blog</a></li>
                <?php endif; ?>
            </ul>
        </article>

        <!-- Hızlı Linkler -->
        <article>
            <h4>Hızlı Linkler</h4>
            <ul>
                <li><a href="<?= ROOT_URL ?>">Ana Sayfa</a></li>
                <li><a href="<?= ROOT_URL ?>hakkimda">Hakkımda</a></li>
                <li><a href="<?= ROOT_URL ?>blog">Blog</a></li>
                <li><a href="<?= ROOT_URL ?>sss">SSS</a></li>
                <li><a href="<?= ROOT_URL ?>iletisim">İletişim</a></li>
                <li><a href="<?= ROOT_URL ?>randevu">Randevu Al</a></li>
                <li><a href="<?= ROOT_URL ?>izmit-psikolog">İzmit Psikolog</a></li>
                <li><a href="<?= ROOT_URL ?>kocaeli-psikolog">Kocaeli Psikolog</a></li>
            </ul>
        </article>

    </div><!-- /.footer__container -->

    <!-- Alt Bilgi -->
    <div class="footer__copyright container">
        <small>
            &copy; <?= date('Y') ?> <?= e(SITE_NAME) ?> — Tüm hakları saklıdır.
        </small>
        <small>
            <a href="<?= ROOT_URL ?>kvkk-aydinlatma">KVKK</a>
            &middot;
            <a href="<?= ROOT_URL ?>gizlilik-politikasi">Gizlilik</a>
            &middot;
            <a href="<?= ROOT_URL ?>cerez-politikasi">Çerezler</a>
        </small>
    </div>

</footer>

<!-- Yukarı Çık -->
<button class="scroll-top" id="scroll-top-btn"
        aria-label="Sayfanın başına git"
        title="Yukarı çık">
    <i class="uil uil-angle-up" aria-hidden="true"></i>
</button>

<!-- Mobil FAB Navigasyon -->
<div class="mob-nav" id="mobile-nav" aria-label="Hızlı iletişim">

    <div class="mob-nav__backdrop" id="mob-backdrop"></div>

    <!-- FAB Öğeleri -->
    <div class="mob-fab__item"
         id="fab-item-wa"
         data-href="https://wa.me/<?= CONTACT_WHATSAPP ?>?text=<?= urlencode('Merhaba, randevu almak istiyorum.') ?>"
         data-external="1">
        <div class="mob-fab__label">WhatsApp</div>
        <div class="mob-fab__circle mob-fab__circle--wa">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
            </svg>
        </div>
    </div>

    <div class="mob-fab__item"
         id="fab-item-rdv"
         data-href="<?= ROOT_URL ?>randevu"
         data-external="0">
        <div class="mob-fab__label">Randevu Al</div>
        <div class="mob-fab__circle mob-fab__circle--rdv">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8"  y1="2" x2="8"  y2="6"/>
                <line x1="3"  y1="10" x2="21" y2="10"/>
                <path d="m9 16 2 2 4-4"/>
            </svg>
        </div>
    </div>

    <div class="mob-fab__item"
         id="fab-item-tel"
         data-href="tel:<?= e(CONTACT_PHONE_HREF) ?>"
         data-external="0">
        <div class="mob-fab__label">Hemen Ara</div>
        <div class="mob-fab__circle mob-fab__circle--tel">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6.18 6.18l1.97-1.97a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
            </svg>
        </div>
    </div>

    <!-- Bottom Bar -->
    <div class="mob-nav__bar">
        <div class="mob-nav__bar-inner">

            <!-- WhatsApp Tab -->
            <button class="mob-tab"
                    id="mob-tab-wa"
                    aria-label="WhatsApp"
                    onclick="mobTabAction('wa', 'https://wa.me/<?= CONTACT_WHATSAPP ?>?text=<?= urlencode('Merhaba, randevu almak istiyorum.') ?>', true)">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                </svg>
                <span>WhatsApp</span>
            </button>

            <!-- Telefon Tab -->
            <button class="mob-tab"
                    id="mob-tab-tel"
                    aria-label="Telefon"
                    onclick="mobTabAction('tel', 'tel:<?= e(CONTACT_PHONE_HREF) ?>', false)">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6.18 6.18l1.97-1.97a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
                </svg>
                <span>Telefon</span>
            </button>

            <!-- FAB Center -->
            <div class="mob-fab__center">
                <div class="mob-fab__notch"></div>
                <button class="mob-fab__btn"
                        id="mob-fab-btn"
                        onclick="mobToggleFab()"
                        aria-label="Hızlı erişim menüsü">
                    <svg class="mob-fab__icon"
                         id="mob-fab-icon"
                         width="26" height="26"
                         viewBox="0 0 24 24" fill="none"
                         stroke="white" stroke-width="2.5"
                         stroke-linecap="round" aria-hidden="true">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5"  y1="12" x2="19" y2="12"/>
                    </svg>
                    <span class="mob-fab__pulse" id="mob-pulse"></span>
                </button>
            </div>

            <!-- Randevu Tab -->
            <button class="mob-tab"
                    id="mob-tab-rdv"
                    aria-label="Randevu"
                    onclick="mobTabAction('rdv', '<?= ROOT_URL ?>randevu', false)">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8"  y1="2" x2="8"  y2="6"/>
                    <line x1="3"  y1="10" x2="21" y2="10"/>
                    <path d="m9 16 2 2 4-4"/>
                </svg>
                <span>Randevu</span>
            </button>

            <!-- Ana Sayfa Tab -->
            <button class="mob-tab mob-tab--active"
                    id="mob-tab-home"
                    aria-label="Ana Sayfa"
                    onclick="mobTabAction('home', '<?= ROOT_URL ?>', false)">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
                <span>Ana Sayfa</span>
            </button>

        </div><!-- /.mob-nav__bar-inner -->
    </div><!-- /.mob-nav__bar -->

</div><!-- /.mob-nav -->

<script src="<?= ROOT_URL ?>js/main.js"></script>

<!-- Service Worker Registration -->
<script>
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
        navigator.serviceWorker.register('<?= ROOT_URL ?>sw.js')
            .then(function (reg) {
                if (<?= APP_DEBUG ? 'true' : 'false' ?>) {
                    console.log('SW registered:', reg.scope);
                }
            })
            .catch(function (err) {
                if (<?= APP_DEBUG ? 'true' : 'false' ?>) {
                    console.warn('SW registration failed:', err);
                }
            });
    });
}
</script>

</body>
</html>
<?php ob_end_flush(); ?>
