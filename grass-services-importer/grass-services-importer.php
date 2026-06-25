<?php
/**
 * Plugin Name:       Grass Services Importer
 * Plugin URI:        https://grasshouston.com
 * Description:        Imports per-service content (from the original grasshouston.com) into the "service" CPT — the "Services Details" ACF fields plus post title and post content. Matches by slug and updates in place (no duplicates). Tools -> Services Import.
 * Version:           1.9.0
 * Author:            GrassHouston
 * License:           GPL-2.0+
 * Requires at least: 5.8
 * Requires PHP:      7.2
 *
 * Safe to re-run: matches posts by slug and UPDATES them (no duplicates).
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'GRASS_SVC_POST_TYPE', 'service' );
define( 'GRASS_SVC_CAP', 'manage_options' );

require_once plugin_dir_path( __FILE__ ) . 'services-data.php';

/**
 * Which fields the importer writes per service.
 *   post_title            <- title
 *   post_content          <- content   (source longIntro paragraphs)
 *   ACF heading           <- heading   (source hero)
 *   ACF short_description <- short_description (source intro)
 *   ACF what_drives_the_cost <- what_drives_the_cost (source pricingNotes)
 *   ACF list_content      <- shared brand credentials list (identical on every
 *                            service page, mirroring the finished sod-installation page)
 *
 * Deliberately NOT touched: ACF list_content_heading (empty on the reference page).
 */

/**
 * The brand-wide credentials list shown in the ACF "list_content" field on every
 * service page. Copied verbatim from the finished sod-installation page so the
 * imported pages match it exactly.
 */
function grass_svc_list_content() {
	return "<ul>\n"
		. '<li class="flex items-start gap-2">Fully licensed and insured across Greater Houston</li>' . "\n"
		. '<li class="flex items-start gap-2">2,400+ properties installed since 2014</li>' . "\n"
		. '<li class="flex items-start gap-2">Residential, commercial, and acreage specialists</li>' . "\n"
		. '<li class="flex items-start gap-2">Free estimates and consultative quotes — many quoted right over the phone</li>' . "\n"
		. '<li class="flex items-start gap-2">In-house sod and hydroseeding crews — not subcontracted</li>' . "\n"
		. '<li class="flex items-start gap-2">Coordinated with your builder, irrigation, and HOA</li>' . "\n"
		. "</ul>";
}

/**
 * Shared generic "How it works" steps. The source site shows these on any service
 * page that has no process of its own (verified in the site bundle: it uses each
 * service's own process if present, else this fallback). Stored as heading/content
 * rows to match the theme's _sa_how_it_works_data repeater.
 */
function grass_svc_generic_how_it_works() {
	return array(
		array( 'heading' => 'On-site assessment', 'content' => 'Free walk-through. We map your property, evaluate soil and drainage, and listen to your goals.' ),
		array( 'heading' => 'Written sod & lawn plan', 'content' => 'A clear, line-item scope of work with grass selection, prep, install method, and post-care plan.' ),
		array( 'heading' => 'Installation', 'content' => 'Our in-house crews execute on schedule — no subcontracting, no surprises.' ),
		array( 'heading' => '30-day check-in', 'content' => 'We follow up after install to verify root establishment and answer questions.' ),
	);
}

/**
 * Download a remote image into the Media Library once and return its attachment ID.
 * Dedupes by source URL stored in attachment meta '_grass_src_url', so re-running
 * never re-downloads or creates duplicate media. Returns attachment ID or 0.
 */
function grass_svc_sideload( $url ) {
	if ( empty( $url ) ) { return 0; }

	$existing = get_posts( array(
		'post_type'   => 'attachment',
		'post_status' => 'inherit',
		'numberposts' => 1,
		'fields'      => 'ids',
		'meta_key'    => '_grass_src_url',
		'meta_value'  => $url,
	) );
	if ( ! empty( $existing ) ) { return (int) $existing[0]; }

	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$tmp = download_url( $url, 60 );
	if ( is_wp_error( $tmp ) ) { return 0; }

	$name = preg_replace( '/[^a-z0-9._-]/i', '', basename( wp_parse_url( $url, PHP_URL_PATH ) ) );
	if ( ! $name ) { $name = 'grass-service.jpg'; }

	$id = media_handle_sideload( array( 'name' => $name, 'tmp_name' => $tmp ), 0 );
	if ( is_wp_error( $id ) ) { @unlink( $tmp ); return 0; }

	update_post_meta( $id, '_grass_src_url', $url );
	return (int) $id;
}

