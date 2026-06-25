=== Grass Services Importer ===
Version 1.9.0

What it does
------------
Imports per-service content from the original grasshouston.com into the "service"
custom post type, mapped to the "Services Details" ACF group plus the theme's
custom repeater metaboxes. It matches each post by slug and updates it in place
(safe to re-run, no duplicates), creating any that are missing.

SCOPED RUN (this version)
-------------------------
This build is gated to ONLY create/update these 4 services:
  acreage-estate-turf-installation
  sports-turf-installation
  new-construction-turf
  hoa-common-area-turf
Every other service in the dataset (hydroseeding, hydromulching,
erosion-control-sod-hydroseed, grass-repair, rye-grass-overseeding,
lawn-replacement) is SKIPPED and left completely untouched. The gate lives in
grass_svc_active_slugs() in grass-services-importer.php — empty that array to
process every service again.

Content source
--------------
acreage-estate-turf-installation is a fully built-out page in the source bundle,
so its body, pricing, Key Features, How It Works, and FAQ are imported verbatim
from the source. sports-turf-installation, new-construction-turf, and
hoa-common-area-turf were stubs in the source (hero + intro + 4 bullets only), so
their body, pricing, Key Features, and FAQ are hand-written here to match the
other service pages. How It Works on those three falls back to the shared generic
steps.

What gets written per service
-----------------------------
  post_title              <- source "title"
  post_content            <- "longIntro" body paragraphs
  ACF heading             <- source "hero"
  ACF short_description   <- source "intro"
  ACF what_drives_the_cost<- "pricingNotes"
  ACF list_content        <- shared brand credentials list (identical on every
                             page, copied verbatim from the finished
                             sod-installation page)
  Featured Image          <- source "hero" image (sideloaded once, deduped by URL)

  meta _sa_whats_included_data  <- "bullets" (flat list)              [What's Included]
  meta _sa_how_it_works_data    <- "process", else generic steps      [How It Works]
  meta _sa_key_features_data    <- "whoItsFor" (heading/content)       [Key Features]
  meta _sa_services_faq_data    <- "faqs" (question/answer)            [FAQ]

NOT touched:
  ACF list_content_heading      (empty on the reference page)
  meta _sa_linked_area_ids      (Linked Service Areas — set by hand)
  meta _sa_related_service_ids  (Related Services — set by hand)

Install & run
-------------
1. Plugins -> Add New -> Upload Plugin -> the zip -> Activate (ACF must be active).
2. Tools -> Services Import. The page shows a "Scoped run" notice listing the 3
   services that will be affected.
3. Click "Preview (dry run)" to see the create/update plan (only the 3 appear).
4. Click "Run Import". Re-running is safe (updates by slug, never duplicates).
The created pages auto-use the shared "service" Elementor template; add them to
your menus/structure manually.

Clean up extra services (Tools -> Services Import)
--------------------------------------------------
The site's Services dropdown auto-lists EVERY "service" post, so extra/orphan
posts appear there. The canonical set is now 11:
  sod-installation, hydroseeding, hydromulching, erosion-control-sod-hydroseed,
  grass-repair, rye-grass-overseeding, lawn-replacement,
  acreage-estate-turf-installation, sports-turf-installation, new-construction-turf,
  hoa-common-area-turf
Use "Preview cleanup (dry run)" to see which posts are non-canonical, then
"Trash extra services" to move them to Trash (reversible — restore from
Posts -> Trash).

Notes
-----
- Delete the plugin once content is verified in place.
- This plugin never modifies list_content_heading.
