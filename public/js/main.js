/* =============================================================
   main.js — v4
   1. Tema (Dark / Light Mode)
   2. Navbar Scroll Davranışı
   3. Mobil Full-Screen Menü (mob-menu)
   4. Mob-Menu Grup Toggle (Hizmetler, Bölgeler)
   5. Admin Sidebar Toggle
   6. FAQ Accordion
   7. Scroll to Top
   8. Sticky CTA
   9. Aktif Nav Linki
   10. Mobil FAB Nav
   ============================================================= */

/* 1. TEMA ───────────────────────────────────────────────────── */
(function () {
    var STORAGE_KEY = 'theme';

    function getCurrentTheme() {
        return document.body.classList.contains('light-mode') ? 'light' : 'dark';
    }

    function applyTheme(theme) {
        document.body.classList.toggle('light-mode', theme === 'light');
        var icon = document.getElementById('theme-icon');
        if (icon) icon.className = theme === 'light' ? 'uil uil-moon' : 'uil uil-sun';
        try { localStorage.setItem(STORAGE_KEY, theme); } catch (e) {}
    }

    document.addEventListener('DOMContentLoaded', function () {
        var icon = document.getElementById('theme-icon');
        if (icon) icon.className = getCurrentTheme() === 'light' ? 'uil uil-moon' : 'uil uil-sun';

        var btn = document.getElementById('theme-toggle');
        if (btn) btn.addEventListener('click', function () {
            applyTheme(getCurrentTheme() === 'light' ? 'dark' : 'light');
        });
    });

    try {
        if (window.matchMedia) {
            window.matchMedia('(prefers-color-scheme: light)').addEventListener('change', function (e) {
                var stored = null;
                try { stored = localStorage.getItem(STORAGE_KEY); } catch (ex) {}
                if (!stored) applyTheme(e.matches ? 'light' : 'dark');
            });
        }
    } catch (e) {}
})();

/* 2. NAVBAR SCROLL ──────────────────────────────────────────── */
(function () {
    document.addEventListener('DOMContentLoaded', function () {
        var nav = document.getElementById('main-nav');
        if (!nav) return;
        function onScroll() { nav.classList.toggle('scrolled', window.scrollY > 20); }
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    });
})();

/* 3. MOBİL FULL-SCREEN MENÜ ─────────────────────────────────── */
(function () {
    document.addEventListener('DOMContentLoaded', function () {
        var menu     = document.getElementById('mobile-menu');
        var backdrop = document.getElementById('mob-menu-backdrop');
        var openBtn  = document.getElementById('open__nav-btn');
        var closeBtn = document.getElementById('close__nav-btn');

        if (!menu || !openBtn) return;

        var isOpen = false;

        function openMenu() {
            if (isOpen) return;
            isOpen = true;

            /* display:flex önce set et, sonra RAF'ta is-open ekle (CSS transition için) */
            menu.style.display = 'flex';
            requestAnimationFrame(function () {
                menu.classList.add('is-open');
            });
            if (backdrop) backdrop.classList.add('is-open');

            menu.setAttribute('aria-hidden', 'false');
            openBtn.setAttribute('aria-expanded', 'true');
            if (closeBtn) {
                closeBtn.setAttribute('aria-expanded', 'true');
                /* Kısa gecikme ile focus — animasyon başladıktan sonra */
                setTimeout(function () { closeBtn.focus(); }, 50);
            }

            document.body.classList.add('nav-open');

            /* İlk focusable element'e geç */
            var firstLink = menu.querySelector('a, button');
            if (firstLink) setTimeout(function () { firstLink.focus(); }, 50);
        }

        function closeMenu() {
            if (!isOpen) return;
            isOpen = false;

            menu.classList.remove('is-open');
            menu.setAttribute('aria-hidden', 'true');
            if (backdrop) backdrop.classList.remove('is-open');

            openBtn.setAttribute('aria-expanded', 'false');
            if (closeBtn) closeBtn.setAttribute('aria-expanded', 'false');

            document.body.classList.remove('nav-open');
            openBtn.focus();

            /* Transition bitince inline display'i temizle — CSS display:none devreye girsin */
            setTimeout(function () {
                if (!isOpen) menu.style.display = '';
            }, 400);
        }

        openBtn.addEventListener('click', openMenu);
        if (closeBtn) closeBtn.addEventListener('click', closeMenu);

        /* Backdrop tıklaması */
        if (backdrop) backdrop.addEventListener('click', closeMenu);

        /* Menü içindeki link'e tıklayınca kapat */
        menu.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', closeMenu);
        });

        /* ESC */
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && isOpen) closeMenu();
        });

        /* Focus trap */
        menu.addEventListener('keydown', function (e) {
            if (e.key !== 'Tab') return;
            var focusable = menu.querySelectorAll(
                'a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])'
            );
            var first = focusable[0];
            var last  = focusable[focusable.length - 1];
            if (e.shiftKey) {
                if (document.activeElement === first) { e.preventDefault(); last.focus(); }
            } else {
                if (document.activeElement === last) { e.preventDefault(); first.focus(); }
            }
        });

        /* Resize: masaüstüne döndüğünde kapat */
        var resizeTimer;
        window.addEventListener('resize', function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function () {
                if (window.innerWidth > 768 && isOpen) closeMenu();
            }, 100);
        });
    });
})();

