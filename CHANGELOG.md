# Changelog

All notable changes to this demo library.

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
