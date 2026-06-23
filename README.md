# GrassHouston — WordPress Importers

Migration tooling that moves content from the original **grasshouston.com** site
(originally built on Replit — a Vite/React SPA) into the **WordPress + Elementor +
ACF** rebuild hosted on Hostinger (`coral-hare-833726.hostingersite.com`).

Each plugin is a one-off, safe-to-re-run importer (matches posts by slug and
updates in place — never creates duplicates). Tools live under **Tools →** in wp-admin.

## Plugins

| Folder | Purpose | Target post type |
|---|---|---|
| `grass-service-areas-importer/` | Per-city Service Areas pages (23 cities) | `services-areas` |
| `grass-services-importer/` | Per-service pages (7 services) | `service` |
| `grass-blog-importer/` | Blog/resource articles | `post` |

Each folder also has a matching `*.zip` ready to upload via Plugins → Add New → Upload Plugin.

## How the source content was obtained

grasshouston.com is a **client-side React SPA** — every route returns the same
homepage HTML shell, so fetching a URL does **not** yield per-page content. The real
content lives in the JS bundle (`/assets/index-<hash>.js`) as a minified data object.
The approach: download the bundle (`original-bundle.js`) and parse the data arrays
(services array `Ir`, plus `siteData` for cities), rather than scraping routes.

Helper scripts used to generate the datasets:
- `gen-cities.ps1`, `original-cities-data.json` → service-areas dataset
- `extract-articles.ps1`, `gen-articles.ps1`, `articles-data.json`, `image-map.json` → blog dataset
- (services dataset was generated from the bundle via Node parsing; see below)

## Services importer — field mapping (`service` CPT)

ACF group "Services Details": `heading` (text), `short_description` (wysiwyg),
`what_drives_the_cost` (wysiwyg), `list_content_heading` (text), `list_content` (wysiwyg).

| Destination | Source (bundle) |
|---|---|
| post_title | `title` |
| Featured Image (hero) | `image` (resolved from bundle asset map → CloudFront URL) |
| post_content (body) | `longIntro` paragraphs |
| ACF `heading` | `hero` |
| ACF `short_description` | `intro` |
| ACF `what_drives_the_cost` | `pricingNotes` |
| ACF `list_content` | shared brand credentials list (same on every page) |
| meta `_sa_whats_included_data` | `bullets` (flat strings) |
| meta `_sa_how_it_works_data` | `process`, else shared generic 4 steps |
| meta `_sa_key_features_data` | `whoItsFor` (heading/content) |
| meta `_sa_services_faq_data` | `faqs` (question/answer) |
| meta `_sa_linked_area_ids` / `_sa_related_service_ids` | left manual (post-ID pickers) |
| ACF `list_content_heading` | left empty (empty on reference page) |

The custom repeater metaboxes (`_sa_*`) are registered in the theme at
`wp-content/themes/astra-child/functions.php`.

### Service coverage notes
- 7 canonical services: `sod-installation`, `hydroseeding`, `hydromulching`,
  `erosion-control-sod-hydroseed`, `grass-repair`, `rye-grass-overseeding`, `lawn-replacement`.
- `sod-installation` was built by hand and is **not** in the importer (left untouched).
- In the source, only **sod-installation** and **hydroseeding** are fully written;
  the other 5 were stubs (heading + intro + bullets only). Original Houston-specific
  copy was written for those 5 (body, pricing, Key Features, FAQ) — see the plugin's
  `services-data.php`. This is generated marketing copy; review before relying on it.
- The Services dropdown menu auto-lists every `service` post, so the plugin includes a
  cleanup tool that trashes any non-canonical service post (reversible).

## Safety
- Re-running updates by slug; never duplicates posts.
- Images sideload once, deduped by source URL (`_grass_src_url` attachment meta).
- Importers overwrite the fields they manage on their target posts each run.
  They never touch `sod-installation`, the manual `_sa_linked_area_ids` /
  `_sa_related_service_ids` pickers, or `list_content_heading`.