/* 4. MOB-MENU GRUP TOGGLE (Hizmetler, Bölgeler) ─────────────── */
(function () {
    document.addEventListener('DOMContentLoaded', function () {
        var toggles = document.querySelectorAll('.mob-menu__group-toggle');
        toggles.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var group = btn.closest('.mob-menu__group');
                if (!group) return;
                var isGroupOpen = group.classList.contains('is-open');
                var sub = group.querySelector('.mob-menu__sub');

                /* Aynı anda sadece bir grup açık */
                document.querySelectorAll('.mob-menu__group.is-open').forEach(function (g) {
                    if (g !== group) {
                        g.classList.remove('is-open');
                        var s = g.querySelector('.mob-menu__sub');
                        if (s) s.setAttribute('aria-hidden', 'true');
                        var t = g.querySelector('.mob-menu__group-toggle');
                        if (t) t.setAttribute('aria-expanded', 'false');
                    }
                });

                group.classList.toggle('is-open', !isGroupOpen);
                btn.setAttribute('aria-expanded', String(!isGroupOpen));
                if (sub) sub.setAttribute('aria-hidden', String(isGroupOpen));
            });
        });
    });
})();

/* 5. ADMIN SIDEBAR TOGGLE ───────────────────────────────────── */
(function () {
    document.addEventListener('DOMContentLoaded', function () {
        var sidebar = document.querySelector('aside');
        var showBtn = document.getElementById('show__sidebar-btn');
        var hideBtn = document.getElementById('hide__sidebar-btn');
        if (!sidebar || !showBtn) return;

        function showSidebar() {
            sidebar.classList.add('open');
            if (showBtn) showBtn.style.display = 'none';
            if (hideBtn) hideBtn.style.display = 'flex';
        }
        function hideSidebar() {
            sidebar.classList.remove('open');
            if (showBtn) showBtn.style.display = 'flex';
            if (hideBtn) hideBtn.style.display = 'none';
        }

        showBtn.addEventListener('click', showSidebar);
        if (hideBtn) hideBtn.addEventListener('click', hideSidebar);

        document.addEventListener('click', function (e) {
            if (window.innerWidth <= 768 &&
                sidebar.classList.contains('open') &&
                !sidebar.contains(e.target) &&
                !showBtn.contains(e.target)) {
                hideSidebar();
            }
        });
    });
})();

