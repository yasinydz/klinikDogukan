<?php
/**
 * templates/pages/appointment.php
 * URL: /randevu
 *
 * Layout: Sol — uzman tanıtım + güven | Sağ — randevu form widget
 * Mobilde: sağdan açılan off-canvas drawer
 */

// public_profile index.php'de yüklenmiyor
require_once BASE_PATH . '/app/helpers/public_profile.php';

$seo_title       = 'Randevu Al | Psikolog Doğukan Kopuk — İzmit';
$seo_description = 'Klinik Psikolog Doğukan Kopuk ile randevu almak için uygun tarih ve saati seçin. Yüz yüze ve online seans seçeneği.';
$seo_canonical   = SITE_URL . '/randevu';
$seo_noindex     = false;

$seo_breadcrumbs = [
    ['name' => 'Anasayfa', 'url' => SITE_URL],
    ['name' => 'Randevu',  'url' => ''],
];

$seo_schemas = [schemaLocalBusiness()];

// Randevu SSS verileri — schema için header öncesinde tanımlanmalı
$apptFaqItems = [
    ['question' => 'İlk seans gerçekten ücretsiz mi?',
     'answer'   => 'Evet. İlk görüşme 10 dakikalık ücretsiz bir değerlendirme seansıdır. Bu seansta ihtiyaçlarınızı konuşur, süreci açıklar ve size uygun planı birlikte belirleriz. Herhangi bir taahhüt gerektirmez.'],
    ['question' => 'Ne zaman geri dönüş yapılır?',
     'answer'   => 'Genellikle iş günleri 24 saat içinde telefon veya WhatsApp ile geri dönüş yapılmaktadır. Acil durumlarda doğrudan arayabilirsiniz.'],
    ['question' => 'Online seans nasıl gerçekleşiyor?',
     'answer'   => 'Online seanslar Zoom veya Google Meet üzerinden gerçekleştirilmektedir. Randevu saatinden önce bağlantı linki tarafınıza iletilir.'],
    ['question' => 'Seansı iptal etmek mümkün mü?',
     'answer'   => 'Evet. Seans saatinden en az 24 saat önce bildirmeniz yeterlidir.'],
];
$seo_schemas[] = schemaFaqPage($apptFaqItems);

$old = $_SESSION['old_input'] ?? [];
unset($_SESSION['old_input']);

