# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

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
