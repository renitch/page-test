/**
 * Live AJAX filter for the Members flexible block.
 *
 * Searches the `members` post type by title (Name input) and the ACF
 * field `member_city` (County input) as the user types. While a filter
 * is active the default slider/grid is hidden and results render into
 * the `.js-members-results` container; clearing both inputs restores
 * the original output without a request.
 *
 * Popups for AJAX-inserted cards are handled here too: the theme's popup
 * script binds its handlers on page load, so dynamically inserted cards
 * never receive them. Popup nodes are inserted as direct children of the
 * block's `.grid-container` — the same DOM level the theme renders its own
 * popups at — and tagged with `js-ajax-popup` so they can be cleaned up on
 * every re-render.
 */
(function () {
    'use strict';

    var DEBOUNCE_MS = 350;

    var filter = document.querySelector('.js-member-filter');

    if (!filter) {
        return;
    }

    var nameInput = filter.querySelector('.js-filter-name');
    var countyInput = filter.querySelector('.js-filter-county');

    var section = filter.closest('.members-section');
    var results = section ? section.querySelector('.js-members-results') : null;
    var emptyMsg = section ? section.querySelector('.js-members-empty') : null;
    var defaults = section ? section.querySelector('.js-members-default') : null;

    if (!results) {
        return;
    }

    var popupParent = results.parentNode;

    var ajaxUrl = filter.getAttribute('data-ajax-url');
    var nonce = filter.getAttribute('data-nonce');
    var categories = filter.getAttribute('data-categories') || '[]';

    var debounceTimer = null;
    var controller = null;

    function currentName() {
        return nameInput ? nameInput.value.trim() : '';
    }

    function currentCounty() {
        return countyInput ? countyInput.value.trim() : '';
    }

    function clearAjaxPopups() {
        var stale = section.querySelectorAll('.member-popup.js-ajax-popup');

        Array.prototype.forEach.call(stale, function (node) {
            node.parentNode.removeChild(node);
        });
    }

    function insertAjaxPopups(html) {
        var holder = document.createElement('div');
        holder.innerHTML = html || '';

        var popups = holder.querySelectorAll('.member-popup');

        Array.prototype.forEach.call(popups, function (node) {
            node.classList.add('js-ajax-popup');
            popupParent.appendChild(node);
        });
    }

    function showDefault() {
        if (defaults) {
            defaults.hidden = false;
        }

        results.hidden = true;
        results.innerHTML = '';
        clearAjaxPopups();

        if (emptyMsg) {
            emptyMsg.hidden = true;
        }
    }

    function showResults(data) {
        if (defaults) {
            defaults.hidden = true;
        }

        results.innerHTML = data.cards || '';
        results.hidden = data.count === 0;

        clearAjaxPopups();
        insertAjaxPopups(data.popups);

        if (emptyMsg) {
            emptyMsg.hidden = data.count !== 0;
        }
    }

    function fetchResults() {
        if (controller) {
            controller.abort();
        }

        controller = new AbortController();

        var body = new URLSearchParams();
        body.append('action', 'members_filter');
        body.append('nonce', nonce);
        body.append('name', currentName());
        body.append('county', currentCounty());
        body.append('categories', categories);

        filter.classList.add('member-filter--loading');

        fetch(ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
            body: body.toString(),
            signal: controller.signal
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Request failed: ' + response.status);
                }

                return response.json();
            })
            .then(function (payload) {
                filter.classList.remove('member-filter--loading');

                if (payload && payload.success) {
                    showResults(payload.data);
                }
            })
            .catch(function (error) {
                if (error.name === 'AbortError') {
                    return;
                }

                filter.classList.remove('member-filter--loading');

                if (window.console && console.error) {
                    console.error('Members filter request failed:', error);
                }
            });
    }

    function onInput() {
        window.clearTimeout(debounceTimer);

        debounceTimer = window.setTimeout(function () {
            if (currentName() === '' && currentCounty() === '') {
                if (controller) {
                    controller.abort();
                }

                filter.classList.remove('member-filter--loading');
                showDefault();

                return;
            }

            fetchResults();
        }, DEBOUNCE_MS);
    }

    function closeAjaxPopup(popup) {
        popup.classList.remove('active');

        var member = results.querySelector('.member[data-member="' + popup.getAttribute('data-popup') + '"]');

        if (member) {
            member.classList.remove('active');
        }
    }

    /*
     * Capture phase, so no stopPropagation() in other scripts can prevent
     * these from running. Scoped to AJAX-inserted nodes only — the default
     * (server-rendered) content stays exclusively theme-handled.
     */
    document.addEventListener('click', function (event) {
        if (!(event.target instanceof Element)) {
            return;
        }

        var member = event.target.closest('.member');

        if (member && results.contains(member)) {
            var popup = section.querySelector('.member-popup.js-ajax-popup[data-popup="' + member.getAttribute('data-member') + '"]');

            if (popup) {
                popup.classList.add('active');
                member.classList.add('active');
            }

            return;
        }

        var openPopup = event.target.closest('.member-popup.js-ajax-popup');

        if (openPopup && event.target.closest('svg')) {
            closeAjaxPopup(openPopup);
        }
    }, true);

    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') {
            return;
        }

        var activePopup = section.querySelector('.member-popup.js-ajax-popup.active');

        if (activePopup) {
            closeAjaxPopup(activePopup);
        }
    }, true);

    if (nameInput) {
        nameInput.addEventListener('input', onInput);
    }

    if (countyInput) {
        countyInput.addEventListener('input', onInput);
    }
})();
