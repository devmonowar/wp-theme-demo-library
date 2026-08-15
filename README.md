# wp-theme-demo-library

Remote demo library for [devmonowar](https://github.com/devmonowar)'s WordPress
themes. Served over **GitHub Pages** and consumed by each theme's demo importer,
so demos can be added or updated **without shipping a new theme release**.

> This repository contains **only demo resources** (exports + images). It never
> contains WordPress theme code, and nothing here is bundled into a theme's
> ThemeForest package.

This is the theme-side twin of
[wp-plugin-demo-library](https://github.com/devmonowar/wp-plugin-demo-library)
and follows the same layout.

## Layout

```
wp-theme-demo-library/
└── portolite/
    ├── demo-library.json      # manifest: list of demos + metadata
    ├── demos/                 # one JSON per demo (file URLs + after-import setup)
    ├── previews/              # card thumbnails
    └── assets/
        └── <demo-id>/         # content.xml, widgets.wie, customizer.dat, uploads/
```

Each theme gets its own top-level folder (`portolite/`, future themes…).

## GitHub Pages

Enable **Settings → Pages → Deploy from branch → `main` / `/` (root)**.

Base URL:

```
https://devmonowar.github.io/wp-theme-demo-library/
```

Theme manifests:

```
https://devmonowar.github.io/wp-theme-demo-library/portolite/demo-library.json
```

## Manifest format

`demo-library.json` lists the available demos:

```json
{
  "schema_version": 1,
  "theme": "portolite",
  "minimum_theme_version": "1.0.0",
  "demos": [
    {
      "id": "car-repair",
      "name": "Car Repair Services",
      "description": "A full workshop site: services, pricing, the technicians…",
      "version": "1.0",
      "updated": "2026-08-15",
      "requires": "1.0.0",
      "category": "Car Repair",
      "tags": ["garage", "servicing"],
      "featured": true,
      "new": true,
      "preview": ".../previews/car-repair.jpg",
      "preview_width": 1200,
      "preview_height": 675,
      "file": ".../demos/car-repair.json"
    }
  ]
}
```

Each `file` points to one demo JSON. A theme demo is more than one file, so that
JSON is a descriptor rather than the payload:

```json
{
  "id": "car-repair",
  "files": {
    "content":    ".../assets/car-repair/content.xml",
    "widgets":    ".../assets/car-repair/widgets.wie",
    "customizer": ".../assets/car-repair/customizer.dat"
  },
  "setup": {
    "front_page": "Home",
    "posts_page": "Blog",
    "menus": { "main-menu": "Main Menu" }
  }
}
```

- `content` — WordPress eXtended RSS export: pages, posts, custom post types,
  menus and media. Image URLs inside it already point at this library, so the
  importer downloads them onto the end-user's site.
- `widgets` — Widget Importer & Exporter format.
- `customizer` — Customizer Export/Import format (serialized `template`, `mods`, `options`).
- `setup` — what the theme applies after the import: which page becomes the
  front page, which becomes the posts page, and which menu goes in which
  location. Each demo carries its own, so demo two does not need demo one's
  page names.

## How themes consume it

The theme fetches its `demo-library.json` server-side (cached ~6 hours), renders
one card per demo in **Appearance → Import Demo Data** (One Click Demo Import),
and version-gates each by `requires`. On import it downloads that demo's files,
sideloads every image into the site's Media Library, and applies the `setup`
block — so nothing here ships inside the theme package.

## Adding a demo

1. Build the demo site locally on the theme.
2. Export it into `portolite/assets/<demo-id>/` — content, widgets, Customizer
   and the images, with the image URLs inside the XML rewritten to this library.
3. Add `portolite/demos/<demo-id>.json` (file URLs + `setup`).
4. Add a 1200×675 card image to `portolite/previews/<demo-id>.jpg`.
5. Add the demo's entry to `portolite/demo-library.json`.
6. Record the image source/license in `portolite/CREDITS.md`.
7. Commit & push. The new demo appears in the theme automatically (after the
   6-hour manifest cache expires, or a manual refresh).

No theme release or Git tag required.

## Image licensing

Use only images whose licence allows redistribution — Unsplash, Pexels, Pixabay
or CC0. Images are downloaded onto end-user sites on import, so the licence must
allow that. Anything from a subscription library (Envato Elements and the like)
must never appear here. See each theme's `CREDITS.md`.