/* 6. FAQ ACCORDION ──────────────────────────────────────────── */
(function () {
    document.addEventListener('DOMContentLoaded', function () {
        var faqItems = document.querySelectorAll('.faq__item');
        if (!faqItems.length) return;

        faqItems.forEach(function (item) {
            var btn    = item.querySelector('.faq__question');
            var answer = item.querySelector('.faq__answer');
            if (!btn || !answer) return;

            btn.addEventListener('click', function () {
                var isItemOpen = item.classList.contains('open');
                faqItems.forEach(function (other) {
                    other.classList.remove('open');
                    var ob = other.querySelector('.faq__question');
                    var oa = other.querySelector('.faq__answer');
                    if (ob) ob.setAttribute('aria-expanded', 'false');
                    if (oa) oa.hidden = true;
                });
                if (!isItemOpen) {
                    item.classList.add('open');
                    btn.setAttribute('aria-expanded', 'true');
                    answer.hidden = false;
                }
            });
        });
    });
})();

/* 7. SCROLL TO TOP ──────────────────────────────────────────── */
(function () {
    document.addEventListener('DOMContentLoaded', function () {
        var btn = document.getElementById('scroll-top-btn');
        if (!btn) return;
        function toggle() { btn.classList.toggle('visible', window.scrollY > 400); }
        window.addEventListener('scroll', toggle, { passive: true });
        toggle();
        btn.addEventListener('click', function () { window.scrollTo({ top: 0, behavior: 'smooth' }); });
    });
})();

/* 8. STİCKY CTA ─────────────────────────────────────────────── */
(function () {
    document.addEventListener('DOMContentLoaded', function () {
        var stickyCta = document.getElementById('sticky-cta');
        if (!stickyCta) return;
        if (window.location.pathname.indexOf('randevu') !== -1) {
            stickyCta.style.display = 'none';
            return;
        }
        function toggle() { stickyCta.classList.toggle('visible', window.scrollY > 300); }
        window.addEventListener('scroll', toggle, { passive: true });
        toggle();
    });
})();

/* 9. AKTİF NAV LİNKİ ───────────────────────────────────────── */
(function () {
    document.addEventListener('DOMContentLoaded', function () {
        var currentPath = window.location.pathname;

        /* Desktop nav */
        document.querySelectorAll('.nav__items--desktop a').forEach(function (link) {
            try {
                var linkPath = new URL(link.href).pathname;
                var isActive = linkPath === currentPath ||
                    (linkPath !== '/' && linkPath !== '' && currentPath.startsWith(linkPath));
                if (isActive) {
                    link.classList.add('active');
                    link.setAttribute('aria-current', 'page');
                }
            } catch (e) {}
        });

        /* Mobil menü */
        document.querySelectorAll('.mob-menu a').forEach(function (link) {
            try {
                var linkPath = new URL(link.href).pathname;
                var isActive = linkPath === currentPath ||
                    (linkPath !== '/' && linkPath !== '' && currentPath.startsWith(linkPath));
                if (isActive) link.classList.add('active');
            } catch (e) {}
        });
    });
})();

