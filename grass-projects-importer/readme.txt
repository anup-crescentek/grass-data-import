=== Grass Projects Importer ===
Version 1.1.0

What it does
------------
Creates the 4 "Completed projects" cards that were missing from the WordPress
`project` post type (the original grasshouston.com homepage shows 8; only 4 had
been migrated). Matches by slug and updates in place (safe to re-run).

The 4 projects created
----------------------
  pearland-builder-closeout      — Pearland Production Builder Closeout (Builder)
  conroe-construction-hydroseed  — Conroe Industrial Site Hydroseed (Hydroseeding)
  sh-99-slope-stabilization      — SH-99 Embankment Stabilization (Erosion Control)
  klein-isd-athletic-field       — Klein ISD Athletic Field (Sports Field)

What gets written per card (ACF "Project Details" group)
--------------------------------------------------------
  location          = source card location   (e.g. "Pearland, TX")
  area              = source card size        (e.g. "42 lots")
  services_name     = source card method      (e.g. "St. Augustine sod, ...")
  short_description = source card scope        (e.g. "42-lot final-grade sod ...")
plus the project-category term (created if missing) and the source card image as
the Featured Image. Requires ACF active.

Disabling the single-page view (do this natively)
-------------------------------------------------
These posts are only shown as cards, so disable their single pages with the
native post-type setting rather than code:
  1. Edit the `project` post type and UNCHECK "Publicly Queryable".
  2. Settings -> Permalinks -> Save (flushes rewrite rules).
Single project URLs will then 404; the Elementor card grid is unaffected (loop
grids query by post type, which this setting does not block). This importer does
NOT change that setting.

Install & run
-------------
1. Plugins -> Add New -> Upload Plugin -> the zip -> Activate (ACF must be active).
2. Tools -> Projects Import.
3. Click "Inspect existing project ACF values" and confirm the mapping looks
   right (location / area / services_name / short_description on the existing 4
   cards).
4. "Preview (dry run)" to see the create plan, then "Run Import".
   Re-running is safe (updates by slug, never duplicates).
