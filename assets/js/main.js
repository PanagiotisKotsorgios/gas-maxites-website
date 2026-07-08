document.addEventListener('DOMContentLoaded', () => {
  // =========================================================
  // Mobile nav drawer
  // ---------------------------------------------------------
  // Slide-in drawer with backdrop, body-scroll lock, Esc to
  // close, click-outside to close, accordion-style dropdowns
  // (tap parent to expand/collapse on mobile only).
  // =========================================================
  const navBtn      = document.querySelector('.nav-toggle');
  const nav         = document.querySelector('.site-nav');
  const navBackdrop = document.querySelector('.nav-backdrop');
  const drawerClose = document.querySelector('.drawer-close');

  const isMobile = () => window.matchMedia('(max-width: 960px)').matches;

  const openDrawer = () => {
    if (!nav) return;
    nav.classList.add('is-open');
    navBackdrop && navBackdrop.classList.add('is-open');
    navBtn && navBtn.setAttribute('aria-expanded', 'true');
    navBtn && navBtn.setAttribute('aria-label', 'Κλείσιμο μενού');
    document.body.classList.add('nav-open');
  };
  const closeDrawer = () => {
    if (!nav) return;
    nav.classList.remove('is-open');
    navBackdrop && navBackdrop.classList.remove('is-open');
    navBtn && navBtn.setAttribute('aria-expanded', 'false');
    navBtn && navBtn.setAttribute('aria-label', 'Άνοιγμα μενού');
    document.body.classList.remove('nav-open');
    // Collapse any expanded accordion when closing
    document.querySelectorAll('.site-nav .has-dropdown.is-expanded').forEach(el => el.classList.remove('is-expanded'));
  };

  if (navBtn && nav) {
    navBtn.addEventListener('click', () => {
      nav.classList.contains('is-open') ? closeDrawer() : openDrawer();
    });
  }
  if (drawerClose) drawerClose.addEventListener('click', closeDrawer);
  if (navBackdrop)  navBackdrop.addEventListener('click', closeDrawer);
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && nav && nav.classList.contains('is-open')) closeDrawer();
  });

  // Close drawer when a leaf link inside is clicked (SPA-like feel)
  document.querySelectorAll('.site-nav .drawer-scroll a').forEach(link => {
    link.addEventListener('click', (e) => {
      // Skip parent-of-dropdown toggle: on mobile that toggles the accordion
      if (isMobile() && link.parentElement.classList.contains('has-dropdown') && link.parentElement.querySelector('.dropdown')) {
        e.preventDefault();
        link.parentElement.classList.toggle('is-expanded');
        return;
      }
      // For leaf links, close the drawer as the page navigates
      if (isMobile()) closeDrawer();
    });
  });

  // Reset drawer state when resizing back up to desktop
  window.addEventListener('resize', () => {
    if (!isMobile() && nav && nav.classList.contains('is-open')) closeDrawer();
  });

  // Lightbox
  const lightbox = document.getElementById('lightbox');
  if (lightbox) {
    const img = lightbox.querySelector('img');
    const cap = lightbox.querySelector('.lb-caption');
    const close = lightbox.querySelector('.lb-close');
    document.querySelectorAll('.gallery-item').forEach(a => {
      a.addEventListener('click', e => {
        e.preventDefault();
        img.src = a.getAttribute('href');
        cap.textContent = a.dataset.caption || '';
        lightbox.hidden = false;
        document.body.style.overflow = 'hidden';
      });
    });
    const closeIt = () => { lightbox.hidden = true; img.src = ''; document.body.style.overflow = ''; };
    close.addEventListener('click', closeIt);
    lightbox.addEventListener('click', e => { if (e.target === lightbox) closeIt(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape' && !lightbox.hidden) closeIt(); });
  }

  // Video modal (athlete fight videos)
  const vmodal = document.getElementById('videoModal');
  if (vmodal) {
    const vif = vmodal.querySelector('iframe');
    const vclose = vmodal.querySelector('.lb-close');
    document.querySelectorAll('.video-btn').forEach(b => {
      b.addEventListener('click', () => {
        vif.src = b.dataset.embed + '?autoplay=1';
        vmodal.hidden = false;
        document.body.style.overflow = 'hidden';
      });
    });
    const closeV = () => { vif.src = ''; vmodal.hidden = true; document.body.style.overflow = ''; };
    vclose.addEventListener('click', closeV);
    vmodal.addEventListener('click', e => { if (e.target === vmodal) closeV(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape' && !vmodal.hidden) closeV(); });
  }

  // Cookie banner
  const cb = document.getElementById('cookie-banner');
  if (cb && !localStorage.getItem('maxites_cookie_choice')) {
    setTimeout(() => { cb.hidden = false; }, 600);
  }

  // Scroll-to-top
  const st = document.getElementById('scrollTop');
  if (st) {
    const onScroll = () => {
      if (window.scrollY > 500) { st.hidden = false; st.classList.add('is-visible'); }
      else st.classList.remove('is-visible');
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    st.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
    onScroll();
  }

  // =========================================================
  // Scroll-triggered entrance animations
  // ---------------------------------------------------------
  // Every "block-level" element below the fold fades up as it
  // enters the viewport. Containers with grid-of-cards get their
  // children staggered via .reveal-group.
  // =========================================================
  const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (!prefersReduced && 'IntersectionObserver' in window) {

    // Sections + banner blocks — each fades in as a whole.
    const blockSel = [
      '.section',
      '.cta-band',
      '.trophies-band',
      '.reviews-band',
      '.split',
      '.master-grid',
      '.timeline',
      '.contact-grid',
      '.article-wrap',
      '.sched-table-wrap',
      '.sched-legend',
      '.footer-newsletter',
      '.footer-grid',
      '.footer-legal',
    ].join(', ');

    // Groups of cards/items whose children should stagger in.
    const groupSel = [
      '.features-grid',
      '.cards-grid',
      '.athletes-grid',
      '.trophies-grid',
      '.gallery-preview',
      '.gallery-grid',
      '.quotes-grid',
      '.programs-list',
      '.timeline',
      '.fact-list',
      '.split-stats',
      '.footer-grid',
    ].join(', ');

    // Skip reveal for elements already fully or partially above the fold —
    // they'd flash otherwise. The hero already has its own page-load stagger.
    const foldY = window.innerHeight * 0.9;
    const isBelowFold = (el) => el.getBoundingClientRect().top >= foldY;

    document.querySelectorAll(blockSel).forEach(el => {
      if (isBelowFold(el)) el.classList.add('reveal');
    });
    document.querySelectorAll(groupSel).forEach(el => {
      Array.from(el.children).forEach((child, i) => {
        child.style.setProperty('--pl-i', i);
      });
      if (isBelowFold(el)) el.classList.add('reveal-group');
    });
    document.querySelectorAll('.section-head, .page-head').forEach(el => {
      if (isBelowFold(el)) el.classList.add('reveal');
    });

    const io = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });

    document.querySelectorAll('.reveal, .reveal-group').forEach(el => io.observe(el));
  } else if (prefersReduced) {
    document.querySelectorAll('.reveal, .reveal-group').forEach(el => el.classList.add('is-visible'));
  }
});

function acceptCookies() { closeCookieBanner('all'); }
function rejectCookies() { closeCookieBanner('essential'); }
function closeCookieBanner(choice) {
  localStorage.setItem('maxites_cookie_choice', choice);
  localStorage.setItem('maxites_cookie_choice_at', new Date().toISOString());
  const cb = document.getElementById('cookie-banner');
  if (!cb) return;
  cb.style.animation = 'cbslideout .35s cubic-bezier(.4,0,.6,1) forwards';
  setTimeout(() => { cb.hidden = true; cb.style.animation = ''; }, 340);
}
