document.addEventListener('DOMContentLoaded', () => {
  // Mobile nav
  const btn = document.querySelector('.nav-toggle');
  const nav = document.querySelector('.site-nav');
  if (btn && nav) {
    btn.addEventListener('click', () => {
      const open = nav.classList.toggle('is-open');
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  }

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
