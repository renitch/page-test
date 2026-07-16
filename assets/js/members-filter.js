/**
 * Live AJAX filter for the Members flexible block.
 *
 * Searches the `members` post type by title (Name input) and the ACF
 * field `member_city` (County input) as the user types. While a filter
 * is active the default slider/grid is hidden and results render into
 * the `.js-members-results` container; clearing both inputs restores
 * the original output without a request.
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
    var popups = section ? section.querySelector('.js-members-popups') : null;
    var emptyMsg = section ? section.querySelector('.js-members-empty') : null;
    var defaults = section ? section.querySelector('.js-members-default') : null;

    if (!results || !popups) {
        return;
    }

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

    function showDefault() {
        if (defaults) {
            defaults.hidden = false;
        }

        results.hidden = true;
        results.innerHTML = '';
        popups.innerHTML = '';

        if (emptyMsg) {
            emptyMsg.hidden = true;
        }
    }

    function showResults(data) {
        if (defaults) {
            defaults.hidden = true;
        }

        results.innerHTML = data.cards || '';
        popups.innerHTML = data.popups || '';
        popups.hidden = false;
        results.hidden = data.count === 0;

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

    if (nameInput) {
        nameInput.addEventListener('input', onInput);
    }

    if (countyInput) {
        countyInput.addEventListener('input', onInput);
    }
})();
