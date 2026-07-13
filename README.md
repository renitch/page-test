# page-test

Static homepage for the **Arkansas Black Mayors Association (ABMA)**, hand-converted
from the `ABMA_Homepage.psd` mockup (1920 × 4825 px) with the goal of being
pixel-identical to the design at a 1920 px viewport.

## Structure

```
index.html        semantic BEM markup — real text everywhere
scss/             source styles (SCSS partials, one per section)
  _settings.scss  colors, fonts, container widths, rem-calc()/breakpoint() helpers
  _base.scss      reset + shared components (.grid-container, .section-title,
                  .title-tail, .button, .circle-arrow)
  _header.scss    yellow top band, logo, menu + hover dropdown, search,
                  navy subscribe bar
  _hero.scss      city panorama, display title, floating "Click Here" bubble
  _cards.scss     quick-link card grid + association intro paragraph
  _join.scss      yellow "Join the Organization" band with watermark
  _news.scss      news feature card straddling the join/board seam
  _board.scss     executive board portraits over the faint Arkansas flag
  _social.scss    "Follow us on Social media" lavender feed card
  _members.scss   active-members slider with filters and arrows
  _footer.scss    yellow footer
css/styles.css    compiled output (libsass, expanded style)
assets/img/       photos and logos exported from the PSD (the only raster
                  assets — everything else is CSS)
```

## Approach

- **Code over images.** All layout, shapes, gradients and decorations are CSS:
  the header wedge and speech-bubble tails are border triangles, the title
  underlines with their notch are `.title-tail` pseudo-elements, the portrait
  gold rings are bordered circles, the corner notch crossing into the footer is
  a `clip-path` polygon, and the faint Arkansas flag is a `background-image`
  at `opacity: .1` matching the PSD layer's 26/255 opacity. Icons are inline
  SVG or Font Awesome; only genuine photography/logos remain as image files.
- **BEM + SCSS conventions** follow the examples in `examples/`
  (`header.txt`, `header css.txt`): block__element class names, `$variables`,
  `rem-calc()` sizing, `breakpoint()` media-query mixin, flex layout,
  visually-hidden `.css-clip` text for screen readers.
- Display headlines use **Archivo** as a stand-in for Europa Grotesk SH (a
  commercial face that cannot be redistributed); body text is **Poppins**,
  small print **Inter** (Google Fonts).
- Sections were measured out of the PSD (text engine data, layer bboxes) and
  the build verified by screenshotting in headless Chromium at 1920 px and
  pixel-diffing against the PSD composite — section boundaries, portrait rows,
  captions and card seams land within ~2 px of the mockup.

## Build

```
pip install libsass
python _extract/build_css.py   # compiles scss/styles.scss -> css/styles.css
```

Open `index.html` directly (needs internet access for Google Fonts and Font
Awesome). Breakpoint styles provide a reasonable reflow below 1460 px, but the
reference layout is the 1920 px design width.