// Müsait tarihleri çek
$availableDates = [];
$checkSlots = mysqli_query($connection, "SHOW TABLES LIKE 'appointment_slots'");
if ($checkSlots && mysqli_num_rows($checkSlots) > 0) {
    $result = mysqli_query($connection, "
        SELECT DISTINCT slot_date
        FROM appointment_slots
        WHERE is_available = 1
          AND is_blocked = 0
          AND slot_date >= CURDATE()
          AND slot_date NOT IN (
              SELECT a.preferred_date
              FROM appointments a
              WHERE a.status NOT IN ('cancelled')
              GROUP BY a.preferred_date
              HAVING COUNT(*) >= (
                  SELECT COUNT(*) FROM appointment_slots
                  WHERE slot_date = a.preferred_date AND is_available = 1
              )
          )
        ORDER BY slot_date ASC
        LIMIT 60
    ");
    while ($row = mysqli_fetch_assoc($result)) {
        $availableDates[] = $row['slot_date'];
    }
}

$_gunKisa = ['Paz','Pzt','Sal','Çar','Per','Cum','Cmt'];
$_ayKisa  = ['','Oca','Şub','Mar','Nis','May','Haz','Tem','Ağu','Eyl','Eki','Kas','Ara'];
$_today   = date('Y-m-d');

require_once BASE_PATH . '/templates/partials/header.php';
?>

<!-- ═══════════════════════════════════════════════════════════
     RANDEVU HERO — 2 kolon layout
     ═══════════════════════════════════════════════════════════ -->
<section class="randevu-hero">
    <div class="container randevu-hero__container">

        <!-- ── SOL: Uzman Tanıtım + Güven ───────────────────── -->
        <div class="randevu-hero__expert">

            <span class="category__button">Randevu</span>
            <h1>İlk Adımı Birlikte Atalım</h1>
            <p class="randevu-hero__desc">
                Size en uygun gün ve saati seçin, 24 saat içinde sizi arayarak
                detayları birlikte netleştirelim.
            </p>

            <!-- Uzman Kartı -->
            <?php $ep = getPublicProfile(); ?>
            <div class="randevu-expert-card">
                <img src="<?= e(publicAvatarUrl()) ?>"
                     alt="<?= e($ep['full_name']) ?>"
                     class="randevu-expert-card__img"
                     width="72" height="72" loading="lazy">
                <div class="randevu-expert-card__info">
                    <strong><?= e($ep['full_name']) ?></strong>
                    <span><?= e($ep['title']) ?></span>
                    <span class="randevu-expert-card__exp">
                        <!-- <i class="uil uil-award" aria-hidden="true"></i>
                        <?= $ep['experience'] ?>+ yıl deneyim -->
                    </span>
                </div>
            </div>

            <!-- Uzmanlık Alanları -->
            <div class="randevu-expert-section">
                <h3><i class="uil uil-focus-target" aria-hidden="true"></i> Uzmanlık Alanları</h3>
                <div class="randevu-expert-tags">
                    <?php foreach (publicSpecialties() as $spec): ?>
                    <span class="randevu-expert-tag"><?= e($spec) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Yaklaşım -->
            <div class="randevu-expert-section">
                <h3><i class="uil uil-lightbulb-alt" aria-hidden="true"></i> Terapi Yaklaşımı</h3>
                <p class="text-muted" style="font-size:0.9rem;line-height:1.7;">
                    <?= e($ep['approach']) ?>
                </p>
            </div>

            <!-- Güven Maddeleri -->
            <ul class="randevu-trust-list">
                <li>
                    <i class="uil uil-check-circle" aria-hidden="true"></i>
                    Ön görüşme ücretsizdir
                </li>
                <li>
                    <i class="uil uil-lock" aria-hidden="true"></i>
                    Bilgileriniz KVKK kapsamında gizli tutulur
                </li>
                <li>
                    <i class="uil uil-video" aria-hidden="true"></i>
                    Yüz yüze veya online seans seçeneği
                </li>
                <li>
                    <i class="uil uil-clock" aria-hidden="true"></i>
                    <?= e(siteSetting('work_hours_weekday', WORK_HOURS_WEEKDAY)) ?>
                </li>
            </ul>

            <!-- Mobile CTA — drawer açar -->
            <div class="randevu-mobile-cta">
                <button type="button" class="btn btn--full" id="open-appt-drawer">
                    <i class="uil uil-calendar-alt" aria-hidden="true"></i>
                    Uygun Saatleri Gör
                </button>
            </div>

            <!-- Alt CTA -->
            <div class="randevu-hero__alt">
                <p>Hemen ulaşmak isterseniz:</p>
                <div class="randevu-hero__alt-btns">
                    <a href="https://wa.me/<?= siteSetting('contact_whatsapp', CONTACT_WHATSAPP) ?>?text=<?= urlencode('Merhaba, randevu almak istiyorum.') ?>"
                       class="btn btn--outline" target="_blank" rel="noopener noreferrer">
                        <i class="uil uil-whatsapp" aria-hidden="true"></i> WhatsApp
                    </a>
                    <a href="tel:<?= e(siteSetting('contact_phone_href', CONTACT_PHONE_HREF)) ?>" class="btn btn--outline">
                        <i class="uil uil-phone" aria-hidden="true"></i>
                        <?= e(siteSetting('contact_phone', CONTACT_PHONE)) ?>
                    </a>
                </div>
            </div>
        </div>

        <!-- ── SAĞ: Randevu Form Widget ─────────────────────── -->
        <!-- Desktop: normal inline sticky | Mobile: sağdan açılan off-canvas drawer -->
        <div class="randevu-form-wrap" id="appt-drawer"
             role="dialog" aria-modal="true" aria-label="Randevu formu" aria-hidden="true">

            <!-- Drawer handle — mobilde sağdan açılan panelin üst çubuğu -->
            <div class="appt-drawer__header">
                <h2 class="appt-drawer__title">Randevu Talebi</h2>
                <button type="button" class="appt-drawer__close" id="close-appt-drawer" aria-label="Randevu formunu kapat">
                    <i class="uil uil-times" aria-hidden="true"></i>
                </button>
            </div>

            <?php flashRender(); ?>

            <form class="randevu-form"
                  method="POST"
                  action="<?= ROOT_URL ?>randevu/al"
                  novalidate>

                <?= csrfField() ?>
                <input type="hidden" name="lead_source" value="<?= e($_GET['utm_source'] ?? '') ?>">

                <!-- Desktop'ta görünür, mobilde drawer header zaten var -->
                <h3 class="randevu-form__desktop-title">Randevu Talebi</h3>

                <!-- ① Görüşme Tipi -->
                <div class="appt-type-tabs" role="tablist" aria-label="Görüşme tipi seçimi">
                    <button type="button" class="appt-type-tab <?= ($old['session_type'] ?? 'in_person') === 'in_person' ? 'active' : '' ?>"
                            role="tab" data-value="in_person"
                            aria-selected="<?= ($old['session_type'] ?? 'in_person') === 'in_person' ? 'true' : 'false' ?>">
                        <i class="uil uil-map-marker" aria-hidden="true"></i>
                        Yüz Yüze
                    </button>
                    <button type="button" class="appt-type-tab <?= ($old['session_type'] ?? '') === 'online' ? 'active' : '' ?>"
                            role="tab" data-value="online"
                            aria-selected="<?= ($old['session_type'] ?? '') === 'online' ? 'true' : 'false' ?>">
                        <i class="uil uil-video" aria-hidden="true"></i>
                        Online
                    </button>
                </div>
                <input type="hidden" id="session_type" name="session_type"
                       value="<?= e($old['session_type'] ?? 'in_person') ?>">

                <!-- ② Tarih Seçimi -->
                <div class="form__control">
                    <label>Tarih Seçin <span aria-hidden="true">*</span></label>
                    <?php if (!empty($availableDates)): ?>
                    <div class="appt-dates" role="listbox" aria-label="Müsait tarihler">
                        <?php foreach ($availableDates as $date):
                            $ts      = strtotime($date);
                            $isToday = ($date === $_today);
                        ?>
                        <div class="appt-date-chip <?= $isToday ? 'today' : '' ?> <?= ($old['preferred_date'] ?? '') === $date ? 'active' : '' ?>"
                             role="option"
                             data-date="<?= e($date) ?>"
                             tabindex="0"
                             aria-label="<?= turkceTarih($date, 'd F Y l') ?>"
                             aria-selected="<?= ($old['preferred_date'] ?? '') === $date ? 'true' : 'false' ?>">
                            <span class="appt-date-weekday"><?= $isToday ? 'Bugün' : $_gunKisa[(int)date('w', $ts)] ?></span>
                            <span class="appt-date-day"><?= date('d', $ts) ?></span>
                            <span class="appt-date-month"><?= $_ayKisa[(int)date('n', $ts)] ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <input type="hidden" id="preferred_date" name="preferred_date"
                           value="<?= e($old['preferred_date'] ?? '') ?>" required>
                    <?php else: ?>
                    <input type="date" id="preferred_date" name="preferred_date"
                           value="<?= e($old['preferred_date'] ?? '') ?>"
                           min="<?= date('Y-m-d') ?>" required>
                    <small style="color:var(--color-text-faint);">
                        Şu anda müsait slot bulunamadı. Tercih ettiğiniz tarihi girerek talebinizi iletebilirsiniz.
                    </small>
                    <?php endif; ?>
                </div>

                <!-- ③ Saat Seçimi -->
                <div class="form__control" id="time-slot-wrapper" style="display:none;">
                    <label>Saat Seçin <span aria-hidden="true">*</span></label>
                    <div id="appt-times-container"></div>
                    <input type="hidden" id="preferred_time" name="preferred_time"
                           value="<?= e($old['preferred_time'] ?? '') ?>">
                </div>

                <!-- ④ Seçim Özeti -->
                <div id="appt-summary" class="appt-summary" style="display:none;">
                    <i class="uil uil-check-circle" aria-hidden="true"></i>
                    <span id="appt-summary-text"></span>
                </div>

                <!-- ⑤ Kişisel Bilgiler -->
                <div class="form__control">
                    <label for="full_name">Ad Soyad <span aria-hidden="true">*</span></label>
                    <input type="text" id="full_name" name="full_name"
                           value="<?= e($old['full_name'] ?? '') ?>"
                           placeholder="Adınız Soyadınız"
                           required autocomplete="name" maxlength="100">
                </div>

                <div class="form__control">
                    <label for="phone">Telefon <span aria-hidden="true">*</span></label>
                    <input type="tel" id="phone" name="phone"
                           value="<?= e($old['phone'] ?? '') ?>"
                           placeholder="0500 000 00 00"
                           required autocomplete="tel" maxlength="20">
                </div>

                <div class="form__control">
                    <label for="email">
                        E-posta
                        <small style="font-weight:400;color:var(--color-text-faint);">(Bildirim almak istiyorsanız(opsiyonel))</small>
                    </label>
                    <input type="email" id="email" name="email"
                           value="<?= e($old['email'] ?? '') ?>"
                           placeholder="ornek@email.com"
                           autocomplete="email" maxlength="150">
                </div>

                <!-- ⑥ KVKK -->
                <div class="contact__consent">
                    <div class="form__control inline">
                        <input type="checkbox" id="privacy_notice_accepted"
                               name="privacy_notice_accepted" value="1" required>
                        <label for="privacy_notice_accepted">
                            <a href="<?= ROOT_URL ?>kvkk-aydinlatma" target="_blank" rel="noopener">
                                KVKK Aydınlatma Metnini
                            </a>
                            okudum ve kişisel verilerimin randevu amacıyla işlenmesini anlıyorum. *
                        </label>
                    </div>
                    <!-- <div class="form__control inline">
                        <input type="checkbox" id="commercial_consent_given"
                               name="commercial_consent_given" value="1">
                        <label for="commercial_consent_given">
                            Ticari elektronik ileti almak istiyorum.
                            <small style="display:block;color:var(--color-text-faint);margin-top:2px;">Opsiyonel</small>
                        </label>
                    </div> -->
                </div>

                <button type="submit" name="submit" class="btn btn--full">
                    <i class="uil uil-calendar-alt" aria-hidden="true"></i>
                    Randevu Talep Et
                </button>

                <p class="randevu-form__note">
                    <i class="uil uil-lock" aria-hidden="true"></i>
                    Bilgileriniz yalnızca randevu amacıyla kullanılır,
                    üçüncü taraflarla paylaşılmaz.
                </p>

            </form>
        </div>

    </div>
</section>

<!-- Drawer backdrop (mobile only) -->
<div class="appt-drawer__backdrop" id="appt-drawer-backdrop"></div>

<!-- ═══════════════════════════════════════════════════════════
     SSS
     ═══════════════════════════════════════════════════════════ -->
<section class="randevu-sss">
    <div class="container">
        <div class="section__header text-center" style="margin-bottom:var(--space-7);">
            <h2>Randevu Hakkında Sık Sorulan Sorular</h2>
        </div>
        <div class="faq__list">
            <?php
            foreach ($apptFaqItems as $item):
            ?>
            <div class="faq__item">
                <button class="faq__question" aria-expanded="false">
                    <?= e($item['question']) ?>
                    <i class="uil uil-angle-down" aria-hidden="true"></i>
                </button>
                <div class="faq__answer" hidden>
                    <p><?= e($item['answer']) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ── Randevu JS ──────────────────────────────────────────── -->
<script>
(function () {
    var apiUrl        = '<?= ROOT_URL ?>randevu/slotlar';
    var dateChips     = document.querySelectorAll('.appt-date-chip');
    var dateInput     = document.getElementById('preferred_date');
    var timeInput     = document.getElementById('preferred_time');
    var timeWrapper   = document.getElementById('time-slot-wrapper');
    var timeContainer = document.getElementById('appt-times-container');
    var summaryEl     = document.getElementById('appt-summary');
    var summaryText   = document.getElementById('appt-summary-text');
    var typeTabs      = document.querySelectorAll('.appt-type-tab');
    var typeInput     = document.getElementById('session_type');

    if (!dateInput || !timeInput) return;

    typeTabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            typeTabs.forEach(function (t) { t.classList.remove('active'); t.setAttribute('aria-selected','false'); });
            this.classList.add('active');
            this.setAttribute('aria-selected','true');
            typeInput.value = this.dataset.value;
            updateSummary();
        });
    });

    dateChips.forEach(function (chip) {
        chip.addEventListener('click', function () { selectDate(this); });
        chip.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); selectDate(this); }
        });
    });

    function selectDate(chip) {
        dateChips.forEach(function (c) { c.classList.remove('active'); c.setAttribute('aria-selected','false'); });
        chip.classList.add('active');
        chip.setAttribute('aria-selected','true');
        dateInput.value = chip.dataset.date;
        timeInput.value = '';
        loadTimes(chip.dataset.date);
        updateSummary();
        // Mobilde seçilen chip'e scroll
        chip.scrollIntoView({ behavior:'smooth', block:'nearest', inline:'center' });
    }

    function loadTimes(date) {
        timeContainer.innerHTML = '<div class="appt-loading"></div>';
        timeWrapper.style.display = 'block';

        fetch(apiUrl + '?date=' + encodeURIComponent(date))
            .then(function (r) { return r.json(); })
            .then(function (slots) {
                if (!slots || slots.length === 0) {
                    timeContainer.innerHTML =
                        '<div class="appt-empty"><i class="uil uil-calendar-slash"></i>Bu tarihte müsait saat bulunmuyor.</div>';
                    return;
                }
                var html = '<div class="appt-times">';
                slots.forEach(function (slot) {
                    html += '<div class="appt-time-pill" data-time="' + slot.time + '" tabindex="0" role="option">' + slot.time + '</div>';
                });
                html += '</div>';
                timeContainer.innerHTML = html;

                timeContainer.querySelectorAll('.appt-time-pill').forEach(function (pill) {
                    pill.addEventListener('click', function () { selectTime(this); });
                    pill.addEventListener('keydown', function (e) {
                        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); selectTime(this); }
                    });
                });

                var oldTime = '<?= e($old['preferred_time'] ?? '') ?>';
                if (oldTime) {
                    var match = timeContainer.querySelector('[data-time="' + oldTime + '"]');
                    if (match) selectTime(match);
                }
            })
            .catch(function () {
                timeContainer.innerHTML =
                    '<div class="appt-empty"><i class="uil uil-exclamation-triangle"></i>Saatler yüklenemedi. Lütfen sayfayı yenileyin.</div>';
            });
    }

    function selectTime(pill) {
        timeContainer.querySelectorAll('.appt-time-pill').forEach(function (p) { p.classList.remove('active'); });
        pill.classList.add('active');
        timeInput.value = pill.dataset.time;
        updateSummary();
    }

    function updateSummary() {
        var date = dateInput.value, time = timeInput.value;
        var type = typeInput.value === 'online' ? 'Online' : 'Yüz Yüze';
        if (date && time) {
            var chip = document.querySelector('.appt-date-chip[data-date="' + date + '"]');
            var dateLabel = chip ? chip.getAttribute('aria-label') : date;
            summaryText.innerHTML = '<strong>' + type + '</strong> · ' + dateLabel + ' · <strong>' + time + '</strong>';
            summaryEl.style.display = 'flex';
        } else {
            summaryEl.style.display = 'none';
        }
    }

    if (dateInput.value) {
        var activeChip = document.querySelector('.appt-date-chip[data-date="' + dateInput.value + '"]');
        if (activeChip) { activeChip.classList.add('active'); activeChip.setAttribute('aria-selected','true'); loadTimes(dateInput.value); }
    }
    if (typeInput.value) {
        typeTabs.forEach(function (t) {
            var isActive = t.dataset.value === typeInput.value;
            t.classList.toggle('active', isActive);
            t.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });
    }
})();

