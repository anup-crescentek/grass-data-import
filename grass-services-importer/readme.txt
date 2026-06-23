=== Grass Services Importer ===
Version 1.4.0

What it does
------------
Imports the per-service content from the original grasshouston.com into the
"service" custom post type, mapped to the "Services Details" ACF group. It
matches each post by slug and updates it in place (safe to re-run, no
duplicates), creating any that are missing.

Services imported (6)
---------------------
  hydroseeding, hydromulching, erosion-control-turfing, grass-repair,
  rye-grass-overseeding, lawn-replacement
(sod-installation is already done, so it is not in the dataset.)

What gets written per service
-----------------------------
  post_title              <- source "title"
  post_content            <- source "longIntro" body paragraphs
  ACF heading             <- source "hero"
  ACF short_description   <- source "intro"
  ACF what_drives_the_cost<- source "pricingNotes"
  ACF list_content        <- shared brand credentials list (identical on every
                             page, copied verbatim from the finished
                             sod-installation page)

  meta _sa_whats_included_data  <- source "bullets" (flat list)        [What's Included]
  meta _sa_how_it_works_data    <- source "process", else generic steps [How It Works]
  meta _sa_key_features_data    <- source "whoItsFor" (heading/content)  [Key Features]
  meta _sa_services_faq_data    <- source "faqs" (question/answer)       [FAQ]

NOT touched:
  ACF list_content_heading      (empty on the reference page)
  meta _sa_linked_area_ids      (Linked Service Areas — set by hand)
  meta _sa_related_service_ids  (Related Services — set by hand)

Repeater coverage: only hydroseeding has its own Key Features / How It Works /
FAQ in the source. The other five are stubs, so they get What's Included (their
bullets) + the shared generic How It Works steps; Key Features and FAQ stay empty
(the plugin skips them rather than overwriting anything you add by hand).

Slug matching (important)
-------------------------
Posts are matched by slug. Confirmed against the live site:
  hydroseeding                  -> updates existing post #313
  grass-repair                  -> updates existing post #315
  erosion-control-sod-hydroseed -> updates existing post #314
  hydromulching                 -> CREATED (no existing post)
  rye-grass-overseeding         -> CREATED (no existing post)
  lawn-replacement              -> CREATED (no existing post)
The three created pages auto-use the shared "service" Elementor template; add
them to your menus/structure manually.

Heads-up about source content
-----------------------------
On the source site only HYDROSEEDING is a fully built-out page. The other five
services are stubs — they only have a hero headline, a one-line intro, and 4
bullets. So for those five, post_content and what_drives_the_cost are imported
as EMPTY (there is nothing to import). Their bullets are stored in the dataset
for reference and can be wired into list_content once that mapping is confirmed.

Install & run
-------------
1. Plugins -> Add New -> Upload Plugin -> the zip -> Activate (ACF must be active).
2. Tools -> Services Import.
3. Click "Inspect" to dump the current values of the existing service posts
   (including the finished sod-installation page) so we can confirm what
   list_content_heading / list_content should contain.
4. Click "Preview (dry run)" to see the create/update plan.
5. Click "Run Import". Re-running is safe (updates by slug, never duplicates).

Clean up extra services (Tools -> Services Import)
--------------------------------------------------
The site's Services dropdown auto-lists EVERY "service" post, so extra/orphan
posts appear there. The canonical set is 7:
  sod-installation, hydroseeding, hydromulching, erosion-control-sod-hydroseed,
  grass-repair, rye-grass-overseeding, lawn-replacement
Use "Preview cleanup (dry run)" to see which posts are non-canonical, then
"Trash extra services" to move them to Trash (reversible — restore from
Posts -> Trash). On this site the extras were: Acreage & Estate Sod Installation,
New Construction Sod Installation, Commercial Sod Installation, and Hybrid Sod +
Hydroseed Plans (all empty; not linked from the main top-level navigation).

Notes
-----
- Delete the plugin once content is verified in place.
- This plugin never modifies list_content_heading.
