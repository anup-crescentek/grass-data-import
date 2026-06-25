<?php
/**
 * Plugin Name:       Grass Projects Importer
 * Plugin URI:        https://grasshouston.com
 * Description:        Creates the missing "Completed projects" cards in the project CPT (matching the original grasshouston.com homepage grid) and disables the single-page view for the project post type (cards only). Tools -> Projects Import.
 * Version:           1.0.0
 * Author:            GrassHouston
 * License:           GPL-2.0+
 * Requires at least: 5.8
 * Requires PHP:      7.2
 *
 * Safe to re-run: matches posts by slug and UPDATES them (no duplicates).
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'GRASS_PRJ_PT',  'project' );
define( 'GRASS_PRJ_TAX', 'project-category' );
define( 'GRASS_PRJ_CAP', 'manage_options' );

/* =========================================================================
 *  Disable the single-page view for the `project` CPT (cards only).
 *  - register_post_type_args makes it non-public-queryable (no single/archive
 *    URL, dropped from search + sitemaps) while keeping it editable and
 *    queryable by Elementor's loop grid (which uses WP_Query, not URL routing).
 *  - template_redirect is a belt-and-suspenders 404 in case the CPT is
 *    re-registered elsewhere with publicly_queryable on.
 * ===================================================================== */
add_filter( 'register_post_type_args', function ( $args, $name ) {
	if ( GRASS_PRJ_PT === $name ) {
		$args['publicly_queryable'] = false;
		$args['exclude_from_search'] = true;
		$args['has_archive'] = false;
		$args['rewrite'] = false;
	}
	return $args;
}, 20, 2 );

add_action( 'template_redirect', function () {
	if ( is_singular( GRASS_PRJ_PT ) ) {
		global $wp_query;
		$wp_query->set_404();
		status_header( 404 );
		nocache_headers();
	}
} );

register_activation_hook( __FILE__, function () { flush_rewrite_rules(); } );
register_deactivation_hook( __FILE__, function () { flush_rewrite_rules(); } );

/* =========================================================================
 *  Data — the 4 cards missing from the WordPress project grid.
 *  Fields mirror the source homepage cards: title, category (project-category
 *  term), location, size, method, image (sideloaded as the Featured Image).
 *  "scope" exists in the source but the WP card template does not display it.
 * ===================================================================== */
function grass_prj_projects() {
	return array(
		array(
			'slug'     => 'pearland-builder-closeout',
			'title'    => 'Pearland Production Builder Closeout',
			'category' => 'Builder',
			'location' => 'Pearland, TX',
			'size'     => '42 lots',
			'method'   => 'St. Augustine sod, scheduled to closings',
			'image'    => 'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779298169722_031a5ed1.png',
		),
		array(
			'slug'     => 'conroe-construction-hydroseed',
			'title'    => 'Conroe Industrial Site Hydroseed',
			'category' => 'Hydroseeding',
			'location' => 'Conroe, TX',
			'size'     => '22 acres',
			'method'   => 'TXDOT-spec hydroseed blend',
			'image'    => 'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779298191962_21a2045b.png',
		),
		array(
			'slug'     => 'sh-99-slope-stabilization',
			'title'    => 'SH-99 Embankment Stabilization',
			'category' => 'Erosion Control',
			'location' => 'Grand Parkway corridor',
			'size'     => '8.5 acres',
			'method'   => 'Bonded fiber matrix hydromulch',
			'image'    => 'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779298213884_c4be72b0.png',
		),
		array(
			'slug'     => 'klein-isd-athletic-field',
			'title'    => 'Klein ISD Athletic Field',
			'category' => 'Sports Field',
			'location' => 'Spring, TX',
			'size'     => '1.8 acres',
			'method'   => 'Tifway 419 Bermuda sod',
			'image'    => 'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779298235170_1d848f68.png',
		),
	);
}

/**
 * Known card values of the EXISTING project posts, used to auto-discover which
 * meta keys hold location / size / method (so we never hardcode a guess).
 */