/* ── Mobile Drawer — Off-Canvas Right ──────────────────────── */
(function () {
    var openBtn   = document.getElementById('open-appt-drawer');
    var closeBtn  = document.getElementById('close-appt-drawer');
    var drawer    = document.getElementById('appt-drawer');
    var backdrop  = document.getElementById('appt-drawer-backdrop');

    if (!openBtn || !drawer) return;

    var isOpen = false;
    var lastFocused = null;
    var scrollPos = 0;

    function openDrawer() {
        if (isOpen) return;
        isOpen = true;
        lastFocused = document.activeElement;

        // Scroll pozisyonunu kaydet
        scrollPos = window.pageYOffset || document.documentElement.scrollTop;

        drawer.classList.add('appt-drawer--open');
        drawer.setAttribute('aria-hidden', 'false');
        backdrop.classList.add('appt-drawer__backdrop--visible');
        document.body.classList.add('drawer-open');

        // Drawer'ı en üste scroll et
        drawer.scrollTop = 0;

        // İlk focusable elemente odaklan
        setTimeout(function () {
            var first = drawer.querySelector('button, [href], input:not([type="hidden"]), select, textarea');
            if (first) first.focus();
        }, 350);
    }

    function closeDrawer() {
        if (!isOpen) return;
        isOpen = false;

        drawer.classList.remove('appt-drawer--open');
        drawer.setAttribute('aria-hidden', 'true');
        backdrop.classList.remove('appt-drawer__backdrop--visible');
        document.body.classList.remove('drawer-open');

        // Scroll pozisyonunu geri yükle
        window.scrollTo(0, scrollPos);

        // Önceki focus'a dön
        if (lastFocused) {
            setTimeout(function () { lastFocused.focus(); }, 100);
        }
    }

    openBtn.addEventListener('click', openDrawer);
    if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
    if (backdrop) backdrop.addEventListener('click', closeDrawer);

    // ESC key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && isOpen) {
            closeDrawer();
        }
    });

    // Focus trap — Tab/Shift+Tab drawer içinde kalsın
    drawer.addEventListener('keydown', function (e) {
        if (e.key !== 'Tab' || !isOpen) return;

        var focusables = drawer.querySelectorAll(
            'button, [href], input:not([type="hidden"]), select, textarea, [tabindex]:not([tabindex="-1"])'
        );
        if (focusables.length === 0) return;

        var first = focusables[0];
        var last  = focusables[focusables.length - 1];

        if (e.shiftKey) {
            if (document.activeElement === first) {
                e.preventDefault();
                last.focus();
            }
        } else {
            if (document.activeElement === last) {
                e.preventDefault();
                first.focus();
            }
        }
    });
})();
</script>

<?php require BASE_PATH . '/templates/partials/footer.php'; ?>
