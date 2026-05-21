<?php
/**
 * templates/partials/trust-block.php
 *
 * Psikolog güven/yetkinlik bloğu.
 * Public profile helper'dan veri çeker.
 *
 * Değişkenler (opsiyonel):
 *   $trustVariant  string  'sidebar' | 'horizontal' | 'compact'
 *   $trustHeading  string  Blok başlığı
 */

require_once BASE_PATH . '/app/helpers/public_profile.php';

$_variant  = $trustVariant ?? 'horizontal';
$_heading  = $trustHeading ?? null;
$_profile  = getPublicProfile();

$_specs      = publicSpecialties();
$_certs      = publicCertifications();
$_approaches = array_filter(array_map('trim', explode(',', 'Bilişsel Davranışçı Terapi (BDT),Şema Terapi,Psikanalitik/Psikodinamik')));
?>

<div class="trust-block trust-block--<?= $_variant ?>" aria-label="Uzman bilgileri">

    <?php if ($_heading): ?>
    <h3 class="trust-block__heading"><?= e($_heading) ?></h3>
    <?php endif; ?>

    <div class="trust-block__grid">

        <div class="trust-block__profile">
            <img src="<?= e(publicAvatarUrl()) ?>"
                 alt="<?= e($_profile['full_name']) ?> — <?= e($_profile['title']) ?>"
                 width="72" height="72"
                 loading="lazy"
                 class="trust-block__avatar">
            <div class="trust-block__identity">
                <strong class="trust-block__name"><?= e($_profile['full_name']) ?></strong>
                <span class="trust-block__title"><?= e($_profile['title']) ?></span>
                <span class="trust-block__exp">
                    <i class="uil uil-award" aria-hidden="true"></i>
                    <?= $_profile['experience'] ?>+ yıl klinik deneyim
                </span>
            </div>
        </div>

        <div class="trust-block__section">
            <h4 class="trust-block__section-title">
                <i class="uil uil-focus-target" aria-hidden="true"></i>
                Uzmanlık Alanları
            </h4>
            <ul class="trust-block__tags" role="list">
                <?php foreach ($_specs as $spec): ?>
                <li><?= e($spec) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="trust-block__section">
            <h4 class="trust-block__section-title">
                <i class="uil uil-lightbulb-alt" aria-hidden="true"></i>
                Terapi Yaklaşımı
            </h4>
            <ul class="trust-block__list" role="list">
                <?php foreach ($_approaches as $approach): ?>
                <li>
                    <i class="uil uil-check-circle" aria-hidden="true"></i>
                    <?= e($approach) ?>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <?php if ($_variant !== 'compact'): ?>
        <div class="trust-block__section">
            <h4 class="trust-block__section-title">
                <i class="uil uil-medal" aria-hidden="true"></i>
                Sertifikalar
            </h4>
            <ul class="trust-block__list" role="list">
                <?php foreach ($_certs as $cert): ?>
                <li>
                    <i class="uil uil-check-circle" aria-hidden="true"></i>
                    <?= e($cert) ?>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

    </div>

    <?php if ($_variant === 'horizontal'): ?>
    <p class="trust-block__bio"><?= e($_profile['bio']) ?></p>
    <?php endif; ?>

</div>