/* 10. MOBİL FAB NAV ─────────────────────────────────────────── */
(function () {
    var fabOpen = false;

    var fabItems = [
        { id: 'fab-item-wa',  tx: -80, ty: -44 },
        { id: 'fab-item-rdv', tx: 0,   ty: -96 },
        { id: 'fab-item-tel', tx: 80,  ty: -44 }
    ];

    window.mobToggleFab = function () {
        fabOpen = !fabOpen;
        var btn      = document.getElementById('mob-fab-btn');
        var icon     = document.getElementById('mob-fab-icon');
        var pulse    = document.getElementById('mob-pulse');
        var backdrop = document.getElementById('mob-backdrop');
        if (!btn) return;

        icon.style.transform = fabOpen ? 'rotate(45deg)' : 'rotate(0deg)';
        btn.style.background = fabOpen
            ? 'linear-gradient(145deg,#2D3748,#1A202C)'
            : 'linear-gradient(145deg,#22836A,#155C48)';
        btn.style.boxShadow  = fabOpen
            ? '0 6px 20px rgba(0,0,0,0.3)'
            : '0 6px 20px rgba(28,104,82,0.4),0 0 0 4px rgba(28,104,82,0.10)';
        btn.style.transform  = fabOpen ? 'translateY(-18px)' : 'translateY(-16px)';
        if (pulse) pulse.style.display = fabOpen ? 'none' : 'block';

        if (backdrop) {
            backdrop.style.opacity       = fabOpen ? '1' : '0';
            backdrop.style.pointerEvents = fabOpen ? 'auto' : 'none';
        }

        fabItems.forEach(function (f) {
            var el = document.getElementById(f.id);
            if (!el) return;
            if (fabOpen) {
                el.style.transform     = 'translateX(calc(-50% + ' + f.tx + 'px)) translateY(' + f.ty + 'px) scale(1)';
                el.style.opacity       = '1';
                el.style.pointerEvents = 'auto';
            } else {
                el.style.transform     = 'translateX(-50%) scale(0.3)';
                el.style.opacity       = '0';
                el.style.pointerEvents = 'none';
            }
        });
    };

    window.mobTabAction = function (id, href, external) {
        if (fabOpen) window.mobToggleFab();
        ['wa', 'tel', 'rdv', 'home'].forEach(function (t) {
            var el = document.getElementById('mob-tab-' + t);
            if (!el) return;
            el.classList.toggle('mob-tab--active', t === id);
        });
        if (!href) return;
        if (external) window.open(href, '_blank', 'noopener,noreferrer');
        else window.location.href = href;
    };

    document.addEventListener('DOMContentLoaded', function () {
        fabItems.forEach(function (f) {
            var el = document.getElementById(f.id);
            if (!el) return;
            el.addEventListener('click', function () {
                var href     = el.dataset.href;
                var external = el.dataset.external === '1';
                if (fabOpen) window.mobToggleFab();
                if (!href) return;
                if (external) window.open(href, '_blank', 'noopener,noreferrer');
                else window.location.href = href;
            });
        });

        var backdrop = document.getElementById('mob-backdrop');
        if (backdrop) backdrop.addEventListener('click', function () {
            if (fabOpen) window.mobToggleFab();
        });

        var path = window.location.pathname;
        var map  = { 'randevu': 'rdv', 'iletisim': 'wa' };
        var matched = null;
        Object.keys(map).forEach(function (k) { if (path.indexOf(k) !== -1) matched = map[k]; });
        if (matched) {
            document.querySelectorAll('.mob-tab').forEach(function (t) {
                t.classList.remove('mob-tab--active');
            });
            var activeTab = document.getElementById('mob-tab-' + matched);
            if (activeTab) activeTab.classList.add('mob-tab--active');
        }
    });
})();

/* 11. SCROLL FADE-IN ────────────────────────────────────────── */
(function () {
    if (!window.IntersectionObserver) return;
    document.addEventListener('DOMContentLoaded', function () {
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

        document.querySelectorAll('.fade-in').forEach(function (el) {
            observer.observe(el);
        });
    });
})();

/* 12. SHARE COPY LINK ───────────────────────────────────────── */
window.shareCopyLink = function (btn) {
    var url = btn.getAttribute('data-url');
    if (!url) return;

    var label = btn.querySelector('.share-btn__label');
    var originalText = label ? label.textContent : '';

    function showCopied() {
        btn.classList.add('copied');
        if (label) label.textContent = 'Kopyalandı!';
        setTimeout(function () {
            btn.classList.remove('copied');
            if (label) label.textContent = originalText;
        }, 2000);
    }

    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(url).then(showCopied).catch(function () {
            fallbackCopy(url, showCopied);
        });
    } else {
        fallbackCopy(url, showCopied);
    }
};

function fallbackCopy(text, cb) {
    var ta = document.createElement('textarea');
    ta.value = text;
    ta.style.cssText = 'position:fixed;top:-9999px;left:-9999px;opacity:0;';
    document.body.appendChild(ta);
    ta.focus();
    ta.select();
    try { document.execCommand('copy'); cb(); } catch (e) {}
    document.body.removeChild(ta);
}

