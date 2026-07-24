# Cards view for The Events Calendar

Adds a **Cards** option to the calendar's view selector, next to **List**,
**Month** and **Day** — a responsive grid of event cards (featured image, date,
title, excerpt, venue).

- **Target:** The Events Calendar 6.17.1 + Events Calendar Pro 7.8.0 (Views V2).
- **No plugin edits.** Everything lives here, inside the theme, under `cards/`.

## Install

Add one line to the theme's `functions.php`:

```php
require get_stylesheet_directory() . '/cards/events-cards-view.php';
```

Then, once:

1. **Events → Settings → Display → "Enable Views"** — tick **Cards**.
   (The loader also appends it automatically; this is only a fallback if your
   site stores an explicit enabled-views list.)
2. **Settings → Permalinks → Save Changes** — registers the `/events/cards/`
   URL. Skip this and the selector link falls back to a query-string URL.

## How it works

The feature never copies or edits a plugin template, so there is nothing to
re-sync when The Events Calendar updates.

| File | Role |
| --- | --- |
| `events-cards-view.php` | Registers the view (`tribe_events_views`), keeps it enabled in the selector, routes its template here (`tribe_template_file`), and enqueues the stylesheet with `filemtime()` cache-busting. |
| `class-events-cards-view.php` | `ABMA\Events_Cards_View` — extends the stock `List_View` with the slug `cards`, made publicly visible. Inherits the list's query, pagination and AJAX. |
| `templates/cards.php` | One-line root template that renders `v2/list.php`. |
| `assets/cards.css` | Grid + card styling, scoped to `.tribe-events-view--cards`. |

Because the view's slug is `cards`, The Events Calendar stamps the view
container with `tribe-events-view--cards`. Rendering the **List** markup under
that class is what lets the CSS reflow the list into a card grid — the list's
own query, pagination and AJAX view-switching all keep working unchanged.

## Customizing

Every visual knob is a CSS custom property on `.tribe-events-view--cards`. Set
them from your theme (no need to touch `cards.css`):

```css
.tribe-events-view--cards {
    --tec-cards-min: 340px;      /* min card width before the grid wraps */
    --tec-cards-gap: 2rem;       /* space between cards               */
    --tec-cards-radius: 8px;     /* card corner radius                */
    --tec-cards-image-ratio: 4 / 3;
    --tec-cards-accent: #b8202e; /* date/time colour                  */
}
```

Reduced-motion and dark-colour-scheme variants are already handled.

## Not yet verified on a live site

The code targets the documented Views V2 extension points, but it has not been
run against a live install from here. On first activation, confirm:

- **Cards** appears in the view selector and `/events/cards/` loads.
- The card grid renders (if the selector entry shows but the URL 404s, re-save
  Permalinks).

If the label shows but styling doesn't apply, verify the container element
carries `tribe-events-view--cards` (browser devtools) — that class is the only
hook the stylesheet relies on.
