=== Grass Blog Importer ===
Version 1.0.0

Imports the 13 Resources/Guides articles from the original grasshouston.com as
standard WordPress posts: title, slug, post content (HTML), excerpt, category
(auto-created), and featured/hero image (sideloaded from the original).

Matches each article by source slug, then by exact title — so it UPDATES the
existing posts and CREATES the missing ones (no duplicates). Safe to re-run.

Install
-------
1. Plugins → Add New → Upload Plugin → the zip → Activate.
2. Tools → Blog Import.

Run
---
1. (Optional) "Inspect existing posts" — dumps existing posts, categories, and
   any post metaboxes, so the mapping can be verified.
2. "Preview (dry run)" — shows which articles would be created vs updated.
3. "Run Import" (keep the featured-images box ticked) — imports all 13.

Notes
-----
- Categories created/assigned: Grass Selection, Care & Maintenance, Installation,
  Tools, Pricing & Planning.
- The 5 "simple" source articles had no hero image; fitting images from the
  original set are assigned automatically.
- Updating an existing post keeps its current slug/URL.
- Delete the plugin once content is verified.
