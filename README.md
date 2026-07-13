# page-test

Static homepage for the **Arkansas Black Mayors Association (ABMA)**, hand-converted
from the `ABMA_Homepage.psd` mockup (1920 × 4825 px) with the goal of being
pixel-identical to the design at a 1920 px viewport.

## Structure

```
index.html        page markup — real, selectable text positioned at PSD coordinates
css/styles.css    all styles (colors, typography, exact positions)
assets/bg-*.png   artwork strips rendered from the PSD (photos, shapes, icons,
                  display headlines set in Europa Grotesk SH)
```

## Approach

- The page is a fixed 1920 px canvas, centered in the viewport.
- Photos, shapes, gradients, icons and the display headlines (Europa Grotesk SH —
  a commercial typeface that cannot be redistributed) are kept as baked artwork
  strips exported from the PSD. Headlines carry screen-reader equivalents.
- All body/UI text is real HTML text set in **Poppins** (Google Fonts) at the
  exact sizes, weights, letter-spacing, and coordinates extracted from the PSD
  text engine data.
- Interactive affordances from the mockup are real elements: navigation links,
  the *About ABMA* hover dropdown (shown open in the mockup), the search field,
  Learn More buttons, and link hotspots over baked imagery.
- Fidelity was verified by screenshotting the page in headless Chromium at
  1920 px and pixel-diffing against the PSD composite.

## Viewing

Open `index.html` in any browser (needs internet access for Google Fonts).
The layout is fixed-width by design — it reproduces the mockup exactly and
does not reflow at other viewport sizes.
