import './bootstrap';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import '../../public/themes/default/js/script.js';

/* helpers */
const $ = (s, root = document) => root.querySelector(s);
const $$ = (s, root = document) => Array.from(root.querySelectorAll(s));
const isMobile = () => window.innerWidth < 768;
const debounce = (fn, wait = 120) => {
  let t;
  return (...args) => { clearTimeout(t); t = setTimeout(() => fn.apply(this, args), wait); };
};

/* 1) کنترل اسلایدر قیمت */
(function initPriceRange() {
  const minPrice = $('#price_min_range');
  const maxPrice = $('#price_max_range');
  const minPriceOutput = $('#price_min_output');
  const maxPriceOutput = $('#price_max_output');

  if (!minPrice || !maxPrice) return;

  function syncPriceRange() {
    const a = Number(minPrice.value);
    const b = Number(maxPrice.value);
    if (a > b) [minPrice.value, maxPrice.value] = [maxPrice.value, minPrice.value];
    if (minPriceOutput) minPriceOutput.textContent = Number(minPrice.value).toLocaleString('fa-IR');
    if (maxPriceOutput) maxPriceOutput.textContent = Number(maxPrice.value).toLocaleString('fa-IR');
  }

  minPrice.addEventListener('input', syncPriceRange);
  maxPrice.addEventListener('input', syncPriceRange);
  syncPriceRange();
})();

