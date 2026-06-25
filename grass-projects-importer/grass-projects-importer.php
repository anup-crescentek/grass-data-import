<?php
/**
 * Plugin Name:       Grass Projects Importer
 * Plugin URI:        https://grasshouston.com
 * Description:        Creates the missing "Completed projects" cards in the project CPT (matching the original grasshouston.com homepage grid), writing the ACF "Project Details" fields. Tools -> Projects Import.
 * Version:           1.1.0
 * Author:            GrassHouston
 * License:           GPL-2.0+
 * Requires at least: 5.8
 * Requires PHP:      7.2
 *
 * Safe to re-run: matches posts by slug and UPDATES them (no duplicates).
 *
 * NOTE: To disable the single-page view for these cards, uncheck "Publicly
 * Queryable" on the project post type, then re-save Permalinks. (Native CPT
 * setting — kept with the post type rather than in this throwaway importer.)
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'GRASS_PRJ_PT',  'project' );
define( 'GRASS_PRJ_TAX', 'project-category' );
define( 'GRASS_PRJ_CAP', 'manage_options' );

/* =========================================================================
 *  Data — the 4 cards missing from the WordPress project grid.
 *  Field names match the ACF "Project Details" group exactly:
 *    location          <- source card "location"
 *    area              <- source card "size"
 *    services_name     <- source card "method"
 *    short_description <- source card "scope"
 *  Plus the project-category term and the source card image (Featured Image).
 * ===================================================================== */
function grass_prj_projects() {
	return array(
		array(
			'slug'              => 'pearland-builder-closeout',
			'title'             => 'Pearland Production Builder Closeout',
			'category'          => 'Builder',
			'location'          => 'Pearland, TX',
			'area'              => '42 lots',
			'services_name'     => 'St. Augustine sod, scheduled to closings',
			'short_description' => '42-lot final-grade sod program',
			'image'             => 'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779298169722_031a5ed1.png',
		),
		array(
			'slug'              => 'conroe-construction-hydroseed',
			'title'             => 'Conroe Industrial Site Hydroseed',
			'category'          => 'Hydroseeding',
			'location'          => 'Conroe, TX',
			'area'              => '22 acres',
			'services_name'     => 'TXDOT-spec hydroseed blend',
			'short_description' => 'Raw-grade vegetative cover + dust control',
			'image'             => 'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779298191962_21a2045b.png',
		),
		array(
			'slug'              => 'sh-99-slope-stabilization',
			'title'             => 'SH-99 Embankment Stabilization',
			'category'          => 'Erosion Control',
			'location'          => 'Grand Parkway corridor',
			'area'              => '8.5 acres',
			'services_name'     => 'Bonded fiber matrix hydromulch',
			'short_description' => 'Slope erosion control + BMP install',
			'image'             => 'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779298213884_c4be72b0.png',
		),
		array(
			'slug'              => 'klein-isd-athletic-field',
			'title'             => 'Klein ISD Athletic Field',
			'category'          => 'Sports Field',
			'location'          => 'Spring, TX',
			'area'              => '1.8 acres',
			'services_name'     => 'Tifway 419 Bermuda sod',
			'short_description' => 'Laser-graded sports field install',
			'image'             => 'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779298235170_1d848f68.png',
		),
	);
}

// The ACF "Project Details" text fields written per card.
function grass_prj_acf_fields() {
	return array( 'location', 'area', 'services_name', 'short_description' );
}

/* -------------------------------------------------------------------------
 *  Admin page
 * ---------------------------------------------------------------------- */
add_action( 'admin_menu', function () {
	add_management_page( 'Projects Import', 'Projects Import', GRASS_PRJ_CAP, 'grass-prj', 'grass_prj_render_page' );
} );

