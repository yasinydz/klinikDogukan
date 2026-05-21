<?php
/**
 * templates/partials/nav.php
 * Kaynak: psikologtype (type1) — centered logo layout
 */
?>
<nav id="main-nav" role="navigation" aria-label="Ana navigasyon">
    <div class="container nav__container">

        <!-- Sol grup -->
        <div class="nav__side nav__side--left">
            <ul class="nav__items nav__items--desktop" role="list">

                <li><a href="<?= ROOT_URL ?>hakkimda">Hakkımda</a></li>

                <li class="nav__profile">
                    <a href="<?= ROOT_URL ?>hizmetler">Hizmetler</a>
                    <ul role="list">
                        <li><a href="<?= ROOT_URL ?>hizmetler">Tüm Hizmetler</a></li>
                        <li><a href="<?= ROOT_URL ?>hizmetler/anksiyete-terapisi">Anksiyete Terapisi</a></li>
                        <li><a href="<?= ROOT_URL ?>hizmetler/depresyon-terapisi">Depresyon Terapisi</a></li>
                        <li><a href="<?= ROOT_URL ?>hizmetler/okb-terapisi">OKB Terapisi</a></li>
                        <li><a href="<?= ROOT_URL ?>hizmetler/travma-terapisi">Travma Terapisi</a></li>
                        <li><a href="<?= ROOT_URL ?>hizmetler/iliskisel-sorunlar">İlişkisel Sorunlar</a></li>
                        <li><a href="<?= ROOT_URL ?>hizmetler/online-terapi">Online Terapi</a></li>
                    </ul>
                </li>

                <li><a href="<?= ROOT_URL ?>blog">Blog</a></li>

            </ul>
        </div>

        <!-- Ortada logo -->
        <a href="<?= ROOT_URL ?>"
           class="nav__logo"
           aria-label="<?= e(SITE_NAME) ?> Ana Sayfa">
            Psikolog Doğukan Kopuk
        </a>

        <!-- Sağ grup -->
        <div class="nav__side nav__side--right">
            <ul class="nav__items nav__items--desktop" role="list">

                <li><a href="<?= ROOT_URL ?>#galeri">Galeri</a></li>
                <li><a href="<?= ROOT_URL ?>sss">SSS</a></li>
                <li><a href="<?= ROOT_URL ?>iletisim">İletişim</a></li>

                <?php if ($isAdminLoggedIn): ?>
                <li class="nav__profile">
                    <a href="<?= ROOT_URL ?>admin"><?= e($adminUsername ?? 'Admin') ?></a>
                    <ul role="list">
                        <li><a href="<?= ROOT_URL ?>admin">Panel</a></li>
                        <li><a href="<?= ROOT_URL ?>admin/cikis">Çıkış Yap</a></li>
                    </ul>
                </li>
                <?php endif; ?>

            </ul>

            <div class="nav__actions">
                <button class="theme-toggle" id="theme-toggle"
                        aria-label="Tema değiştir" title="Tema değiştir">
                    <i class="uil uil-moon" id="theme-icon" aria-hidden="true"></i>
                </button>
                <a href="<?= ROOT_URL ?>randevu"
                   class="btn sm nav__randevu-desktop"
                   aria-label="Randevu al">
                    Randevu Al
                </a>
                <button id="open__nav-btn"
                        aria-label="Menüyü aç"
                        aria-expanded="false"
                        aria-controls="mobile-menu">
                    <i class="uil uil-bars" aria-hidden="true"></i>
                </button>
            </div>
        </div>

    </div>
</nav>

<!-- Mobil full-screen menü -->
<div class="mob-menu" id="mobile-menu" role="dialog"
     aria-label="Navigasyon menüsü" aria-modal="true" aria-hidden="true">

    <div class="mob-menu__header">
        <a href="<?= ROOT_URL ?>" class="mob-menu__logo" tabindex="-1">
            Psikolog Doğukan Kopuk
        </a>
        <button class="mob-menu__close" id="close__nav-btn"
                aria-label="Menüyü kapat" aria-expanded="false">
            <i class="uil uil-times" aria-hidden="true"></i>
        </button>
    </div>

    <nav class="mob-menu__nav" aria-label="Mobil navigasyon">
        <ul role="list">
            <li>
                <a href="<?= ROOT_URL ?>hakkimda" class="mob-menu__link">
                    <i class="uil uil-user-circle" aria-hidden="true"></i>
                    Hakkımda
                </a>
            </li>
            <li class="mob-menu__group">
                <button class="mob-menu__group-toggle" aria-expanded="false">
                    <span>
                        <i class="uil uil-heart-rate" aria-hidden="true"></i>
                        Hizmetler
                    </span>
                    <i class="uil uil-angle-down mob-menu__chevron" aria-hidden="true"></i>
                </button>
                <ul class="mob-menu__sub" aria-hidden="true" role="list">
                    <li><a href="<?= ROOT_URL ?>hizmetler/anksiyete-terapisi">Anksiyete Terapisi</a></li>
                    <li><a href="<?= ROOT_URL ?>hizmetler/depresyon-terapisi">Depresyon Terapisi</a></li>
                    <li><a href="<?= ROOT_URL ?>hizmetler/okb-terapisi">OKB Terapisi</a></li>
                    <li><a href="<?= ROOT_URL ?>hizmetler/travma-terapisi">Travma Terapisi</a></li>
                    <li><a href="<?= ROOT_URL ?>hizmetler/iliskisel-sorunlar">İlişkisel Sorunlar</a></li>
                    <li><a href="<?= ROOT_URL ?>hizmetler/online-terapi">Online Terapi</a></li>
                </ul>
            </li>
            <li>
                <a href="<?= ROOT_URL ?>blog" class="mob-menu__link">
                    <i class="uil uil-newspaper" aria-hidden="true"></i>
                    Blog
                </a>
            </li>
            <li>
                <a href="<?= ROOT_URL ?>#galeri" class="mob-menu__link">
                    <i class="uil uil-image" aria-hidden="true"></i>
                    Galeri
                </a>
            </li>
            <li>
                <a href="<?= ROOT_URL ?>sss" class="mob-menu__link">
                    <i class="uil uil-question-circle" aria-hidden="true"></i>
                    SSS
                </a>
            </li>
            <li>
                <a href="<?= ROOT_URL ?>iletisim" class="mob-menu__link">
                    <i class="uil uil-envelope" aria-hidden="true"></i>
                    İletişim
                </a>
            </li>
            <?php if ($isAdminLoggedIn): ?>
            <li>
                <a href="<?= ROOT_URL ?>admin" class="mob-menu__link">
                    <i class="uil uil-dashboard" aria-hidden="true"></i>
                    <?= e($adminUsername ?? 'Admin') ?>
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="mob-menu__footer">
        <a href="<?= ROOT_URL ?>randevu" class="btn mob-menu__cta">
            <i class="uil uil-calendar-alt" aria-hidden="true"></i>
            Randevu Al
        </a>
        <p class="mob-menu__note">Ön görüşme ücretsizdir</p>
    </div>

</div>

<div class="mob-menu__backdrop" id="mob-menu-backdrop" aria-hidden="true"></div>
