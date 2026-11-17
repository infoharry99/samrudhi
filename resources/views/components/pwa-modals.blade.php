<!-- PWA iOS Instructions Modal -->
<div id="pwa-ios-modal" class="fixed inset-0 z-[9999] bg-slate-900/80 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 text-slate-800 shadow-2xl relative border border-slate-100 animate-in fade-in zoom-in duration-200">
        <button onclick="closePwaModal('pwa-ios-modal')" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 p-2">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>

        <div class="text-center mb-4">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-brand-600 to-indigo-600 mx-auto flex items-center justify-center text-white text-3xl shadow-xl shadow-brand-500/30 mb-3">
                <i class="fa-solid fa-mobile-screen"></i>
            </div>
            <h3 class="text-lg font-bold font-display text-slate-900">Install Shri Samruddhi App</h3>
            <p class="text-xs text-slate-500 mt-0.5">Quick setup for iPhone & iPad (Safari)</p>
        </div>

        <div class="space-y-3 bg-slate-50 rounded-2xl p-4 border border-slate-100 text-xs text-slate-700">
            <div class="flex items-start gap-3">
                <div class="w-6 h-6 rounded-full bg-brand-100 text-brand-700 font-bold flex items-center justify-center shrink-0 text-[11px]">1</div>
                <div>
                    Tap the <strong>Share</strong> button <i class="fa-solid fa-arrow-up-from-bracket text-brand-600 mx-1"></i> in the Safari toolbar at the bottom of your screen.
                </div>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-6 h-6 rounded-full bg-brand-100 text-brand-700 font-bold flex items-center justify-center shrink-0 text-[11px]">2</div>
                <div>
                    Scroll down and tap <strong>"Add to Home Screen"</strong> <i class="fa-regular fa-square-plus text-brand-600 mx-1"></i>.
                </div>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-6 h-6 rounded-full bg-brand-100 text-brand-700 font-bold flex items-center justify-center shrink-0 text-[11px]">3</div>
                <div>
                    Tap <strong>"Add"</strong> at the top right. Samruddhi App will appear on your Home Screen!
                </div>
            </div>
        </div>

        <button onclick="closePwaModal('pwa-ios-modal')" class="w-full mt-5 py-3 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-lg shadow-brand-500/25 transition">
            Got It!
        </button>
    </div>
</div>

<!-- PWA Desktop / Browser Modal -->
<div id="pwa-desktop-modal" class="fixed inset-0 z-[9999] bg-slate-900/80 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 text-slate-800 shadow-2xl relative border border-slate-100 animate-in fade-in zoom-in duration-200">
        <button onclick="closePwaModal('pwa-desktop-modal')" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 p-2">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>

        <div class="text-center mb-4">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-600 mx-auto flex items-center justify-center text-white text-3xl shadow-xl shadow-emerald-500/30 mb-3">
                <i class="fa-solid fa-download"></i>
            </div>
            <h3 class="text-lg font-bold font-display text-slate-900">Install PWA Application</h3>
            <p class="text-xs text-slate-500 mt-0.5">Standalone App for Mobile & Desktop</p>
        </div>

        <div class="space-y-3 bg-slate-50 rounded-2xl p-4 border border-slate-100 text-xs text-slate-700">
            <div class="flex items-start gap-3">
                <i class="fa-brands fa-chrome text-brand-600 text-base mt-0.5"></i>
                <div>
                    <strong>In Chrome / Edge:</strong> Click the <i class="fa-solid fa-laptop-arrow-down text-brand-600"></i> install icon on the right end of your URL address bar.
                </div>
            </div>
            <div class="flex items-start gap-3">
                <i class="fa-brands fa-android text-emerald-600 text-base mt-0.5"></i>
                <div>
                    <strong>On Android:</strong> Tap browser menu <i class="fa-solid fa-ellipsis-vertical text-slate-600"></i> and select <strong>"Install app"</strong> or <strong>"Add to Home Screen"</strong>.
                </div>
            </div>
        </div>

        <button onclick="closePwaModal('pwa-desktop-modal')" class="w-full mt-5 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-lg transition">
            Close
        </button>
    </div>
</div>

<!-- PWA Open App Modal -->
<div id="pwa-open-modal" class="fixed inset-0 z-[9999] bg-slate-900/80 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 text-slate-800 shadow-2xl relative border border-slate-100 text-center animate-in fade-in zoom-in duration-200">
        <button onclick="closePwaModal('pwa-open-modal')" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 p-2">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>

        <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-brand-600 to-indigo-600 mx-auto flex items-center justify-center text-white text-3xl shadow-xl shadow-brand-500/30 mb-3">
            <i class="fa-solid fa-mobile-screen-button"></i>
        </div>
        <h3 class="text-lg font-bold font-display text-slate-900">Open Samruddhi App</h3>
        <p class="text-xs text-slate-500 mt-1 mb-4">
            If you have already installed the app, tap the <strong>Samruddhi icon</strong> on your phone's Home Screen for the full fullscreen app experience!
        </p>

        <div class="flex flex-col gap-2">
            <button onclick="window.triggerPwaInstall(); closePwaModal('pwa-open-modal');" class="w-full py-3 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-lg shadow-brand-500/25 transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-download"></i>
                <span>Install App on this Device</span>
            </button>
            <button onclick="closePwaModal('pwa-open-modal')" class="w-full py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition">
                Continue in Browser
            </button>
        </div>
    </div>
</div>