/* 2) مدیریت منوی موبایل، دسته‌بندی‌ها و منوی پروفایل (یک DOMContentLoaded واحد و منسجم) */
document.addEventListener('DOMContentLoaded', function () {

  const toggle = document.getElementById('mobileMenuToggle');
  const panel = document.getElementById('mobileMenuPanel');

  /* پاکسازی وضعیت‌های پیش‌فرض */
  (function clearDefaults() {
    $$('.mobile-menu-panel.show').forEach(el => el.classList.remove('show'));
    $$('.mobile-menu-panel .nav-item.category-open').forEach(el => el.classList.remove('category-open'));
    $$('.category-menu .dropdown-menu.show').forEach(el => el.classList.remove('show'));
    $$('.site-tools .dropdown-toggle[aria-expanded="true"]').forEach(btn => btn.setAttribute('aria-expanded', 'false'));
    if (toggle) toggle.setAttribute('aria-expanded', 'false');
  })();

  /* توابع باز/بسته کردن منوی موبایل */
  function openMobileMenu() {
    if (!panel) return;
    panel.classList.add('show');
    panel.setAttribute('aria-hidden', 'false');
    if (toggle) toggle.setAttribute('aria-expanded', 'true');
    document.documentElement.classList.add('no-scroll--menu-open');
  }
  function closeMobileMenu() {
    if (!panel) return;
    panel.classList.remove('show');
    panel.setAttribute('aria-hidden', 'true');
    if (toggle) toggle.setAttribute('aria-expanded', 'false');
    document.documentElement.classList.remove('no-scroll--menu-open');
  }

  /* اگر پنل یا دکمه وجود ندارد، فقط تراز منوی پروفایل را تنظیم کن و خارج شو */
  if (!panel || !toggle) {
    alignProfileDropdown();
    window.addEventListener('resize', debounce(alignProfileDropdown, 120));
    return;
  }

  /* باز/بسته کردن منوی موبایل با دکمه */
  toggle.addEventListener('click', function (e) {
    e.preventDefault();
    panel.classList.contains('show') ? closeMobileMenu() : openMobileMenu();
  });

  /* بستن با کلیک خارج */
  document.addEventListener('click', function (ev) {
    if (!panel.classList.contains('show')) return;
    if (panel.contains(ev.target) || (toggle && toggle.contains(ev.target))) return;
    closeMobileMenu();
  });

  /* بستن با Escape */
  document.addEventListener('keydown', function (ev) {
    if (ev.key === 'Escape' && panel.classList.contains('show')) closeMobileMenu();
  });

  /* لینک‌های داخل پنل: اگر لینک والد زیرمنو دارد، اجازهٔ هدایت نده؛ در غیر این صورت منو را ببند */
  panel.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', function (ev) {
      const parent = link.closest('.nav-item');
      const hasChildren = parent && parent.querySelector('.category-child');
      if (hasChildren) {
        // اگر لینک ریشه است و کاربر Ctrl/Meta کلیک نکرده، رفتار toggle را مدیریت می‌کنیم (در ادامه delegation)
        return;
      }
      closeMobileMenu();
    });
  });

  /* دسته‌بندی‌ها: init — اطمینان از بسته بودن همهٔ زیرمنوها */
  panel.querySelectorAll('.nav-item').forEach(item => {
    item.classList.remove('category-open');
    item.setAttribute('aria-expanded', 'false');
  });

  /* اطمینان از اینکه همهٔ زیرمنوها بسته هستند */
  panel.querySelectorAll('.category-child').forEach(child => {
    child.style.display = 'none';
  });

  /* toggle دسته‌بندی‌ها با delegation (تنها منبع رویداد؛ از دوبار toggle شدن جلوگیری می‌کند) */
  panel.addEventListener('click', function (e) {
    const target = e.target;

    // دکمهٔ toggle کلیک شده
    const toggleBtn = target.closest('.category-toggle');
    if (toggleBtn) {
      e.preventDefault();
      const parentLi = toggleBtn.closest('.nav-item');
      if (!parentLi) return;
      const id = toggleBtn.getAttribute('aria-controls');
      const child = id ? document.getElementById(id) : parentLi.querySelector('.category-child');
      const isOpen = parentLi.classList.toggle('category-open');
      toggleBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      if (child) child.style.display = isOpen ? 'block' : 'none';
      return;
    }

    // کلیک روی لینک ریشه
    const rootLink = target.closest('a.category-root, .category-root');
    if (rootLink) {
      const parentLi = rootLink.closest('.nav-item');
      if (!parentLi) return;
      const child = parentLi.querySelector('.category-child');
      if (child) {
        if (e.ctrlKey || e.metaKey || e.button === 1) return;
        e.preventDefault();
        const isOpen = parentLi.classList.toggle('category-open');
        parentLi.querySelector('.category-toggle')?.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        child.style.display = isOpen ? 'block' : 'none';
      }
      return;
    }

    // کلیک روی لینک داخل زیرمنو: اجازهٔ هدایت بده و منو را ببند
    const insideLink = target.closest('.category-child a, .category-child .nav-link');
    if (insideLink) {
      if (panel.classList.contains('show')) {
        setTimeout(() => {
          closeMobileMenu();
        }, 80);
      }
      return;
    }
  });

  /* جلوگیری از باز شدن خودکار bootstrap در موبایل برای dropdown های site-tools */
  $$('.site-tools [data-bs-toggle="dropdown"]').forEach(btn => {
    btn.addEventListener('show.bs.dropdown', function (e) {
      if (isMobile()) {
        e.preventDefault();
        const navItem = btn.closest('.nav-item');
        const menu = navItem ? navItem.querySelector('.dropdown-menu') : null;
        if (!menu) return;
        const shown = menu.classList.toggle('show');
        btn.setAttribute('aria-expanded', shown ? 'true' : 'false');
      }
    });
  });

  /* تراز منوی پروفایل */
  function alignProfileDropdown() {
    $$('.site-tools .dropdown-menu').forEach(menu => {
      menu.style.left = '';
      menu.style.right = '';
      menu.style.transformOrigin = '';
      if (isMobile()) {
        menu.style.left = '8px';
        menu.style.right = 'auto';
        menu.style.transformOrigin = 'top left';
      } else {
        menu.style.right = '0';
        menu.style.left = 'auto';
        menu.style.transformOrigin = 'top right';
      }
    });
  }
  alignProfileDropdown();
  window.addEventListener('resize', debounce(alignProfileDropdown, 120));

  /* محافظت نهایی: پاکسازی نمایش‌های ناخواسته در دسکتاپ */
  setTimeout(() => {
    if (!isMobile()) {
      $$('.site-tools .dropdown-menu.show').forEach(menu => menu.classList.remove('show'));
      $$('.site-tools .dropdown-toggle[aria-expanded="true"]').forEach(btn => btn.setAttribute('aria-expanded', 'false'));
    }
  }, 400);

}); // DOMContentLoaded end
