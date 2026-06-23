=== Grass Service Areas Importer ===
Version 1.3.0

What it does
------------
Imports the REAL per-city content from the original grasshouston.com into the
Service Areas (services-areas) pages, mapped EXACTLY like the live Houston page,
for all 23 full cities. It matches by slug and updates in place, so it BOTH:
  - corrects existing pages that currently show Houston content (Katy, Spring,
    Richmond, Fulshear, Tomball, Willis, Magnolia, Friendswood, Houston), AND
  - creates the 14 missing pages (League City, Humble, Kingwood, Missouri City,
    Rosenberg, Pasadena, Clear Lake, Baytown, Pearland, The Woodlands, Conroe,
    Sugar Land, Cypress, Hockley).
(Jersey Village is skipped — the source has no real content for it.)

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