function grass_prj_render_page() {
	if ( ! current_user_can( GRASS_PRJ_CAP ) ) { wp_die( 'No permission.' ); }

	$results   = null;
	$dry_run   = true;
	$inspect   = null;
	$acf_ready = function_exists( 'update_field' );

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
	?>
	<div class="wrap">
		<h1>Grass Projects Importer</h1>
		<p>Creates the <strong>missing "Completed projects" cards</strong> in the <code><?php echo esc_html( GRASS_PRJ_PT ); ?></code> post type, writing the ACF <em>Project Details</em> fields (<code>location</code>, <code>area</code>, <code>services_name</code>, <code>short_description</code>), the <code><?php echo esc_html( GRASS_PRJ_TAX ); ?></code> term, and the card image as the Featured Image. Matches by slug and updates in place (no duplicates).</p>

		<?php if ( ! $acf_ready ) : ?><div class="notice notice-error"><p><strong>ACF not active.</strong> Activate Advanced Custom Fields first.</p></div><?php endif; ?>
		<?php if ( ! $cpt_ok ) : ?><div class="notice notice-warning"><p>Post type <code><?php echo esc_html( GRASS_PRJ_PT ); ?></code> is not registered.</p></div><?php endif; ?>

		<p><em>To hide single project pages, uncheck "Publicly Queryable" on the project post type, then re-save Permalinks. This importer no longer touches that setting.</em></p>

		<form method="post">
			<?php wp_nonce_field( 'grass_prj_run' ); ?>
			<p>
				<button type="submit" name="grass_prj_action" value="preview" class="button">Preview (dry run — no changes)</button>
				<button type="submit" name="grass_prj_action" value="import" class="button button-primary" <?php disabled( ! $acf_ready ); ?> onclick="return confirm('Create the 4 missing project cards now?');">Run Import</button>
				<button type="submit" name="grass_prj_action" value="inspect" class="button">Inspect existing project ACF values</button>
			</p>
		</form>

		<?php if ( null !== $inspect ) : ?>
			<h2>Inspect output (existing cards — confirm the field mapping)</h2>
			<textarea readonly onclick="this.select()" style="width:100%;height:360px;font-family:monospace;font-size:12px;"><?php echo esc_textarea( $inspect ); ?></textarea>
		<?php endif; ?>

		<?php if ( is_array( $results ) ) : ?>
			<hr>
			<h2><?php echo $dry_run ? 'Preview (nothing saved)' : 'Import complete'; ?></h2>
			<table class="widefat striped" style="max-width:980px">
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
	</div>
	<?php
}

/* -------------------------------------------------------------------------
 *  Import
 * ---------------------------------------------------------------------- */
function grass_prj_run_import( $dry_run ) {
	$results = array();
	$wrote_str = implode( ', ', array_merge( array( 'title', 'category' ), grass_prj_acf_fields(), array( 'featured_image' ) ) );

	foreach ( grass_prj_projects() as $pr ) {
		$existing = get_page_by_path( $pr['slug'], OBJECT, GRASS_PRJ_PT );
		$action   = $existing ? 'update' : 'create';
		$post_id  = $existing ? (int) $existing->ID : 0;

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

		// ACF "Project Details" fields, written by field name.
		foreach ( grass_prj_acf_fields() as $f ) { update_field( $f, $pr[ $f ], $post_id ); }

		// Featured image (the card image), sideloaded once, deduped by source URL.
		$att_id = grass_prj_sideload( $pr['image'] );
		if ( $att_id ) { set_post_thumbnail( $post_id, $att_id ); }

		$results[] = array( 'title' => $pr['title'], 'slug' => $pr['slug'], 'category' => $pr['category'], 'action' => $action . 'd', 'wrote' => $wrote_str, 'id' => (int) $post_id );
	}
	return $results;
}

/* -------------------------------------------------------------------------
 *  Inspector — dump the ACF "Project Details" values of the existing project
 *  posts so the source -> field mapping can be confirmed before importing.
 * ---------------------------------------------------------------------- */
function grass_prj_inspect() {
	$out = "ACF 'Project Details' values on the existing project cards:\n";
	$existing = get_posts( array(
		'post_type'      => GRASS_PRJ_PT,
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'orderby'        => 'title',
		'order'          => 'ASC',
	) );
	if ( ! $existing ) { return $out . "(no project posts found)\n"; }

	foreach ( $existing as $p ) {
		$tt = wp_get_object_terms( $p->ID, GRASS_PRJ_TAX, array( 'fields' => 'names' ) );
		$out .= "\n================ {$p->post_title}  ({$p->post_name} #{$p->ID}) ================\n";
		$out .= "category: " . ( is_array( $tt ) ? implode( ', ', $tt ) : '' ) . "   featured: #" . (int) get_post_thumbnail_id( $p->ID ) . "\n";
		foreach ( grass_prj_acf_fields() as $f ) {
			$val = function_exists( 'get_field' ) ? get_field( $f, $p->ID ) : get_post_meta( $p->ID, $f, true );
			$out .= "  {$f} = " . ( is_scalar( $val ) ? (string) $val : wp_json_encode( $val ) ) . "\n";
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
