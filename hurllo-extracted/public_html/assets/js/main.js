/**
 * Hurllo - South Africa Car Hire Intelligence Platform
 * Main JavaScript
 */

(function() {
  'use strict';

  // ---- Mobile Navigation ----
  const navToggle = document.getElementById('nav-toggle');
  const mainNav = document.getElementById('main-nav');
  const overlay = document.getElementById('mobile-nav-overlay');

  if (navToggle && mainNav) {
    navToggle.addEventListener('click', function() {
      const isOpen = mainNav.classList.toggle('nav-open');
      navToggle.setAttribute('aria-expanded', isOpen);
      if (overlay) overlay.classList.toggle('active', isOpen);
      document.body.style.overflow = isOpen ? 'hidden' : '';
    });

    if (overlay) {
      overlay.addEventListener('click', function() {
        mainNav.classList.remove('nav-open');
        navToggle.setAttribute('aria-expanded', 'false');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
      });
    }
  }

  // ---- Sticky Header Shadow ----
  const header = document.getElementById('site-header');
  if (header) {
    window.addEventListener('scroll', function() {
      header.style.boxShadow = window.scrollY > 10
        ? '0 4px 20px rgba(0,0,0,0.5)'
        : 'none';
    }, { passive: true });
  }

  // ---- Back to Top ----
  const backToTop = document.getElementById('back-to-top');
  if (backToTop) {
    window.addEventListener('scroll', function() {
      backToTop.classList.toggle('visible', window.scrollY > 400);
    }, { passive: true });

    backToTop.addEventListener('click', function() {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  // ---- Risk Simulator ----
  const simDays = document.getElementById('sim-days');
  const simRate = document.getElementById('sim-rate');
  const simDeposit = document.getElementById('sim-deposit');
  const simExcess = document.getElementById('sim-excess');

  function formatZAR(amount) {
    return 'R ' + Math.round(amount).toLocaleString('en-ZA');
  }

  function updateSimulator() {
    if (!simDays) return;

    const days = parseInt(simDays.value);
    const rate = parseInt(simRate.value);
    const deposit = parseInt(simDeposit.value);
    const excess = parseInt(simExcess.value);

    const rentalCost = days * rate;
    const maxExposure = deposit + excess;
    const totalUpfront = rentalCost + deposit;

    // Update display values
    document.getElementById('sim-days-val').textContent = days;
    document.getElementById('sim-rate-val').textContent = rate.toLocaleString('en-ZA');
    document.getElementById('sim-deposit-val').textContent = deposit.toLocaleString('en-ZA');
    document.getElementById('sim-excess-val').textContent = excess.toLocaleString('en-ZA');

    // Update result cards
    const rentalEl = document.getElementById('sim-rental-cost');
    const depositEl = document.getElementById('sim-deposit-held');
    const exposureEl = document.getElementById('sim-max-exposure');
    const totalEl = document.getElementById('sim-total');

    if (rentalEl) rentalEl.textContent = formatZAR(rentalCost);
    if (depositEl) depositEl.textContent = formatZAR(deposit);
    if (exposureEl) exposureEl.textContent = formatZAR(maxExposure);
    if (totalEl) totalEl.textContent = formatZAR(totalUpfront);
  }

  if (simDays) {
    [simDays, simRate, simDeposit, simExcess].forEach(function(slider) {
      if (slider) {
        slider.addEventListener('input', updateSimulator);
      }
    });
    updateSimulator();
  }

  // ---- Score Bar Animation (Intersection Observer) ----
  const scoreBarFills = document.querySelectorAll('.score-bar__fill');

  if (scoreBarFills.length > 0 && 'IntersectionObserver' in window) {
    const observer = new IntersectionObserver(function(entries) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting) {
          const fill = entry.target;
          const score = parseFloat(fill.getAttribute('data-score') || '0');
          const percent = (score / 10) * 100;
          fill.style.width = percent + '%';
          observer.unobserve(fill);
        }
      });
    }, { threshold: 0.1 });

    scoreBarFills.forEach(function(fill) {
      fill.style.width = '0%';
      observer.observe(fill);
    });
  }

  // ---- Score Counter Animation ----
  const scoreValues = document.querySelectorAll('.overall-score-value[data-score]');

  if (scoreValues.length > 0 && 'IntersectionObserver' in window) {
    const counterObserver = new IntersectionObserver(function(entries) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting) {
          const el = entry.target;
          const target = parseFloat(el.getAttribute('data-score'));
          animateCounter(el, 0, target, 1000);
          counterObserver.unobserve(el);
        }
      });
    }, { threshold: 0.5 });

    scoreValues.forEach(function(el) {
      counterObserver.observe(el);
    });
  }

  function animateCounter(el, start, end, duration) {
    const startTime = performance.now();
    function update(currentTime) {
      const elapsed = currentTime - startTime;
      const progress = Math.min(elapsed / duration, 1);
      const eased = 1 - Math.pow(1 - progress, 3); // ease out cubic
      const current = start + (end - start) * eased;
      el.textContent = current.toFixed(1);
      if (progress < 1) {
        requestAnimationFrame(update);
      }
    }
    requestAnimationFrame(update);
  }

  // ---- Smooth Sort Transitions ----
  const companyList = document.getElementById('company-list');
  if (companyList) {
    const sortOptions = document.querySelectorAll('.sort-option');
    sortOptions.forEach(function(option) {
      option.addEventListener('click', function() {
        companyList.style.opacity = '0.5';
        companyList.style.transition = 'opacity 0.2s ease';
        setTimeout(function() {
          companyList.style.opacity = '1';
        }, 300);
      });
    });
  }

  // ---- Lazy Image Loading Enhancement ----
  const lazyImages = document.querySelectorAll('img[loading="lazy"]');
  lazyImages.forEach(function(img) {
    img.addEventListener('error', function() {
      const src = img.getAttribute('src') || '';
      if (src.includes('/logos/')) {
        img.src = '/assets/images/logos/default-logo.png';
      } else if (src.includes('/vehicles/')) {
        img.src = '/assets/images/vehicles/default-vehicle.jpg';
      }
    });
  });

  // ---- Compare Page: Auto-submit on select change ----
  const compareSelects = document.querySelectorAll('.compare-select');
  compareSelects.forEach(function(select) {
    select.addEventListener('change', function() {
      const form = select.closest('form');
      if (form) {
        form.submit();
      }
    });
  });

  // ---- Filter Form: Smooth transitions ----
  const filterForm = document.getElementById('filter-form');
  if (filterForm) {
    filterForm.addEventListener('change', function() {
      const list = document.getElementById('company-list');
      if (list) {
        list.style.opacity = '0.6';
        list.style.transition = 'opacity 0.3s ease';
      }
    });
  }

  // ---- Demand Bar Animation ----
  const demandBars = document.querySelectorAll('.demand-bar-mini__fill, .demand-meter__fill');
  if (demandBars.length > 0 && 'IntersectionObserver' in window) {
    const demandObserver = new IntersectionObserver(function(entries) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting) {
          entry.target.style.transition = 'width 1s ease';
          demandObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1 });

    demandBars.forEach(function(bar) {
      const originalWidth = bar.style.width;
      bar.style.width = '0%';
      setTimeout(function() {
        bar.style.width = originalWidth;
      }, 100);
      demandObserver.observe(bar);
    });
  }

  // ---- Keyboard Navigation ----
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      if (mainNav && mainNav.classList.contains('nav-open')) {
        mainNav.classList.remove('nav-open');
        if (navToggle) navToggle.setAttribute('aria-expanded', 'false');
        if (overlay) overlay.classList.remove('active');
        document.body.style.overflow = '';
      }
    }
  });

  // ---- Card Hover Effects ----
  const cards = document.querySelectorAll('.company-card, .vehicle-card, .market-card');
  cards.forEach(function(card) {
    card.addEventListener('mouseenter', function() {
      card.style.zIndex = '10';
    });
    card.addEventListener('mouseleave', function() {
      card.style.zIndex = '';
    });
  });

  // ---- Tooltip for Score Bars ----
  const scoreBarWrappers = document.querySelectorAll('.score-bar-wrapper');
  scoreBarWrappers.forEach(function(wrapper) {
    const fill = wrapper.querySelector('.score-bar__fill');
    const label = wrapper.querySelector('.score-bar-label');
    if (fill && label) {
      const score = fill.getAttribute('data-score');
      wrapper.setAttribute('title', label.textContent.trim() + ': ' + score + '/10');
    }
  });

  console.log('Hurllo Intelligence Platform loaded ✓');

})();