function grass_prj_reference() {
	return array(
		'magnolia-14-acre-estate'               => array( 'location' => 'Magnolia, TX',  'size' => '14 acres',  'method' => 'Hybrid plan — St. Augustine sod + custom hydroseed blend' ),
		'energy-corridor-office-park'           => array( 'location' => 'Houston, TX',   'size' => '6.4 acres', 'method' => 'Hybrid (Bermuda sod + hydroseed)' ),
		'cinco-ranch-hoa-entry-restoration'     => array( 'location' => 'Katy, TX',      'size' => '0.9 acres', 'method' => 'Premium Zoysia sod' ),
		'fulshear-detention-pond-stabilization' => array( 'location' => 'Fulshear, TX',  'size' => '3.2 acres', 'method' => 'Bonded hydromulch + blanket' ),
	);
}

/**
 * Discover the postmeta keys that hold location / size / method by matching the
 * known values above against the existing posts' meta. Returns
 * array('location'=>key, 'size'=>key, 'method'=>key) for whatever it can resolve.
 */
function grass_prj_discover_keys() {
	$ref   = grass_prj_reference();
	$tally = array( 'location' => array(), 'size' => array(), 'method' => array() );

	foreach ( $ref as $slug => $vals ) {
		$p = get_page_by_path( $slug, OBJECT, GRASS_PRJ_PT );
		if ( ! $p ) { continue; }
		foreach ( get_post_meta( $p->ID ) as $k => $arr ) {
			$v = maybe_unserialize( $arr[0] );
			if ( ! is_string( $v ) ) { continue; }
			foreach ( array( 'location', 'size', 'method' ) as $field ) {
				if ( trim( $v ) === trim( $vals[ $field ] ) ) {
					$tally[ $field ][ $k ] = ( isset( $tally[ $field ][ $k ] ) ? $tally[ $field ][ $k ] : 0 ) + 1;
				}
			}
		}
	}

	$keys = array();
	foreach ( $tally as $field => $cands ) {
		if ( ! empty( $cands ) ) { arsort( $cands ); $keys[ $field ] = key( $cands ); }
	}
	return $keys;
}

/* -------------------------------------------------------------------------
 *  Admin page
 * ---------------------------------------------------------------------- */
add_action( 'admin_menu', function () {
	add_management_page( 'Projects Import', 'Projects Import', GRASS_PRJ_CAP, 'grass-prj', 'grass_prj_render_page' );
} );

