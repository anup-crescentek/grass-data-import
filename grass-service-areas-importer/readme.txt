=== Grass Service Areas Importer ===
Version 1.7.0

What it does
------------
Imports the REAL per-city content from the original grasshouston.com into the
Service Areas (services-areas) pages, mapped EXACTLY like the live Houston page.
It matches by slug and updates in place, correcting pages that show Houston
content and creating any that are missing. The dataset now covers all 24 cities
(Jersey Village was added in 1.7.0 — see below).

SCOPED RUN (this version)
-------------------------
This build is gated by grass_sai_active_slugs() to ONLY create/update:
  jersey-village-tx
Every other city is SKIPPED and left completely untouched, so the 23 already-
imported pages are not re-written. The admin page shows a "Scoped run" notice.
Empty grass_sai_active_slugs() to process every city again.

Jersey Village (added 1.7.0)
----------------------------
Jersey Village was a stub in the source (basic location data only), so its page
was previously left as a Houston duplicate (wrong body content; grass cards
showed all three varieties). This version adds a full Jersey-Village-specific
entry — mature-canopy shade, White Oak Bayou drainage/flood focus, clay
subgrades, Jersey Meadow Golf Course — built from the source skeleton plus
hand-written copy, with the grass toggles corrected to St. Augustine only
(Bermuda + Zoysia off).

What gets written per city
--------------------------
1. The 15 text/wysiwyg ACF fields (Services Area Details group), mapped exactly
   like Houston:
     short_heading                 = "City"
     heading_text                  = "Sod Installation & Grass Establishment in City, TX"
     short_description_listing_view / short_description = source blurb
     tagline                       = brand tagline (with quotes)
     installation_done             = "NNN+ installed · Since YYYY"
     lawn_establishment_heading    = "Premium sod & lawn establishment for City, TX properties"
     lawn_establishment_sub_heading= templated intro line
     lawn_establishment_content    = source intro paras + "Recommended grass / Soil profile" line
     deep_dive_heading             = "What makes City lawn installation different"
     deep_dive_content             = source long-form narrative
     grass_installation_conetent   = soil note + fixed residential bullet list
     signature_method_content      = fixed hybrid bullet list (City swapped in)
     commercial_sod_installation_content = fixed commercial bullet list (City swapped in)
     why_choose_us_heading_text    = "Why City customers choose GrassHouston"
2. _sa_faq_data  — the native FAQ metabox, real per-city Q&A (pricing is the last FAQ).
3. _sa_show_st_augustine / _sa_show_bermuda / _sa_show_zoysia — set from each
   city's recommended grasses.
4. The 3 image fields — optional (see below).

NOT touched: _sa_selected_grid_posts (the "related areas" grid) — set manually if used.

Install & run
-------------
1. Plugins → Add New → Upload Plugin → the zip → Activate (ACF must be active).
2. Tools → Service Areas Import.
3. (Optional) tick "Also import the original's images" to pull the source images
   into the Media Library and assign the 3 section images.
4. Preview (dry run) to see the create/update plan, then Run Import.
   Re-running is safe (updates by slug, never duplicates).

Inspect button
--------------
"Inspect ACF fields" dumps the live field structure + values for cross-checking.

Notes
-----
- Images are one shared set (~26) used across all cities, not per-city photos.
- Delete the plugin once content is verified in place.
