# Building a Kivora demo

Everything needed to rebuild any of the demos from scratch. These scripts made
the exports in `../assets/`; they live here so that rebuilding a demo after a
copy edit does not mean writing them again.

They are development tooling, not part of what a buyer downloads. Nothing here
is fetched by the theme or by One Click Demo Import.

```
build/
  creative-studio/   build-demo.php + content.php   (Demo 1, business)
  fieldnotes/        build.php + content.php        (Demo 2, personal blog)
                     shot.js, preview.js            (screenshots, preview image)
  solstice-energy/   build.php + content.php        (Demo 3, services with prices)
  img/               fetch-openverse.php            (find CC0 candidates)
                     slots-<demo>.php               (what to search for)
                     chosen-<demo>.php              (which candidate, and its alt text)
                     build-assets.php               (crop the chosen ones)
                     draw-solstice-marks.php        (logo, site icon, client wordmarks)
                     cand/<slot>/meta.json          (where each candidate came from)
                     credits-<demo>.json            (what a build script reads for alt text)
```

The image scripts take `--set=<demo>` and default to `fieldnotes`, which is the
demo they were first written for.

## What runs where

Every build script writes to a **local WordPress at
`http://localhost/kivora-demo`** and refuses to run anywhere else. That install
exists only to be rebuilt; every run clears the previous one first — including
the global styles post, so a demo that names a style variation cannot leave the
next one the wrong colour.

```
php creative-studio/build-demo.php
php fieldnotes/build.php
php solstice-energy/build.php
```

The images come from `../assets/<demo-id>/uploads/2026/08/` — the same files the
export publishes, so the repository holds one copy rather than two.

## The whole loop

1. Edit the copy in `<demo>/content.php`.
2. Run the build script. It stops on a warning if a pattern's sample string no
   longer matches, rather than shipping the theme's own marketing copy inside a
   demo.
3. Look at every page at 1440x900 and 390x844 (`fieldnotes/shot.js`), and run
   the theme's checks against the demo site:
   `KIVORA_WP_LOAD=C:/xampp/htdocs/kivora-demo/wp-load.php node tests/visual/a11y-check.js`
4. Export: `wp eval-file tools/export-demo.php <demo-id> C:\github\wp-theme-demo-library`
   from the demo site.
5. By hand: `demos/<id>.json`, the `demo-library.json` entry, a 1200x675 preview
   (`fieldnotes/preview.js`), the `CREDITS.md` rows, the browsable page.
6. `php tests/demo-library-check.php` in the theme, then a real import onto a
   throwaway install. Both are described in the theme's `docs/demo-library.md`.

## Photographs

`img/fetch-openverse.php` searches Openverse for CC0 images, keeps six
candidates per slot, and writes a numbered contact sheet so the choice is made
by looking rather than by reading file names. Several candidates were rejected
for carrying a stock library's watermark despite being listed as CC0, and every
portrait was rejected outright — the CC0 portraits on offer are photographs of
identifiable people, and a demo that lands on other people's sites is the wrong
place to carry a face.

The further a demo gets from landscape and office life, the thinner the CC0
supply: Solstice Energy's technical slots came back with one or two usable
candidates each where the countryside slots came back with six. Where a slot
came back empty the copy was rewritten around the picture that did exist, rather
than the picture being stretched to fit a caption written first.

Logos are drawn rather than found — `img/draw-solstice-marks.php`. Every real
company logo is somebody's trademark, and a demo that lands on thousands of
sites is the last place to borrow one.

Openverse sits behind Cloudflare and answers a burst of anonymous searches with
a 429 followed by several minutes of challenge pages, so the fetcher makes one
search every twenty-five seconds. Wikimedia Commons was tried as an alternative
and abandoned: it has plenty of CC0, but almost all of it is documentary —
roadworks and flat fields — where Openverse reaches the stock libraries.

`img/cand/<slot>/meta.json` records the source, photographer and licence of
every candidate that was downloaded, so a choice can be revisited or an image
re-fetched without searching again.
