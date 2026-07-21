# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Progressive "See all partners" reveal on the Partners block
  (`partners.php`): the logo grid shows the first 8 logos and a
  "See all partners" button; each click reveals the next 8 and the button
  removes itself once every logo is visible. Logos past the first batch are
  server-collapsed (`.partner-image--overflow`), with a `<noscript>` fallback
  that shows every logo when JavaScript is off. Supports several partners
  blocks per page via a `wp_unique_id()` grid id and `aria-controls`.
- `assets/js/partners-toggle.js` — dependency-free reveal logic scoped per
  block (batch size from `data-partners-step`); reveals the `hidden` button
  only when JS is present and moves keyboard focus to the first newly revealed
  logo when the button is removed.
- `scss/_partners.scss` (imported in `styles.scss`, recompiled into
  `css/styles.css`) — responsive 4/3/2-column logo grid, the `--overflow`
  hide rule, and the centered "See all partners" button reusing the shared
  `.button` style.
- `inc/partners-toggle.php` — enqueues the toggle script with `filemtime()`
  cache-busting. Include it from the theme's `functions.php`.

### Changed
- Title underlines rebuilt with pseudo-elements only (`.section-title--tail`
  in `_base.scss`): `::after` draws the interrupted 3px line as two
  background-gradient segments in flow, `::before` hangs the notch — its
  vertical left edge plus a 45° corner-to-corner gradient diagonal — below
  the gap. The `<span class="title-tail">` elements (join, news, board,
  members) and `<span class="social__rule">` are removed from the markup;
  per-section geometry moved to `--tail-*` custom properties.
- Underline made length-agnostic: the notch position is a fraction of the
  line (`--tail-pos`, default 0.55 as measured in the PSD) applied through
  percentage background sizes and a `calc()` length×number product, so the
  tail works on any line width with no per-width pixel tuning; only join
  (0.65) and board (0.775) override the fraction.

### Fixed
- Notch shape and placement now match the PSD (verified by headless-Chromium
  screenshot diffing against `reference.png`): the tail is the mockup's
  hollow speech-tail (vertical drop + diagonal return) instead of a solid
  triangle; the join notch moved from 20% to 65% along the line (208px of
  318px, per the PSD at y=1791); members/board/social notch positions
  re-measured from the PSD; the social rule now stops at 224px like the
  mockup instead of running to the card edge.

### Added
- `css/gradient.css` — navy overlay gradient reproduced from
  `images/gradient.jpg` (Photoshop dialog): linear `#1e3160` (the `$navy`
  token) → transparent at `170.31deg` (converted from Photoshop's -80.31deg),
  exposed as `--gradient-*` custom properties with `.gradient` and an
  `in oklab` `.gradient--perceptual` utility.
- Live AJAX search filter for the Members flexible block
  (`flexible-members.php`): filters the `members` post type by post title
  (Name input) and the ACF field `member_city` (County input), scoped to the
  block's selected `members_cat` categories and secured with a nonce.
- `inc/members-filter-ajax.php` — `wp_ajax_(nopriv_)members_filter` handler
  with a title-only `LIKE` search (custom `posts_where` via the
  `members_title_like` query var), `member_city` meta `LIKE` query, and
  card/popup renderers matching the block's grid markup. Include it from the
  theme's `functions.php`.
- `assets/js/members-filter.js` — debounced (350 ms) fetch-based live search
  with request aborting, an empty-results message, and restore of the default
  slider/grid when both inputs are cleared.

### Fixed
- Member popups not opening for AJAX-inserted search results: the theme's
  popup script binds handlers on page load, so filtered cards never received
  them. `members-filter.js` now delegates clicks for members inside the AJAX
  results container — clicking a card adds `active` to its matching
  `.member-popup` (and the card), the close icon or Escape removes it —
  without touching the theme-handled default content.
- Popup delegation still not firing after the first fix: the enqueued script
  version was a constant `1.0.0`, so browsers kept serving the cached first
  build of `members-filter.js`; the version is now `filemtime()`-based so
  every change busts the cache. AJAX popups are also no longer wrapped in a
  helper `<div>` — they are inserted as direct children of the block's
  `.grid-container` (the same DOM level as theme-rendered popups, tagged
  `js-ajax-popup`), so positional CSS selectors match, and the delegated
  listeners now run in capture phase so a `stopPropagation()` in another
  script cannot swallow the click.

### Changed
- Rebuilt the page as code instead of baked artwork strips, following the BEM /
  SCSS conventions of `examples/header.txt` and `examples/header css.txt`:
  semantic markup with real text everywhere, SCSS partials per section with
  `$variables`, `rem-calc()` and `breakpoint()` helpers compiled by libsass,
  flex layout, inline SVG + Font Awesome icons.
- All shapes and decorations are now CSS: header wedge and bubble tails
  (border triangles), title underlines with notch (`.title-tail`), portrait
  gold rings (bordered circles), footer corner notch (`clip-path`), "AB" join
  watermark (`::before` text), Arkansas flag at `opacity: .1` as a positioned
  `background-image` — matching the PSD layer's 26/255 opacity and blend.
- Only genuine photos/logos remain as images, re-exported into `assets/img/`
  (hero, card tiles, news photo, flag art, 14 portraits, 4 social tiles,
  2 logos); hero re-cut to the exact 1920 × 719 design viewport.
- Section geometry re-measured from the PSD composite and verified by
  headless-Chromium screenshot diffing: section boundaries, portrait rows,
  captions, news-card seam and footer rows land within ~2 px of the mockup.
- README rewritten for the SCSS/BEM structure and build command.

### Removed
- Obsolete background strips `assets/bg-0..5.png` and the fixed-canvas
  text-overlay implementation they supported.
- Members slider arrow buttons — the mockup shows no arrows beside the
  portrait rows.

### Fixed
- Dark blurred rectangle in the hero sky left of "United for Progress" — the
  area reconstructed behind the mockup's open dropdown was rendered without
  the hero Curves adjustment. Now tone-matched with a master curve plus a
  membrane (harmonic) offset so the patch meets the surrounding sky exactly.

### Added
- Initial pixel-identical HTML/CSS implementation of the ABMA homepage mockup
  (`ABMA_Homepage.psd`, 1920 × 4825 px): header with hover dropdown, hero,
  quick-link cards, join section, latest news, executive board, social media
  feed, active members, and footer.
- Background artwork strips exported from the PSD (`assets/bg-0..5.png`).
- Real-text overlay for all Poppins-set copy at exact PSD coordinates.
- Link hotspots for interactive areas (navigation, buttons, social icons,
  cards, sliders).
- README with structure and approach notes.
