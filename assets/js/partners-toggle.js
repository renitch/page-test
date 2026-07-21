/**
 * "See all partners" progressive reveal for the Partners block.
 *
 * Each block (partners.php) renders its full logo grid but collapses every
 * logo past the initial batch (`.partner-image--overflow`, hidden in CSS).
 * The server-rendered "See all partners" button ships `hidden` so it never
 * shows without JavaScript; this script reveals it, then uncovers the next
 * batch of logos on each click. When no collapsed logos remain the button
 * removes itself and keyboard focus moves into the freshly revealed content.
 *
 * Batch size is read from `data-partners-step` on the grid, and several
 * partners blocks on one page are each wired independently.
 */
(function () {
    'use strict';

    var BLOCK_SELECTOR = '.js-partners';
    var GRID_SELECTOR = '.partners__images';
    var BUTTON_SELECTOR = '.js-see-all-partners';
    var OVERFLOW_CLASS = 'partner-image--overflow';
    var DEFAULT_STEP = 8;

    /**
     * Reveal up to `step` still-collapsed logos.
     *
     * @return {{revealed: Array, remaining: number}}
     */
    function revealNext(grid, step) {
        var collapsed = grid.querySelectorAll('.' + OVERFLOW_CLASS);
        var count = Math.min(step, collapsed.length);
        var revealed = [];

        for (var i = 0; i < count; i++) {
            collapsed[i].classList.remove(OVERFLOW_CLASS);
            revealed.push(collapsed[i]);
        }

        return { revealed: revealed, remaining: collapsed.length - count };
    }

    function parseStep(grid) {
        var step = parseInt(grid.getAttribute('data-partners-step'), 10);

        return (step && step > 0) ? step : DEFAULT_STEP;
    }

    function setupBlock(block) {
        var grid = block.querySelector(GRID_SELECTOR);
        var button = block.querySelector(BUTTON_SELECTOR);

        if (!grid || !button) {
            return;
        }

        var step = parseStep(grid);

        // Only meaningful with JS available, so it stays hidden until now.
        button.hidden = false;

        button.addEventListener('click', function () {
            var result = revealNext(grid, step);

            if (result.remaining > 0) {
                return;
            }

            var wrapper = button.closest('.partners__see-all') || button;
            wrapper.parentNode.removeChild(wrapper);

            // Keep keyboard users anchored to the content that replaced the
            // control instead of dropping focus back to <body>.
            if (result.revealed.length) {
                var target = result.revealed[0];
                target.setAttribute('tabindex', '-1');
                target.focus();
            }
        });
    }

    var blocks = document.querySelectorAll(BLOCK_SELECTOR);

    Array.prototype.forEach.call(blocks, setupBlock);
})();