/* -------------------------------------------------------------------------
 *  Admin page
 * ---------------------------------------------------------------------- */
add_action( 'admin_menu', function () {
	add_management_page( 'Services Import', 'Services Import', GRASS_SVC_CAP, 'grass-svc', 'grass_svc_render_page' );
} );

function grass_svc_render_page() {
	if ( ! current_user_can( GRASS_SVC_CAP ) ) { wp_die( 'No permission.' ); }

	$results     = null;
	$dry_run     = true;
	$inspect     = null;
	$cleanup     = null;
	$cleanup_dry = true;
	$acf_ready   = function_exists( 'update_field' );

	if ( isset( $_POST['grass_svc_action'] ) ) {
		check_admin_referer( 'grass_svc_run' );
		$act = $_POST['grass_svc_action'];
		if ( 'inspect' === $act ) {
			$inspect = grass_svc_inspect();
		} elseif ( 'codescan' === $act ) {
			$inspect = grass_svc_codescan();
		} elseif ( 'mapdump' === $act ) {
			$inspect = grass_svc_mapdump();
		} elseif ( 'cleanup' === $act || 'cleanup_preview' === $act ) {
			$cleanup_dry = ( 'cleanup' !== $act );
			$cleanup     = grass_svc_cleanup( $cleanup_dry );
		} else {
			$dry_run = ( 'import' !== $act );
			$results = grass_svc_run_import( $dry_run );
		}
	}

	$cpt_ok = post_type_exists( GRASS_SVC_POST_TYPE );
	?>
	<div class="wrap">
		<h1>Grass Services Importer</h1>
		<p>Imports the <strong>per-service content</strong> from the original grasshouston.com into the <code><?php echo esc_html( GRASS_SVC_POST_TYPE ); ?></code> post type. Matches by slug and updates in place (no duplicates), so it both <em>corrects</em> existing pages and <em>creates</em> the missing ones.</p>

		<?php $active = grass_svc_active_slugs(); if ( ! empty( $active ) ) : ?>
			<div class="notice notice-info inline"><p><strong>Scoped run:</strong> only these <strong><?php echo count( $active ); ?></strong> services will be created/updated — <code><?php echo implode( '</code>, <code>', array_map( 'esc_html', $active ) ); ?></code>. Every other service (including the previously-imported ones) is left untouched. To process all services, empty <code>grass_svc_active_slugs()</code>.</p></div>
		<?php endif; ?>

		<?php if ( ! $acf_ready ) : ?><div class="notice notice-error"><p><strong>ACF not active.</strong> Activate Advanced Custom Fields first.</p></div><?php endif; ?>
		<?php if ( ! $cpt_ok ) : ?><div class="notice notice-warning"><p>Post type <code><?php echo esc_html( GRASS_SVC_POST_TYPE ); ?></code> is not registered — activate your Services CPT first.</p></div><?php endif; ?>

		<form method="post">
			<?php wp_nonce_field( 'grass_svc_run' ); ?>
			<p>
				<button type="submit" name="grass_svc_action" value="preview" class="button">Preview (dry run — no changes)</button>
				<button type="submit" name="grass_svc_action" value="import" class="button button-primary" <?php disabled( ! $acf_ready ); ?> onclick="return confirm('Import service content now? Existing pages matched by slug will be updated.');">Run Import</button>
				<button type="submit" name="grass_svc_action" value="inspect" class="button">Inspect (dump existing service values + full meta)</button>
				<button type="submit" name="grass_svc_action" value="codescan" class="button">Find metabox code (scan theme/plugins)</button>
				<button type="submit" name="grass_svc_action" value="mapdump" class="button">Dump repeater data (for mapping)</button>
			</p>
		</form>

		<hr>
		<h2>Clean up extra services</h2>
		<p>Your Services menu auto-lists <em>every</em> <code>service</code> post. Only these <strong>11</strong> are canonical:
			<code>sod-installation</code>, <code>hydroseeding</code>, <code>hydromulching</code>, <code>erosion-control-sod-hydroseed</code>, <code>grass-repair</code>, <code>rye-grass-overseeding</code>, <code>lawn-replacement</code>, <code>acreage-estate-turf-installation</code>, <code>sports-turf-installation</code>, <code>new-construction-turf</code>, <code>hoa-common-area-turf</code>.
			Any other <code>service</code> post is sent to <strong>Trash</strong> (reversible — restore from Posts &rarr; Trash). Preview first.</p>
		<form method="post">
			<?php wp_nonce_field( 'grass_svc_run' ); ?>
			<p>
				<button type="submit" name="grass_svc_action" value="cleanup_preview" class="button">Preview cleanup (dry run)</button>
				<button type="submit" name="grass_svc_action" value="cleanup" class="button" onclick="return confirm('Move every non-canonical service post to Trash now? This is reversible from Posts → Trash.');">Trash extra services</button>
			</p>
		</form>

		<?php if ( is_array( $cleanup ) ) : ?>
			<h3><?php echo $cleanup_dry ? 'Cleanup preview (nothing trashed)' : 'Cleanup complete'; ?></h3>
			<?php if ( empty( $cleanup ) ) : ?>
				<p><strong>Nothing to remove</strong> — only the canonical 7 services exist.</p>
			<?php else : ?>
				<table class="widefat striped" style="max-width:700px">
					<thead><tr><th>Title</th><th>Slug</th><th>Action</th><th>Post</th></tr></thead>
					<tbody>
					<?php foreach ( $cleanup as $c ) : ?>
						<tr>
							<td><?php echo esc_html( $c['title'] ); ?></td>
							<td><code><?php echo esc_html( $c['slug'] ); ?></code></td>
							<td><?php echo esc_html( $c['action'] ); ?></td>
							<td>#<?php echo (int) $c['id']; ?></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		<?php endif; ?>

		<hr>
		<h2>What gets written per service</h2>
		<p><strong>ACF / post:</strong> <code>post_title</code>, <code>post_content</code> (body), <code>heading</code>, <code>short_description</code>, <code>what_drives_the_cost</code>, <code>list_content</code> (shared credentials list), plus the <strong>Featured Image</strong> (the hero image; sideloaded once, deduped by source URL).<br>
		<strong>Theme repeater metaboxes:</strong> <code>_sa_whats_included_data</code> (What's Included ← bullets), <code>_sa_how_it_works_data</code> (How It Works ← source steps, or shared generic steps for stubs), <code>_sa_key_features_data</code> (Key Features ← only where the source has them), <code>_sa_services_faq_data</code> (FAQ ← only where the source has them).<br>
		<strong>Not touched:</strong> <code>list_content_heading</code>, <code>_sa_linked_area_ids</code>, <code>_sa_related_service_ids</code> (set those by hand). The five stub services now also carry hand-written body, pricing, Key Features, and FAQ copy (the source had none); only How It Works stays on the shared generic steps for stubs.</p>

		<?php if ( null !== $inspect ) : ?>
			<h2>Inspect / code-scan output</h2>
			<p>Copy <strong>everything</strong> below and send it back so the importer can be mapped to the repeater metaboxes (What's Included, How It Works, FAQ, etc.):</p>
			<textarea readonly onclick="this.select()" style="width:100%;height:480px;font-family:monospace;font-size:12px;"><?php echo esc_textarea( $inspect ); ?></textarea>
		<?php endif; ?>

		<?php if ( is_array( $results ) ) : ?>
			<hr>
			<h2><?php echo $dry_run ? 'Preview (nothing saved)' : 'Import complete'; ?></h2>
			<table class="widefat striped" style="max-width:900px">
				<thead><tr><th>Service</th><th>Slug</th><th>Action</th><th>Wrote</th><th>Post</th></tr></thead>
				<tbody>
				<?php foreach ( $results as $r ) : ?>
					<tr>
						<td><?php echo esc_html( $r['title'] ); ?></td>
						<td><code><?php echo esc_html( $r['slug'] ); ?></code></td>
						<td><?php echo esc_html( $r['action'] ); ?></td>
						<td><?php echo esc_html( $r['wrote'] ); ?></td>
						<td><?php echo ( $r['id'] && ! $dry_run ) ? '<a href="' . esc_url( get_edit_post_link( $r['id'] ) ) . '">#' . (int) $r['id'] . '</a>' : esc_html( $r['id'] ? $r['id'] : '—' ); ?></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
	</div>
	<?php
}

/* -------------------------------------------------------------------------
 *  Import
 * ---------------------------------------------------------------------- */
/**
 * Slugs the importer is allowed to create/update in this run. Everything else in
 * services-data.php is skipped, so previously-imported pages are never touched.
 * Return an empty array() to lift the gate and process every service.
 */
function grass_svc_active_slugs() {
	return array(
		'acreage-estate-turf-installation',
		'sports-turf-installation',
		'new-construction-turf',
		'hoa-common-area-turf',
	);
}

function grass_svc_run_import( $dry_run ) {
	$services = grass_svc_services();
	$active   = grass_svc_active_slugs();
	$results  = array();

	foreach ( $services as $s ) {
		// Gate: only touch the active slugs (if the gate list is non-empty).
		if ( ! empty( $active ) && ! in_array( $s['slug'], $active, true ) ) { continue; }

		$existing = get_page_by_path( $s['slug'], OBJECT, GRASS_SVC_POST_TYPE );
		$action   = $existing ? 'update' : 'create';
		$post_id  = $existing ? (int) $existing->ID : 0;

		// Summarise which fields actually carry content for this service.
		$wrote = array( 'title', 'heading', 'short_description', 'list_content' );
		if ( '' !== $s['content'] )              { $wrote[] = 'content'; }
		if ( '' !== $s['what_drives_the_cost'] ) { $wrote[] = 'what_drives_the_cost'; }
		if ( ! empty( $s['whats_included'] ) )   { $wrote[] = 'whats_included'; }
		$wrote[] = ! empty( $s['how_it_works'] ) ? 'how_it_works' : 'how_it_works(generic)';
		if ( ! empty( $s['key_features'] ) )     { $wrote[] = 'key_features'; }
		if ( ! empty( $s['faqs'] ) )             { $wrote[] = 'faqs'; }
		if ( ! empty( $s['image'] ) )            { $wrote[] = 'featured_image'; }
		$wrote_str = implode( ', ', $wrote );

		if ( $dry_run ) {
			$results[] = array( 'title' => $s['title'], 'slug' => $s['slug'], 'action' => 'would ' . $action, 'wrote' => $wrote_str, 'id' => $post_id );
			continue;
		}

		$postarr = array(
			'post_type'    => GRASS_SVC_POST_TYPE,
			'post_status'  => 'publish',
			'post_title'   => $s['title'],
			'post_name'    => $s['slug'],
			'post_content' => $s['content'],
		);
		if ( $existing ) { $postarr['ID'] = $existing->ID; $post_id = wp_update_post( $postarr, true ); }
		else { $post_id = wp_insert_post( $postarr, true ); }

		if ( is_wp_error( $post_id ) || ! $post_id ) {
			$results[] = array( 'title' => $s['title'], 'slug' => $s['slug'], 'action' => 'ERROR', 'wrote' => '', 'id' => 0 );
			continue;
		}

		// ACF fields. (list_content_heading intentionally left untouched — empty on the reference page.)
		update_field( 'heading', $s['heading'], $post_id );
		update_field( 'short_description', $s['short_description'], $post_id );
		update_field( 'what_drives_the_cost', $s['what_drives_the_cost'], $post_id );
		update_field( 'list_content', grass_svc_list_content(), $post_id );

		// Custom theme repeater metaboxes (stored as native serialized arrays).
		// wp_slash() cancels the wp_unslash() inside update_metadata so values store verbatim.
		update_post_meta( $post_id, '_sa_whats_included_data', wp_slash( $s['whats_included'] ) );
		$how = ! empty( $s['how_it_works'] ) ? $s['how_it_works'] : grass_svc_generic_how_it_works();
		update_post_meta( $post_id, '_sa_how_it_works_data', wp_slash( $how ) );
		// Only write key_features / faqs when the source actually has them, so we
		// never clobber anything added by hand on the stub pages.
		if ( ! empty( $s['key_features'] ) ) { update_post_meta( $post_id, '_sa_key_features_data', wp_slash( $s['key_features'] ) ); }
		if ( ! empty( $s['faqs'] ) )         { update_post_meta( $post_id, '_sa_services_faq_data', wp_slash( $s['faqs'] ) ); }

		// Featured image (used by the hero). Sideloaded once, deduped by source URL.
		if ( ! empty( $s['image'] ) ) {
			$att_id = grass_svc_sideload( $s['image'] );
			if ( $att_id ) { set_post_thumbnail( $post_id, $att_id ); }
		}

		$results[] = array( 'title' => $s['title'], 'slug' => $s['slug'], 'action' => $action . 'd', 'wrote' => $wrote_str, 'id' => (int) $post_id );
	}
	return $results;
}

/* -------------------------------------------------------------------------
 *  Cleanup — trash any "service" post that is not one of the canonical 7.
 *  The Services dropdown auto-lists every service post, so extra/orphan posts
 *  show up there; trashing them (reversible) leaves exactly the 7.
 * ---------------------------------------------------------------------- */
function grass_svc_canonical_slugs() {
	return array(
		'sod-installation',
		'hydroseeding',
		'hydromulching',
		'erosion-control-sod-hydroseed',
		'grass-repair',
		'rye-grass-overseeding',
		'lawn-replacement',
		'acreage-estate-turf-installation',
		'sports-turf-installation',
		'new-construction-turf',
		'hoa-common-area-turf',
	);
}

function grass_svc_cleanup( $dry_run ) {
	$canon = grass_svc_canonical_slugs();
	$posts = get_posts( array(
		'post_type'      => GRASS_SVC_POST_TYPE,
		'post_status'    => array( 'publish', 'draft', 'pending', 'private', 'future' ),
		'posts_per_page' => -1,
		'orderby'        => 'title',
		'order'          => 'ASC',
	) );

	$out = array();
	foreach ( $posts as $p ) {
		if ( in_array( $p->post_name, $canon, true ) ) { continue; }
		if ( ! $dry_run ) { wp_trash_post( $p->ID ); }
		$out[] = array(
			'title'  => $p->post_title,
			'slug'   => $p->post_name,
			'id'     => (int) $p->ID,
			'action' => $dry_run ? 'would trash' : 'trashed',
		);
	}
	return $out;
}

/* -------------------------------------------------------------------------
 *  Inspector — dumps the current values of every service post so we can see
 *  what list_content_heading / list_content already hold (e.g. on the
 *  already-finished sod-installation page) before mapping them.
 * ---------------------------------------------------------------------- */
function grass_svc_inspect() {
	$out = 'Post type: ' . GRASS_SVC_POST_TYPE . "\n\n";

	if ( function_exists( 'acf_get_field_groups' ) ) {
		$out .= "############ ACF GROUPS on this post type ############\n";
		foreach ( acf_get_field_groups( array( 'post_type' => GRASS_SVC_POST_TYPE ) ) as $g ) {
			$out .= "=== {$g['title']} ({$g['key']}) ===\n";
			foreach ( acf_get_fields( $g['key'] ) as $f ) {
				$out .= "  - {$f['name']}  [{$f['type']}]  \"{$f['label']}\"\n";
			}
		}
		$out .= "\n";
	}

	$posts = get_posts( array(
		'post_type'      => GRASS_SVC_POST_TYPE,
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'orderby'        => 'title',
		'order'          => 'ASC',
	) );

	$out .= "############ EXISTING " . GRASS_SVC_POST_TYPE . " POSTS (" . count( $posts ) . ") ############\n";
	$fields = array( 'heading', 'short_description', 'what_drives_the_cost', 'list_content_heading', 'list_content' );
	foreach ( $posts as $p ) {
		$out .= "\n==================================================\n";
		$out .= "TITLE: {$p->post_title}\nSLUG:  {$p->post_name}  (#{$p->ID})\n";
		$out .= "\n--- post_content ---\n" . mb_substr( (string) $p->post_content, 0, 1200 ) . "\n";
		foreach ( $fields as $fn ) {
			$val = function_exists( 'get_field' ) ? get_field( $fn, $p->ID ) : get_post_meta( $p->ID, $fn, true );
			if ( is_array( $val ) ) { $val = wp_json_encode( $val ); }
			$out .= "\n--- {$fn} ---\n" . mb_substr( (string) $val, 0, 1500 ) . "\n";
		}
	}

	// FULL post-meta dump of the already-built reference pages. This reveals the
	// meta keys + array structure behind the custom repeater metaboxes
	// (What's Included, Key Features, How It Works, FAQ) so the importer can fill them.
	$out .= "\n\n############ FULL POST META — reference pages (copy ALL of this) ############\n";
	foreach ( array( 'sod-installation', 'hydroseeding' ) as $ref ) {
		$rp = get_page_by_path( $ref, OBJECT, GRASS_SVC_POST_TYPE );
		if ( ! $rp ) { $out .= "\n(no post found for '{$ref}')\n"; continue; }
		$out .= "\n================ {$rp->post_title} (#{$rp->ID}) ================\n";
		foreach ( get_post_meta( $rp->ID ) as $k => $v ) {
			$raw = is_array( $v ) ? reset( $v ) : $v;
			$val = maybe_unserialize( $raw );
			$out .= "\n--- {$k}  (type: " . gettype( $val ) . ") ---\n";
			$out .= mb_substr( var_export( $val, true ), 0, 3000 ) . "\n";
		}
	}
	return $out;
}

/* -------------------------------------------------------------------------
 *  Map dump — compact dump of ONLY the custom service-repeater meta keys on the
 *  already-built reference pages, so their exact stored array shape is visible.
 * ---------------------------------------------------------------------- */
function grass_svc_mapdump() {
	$keys = array(
		'_sa_key_features_data',
		'_sa_whats_included_data',
		'_sa_how_it_works_data',
		'_sa_services_faq_data',
		'_sa_linked_area_ids',
		'_sa_related_service_ids',
	);
	$out = "Exact stored structure of the service repeater meta on the reference pages.\nCopy ALL of this.\n";
	foreach ( array( 'sod-installation', 'hydroseeding' ) as $ref ) {
		$rp = get_page_by_path( $ref, OBJECT, GRASS_SVC_POST_TYPE );
		$out .= "\n================ {$ref} ================\n";
		if ( ! $rp ) { $out .= "(no post found)\n"; continue; }
		foreach ( $keys as $k ) {
			$val = maybe_unserialize( get_post_meta( $rp->ID, $k, true ) );
			$out .= "\n--- {$k}  (type: " . gettype( $val ) . ") ---\n";
			$out .= var_export( $val, true ) . "\n";
		}
	}
	return $out;
}

/* -------------------------------------------------------------------------
 *  Code scan — locate the theme/plugin PHP that registers the custom service
 *  metaboxes (What's Included, How It Works, FAQ, etc.) so their exact meta
 *  keys, array shape, and save/sanitize logic are visible.
 * ---------------------------------------------------------------------- */
function grass_svc_codescan() {
	// Distinctive strings from the metabox UI ("Add item lines included with this
	// service specifications" / "Add Row Item") plus generic registration hooks.
	$needles = array( 'service specifications', 'Add Row Item', "What's Included", 'Whats Included', 'How It Works', 'How it works' );

	$roots = array();
	if ( defined( 'WP_PLUGIN_DIR' ) )   { $roots[] = WP_PLUGIN_DIR; }
	if ( defined( 'WPMU_PLUGIN_DIR' ) ) { $roots[] = WPMU_PLUGIN_DIR; }
	$roots[] = get_stylesheet_directory();
	$roots[] = get_template_directory();

	$found = array();
	foreach ( array_unique( $roots ) as $root ) {
		if ( ! is_dir( $root ) ) { continue; }
		try {
			$it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $root, FilesystemIterator::SKIP_DOTS ) );
		} catch ( Exception $e ) { continue; }
		foreach ( $it as $file ) {
			if ( 'php' !== strtolower( $file->getExtension() ) ) { continue; }
			if ( $file->getSize() > 1500000 ) { continue; }
			$path = $file->getPathname();
			if ( preg_match( '#[\\\\/](vendor|node_modules)[\\\\/]#', $path ) ) { continue; }
			$content = @file_get_contents( $path );
			if ( ! $content ) { continue; }
			foreach ( $needles as $n ) {
				if ( false !== stripos( $content, $n ) ) { $found[ $path ] = $content; break; }
			}
		}
	}

	if ( ! $found ) {
		return "No theme/plugin PHP file matched any of: " . implode( ', ', $needles ) . "\nShare your theme's functions.php directly and I'll map it.";
	}

	// Pull the meta keys these files read/write (get_post_meta / update_post_meta).
	$keys = array();
	foreach ( $found as $content ) {
		if ( preg_match_all( '/(?:get_post_meta|update_post_meta|add_meta_box)\s*\(\s*[^,]*,\s*[\'"]([^\'"]+)[\'"]/', $content, $m ) ) {
			$keys = array_merge( $keys, $m[1] );
		}
	}
	$keys = array_values( array_unique( $keys ) );

	$out  = 'FILES FOUND (' . count( $found ) . "):\n  " . implode( "\n  ", array_keys( $found ) ) . "\n\n";
	$out .= 'META KEYS / metabox ids referenced (' . count( $keys ) . "):\n  " . implode( "\n  ", $keys ) . "\n\n";

	$budget = 120000;
	foreach ( $found as $path => $content ) {
		$out  .= "================ FILE: {$path} (" . strlen( $content ) . " bytes) ================\n";
		$chunk = substr( $content, 0, min( $budget, 80000 ) );
		$out  .= $chunk . "\n";
		if ( strlen( $content ) > strlen( $chunk ) ) { $out .= "\n...[truncated — share this file directly if cut off]...\n"; }
		$budget -= strlen( $chunk );
		if ( $budget <= 0 ) { $out .= "\n[output budget reached]\n"; break; }
	}
	return $out;
}
