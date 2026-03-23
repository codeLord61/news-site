/**
 * dashboard.js
 * Dashboard UI interactions:
 *  - Sidebar collapse / expand (desktop)
 *  - Sidebar open / close (mobile overlay)
 *  - Profile dropdown toggle
 *  - Notification dropdown toggle
 *  - Active nav item highlight
 */

(function () {
  'use strict';

  /* ── Element references ── */
  const sidebar      = document.getElementById('sidebar');
  const mainWrapper  = document.getElementById('main-wrapper');
  const hamburgerBtn = document.getElementById('hamburger-btn');
  const hamburgerIcon = document.getElementById('hamburger-icon');
  const overlay      = document.getElementById('sidebar-overlay');

  const profileBtn      = document.getElementById('profile-btn');
  const profileDropdown = document.getElementById('profile-dropdown');

  const notifBtn      = document.getElementById('notif-btn');
  const notifDropdown = document.getElementById('notif-dropdown');

  /* ── Helpers ── */
  const isMobile = () => window.innerWidth < 768;

  function closeAllDropdowns() {
    [profileDropdown, notifDropdown].forEach(dd => {
      if (dd) dd.classList.add('hidden');
    });
    if (profileBtn) profileBtn.setAttribute('aria-expanded', 'false');
    if (notifBtn)   notifBtn.setAttribute('aria-expanded', 'false');
  }

  /* ── Sidebar ── */
  function toggleSidebar() {
    if (isMobile()) {
      const open = sidebar.classList.toggle('mobile-open');
      overlay.classList.toggle('hidden', !open);
      hamburgerBtn.setAttribute('aria-expanded', open.toString());
    } else {
      const collapsed = sidebar.classList.toggle('sidebar-collapsed');

      hamburgerIcon.className = collapsed
        ? 'fa-solid fa-bars-staggered'
        : 'fa-solid fa-bars';
      hamburgerBtn.setAttribute('aria-expanded', (!collapsed).toString());

      localStorage.setItem('sidebarCollapsed', collapsed ? '1' : '0');
    }
  }

  function closeMobileSidebar() {
    sidebar.classList.remove('mobile-open');
    overlay.classList.add('hidden');
    hamburgerBtn.setAttribute('aria-expanded', 'false');
  }

  /* Restore preference */
  (function restoreSidebar() {
    if (!isMobile() && localStorage.getItem('sidebarCollapsed') === '1') {
      sidebar.classList.add('sidebar-collapsed');
      if (hamburgerIcon) hamburgerIcon.className = 'fa-solid fa-bars-staggered';
      if (hamburgerBtn) hamburgerBtn.setAttribute('aria-expanded', 'false');
    }
  })();

  window.addEventListener('resize', () => {
    if (!isMobile()) {
      closeMobileSidebar();
    }
  });

  if (hamburgerBtn) hamburgerBtn.addEventListener('click', toggleSidebar);
  if (overlay)      overlay.addEventListener('click', closeMobileSidebar);

  /* ── Dropdown toggles ── */
  function setupDropdown(triggerEl, dropdownEl) {
    if (!triggerEl || !dropdownEl) return;

    triggerEl.addEventListener('click', function (e) {
      e.stopPropagation();
      const isHidden = dropdownEl.classList.contains('hidden');

      closeAllDropdowns();

      if (isHidden) {
        dropdownEl.classList.remove('hidden');
        dropdownEl.classList.add('dash-dropdown');
        triggerEl.setAttribute('aria-expanded', 'true');
      }
    });
  }

  setupDropdown(profileBtn, profileDropdown);
  setupDropdown(notifBtn,   notifDropdown);

  document.addEventListener('click', closeAllDropdowns);

  /* Prevent dropdown from closing when clicking inside */
  [profileDropdown, notifDropdown].forEach(dd => {
    if (dd) dd.addEventListener('click', e => e.stopPropagation());
  });

  /* ── Active nav highlight ── */
  document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function () {
      document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
      this.classList.add('active');

      // Update topbar title
      const label = this.querySelector('.sidebar-nav-text')?.textContent?.trim();
      const titleEl = document.getElementById('topbar-title');
      if (label && titleEl) titleEl.textContent = label;

      // Close mobile sidebar on nav click
      if (isMobile()) closeMobileSidebar();
    });
  });

    // Global logout handler for portability
    window.handleLogout = async function() {
      if (!confirm('Are you sure you want to sign out?')) return;
      
      try {
        // Call logout API to clear server-side session/cookie
        await fetch((window.appBaseUrl || "") + "/api/v1/logout", { 
          method: "POST",
          headers: {
            'Accept': 'application/json'
          }
        });
      } catch (err) {
        console.error("Logout API failed, proceeding with local clear:", err);
      }

      // Clear local auth data as well
      localStorage.removeItem('auth_token');
      localStorage.removeItem('user_role');

      // Redirect to login/auth page
      window.location.href = (window.appBaseUrl || "") + "/auth";
    };

})();
