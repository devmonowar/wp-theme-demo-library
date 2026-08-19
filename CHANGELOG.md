# Changelog

All notable changes to this demo library.

## 2026-08-20

- Added **Fieldnotes** to Kivora: the second and last demo the theme's v1 scope
  allows, and deliberately the opposite of Creative Studio. A personal journal
  of walking and writing -- 6 pages, 8 posts across three categories with one
  sticky post and a comment thread, a gallery page, 10 CC0 photographs, two
  menus, the sidebar and three of the four footer columns. Its theme settings
  are chosen to differ from the first demo's (sidebar on the left, none on a
  single post, no sticky header) so the two together show what those controls
  do. Imported end to end onto a clean install before publishing.
- The demo carries no portrait and no identifiable person in any photograph.
  The CC0 portraits available are photographs of real people, and a demo that
  lands on other people's sites is the wrong place to carry someone's face.
- Its footer copyright line names nobody. That string is a setting, not
  content: it arrives switched on and would otherwise credit a fictional
  author in the footer of every site that imported the demo.

## 2026-08-19 (later)

- Kivora's demo card in One Click Demo Import now carries a **Preview Demo**
  button. The manifest had no `preview_url`, so the theme passed an empty one and
  the plugin drew the card without the link -- there was no way to see what the
  demo looked like except by importing it. It points at the demo's own page here.

## 2026-08-19

- Added **Kivora** (`kivora/`) with its first demo, **Creative Studio**: a design
  studio site of 7 pages, 6 posts with comments, 15 CC0 images, two menus, five
  widget areas, a logo, a site icon and the theme's own settings. Every page is
  assembled from Kivora's registered block patterns, so the demo doubles as a
  check that the patterns still compose. Imported end to end on a clean install
  before publishing.
- `tools/export-demo.php` now exports options named after the theme alongside the
  theme mods. Kivora keeps all of its settings in one prefixed option, so a demo
  exported with mods alone arrived on the buyer's site with the theme back at its
  defaults -- sticky header off, sidebar on the wrong side, live search disabled.

## 2026-08-18 (later)

- **Car Repair Services** 1.3. The footer's "Quick links" widget is gone: its five
  links duplicated the footer bottom menu added in 1.2, three of them exactly, and
  removing it leaves the footer as logo + Get in touch + Our Services + Opening
  hours -- four columns, one row.
- Five posts and four pages carried `post_author = 0`, so the blog byline rendered
  as an icon with nothing beside it. All nine now name the site's user, which is
  what the other four posts already did.

## 2026-08-18

- **Car Repair Services** 1.2. Every image in the demo now carries alt text
  written from the photograph, except the three testimonial avatars, which sit
  beside the person's name in text and are correctly left empty. A **Footer
  Menu** was added and assigned to the theme's `footer-menu` location, which the
  theme registers but no demo had ever filled.
- `tools/export-demo.php` drops anything in the trash. WordPress's exporter
  takes every status but auto-draft, so the default Privacy Policy page someone
  threw away was travelling to buyers and landing in their trash.

## 2026-08-15

- Added **PortoLite** (`portolite/`) with its first demo, **Car Repair Services**:
  7 pages, 8 posts, 6 team members, 32 images, widgets and Customizer settings.