function grass_prj_render_page() {
	if ( ! current_user_can( GRASS_PRJ_CAP ) ) { wp_die( 'No permission.' ); }

	$results = null;
	$dry_run = true;
	$inspect = null;

	if ( isset( $_POST['grass_prj_action'] ) ) {
		check_admin_referer( 'grass_prj_run' );
		$act = $_POST['grass_prj_action'];
		if ( 'inspect' === $act ) {
			$inspect = grass_prj_inspect();
		} else {
			$dry_run = ( 'import' !== $act );
			$results = grass_prj_run_import( $dry_run );
		}
	}

	$cpt_ok = post_type_exists( GRASS_PRJ_PT );
	$keys   = grass_prj_discover_keys();
	?>
	<div class="wrap">
		<h1>Grass Projects Importer</h1>
		<p>Creates the <strong>missing "Completed projects" cards</strong> in the <code><?php echo esc_html( GRASS_PRJ_PT ); ?></code> post type, matching the original grasshouston.com homepage grid. Matches by slug and updates in place (no duplicates). The single-page view for this post type is <strong>disabled</strong> (cards only) while this plugin is active.</p>

		<?php if ( ! $cpt_ok ) : ?><div class="notice notice-warning"><p>Post type <code><?php echo esc_html( GRASS_PRJ_PT ); ?></code> is not registered.</p></div><?php endif; ?>

		<div class="notice notice-<?php echo ( isset( $keys['location'], $keys['size'], $keys['method'] ) ? 'success' : 'error' ); ?> inline"><p>
			<strong>Detected card meta keys:</strong>
			location = <code><?php echo esc_html( isset( $keys['location'] ) ? $keys['location'] : 'NOT FOUND' ); ?></code>,
			size = <code><?php echo esc_html( isset( $keys['size'] ) ? $keys['size'] : 'NOT FOUND' ); ?></code>,
			method = <code><?php echo esc_html( isset( $keys['method'] ) ? $keys['method'] : 'NOT FOUND' ); ?></code>.
			<?php if ( ! isset( $keys['location'], $keys['size'], $keys['method'] ) ) : ?>
				<br>One or more keys could not be auto-detected — click <em>Inspect</em> and send the output so the mapping can be corrected before importing.
			<?php endif; ?>
		</p></div>

		<form method="post">
			<?php wp_nonce_field( 'grass_prj_run' ); ?>
			<p>
				<button type="submit" name="grass_prj_action" value="preview" class="button">Preview (dry run — no changes)</button>
				<button type="submit" name="grass_prj_action" value="import" class="button button-primary" onclick="return confirm('Create the 4 missing project cards now?');">Run Import</button>
				<button type="submit" name="grass_prj_action" value="inspect" class="button">Inspect existing project meta</button>
			</p>
		</form>

		<?php if ( null !== $inspect ) : ?>
			<h2>Inspect output</h2>
			<textarea readonly onclick="this.select()" style="width:100%;height:420px;font-family:monospace;font-size:12px;"><?php echo esc_textarea( $inspect ); ?></textarea>
		<?php endif; ?>

		<?php if ( is_array( $results ) ) : ?>
			<hr>
			<h2><?php echo $dry_run ? 'Preview (nothing saved)' : 'Import complete'; ?></h2>
			<?php if ( isset( $results['error'] ) ) : ?>
				<div class="notice notice-error"><p><?php echo esc_html( $results['error'] ); ?></p></div>
			<?php else : ?>
				<table class="widefat striped" style="max-width:900px">
					<thead><tr><th>Project</th><th>Slug</th><th>Category</th><th>Action</th><th>Wrote</th><th>Post</th></tr></thead>
					<tbody>
					<?php foreach ( $results as $r ) : ?>
						<tr>
							<td><?php echo esc_html( $r['title'] ); ?></td>
							<td><code><?php echo esc_html( $r['slug'] ); ?></code></td>
							<td><?php echo esc_html( $r['category'] ); ?></td>
							<td><?php echo esc_html( $r['action'] ); ?></td>
							<td><?php echo esc_html( $r['wrote'] ); ?></td>
							<td><?php echo ( $r['id'] && ! $dry_run ) ? '<a href="' . esc_url( get_edit_post_link( $r['id'] ) ) . '">#' . (int) $r['id'] . '</a>' : esc_html( $r['id'] ? $r['id'] : '—' ); ?></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		<?php endif; ?>
	</div>
	<?php
}

/* -------------------------------------------------------------------------
 *  Import
 * ---------------------------------------------------------------------- */
