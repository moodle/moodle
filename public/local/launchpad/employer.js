/**
 * Employer shell: mobile nav, journey bars, Dot Dot orbit (Fellowship parity).
 *
 * @module     local_launchpad
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
(function () {
    'use strict';

    var shell = document.getElementById('lp-shell');
    if (!shell) {
        return;
    }

    var toggle = shell.querySelector('[data-lp-nav-toggle]');
    var scrim = shell.querySelector('[data-lp-nav-close]');
    var side = document.getElementById('lp-side');

    function setOpen(open) {
        shell.classList.toggle('is-nav-open', open);
        if (toggle) {
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        }
        if (scrim) {
            if (open) {
                scrim.removeAttribute('hidden');
            } else {
                scrim.setAttribute('hidden', 'hidden');
            }
        }
    }

    if (toggle) {
        toggle.addEventListener('click', function () {
            setOpen(!shell.classList.contains('is-nav-open'));
        });
    }
    if (scrim) {
        scrim.addEventListener('click', function () {
            setOpen(false);
        });
    }
    if (side) {
        side.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.matchMedia('(max-width: 900px)').matches) {
                    setOpen(false);
                }
            });
        });
    }
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            setOpen(false);
        }
    });

    var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    function animateDash(el) {
        var to = el.getAttribute('data-to');
        if (to === null || to === '') {
            return;
        }
        if (reduce) {
            el.style.strokeDashoffset = to;
            return;
        }
        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                el.style.strokeDashoffset = to;
            });
        });
    }
    shell.querySelectorAll('.lp-journey-progress, .lp-ring-arc').forEach(animateDash);

    if (!reduce) {
        shell.querySelectorAll('.lp-side-xpbar i, .lp-bar i').forEach(function (bar) {
            var w = bar.style.width;
            if (!w) {
                return;
            }
            bar.style.width = '0%';
            requestAnimationFrame(function () {
                requestAnimationFrame(function () {
                    bar.style.width = w;
                });
            });
        });
    }

    // Dot Dot orbit (bottom-right companion) — same behaviour as Fellowship.
    var orbit = document.getElementById('lp-orbit');
    var orbitHide = document.querySelector('[data-lp-orbit-hide]');
    var orbitSay = document.getElementById('lp-orbit-say');
    var STORAGE_KEY = 'lp-employer-orbit-hidden';

    function setOrbitHidden(hidden) {
        document.body.classList.toggle('lp-orbit-is-hidden', hidden);
        if (orbitHide) {
            orbitHide.textContent = hidden
                ? (orbitHide.getAttribute('data-show-label') || 'Show Dot Dot')
                : (orbitHide.getAttribute('data-hide-label') || 'Hide Dot Dot');
        }
        try {
            window.localStorage.setItem(STORAGE_KEY, hidden ? '1' : '0');
        } catch (e) {
            // Ignore private-mode storage failures.
        }
    }

    if (orbitHide) {
        orbitHide.setAttribute('data-hide-label', orbitHide.textContent);
        orbitHide.setAttribute('data-show-label', 'Show Dot Dot');
        var stored = null;
        try {
            stored = window.localStorage.getItem(STORAGE_KEY);
        } catch (e) {
            stored = null;
        }
        if (stored === '1') {
            setOrbitHidden(true);
        }
        orbitHide.addEventListener('click', function () {
            setOrbitHidden(!document.body.classList.contains('lp-orbit-is-hidden'));
        });
    }

    if (orbit) {
        var hopBtn = orbit.querySelector('[data-lp-orbit-hop]');
        if (hopBtn) {
            hopBtn.addEventListener('click', function () {
                if (reduce) {
                    return;
                }
                orbit.classList.remove('is-hop');
                // Force reflow so the hop can replay.
                void orbit.offsetWidth;
                orbit.classList.add('is-hop');
            });
        }
    }

    var dismiss = document.querySelector('[data-lp-orbit-dismiss]');
    if (dismiss && orbitSay) {
        dismiss.addEventListener('click', function () {
            orbitSay.classList.add('is-hide');
        });
    }

})();