/* 11. LIGHTBOX / GALERİ MODAL ──────────────────────────────── */
(function () {
    'use strict';

    var items = [];
    var currentIndex = 0;
    var lightbox = null;
    var imgEl = null;
    var captionEl = null;
    var lastFocused = null;
    var touchStartX = 0;

    function buildDOM() {
        if (document.getElementById('js-lightbox')) return;

        lightbox = document.createElement('div');
        lightbox.id = 'js-lightbox';
        lightbox.className = 'lightbox';
        lightbox.setAttribute('role', 'dialog');
        lightbox.setAttribute('aria-modal', 'true');
        lightbox.setAttribute('aria-label', 'Galeri görseli');

        lightbox.innerHTML =
            '<button class="lightbox__close" aria-label="Kapat"><i class="uil uil-times"></i></button>' +
            '<button class="lightbox__nav lightbox__nav--prev" aria-label="Önceki"><i class="uil uil-angle-left"></i></button>' +
            '<button class="lightbox__nav lightbox__nav--next" aria-label="Sonraki"><i class="uil uil-angle-right"></i></button>' +
            '<img class="lightbox__img" src="" alt="">' +
            '<div class="lightbox__caption"></div>';

        document.body.appendChild(lightbox);

        imgEl = lightbox.querySelector('.lightbox__img');
        captionEl = lightbox.querySelector('.lightbox__caption');

        lightbox.querySelector('.lightbox__close').addEventListener('click', closeLB);
        lightbox.querySelector('.lightbox__nav--prev').addEventListener('click', function () { navigate(-1); });
        lightbox.querySelector('.lightbox__nav--next').addEventListener('click', function () { navigate(1); });

        lightbox.addEventListener('click', function (e) {
            if (e.target === lightbox) closeLB();
        });

        // Touch swipe
        lightbox.addEventListener('touchstart', function (e) {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });

        lightbox.addEventListener('touchend', function (e) {
            var diff = e.changedTouches[0].screenX - touchStartX;
            if (Math.abs(diff) > 50) {
                navigate(diff > 0 ? -1 : 1);
            }
        }, { passive: true });
    }

    function openLB(index) {
        buildDOM();
        lastFocused = document.activeElement;
        currentIndex = index;
        show();
        lightbox.classList.add('lightbox--open');
        document.body.style.overflow = 'hidden';
        document.addEventListener('keydown', onKey);
        setTimeout(function () { lightbox.querySelector('.lightbox__close').focus(); }, 100);
    }

    function closeLB() {
        if (!lightbox) return;
        lightbox.classList.remove('lightbox--open');
        document.body.style.overflow = '';
        document.removeEventListener('keydown', onKey);
        if (lastFocused) lastFocused.focus();
    }

    function show() {
        var item = items[currentIndex];
        if (!item) return;
        imgEl.src = item.src;
        imgEl.alt = item.alt || '';
        captionEl.textContent = item.caption || '';

        var prev = lightbox.querySelector('.lightbox__nav--prev');
        var next = lightbox.querySelector('.lightbox__nav--next');
        if (prev) prev.style.display = items.length > 1 ? '' : 'none';
        if (next) next.style.display = items.length > 1 ? '' : 'none';
    }

    function navigate(dir) {
        currentIndex = (currentIndex + dir + items.length) % items.length;
        show();
    }

    function onKey(e) {
        if (e.key === 'Escape') closeLB();
        if (e.key === 'ArrowLeft') navigate(-1);
        if (e.key === 'ArrowRight') navigate(1);
    }

    document.addEventListener('DOMContentLoaded', function () {
        var links = document.querySelectorAll('[data-lightbox="gallery"]');
        if (!links.length) return;

        links.forEach(function (link, i) {
            items.push({
                src: link.href,
                alt: link.querySelector('img') ? link.querySelector('img').alt : '',
                caption: link.getAttribute('data-caption') || ''
            });

            link.addEventListener('click', function (e) {
                e.preventDefault();
                openLB(i);
            });
        });
    });
})();