function grass_prj_run_import( $dry_run ) {
	$keys = grass_prj_discover_keys();

	if ( ! $dry_run ) {
		$missing = array_diff( array( 'location', 'size', 'method' ), array_keys( $keys ) );
		if ( ! empty( $missing ) ) {
			return array( 'error' => 'Could not auto-detect meta key(s) for: ' . implode( ', ', $missing ) . '. Run Inspect and review before importing.' );
		}
	}

	$results = array();
	foreach ( grass_prj_projects() as $pr ) {
		$existing = get_page_by_path( $pr['slug'], OBJECT, GRASS_PRJ_PT );
		$action   = $existing ? 'update' : 'create';
		$post_id  = $existing ? (int) $existing->ID : 0;

		$wrote = array( 'title', 'category' );
		foreach ( array( 'location', 'size', 'method' ) as $f ) { if ( isset( $keys[ $f ] ) ) { $wrote[] = $f; } }
		$wrote[] = 'featured_image';
		$wrote_str = implode( ', ', $wrote );

		if ( $dry_run ) {
			$results[] = array( 'title' => $pr['title'], 'slug' => $pr['slug'], 'category' => $pr['category'], 'action' => 'would ' . $action, 'wrote' => $wrote_str, 'id' => $post_id );
			continue;
		}

		$postarr = array(
			'post_type'   => GRASS_PRJ_PT,
			'post_status' => 'publish',
			'post_title'  => $pr['title'],
			'post_name'   => $pr['slug'],
		);
		if ( $existing ) { $postarr['ID'] = $existing->ID; $post_id = wp_update_post( $postarr, true ); }
		else { $post_id = wp_insert_post( $postarr, true ); }

		if ( is_wp_error( $post_id ) || ! $post_id ) {
			$results[] = array( 'title' => $pr['title'], 'slug' => $pr['slug'], 'category' => $pr['category'], 'action' => 'ERROR', 'wrote' => '', 'id' => 0 );
			continue;
		}

		// project-category term (created if it does not exist yet).
		wp_set_object_terms( $post_id, array( $pr['category'] ), GRASS_PRJ_TAX, false );

		// Card fields into the discovered meta keys.
		if ( isset( $keys['location'] ) ) { update_post_meta( $post_id, $keys['location'], $pr['location'] ); }
		if ( isset( $keys['size'] ) )     { update_post_meta( $post_id, $keys['size'], $pr['size'] ); }
		if ( isset( $keys['method'] ) )   { update_post_meta( $post_id, $keys['method'], $pr['method'] ); }

		// Featured image (the card image), sideloaded once, deduped by source URL.
		$att_id = grass_prj_sideload( $pr['image'] );
		if ( $att_id ) { set_post_thumbnail( $post_id, $att_id ); }

		$results[] = array( 'title' => $pr['title'], 'slug' => $pr['slug'], 'category' => $pr['category'], 'action' => $action . 'd', 'wrote' => $wrote_str, 'id' => (int) $post_id );
	}
	return $results;
}

/* -------------------------------------------------------------------------
 *  Inspector — dumps existing project posts' custom meta + terms so the
 *  location/size/method key mapping can be verified.
 * ---------------------------------------------------------------------- */
function grass_prj_inspect() {
	$keys = grass_prj_discover_keys();
	$out  = "Auto-detected keys: " . wp_json_encode( $keys ) . "\n";
	$out .= "project-category terms: ";
	$terms = get_terms( array( 'taxonomy' => GRASS_PRJ_TAX, 'hide_empty' => false ) );
	$out  .= ( is_array( $terms ) ? implode( ', ', wp_list_pluck( $terms, 'name' ) ) : '(none)' ) . "\n";

	// Ignore obvious theme/system meta so the real card fields stand out.
	$skip = '/^(_edit_|_wp_|_thumbnail_id|ast-|site-|theme-|footer-|header-|stick-|adv-|astra-|_elementor_|_yoast|rank_math|_acf_changed)/';

	foreach ( array_keys( grass_prj_reference() ) as $slug ) {
		$p = get_page_by_path( $slug, OBJECT, GRASS_PRJ_PT );
		$out .= "\n================ {$slug} ================\n";
		if ( ! $p ) { $out .= "(not found)\n"; continue; }
		$tt = wp_get_object_terms( $p->ID, GRASS_PRJ_TAX, array( 'fields' => 'names' ) );
		$out .= "category: " . ( is_array( $tt ) ? implode( ', ', $tt ) : '' ) . "   featured: #" . (int) get_post_thumbnail_id( $p->ID ) . "\n";
		foreach ( get_post_meta( $p->ID ) as $k => $arr ) {
			if ( preg_match( $skip, $k ) ) { continue; }
			$v = maybe_unserialize( $arr[0] );
			$out .= "  {$k} = " . mb_substr( is_scalar( $v ) ? (string) $v : wp_json_encode( $v ), 0, 200 ) . "\n";
		}
	}
	return $out;
}

/**
 * Download a remote image into the Media Library once. Dedupes by source URL
 * stored in attachment meta '_grass_src_url'. Returns attachment ID or 0.
 */
function grass_prj_sideload( $url ) {
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
	if ( ! $name ) { $name = 'grass-project.png'; }

	$id = media_handle_sideload( array( 'name' => $name, 'tmp_name' => $tmp ), 0 );
	if ( is_wp_error( $id ) ) { @unlink( $tmp ); return 0; }

	update_post_meta( $id, '_grass_src_url', $url );
	return (int) $id;
}
