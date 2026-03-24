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
  // Returns true on small screens where sidebar should behave as overlay.
  const isMobile = () => window.innerWidth < 768;

  /**
   * Close profile/notification dropdowns and reset accessibility flags.
   * Output: no return value; updates dropdown visibility in DOM.
   */
  function closeAllDropdowns() {
    [profileDropdown, notifDropdown].forEach(dd => {
      if (dd) dd.classList.add('hidden');
    });
    if (profileBtn) profileBtn.setAttribute('aria-expanded', 'false');
    if (notifBtn)   notifBtn.setAttribute('aria-expanded', 'false');
  }

  /* ── Sidebar ── */
  /**
   * Toggle sidebar state.
   *
   * Mobile: opens/closes overlay drawer.
   * Desktop: toggles collapsed class and stores preference in localStorage.
   */
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

      // Persist sidebar state so it remains after page refresh.
      localStorage.setItem('sidebarCollapsed', collapsed ? '1' : '0');
    }
  }

  /**
   * Force-close mobile sidebar and overlay backdrop.
   */
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
  /**
   * Wire one trigger element to one dropdown panel.
   *
   * Input: trigger button + dropdown element.
   * Output: no return value; click handlers are attached.
   */
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
      // UI state update: exactly one nav link should remain active.
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

    /**
     * Logout function exposed globally for dashboard buttons.
     *
     * Output flow:
     * 1) attempt server logout
     * 2) clear client-side auth cache
     * 3) redirect to /auth
     */
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
