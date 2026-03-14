/**
 * Prop Trading 101 — Main JS v6
 * Loaded in footer (DOM already parsed) — wrapped in IIFE, no DOMContentLoaded needed.
 */
(function () {
  'use strict';

  /* ── PROGRAMS DROPDOWN ─────────────────────── */
  var ddItem    = document.getElementById('nav-programs');
  var ddTrigger = ddItem ? ddItem.querySelector('.nav-dropdown-trigger') : null;

  if (ddTrigger) {
    ddTrigger.addEventListener('click', function (e) {
      e.stopPropagation();
      var open = ddItem.classList.toggle('open');
      ddTrigger.setAttribute('aria-expanded', String(open));
    });
    document.addEventListener('click', function (e) {
      if (ddItem && !ddItem.contains(e.target)) {
        ddItem.classList.remove('open');
        ddTrigger.setAttribute('aria-expanded', 'false');
      }
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && ddItem.classList.contains('open')) {
        ddItem.classList.remove('open');
        ddTrigger.setAttribute('aria-expanded', 'false');
        ddTrigger.focus();
      }
    });
  }

  /* ── HEADER SCROLL (rAF-debounced for INP) ─── */
  var header      = document.getElementById('site-header');
  var scrollTick  = false;
  if (header) {
    window.addEventListener('scroll', function () {
      if (!scrollTick) {
        requestAnimationFrame(function () {
          header.classList.toggle('scrolled', window.scrollY > 20);
          scrollTick = false;
        });
        scrollTick = true;
      }
    }, { passive: true });
  }

  /* ── MOBILE MENU ───────────────────────────── */
  var hamburger = document.getElementById('hamburger');
  var drawer    = document.getElementById('mobile-drawer');

  if (hamburger && drawer) {
    var lastActiveEl = null;

    function getFocusable(root) {
      return Array.from(root.querySelectorAll(
        'a[href], button:not([disabled]), input:not([disabled]), [tabindex]:not([tabindex="-1"])'
      )).filter(function (el) {
        return !!(el.offsetWidth || el.offsetHeight || el.getClientRects().length);
      });
    }

    function toggleDrawer(open) {
      if (typeof open === 'undefined') open = !drawer.classList.contains('open');
      drawer.classList.toggle('open', open);
      hamburger.classList.toggle('open', open);
      hamburger.setAttribute('aria-expanded', String(open));
      drawer.setAttribute('aria-hidden', open ? 'false' : 'true');
      document.body.style.overflow = open ? 'hidden' : '';
      if (open) {
        lastActiveEl = document.activeElement;
        var f = getFocusable(drawer);
        if (f[0]) f[0].focus();
      } else if (lastActiveEl && typeof lastActiveEl.focus === 'function') {
        lastActiveEl.focus();
        lastActiveEl = null;
      }
    }

    hamburger.addEventListener('click', function () { toggleDrawer(); });
    hamburger.addEventListener('touchend', function (e) { e.preventDefault(); toggleDrawer(); });

    drawer.querySelectorAll('a').forEach(function (link) {
      link.addEventListener('click', function () { toggleDrawer(false); });
    });

    document.addEventListener('click', function (e) {
      if (drawer.classList.contains('open') &&
          !drawer.contains(e.target) &&
          !hamburger.contains(e.target)) {
        toggleDrawer(false);
      }
    });

    document.addEventListener('keydown', function (e) {
      if (!drawer.classList.contains('open')) return;
      if (e.key === 'Escape') { e.preventDefault(); toggleDrawer(false); return; }
      if (e.key !== 'Tab') return;
      var f      = getFocusable(drawer);
      if (!f.length) return;
      var first  = f[0];
      var last   = f[f.length - 1];
      var active = document.activeElement;
      if (e.shiftKey) {
        if (active === first || !drawer.contains(active)) { e.preventDefault(); last.focus(); }
      } else {
        if (active === last) { e.preventDefault(); first.focus(); }
      }
    });
  }

  /* ── ACCORDION — event delegation (1 listener instead of N) ── */
  var accWrap = document.querySelector('.features-section');
  if (accWrap) {
    var accItems = accWrap.querySelectorAll('.acc-item');
    accWrap.addEventListener('click', function (e) {
      var btn = e.target.closest('.acc-btn');
      if (!btn) return;
      var item   = btn.closest('.acc-item');
      var isOpen = item.classList.contains('open');
      accItems.forEach(function (el) {
        el.classList.remove('open');
        var b = el.querySelector('.acc-btn');
        if (b) b.setAttribute('aria-expanded', 'false');
      });
      if (!isOpen) {
        item.classList.add('open');
        btn.setAttribute('aria-expanded', 'true');
      }
    });
  }

  /* ── TESTIMONIAL CAROUSEL ───────────────────── */
  var carousel = document.getElementById('testi-carousel');
  if (carousel) {
    var slides    = Array.from(carousel.querySelectorAll('.testi-card'));
    var dots      = Array.from(document.querySelectorAll('.testi-dot'));
    var prevBtn   = document.getElementById('testi-prev');
    var nextBtn   = document.getElementById('testi-next');
    var current   = 0;
    var animating = false;

    dots.forEach(function (d, i) { d.setAttribute('aria-current', i === 0 ? 'true' : 'false'); });

    function goTo(idx, dir) {
      if (animating || idx === current) return;
      animating = true;
      var safetyTimer = setTimeout(function () { animating = false; }, 800);
      var next     = (idx + slides.length) % slides.length;
      var inClass  = dir === 'next' ? 'slide-in-right' : 'slide-in-left';
      var outClass = dir === 'next' ? 'slide-out-left'  : 'slide-out-right';

      slides[current].classList.add(outClass);
      setTimeout(function () {
        slides[current].style.display = 'none';
        slides[current].classList.remove(outClass);
        if (dots[current]) { dots[current].classList.remove('active'); dots[current].setAttribute('aria-current', 'false'); }
        current = next;
        if (dots[current]) { dots[current].classList.add('active'); dots[current].setAttribute('aria-current', 'true'); }
        slides[current].style.display = 'block';
        slides[current].classList.add(inClass);
        setTimeout(function () { slides[current].classList.remove(inClass); animating = false; clearTimeout(safetyTimer); }, 360);
      }, 320);
    }

    if (prevBtn) prevBtn.addEventListener('click', function () { goTo(current - 1, 'prev'); });
    if (nextBtn) nextBtn.addEventListener('click', function () { goTo(current + 1, 'next'); });
    dots.forEach(function (d) {
      d.addEventListener('click', function () {
        var idx = parseInt(d.dataset.dot, 10);
        if (isNaN(idx)) return;
        goTo(idx, idx > current ? 'next' : 'prev');
      });
    });

    var touchX = 0;
    carousel.addEventListener('touchstart', function (e) { touchX = e.touches[0].clientX; }, { passive: true });
    carousel.addEventListener('touchend',   function (e) {
      var diff = touchX - e.changedTouches[0].clientX;
      if (Math.abs(diff) > 40) goTo(diff > 0 ? current + 1 : current - 1, diff > 0 ? 'next' : 'prev');
    }, { passive: true });

    function startAutoplay() { return setInterval(function () { goTo(current + 1, 'next'); }, 6000); }
    var autoplay = startAutoplay();
    carousel.addEventListener('mouseenter', function () { clearInterval(autoplay); });
    carousel.addEventListener('mouseleave', function () { autoplay = startAutoplay(); });
    carousel.addEventListener('touchstart', function () { clearInterval(autoplay); }, { passive: true });
  }

  /* ── SCROLL COUNTER ANIMATION ───────────────── */
  if ('IntersectionObserver' in window) {
    var obs = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        var el       = entry.target;
        var target   = parseInt(el.dataset.count, 10);
        var suffix   = el.dataset.suffix || '';
        var duration = 1400;
        var start    = null;
        obs.unobserve(el);
        requestAnimationFrame(function step(ts) {
          if (!start) start = ts;
          var p   = Math.min((ts - start) / duration, 1);
          var ease = 1 - Math.pow(1 - p, 3);
          var val  = Math.round(ease * target);
          el.textContent = (target >= 1000
            ? Math.floor(val / 1000) + '\u00a0' + String(val % 1000).padStart(3, '0')
            : String(val)) + suffix;
          if (p < 1) requestAnimationFrame(step);
        });
      });
    }, { threshold: 0.5 });
    document.querySelectorAll('.tm-num[data-count]').forEach(function (el) { obs.observe(el); });
  }

  /* ── PROGRAMS PAGE TABS ──────────────────────── */
  var pgTabsWrap = document.getElementById('pg-tabs-wrap');
  if (pgTabsWrap) {
    var pgTabs = pgTabsWrap.querySelectorAll('.pg-tab');
    var pgGrid = document.getElementById('pg-grid');

    function pgActivate(key) {
      pgTabs.forEach(function (btn) {
        var on = btn.dataset.tab === key;
        btn.classList.toggle('active', on);
        btn.setAttribute('aria-selected', on ? 'true' : 'false');
      });
      if (pgGrid) {
        pgGrid.querySelectorAll('.pg-card').forEach(function (card) {
          var tabs  = (card.dataset.tab || '').split(' ');
          var match = key === 'all' || tabs.indexOf(key) !== -1;
          card.classList.toggle('pg-hidden', !match);
        });
      }
    }

    pgTabsWrap.addEventListener('click', function (e) {
      var btn = e.target.closest('.pg-tab');
      if (btn) pgActivate(btn.dataset.tab);
    });
    pgTabsWrap.addEventListener('touchend', function (e) {
      var btn = e.target.closest('.pg-tab');
      if (btn) { e.preventDefault(); pgActivate(btn.dataset.tab); }
    });

    pgActivate('all');
  }

  /* ── SMOOTH SCROLL — event delegation ─────── */
  document.addEventListener('click', function (e) {
    var a = e.target.closest('a[href^="#"]');
    if (!a) return;
    var id = a.getAttribute('href');
    if (id === '#') return;
    var target = document.querySelector(id);
    if (target) {
      e.preventDefault();
      var offset = header ? header.offsetHeight + 16 : 86;
      window.scrollTo({ top: target.getBoundingClientRect().top + window.scrollY - offset, behavior: 'smooth' });
    }
  });

  /* ── COURSE MODULE ACCORDION — event delegation ── */
  document.querySelectorAll('.cdp-modules').forEach(function (wrap) {
    wrap.addEventListener('click', function (e) {
      var btn = e.target.closest('.cdp-module-btn');
      if (!btn) return;
      var item   = btn.closest('.cdp-module');
      var isOpen = item.classList.contains('open');
      wrap.querySelectorAll('.cdp-module').forEach(function (el) {
        el.classList.remove('open');
        var b = el.querySelector('.cdp-module-btn');
        if (b) b.setAttribute('aria-expanded', 'false');
      });
      if (!isOpen) {
        item.classList.add('open');
        btn.setAttribute('aria-expanded', 'true');
      }
    });
  });

  /* ── VIEW FULL CURRICULUM TOGGLE ─────────────── */
  var curriculumBtn = document.getElementById('cdp-curriculum-toggle');
  if (curriculumBtn) {
    var allModules = document.querySelectorAll('.cdp-module');
    var expanded   = false;
    curriculumBtn.addEventListener('click', function () {
      expanded = !expanded;
      allModules.forEach(function (item) {
        var b = item.querySelector('.cdp-module-btn');
        item.classList.toggle('open', expanded);
        if (b) b.setAttribute('aria-expanded', expanded ? 'true' : 'false');
      });
      curriculumBtn.innerHTML = expanded
        ? 'Collapse curriculum <span aria-hidden="true">↑</span>'
        : 'View full curriculum <span aria-hidden="true">→</span>';
    });
  }

  /* ── COOKIE SETTINGS BUTTON ─────────────────── */
  var cookieBtn = document.querySelector('[data-action="open-cookie-settings"]');
  if (cookieBtn) {
    cookieBtn.addEventListener('click', function () {
      document.dispatchEvent(new Event('pt101:open-cookie-settings'));
    });
  }

  /* ── MARQUEE: PAUSE WHEN TAB HIDDEN ─────────── */
  document.addEventListener('visibilitychange', function () {
    var state = document.hidden ? 'paused' : 'running';
    document.querySelectorAll('.firms-track, .logos-belt-track').forEach(function (t) {
      t.style.animationPlayState = state;
    });
  });

})();
