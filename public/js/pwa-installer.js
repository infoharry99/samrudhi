/**
 * Shri Samruddhi Society - PWA Installer & Standalone Controller
 */
(function() {
    let deferredPrompt = null;

    // Detect Standalone / Installed App mode
    const isStandalone = window.matchMedia('(display-mode: standalone)').matches 
        || window.navigator.standalone === true 
        || document.referrer.includes('android-app://');

    // Detect iOS (Safari)
    const isIos = () => {
        const ua = window.navigator.userAgent.toLowerCase();
        return /iphone|ipad|ipod/.test(ua);
    };

    // Detect Android
    const isAndroid = () => {
        return /android/.test(window.navigator.userAgent.toLowerCase());
    };

    function updateUiState() {
        const installBtns = document.querySelectorAll('.pwa-install-btn');
        const openBtns = document.querySelectorAll('.pwa-open-btn');
        const standaloneBadges = document.querySelectorAll('.pwa-standalone-badge');
        const banners = document.querySelectorAll('.pwa-install-banner');

        if (isStandalone) {
            // Already running in the installed PWA
            installBtns.forEach(btn => btn.style.display = 'none');
            openBtns.forEach(btn => btn.style.display = 'none');
            standaloneBadges.forEach(badge => {
                badge.classList.remove('hidden');
                badge.style.display = 'inline-flex';
            });
            banners.forEach(b => b.style.display = 'none');
        } else {
            // Running in regular browser
            standaloneBadges.forEach(badge => badge.style.display = 'none');

            // If prompt is ready or on mobile
            if (deferredPrompt || isIos() || isAndroid()) {
                installBtns.forEach(btn => {
                    btn.classList.remove('hidden');
                    btn.style.display = 'inline-flex';
                });
                openBtns.forEach(btn => {
                    btn.classList.remove('hidden');
                    btn.style.display = 'inline-flex';
                });
                banners.forEach(banner => {
                    if (!sessionStorage.getItem('pwa_banner_dismissed')) {
                        banner.classList.remove('hidden');
                        banner.style.display = 'block';
                    }
                });
            }
        }
    }

    // Capture the beforeinstallprompt event
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        window.samruddhiPwaPrompt = e;
        updateUiState();
    });

    window.addEventListener('appinstalled', () => {
        deferredPrompt = null;
        console.log('Shri Samruddhi PWA successfully installed');
        showToast('App installed successfully! You can now open it from your home screen.');
        setTimeout(() => {
            updateUiState();
        }, 1000);
    });

    // Public method to trigger install
    window.triggerPwaInstall = async function() {
        if (deferredPrompt) {
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            if (outcome === 'accepted') {
                deferredPrompt = null;
            }
        } else if (isIos()) {
            openIosInstructionModal();
        } else {
            openDesktopInstructionModal();
        }
    };

    // Public method to open app
    window.openPwaApp = function() {
        if (isStandalone) {
            showToast('You are already using the installed App!');
        } else {
            // Attempt to trigger native install or prompt
            if (deferredPrompt) {
                window.triggerPwaInstall();
            } else {
                openOpenAppModal();
            }
        }
    };

    // Dismiss banner
    window.dismissPwaBanner = function() {
        sessionStorage.setItem('pwa_banner_dismissed', 'true');
        document.querySelectorAll('.pwa-install-banner').forEach(b => {
            b.style.display = 'none';
        });
    };

    // Helper: Modal for iOS installation guidance
    function openIosInstructionModal() {
        const modal = document.getElementById('pwa-ios-modal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.style.display = 'flex';
        } else {
            alert("To install on iOS: Tap the Share button below, then tap 'Add to Home Screen'.");
        }
    }

    // Helper: Modal for general instruction / open app
    function openDesktopInstructionModal() {
        const modal = document.getElementById('pwa-desktop-modal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.style.display = 'flex';
        } else {
            alert("To install this App:\n• In Chrome/Edge: Click the install icon (⊕) on the right side of the address bar.\n• On Mobile: Tap browser menu (⋮) -> 'Install app' or 'Add to Home Screen'.");
        }
    }

    function openOpenAppModal() {
        const modal = document.getElementById('pwa-open-modal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.style.display = 'flex';
        } else {
            window.triggerPwaInstall();
        }
    }

    window.closePwaModal = function(id) {
        const m = document.getElementById(id);
        if (m) {
            m.classList.add('hidden');
            m.style.display = 'none';
        }
    };

    function showToast(msg) {
        const toast = document.createElement('div');
        toast.className = 'fixed top-5 right-5 z-[9999] bg-emerald-600 text-white text-xs font-bold px-4 py-3 rounded-xl shadow-2xl flex items-center gap-2 border border-emerald-400/40 animate-bounce';
        toast.innerHTML = `<i class="fa-solid fa-circle-check text-base"></i><span>${msg}</span>`;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 4000);
    }

    // Initialize on DOM load
    document.addEventListener('DOMContentLoaded', () => {
        updateUiState();
        // Also bind any buttons with [data-pwa-action="install"] or [data-pwa-action="open"]
        document.querySelectorAll('[data-pwa-action="install"]').forEach(el => {
            el.addEventListener('click', (e) => {
                e.preventDefault();
                window.triggerPwaInstall();
            });
        });
        document.querySelectorAll('[data-pwa-action="open"]').forEach(el => {
            el.addEventListener('click', (e) => {
                e.preventDefault();
                window.openPwaApp();
            });
        });
    });
})();
