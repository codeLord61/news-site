document.addEventListener("DOMContentLoaded", () => {
    const hamburgerBtn = document.getElementById('hamburgerMenuBtn');
    const closeBtn = document.getElementById('closeOverlayBtn');
    const overlayMenu = document.getElementById('overlayMenu');
    
    // Auth elements
    const overlayLoggedOut = document.getElementById('overlayLoggedOut');
    const overlayLoggedIn = document.getElementById('overlayLoggedIn');
    const overlayUserName = document.getElementById('overlayUserName');
    const overlayUserAvatar = document.getElementById('overlayUserAvatar');
    const overlayUserDefaultIcon = document.getElementById('overlayUserDefaultIcon');
    const overlayLogoutBtn = document.getElementById('overlayLogoutBtn');

    // UI state toggles
    function openOverlay() {
        overlayMenu.classList.remove('hidden');
        overlayMenu.classList.add('flex');
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
        checkAuthState(); // fetch user state every time we open
    }

    function closeOverlay() {
        overlayMenu.classList.add('hidden');
        overlayMenu.classList.remove('flex');
        document.body.style.overflow = ''; // Restore scrolling
    }

    if (hamburgerBtn) {
        hamburgerBtn.addEventListener('click', openOverlay);
    }
    
    if (closeBtn) {
        closeBtn.addEventListener('click', closeOverlay);
    }

    // Toggle dropdowns inside categories
    const dropdownToggles = document.querySelectorAll('.overlay-dropdown-toggle');
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', (e) => {
            const targetId = toggle.getAttribute('data-target');
            const targetElement = document.getElementById(targetId);
            const icon = toggle.querySelector('i');
            
            if (targetElement.classList.contains('hidden')) {
                // Open
                targetElement.classList.remove('hidden');
                targetElement.classList.add('block');
                icon.style.transform = 'rotate(180deg)';
            } else {
                // Close
                targetElement.classList.add('hidden');
                targetElement.classList.remove('block');
                icon.style.transform = 'rotate(0deg)';
            }
        });
    });

    // Check Auth and update UI
    async function checkAuthState() {
        const token = localStorage.getItem('auth_token');
        if (!token) {
            showLoggedOutState();
            return;
        }

        try {
            const response = await fetch((window.appBaseUrl || "") + "/api/v1/auth/me", {
                method: "GET",
                headers: {
                    "Authorization": "Bearer " + token,
                    "Accept": "application/json"
                },
                credentials: "include"
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success && data.user) {
                    showLoggedInState(data.user);
                } else {
                    showLoggedOutState();
                }
            } else {
                showLoggedOutState();
            }
        } catch (e) {
            console.error("Failed to fetch user state for overlay:", e);
            showLoggedOutState();
        }
    }

    function showLoggedOutState() {
        overlayLoggedIn.classList.add('hidden');
        overlayLoggedIn.classList.remove('flex');
        overlayLoggedOut.classList.remove('hidden');
        overlayLoggedOut.classList.add('block');
    }

    function showLoggedInState(user) {
        overlayLoggedOut.classList.add('hidden');
        overlayLoggedOut.classList.remove('block');
        overlayLoggedIn.classList.remove('hidden');
        overlayLoggedIn.classList.add('flex');
        
        overlayUserName.textContent = user.name || "User";
        
        // If we had a real avatar URL from API, we'd use it. For now, just initials or default icon
        overlayUserDefaultIcon.classList.remove('hidden');
        overlayUserAvatar.classList.add('hidden');
    }

    // Logout from Overlay
    if (overlayLogoutBtn) {
        overlayLogoutBtn.addEventListener('click', async () => {
            const token = localStorage.getItem('auth_token');
            try {
                await fetch((window.appBaseUrl || "") + "/api/v1/logout", {
                    method: "POST",
                    headers: {
                        "Authorization": "Bearer " + token,
                        "Accept": "application/json"
                    },
                    credentials: "include"
                });
            } catch (e) {
                console.error("Logout network failed:", e);
            }
            
            // Clear locals
            localStorage.removeItem('auth_token');
            localStorage.removeItem('user_role');
            
            // Reload page or just update UI
            location.reload();
        });
    }
});
