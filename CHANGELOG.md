# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

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
