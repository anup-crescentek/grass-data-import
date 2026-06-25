=== Grass Projects Importer ===
Version 1.0.0

What it does
------------
1. Creates the 4 "Completed projects" cards that were missing from the WordPress
   `project` post type (the original grasshouston.com homepage shows 8; only 4
   had been migrated). Matches by slug and updates in place (safe to re-run).
2. Disables the single-page view for the `project` post type — these posts are
   only ever shown as cards in the homepage grid, so single project URLs now
   return 404. The Elementor loop grid that renders the cards is unaffected.

The 4 projects created
----------------------
  pearland-builder-closeout      — Pearland Production Builder Closeout (Builder)
  conroe-construction-hydroseed  — Conroe Industrial Site Hydroseed (Hydroseeding)
  sh-99-slope-stabilization      — SH-99 Embankment Stabilization (Erosion Control)
  klein-isd-athletic-field       — Klein ISD Athletic Field (Sports Field)

Each card carries: title, project-category term (created if missing), location,
size, method, and the source card image as the Featured Image. The source also
has a "scope" field, but the WordPress card template does not display it, so it
is not written.

How the card fields are mapped (no guessing)
--------------------------------------------
location / size / method are stored in custom postmeta whose key names are not
exposed over the REST API. Instead of hardcoding a guess, the plugin AUTO-DETECTS
those keys by reading the 4 existing project posts and matching their known card
values (e.g. "Magnolia, TX", "14 acres"). The detected keys are shown at the top
of the admin page. If detection fails (shows "NOT FOUND"), click Inspect and the
mapping can be corrected before importing.

Install & run
-------------
1. Plugins -> Add New -> Upload Plugin -> the zip -> Activate.
2. Tools -> Projects Import.
3. Confirm the "Detected card meta keys" banner shows real keys (not NOT FOUND).
4. Click "Preview (dry run)" to see the create plan, then "Run Import".
   Re-running is safe (updates by slug, never duplicates).

Single-page disable — keep this active
--------------------------------------
The single-page 404 behaviour only applies while this plugin is active. If you
later delete the importer but still want single project pages disabled, move this
snippet into your child theme's functions.php:

  add_filter('register_post_type_args', function($a,$n){
    if($n==='project'){ $a['publicly_queryable']=false; $a['exclude_from_search']=true;
      $a['has_archive']=false; $a['rewrite']=false; }
    return $a;
  },20,2);
  add_action('template_redirect', function(){
    if(is_singular('project')){ global $wp_query; $wp_query->set_404(); status_header(404); }
  });

Then visit Settings -> Permalinks once (no changes needed) to flush rewrite rules.
