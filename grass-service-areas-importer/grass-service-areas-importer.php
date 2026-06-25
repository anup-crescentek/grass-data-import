<?php
/**
 * Plugin Name:       Grass Service Areas Importer
 * Plugin URI:        https://grasshouston.com
 * Description:        Imports REAL per-city content (from the original grasshouston.com) into the Service Areas ACF fields + native FAQ metabox for all cities, mirroring the live Houston page mapping. Optionally imports the source images. Tools -> Service Areas Import.
 * Version:           1.7.0
 * Author:            GrassHouston
 * License:           GPL-2.0+
 * Requires at least: 5.8
 * Requires PHP:      7.2
 *
 * Safe to re-run: matches posts by slug and UPDATES them (no duplicates).
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'GRASS_SAI_POST_TYPE', 'services-areas' );
define( 'GRASS_SAI_CAP', 'manage_options' );
// Original site's shared hero image -> used as the WordPress Featured Image.
define( 'GRASS_SAI_HERO', 'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779247728767_05ff316c.png' );

require_once plugin_dir_path( __FILE__ ) . 'cities-data.php';

/**
 * Source images (shared across the original site). The first three are assigned
 * to the three ACF image fields on every page; all are imported to the Media
 * Library so you can reassign freely. Re-running never re-downloads (dedup by URL).
 */
function grass_sai_images() {
	return array(
		// Mapped to the original site's named section images (bundle: grass1 / hybridAerial / commercial).
		'grass_installation_image'          => 'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779247745126_1332fe27.jpg', // grass1
		'signature_method_image'            => 'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779247810390_08d59034.png', // hybridAerial
		'commercial_sod_installation_image' => 'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779247828949_b4bf2d2f.jpg', // commercial
	);
}

function grass_sai_gallery() {
	return array(
		'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779247728767_05ff316c.png',
		'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779247745126_1332fe27.jpg',
		'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779247750460_0f39badc.png',
		'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779247753177_210d3ce5.png',
		'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779247769692_9f6dd6ac.jpg',
		'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779247788720_779e09c1.png',
		'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779247810390_08d59034.png',
		'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779247828949_b4bf2d2f.jpg',
		'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779247846886_7f420ca0.jpg',
		'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779247847664_54410f9f.jpg',
		'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779298084085_1e898654.jpg',
		'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779298101805_50c23209.jpg',
		'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779298122126_b28feb1b.png',
		'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779298146360_4ccb7fca.png',
		'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779298169722_031a5ed1.png',
		'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779298191962_21a2045b.png',
		'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779298213884_c4be72b0.png',
		'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779298235170_1d848f68.png',
		'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779299229599_c5377e74.jpg',
		'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779299253943_b65b0883.png',
		'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779299275786_bb4d2d28.png',
		'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779299312049_bd9547fb.png',
		'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779299330041_b8751b06.jpg',
		'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779299348986_e90210ed.jpg',
		'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779299383928_da3a0de9.png',
		'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779299407055_ba03e9c8.png',
	);
}

/* -------------------------------------------------------------------------
 *  Admin page
 * ---------------------------------------------------------------------- */
add_action( 'admin_menu', function () {
	add_management_page( 'Service Areas Import', 'Service Areas Import', GRASS_SAI_CAP, 'grass-sai', 'grass_sai_render_page' );
} );

function grass_sai_render_page() {
	if ( ! current_user_can( GRASS_SAI_CAP ) ) { wp_die( 'No permission.' ); }

	$results   = null;
	$dry_run   = true;
	$do_images = false;
	$img_note  = '';
	$inspect   = null;
	$acf_ready = function_exists( 'update_field' );

	if ( isset( $_POST['grass_sai_action'] ) ) {
		check_admin_referer( 'grass_sai_run' );
		if ( 'inspect' === $_POST['grass_sai_action'] ) {
			$inspect = grass_sai_inspect();
		} elseif ( 'codescan' === $_POST['grass_sai_action'] ) {
			$inspect = grass_sai_codescan();
		} else {
			$dry_run   = ( 'import' !== $_POST['grass_sai_action'] );
			$do_images = ! empty( $_POST['do_images'] );
			$results   = grass_sai_run_import( $dry_run, $do_images, $img_note );
		}
	}

	$cpt_ok = post_type_exists( GRASS_SAI_POST_TYPE );
	?>
	<div class="wrap">
		<h1>Grass Service Areas Importer</h1>
		<p>Imports the <strong>real per-city content</strong> from the original grasshouston.com into the Service Areas ACF fields. Matches by slug and updates in place (no duplicates), so it both <em>corrects</em> existing pages that show Houston content and <em>creates</em> the missing ones.</p>

		<?php $active = grass_sai_active_slugs(); if ( ! empty( $active ) ) : ?>
			<div class="notice notice-info inline"><p><strong>Scoped run:</strong> only these <strong><?php echo count( $active ); ?></strong> city page(s) will be created/updated — <code><?php echo implode( '</code>, <code>', array_map( 'esc_html', $active ) ); ?></code>. Every other city is left untouched. To process all cities, empty <code>grass_sai_active_slugs()</code>.</p></div>
		<?php endif; ?>

		<?php if ( ! $acf_ready ) : ?><div class="notice notice-error"><p><strong>ACF not active.</strong> Activate Advanced Custom Fields first.</p></div><?php endif; ?>
		<?php if ( ! $cpt_ok ) : ?><div class="notice notice-warning"><p>Post type <code><?php echo esc_html( GRASS_SAI_POST_TYPE ); ?></code> is not registered — activate your Service Areas CPT first.</p></div><?php endif; ?>

		<form method="post">
			<?php wp_nonce_field( 'grass_sai_run' ); ?>
			<p><label><input type="checkbox" name="do_images" value="1"> Also import the original's images into the Media Library and assign the 3 section images (slower — downloads ~26 files the first time).</label></p>
			<p>
				<button type="submit" name="grass_sai_action" value="preview" class="button">Preview (dry run — no changes)</button>
				<button type="submit" name="grass_sai_action" value="import" class="button button-primary" <?php disabled( ! $acf_ready ); ?> onclick="return confirm('Import real content into all Service Area pages now? Existing pages will be overwritten with their correct city content.');">Run Import</button>
				<button type="submit" name="grass_sai_action" value="inspect" class="button">Inspect ACF fields (for mapping)</button>
				<button type="submit" name="grass_sai_action" value="codescan" class="button">Find metabox code</button>
			</p>
		</form>

		<?php if ( null !== $inspect ) : ?>
			<h2>ACF fields attached to Service Areas</h2>
			<p>Copy everything below and send it back so the importer can target the real FAQ/pricing/etc. fields:</p>
			<textarea readonly onclick="this.select()" style="width:100%;height:360px;font-family:monospace;font-size:12px;"><?php echo esc_textarea( $inspect ); ?></textarea>
		<?php endif; ?>

		<hr>
		<h2>What gets written per city</h2>
		<p>The 15 text/wysiwyg ACF fields (mapped exactly like Houston), plus every custom metabox: <code>_sa_faq_data</code> (FAQs), <code>_sa_show_*</code> grass toggles, <code>_for_residential_data</code>, <code>_for_commercial_data</code>, <code>_lawn_challenges_data</code>, <code>_we_install_in_data</code> (neighborhood cards), <code>_areas_we_serve_data</code> (pills), <code>_growth_areas_data</code> (landmarks), and <code>_why_choose_us_data</code>. Only <code>_sa_selected_grid_posts</code> (related-areas grid) is left untouched — set those manually if you use them.</p>

		<?php if ( is_array( $results ) ) : ?>
			<hr>
			<h2><?php echo $dry_run ? 'Preview (nothing saved)' : 'Import complete'; ?></h2>
			<?php if ( $img_note ) : ?><p><em><?php echo esc_html( $img_note ); ?></em></p><?php endif; ?>
			<table class="widefat striped" style="max-width:820px">
				<thead><tr><th>City</th><th>Slug</th><th>Action</th><th>Post</th></tr></thead>
				<tbody>
				<?php foreach ( $results as $r ) : ?>
					<tr>
						<td><?php echo esc_html( $r['name'] ); ?></td>
						<td><code><?php echo esc_html( $r['slug'] ); ?></code></td>
						<td><?php echo esc_html( $r['action'] ); ?></td>
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
 *  Inspector — reveals ALL ACF fields (incl. code-registered) + where data lives
 * ---------------------------------------------------------------------- */
function grass_sai_inspect() {
	if ( ! function_exists( 'acf_get_field_groups' ) ) { return 'ACF not active.'; }
	global $wpdb;

	$houston = get_page_by_path( 'houston-tx', OBJECT, GRASS_SAI_POST_TYPE );
	$pid     = $houston ? (int) $houston->ID : 0;
	$out     = 'Post type: ' . GRASS_SAI_POST_TYPE . "\nSample post: Houston (#{$pid})\n\n";

	// A) EVERY field group with its location rules + fields.
	$out .= "############ ALL ACF FIELD GROUPS ############\n\n";
	foreach ( acf_get_field_groups() as $g ) {
		$loc = array();
		if ( ! empty( $g['location'] ) ) {
			foreach ( $g['location'] as $orGroup ) {
				$parts = array();
				foreach ( $orGroup as $rule ) { $parts[] = "{$rule['param']} {$rule['operator']} {$rule['value']}"; }
				$loc[] = '(' . implode( ' AND ', $parts ) . ')';
			}
		}
		$out .= "=== GROUP: {$g['title']}  ({$g['key']}) ===\n";
		$out .= '    location: ' . ( $loc ? implode( ' OR ', $loc ) : 'none' ) . "\n";
		$out .= grass_sai_dump_fields( acf_get_fields( $g['key'] ), 0 );
		$out .= "\n";
	}

	// B) DB scan: where does FAQ/pricing/who-for data actually live?
	$out .= "############ META-KEY SCAN (faq/question/answer/price/who/audience/landmark) ############\n";
	$rows = $wpdb->get_results(
		"SELECT meta_key, COUNT(*) c FROM {$wpdb->postmeta}
		 WHERE meta_key REGEXP 'faq|question|answer|pric|who_|audience|landmark|process|neighbor'
		 GROUP BY meta_key ORDER BY meta_key LIMIT 300"
	);
	if ( $rows ) {
		foreach ( $rows as $r ) { $out .= "  {$r->meta_key}  (x{$r->c})\n"; }
	} else {
		$out .= "  (no matching meta keys found anywhere)\n";
	}

	// C) All custom _sa_* meta keys (the site's own section storage).
	$out .= "\n############ CUSTOM _sa_* META KEYS ############\n";
	$sa = $wpdb->get_results( "SELECT meta_key, COUNT(*) c FROM {$wpdb->postmeta} WHERE meta_key REGEXP '^_?sa_' GROUP BY meta_key ORDER BY meta_key LIMIT 100" );
	if ( $sa ) {
		foreach ( $sa as $r ) { $out .= "  {$r->meta_key}  (x{$r->c})\n"; }
	} else {
		$out .= "  (none)\n";
	}

	// C2) Full STRUCTURE of every _sa_* value on Houston (reveals array shape/keys).
	if ( $pid ) {
		$out .= "\n############ STRUCTURE of _sa_* values on Houston (#{$pid}) ############\n";
		foreach ( get_post_meta( $pid ) as $k => $v ) {
			if ( ! preg_match( '/^_?sa_/', $k ) ) { continue; }
			$raw = is_array( $v ) ? reset( $v ) : $v;
			$val = maybe_unserialize( $raw );
			$out .= "\n--- {$k}  (type: " . gettype( $val ) . ") ---\n";
			$out .= mb_substr( var_export( $val, true ), 0, 2500 ) . "\n";
		}
	}

	// D) FULL untruncated value of every Services Area ACF field on Houston,
	//    so the importer can mirror the exact source -> field mapping per city.
	if ( $pid ) {
		$fields18 = array(
			'short_heading', 'heading_text', 'short_description_listing_view', 'short_description',
			'tagline', 'installation_done', 'lawn_establishment_heading', 'lawn_establishment_sub_heading',
			'lawn_establishment_content', 'deep_dive_heading', 'deep_dive_content', 'grass_installation_conetent',
			'signature_method_content', 'commercial_sod_installation_content', 'why_choose_us_heading_text',
			'grass_installation_image', 'signature_method_image', 'commercial_sod_installation_image',
		);
		$out .= "\n############ FULL Houston (#{$pid}) ACF VALUES ############\n";
		foreach ( $fields18 as $fn ) {
			$out .= "\n--- {$fn} ---\n" . get_post_meta( $pid, $fn, true ) . "\n";
		}

		// E) Houston metabox repeater values (the custom non-_sa_ meta).
		$repeaterKeys = array(
			'_for_residential_data', '_for_commercial_data', '_lawn_challenges_data',
			'_we_install_in_data', '_areas_we_serve_data', '_growth_areas_data', '_why_choose_us_data',
		);
		$out .= "\n############ Houston metabox repeater values ############\n";
		foreach ( $repeaterKeys as $mk ) {
			$val  = maybe_unserialize( get_post_meta( $pid, $mk, true ) );
			$out .= "\n--- {$mk}  (type: " . gettype( $val ) . ") ---\n";
			$out .= mb_substr( var_export( $val, true ), 0, 4000 ) . "\n";
		}
	}
	return $out;
}

/**
 * Locate and dump the PHP that registers the custom _sa_* metaboxes, so their
 * meta keys + array structures can be read and filled by the importer.
 */
function grass_sai_codescan() {
	$needle = '_sa_faq_data';
	$roots  = array();
	if ( defined( 'WP_PLUGIN_DIR' ) ) { $roots[] = WP_PLUGIN_DIR; }
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
			$path = $file->getPathname();
			if ( 'php' !== strtolower( $file->getExtension() ) ) { continue; }
			if ( $file->getSize() > 800000 ) { continue; }
			if ( preg_match( '#[\\\\/](vendor|node_modules)[\\\\/]#', $path ) ) { continue; }
			$content = @file_get_contents( $path );
			if ( $content && false !== strpos( $content, $needle ) ) {
				$found[ $path ] = $content;
			}
		}
	}

	if ( ! $found ) {
		return "No PHP file containing '{$needle}' found in plugins/mu-plugins/theme.";
	}

	// Compact summary across all matches: distinct _sa_* keys + metabox titles.
	$allKeys = array();
	$titles  = array();
	foreach ( $found as $content ) {
		if ( preg_match_all( '/_sa_[a-z0-9_]+/', $content, $m ) ) { $allKeys = array_merge( $allKeys, $m[0] ); }
		if ( preg_match_all( '/add_meta_box\s*\(\s*[\'"][^\'"]+[\'"]\s*,\s*[\'"]([^\'"]+)[\'"]/', $content, $t ) ) { $titles = array_merge( $titles, $t[1] ); }
	}
	$allKeys = array_values( array_unique( $allKeys ) );
	sort( $allKeys );
	$titles  = array_values( array_unique( $titles ) );

	$out  = "FILES FOUND (" . count( $found ) . "):\n  " . implode( "\n  ", array_keys( $found ) ) . "\n\n";
	$out .= "DISTINCT _sa_* KEYS (" . count( $allKeys ) . "):\n  " . implode( "\n  ", $allKeys ) . "\n\n";
	$out .= "METABOX TITLES (" . count( $titles ) . "):\n  " . implode( "\n  ", $titles ) . "\n\n";

	// Full source (capped) so the array structures are visible.
	$budget = 110000;
	foreach ( $found as $path => $content ) {
		$out  .= "================ FILE: {$path} (" . strlen( $content ) . " bytes) ================\n";
		$chunk = substr( $content, 0, min( $budget, 70000 ) );
		$out  .= $chunk . "\n";
		if ( strlen( $content ) > strlen( $chunk ) ) { $out .= "\n...[truncated — share this file directly if structures are cut off]...\n"; }
		$budget -= strlen( $chunk );
		if ( $budget <= 0 ) { $out .= "\n[output budget reached]\n"; break; }
	}
	return $out;
}

function grass_sai_dump_fields( $fields, $depth ) {
	$out = '';
	if ( empty( $fields ) ) { return $out; }
	$pad = str_repeat( '    ', $depth );
	foreach ( $fields as $f ) {
		$out .= "{$pad}- {$f['name']}  [{$f['type']}]  \"{$f['label']}\"\n";
		if ( in_array( $f['type'], array( 'repeater', 'group' ), true ) && ! empty( $f['sub_fields'] ) ) {
			$out .= grass_sai_dump_fields( $f['sub_fields'], $depth + 1 );
		}
		if ( 'flexible_content' === $f['type'] && ! empty( $f['layouts'] ) ) {
			foreach ( $f['layouts'] as $lay ) {
				$out .= "{$pad}    [layout] {$lay['name']}\n";
				if ( ! empty( $lay['sub_fields'] ) ) { $out .= grass_sai_dump_fields( $lay['sub_fields'], $depth + 2 ); }
			}
		}
	}
	return $out;
}

/* -------------------------------------------------------------------------
 *  Import
 * ---------------------------------------------------------------------- */
/**
 * Slugs the importer is allowed to create/update in this run. Everything else in
 * cities-data.php is skipped, so previously-imported city pages are never touched.
 * Return an empty array() to lift the gate and process every city.
 */
function grass_sai_active_slugs() {
	return array(
		'jersey-village-tx',
	);
}

function grass_sai_run_import( $dry_run, $do_images, &$img_note ) {
	$cities  = grass_sai_cities();
	$active  = grass_sai_active_slugs();
	$results = array();

	$assign_ids = array();
	$hero_id    = 0;
	if ( $do_images && ! $dry_run ) {
		foreach ( grass_sai_gallery() as $url ) { grass_sai_sideload( $url ); }
		foreach ( grass_sai_images() as $field => $url ) {
			$id = grass_sai_sideload( $url );
			if ( $id ) { $assign_ids[ $field ] = $id; }
		}
		$hero_id = grass_sai_sideload( GRASS_SAI_HERO );
		$img_note = sprintf(
			'%d source images in Media Library; assigned %d section images%s per page.',
			count( grass_sai_gallery() ), count( $assign_ids ), $hero_id ? ' + hero featured image' : ''
		);
	}

	foreach ( $cities as $c ) {
		// Gate: only touch the active slugs (if the gate list is non-empty).
		if ( ! empty( $active ) && ! in_array( $c['slug'], $active, true ) ) { continue; }

		$existing = get_page_by_path( $c['slug'], OBJECT, GRASS_SAI_POST_TYPE );
		$action   = $existing ? 'update' : 'create';
		$post_id  = $existing ? (int) $existing->ID : 0;

		if ( $dry_run ) {
			$results[] = array( 'name' => $c['name'], 'slug' => $c['slug'], 'action' => 'would ' . $action, 'id' => $post_id );
			continue;
		}

		$postarr = array(
			'post_type'   => GRASS_SAI_POST_TYPE,
			'post_status' => 'publish',
			'post_title'  => $c['name'] . ', TX',
			'post_name'   => $c['slug'],
		);
		if ( $existing ) { $postarr['ID'] = $existing->ID; $post_id = wp_update_post( $postarr, true ); }
		else { $post_id = wp_insert_post( $postarr, true ); }

		if ( is_wp_error( $post_id ) || ! $post_id ) {
			$results[] = array( 'name' => $c['name'], 'slug' => $c['slug'], 'action' => 'ERROR', 'id' => 0 );
			continue;
		}

		// 15 text/wysiwyg ACF fields, mapped exactly like the live Houston page.
		foreach ( $c['fields'] as $fname => $val ) { update_field( $fname, $val, $post_id ); }

		// Optional shared section images + hero (Featured Image).
		foreach ( $assign_ids as $field => $att_id ) { update_field( $field, $att_id, $post_id ); }
		if ( $hero_id ) { set_post_thumbnail( $post_id, $hero_id ); }

		// Native FAQ metabox + grass-type display toggles (custom _sa_* meta).
		update_post_meta( $post_id, '_sa_faq_data', wp_slash( $c['faqs'] ) );
		update_post_meta( $post_id, '_sa_show_st_augustine', $c['show']['st_augustine'] );
		update_post_meta( $post_id, '_sa_show_bermuda', $c['show']['bermuda'] );
		update_post_meta( $post_id, '_sa_show_zoysia', $c['show']['zoysia'] );

		// Custom metabox repeaters registered in the child theme (functions.php).
		$m = $c['meta'];
		update_post_meta( $post_id, '_for_residential_data', wp_slash( $m['residential'] ) );
		update_post_meta( $post_id, '_for_commercial_data', wp_slash( $m['commercial'] ) );
		update_post_meta( $post_id, '_lawn_challenges_data', wp_slash( $m['lawn_challenges'] ) );
		update_post_meta( $post_id, '_we_install_in_data', wp_slash( $m['we_install_in'] ) );
		update_post_meta( $post_id, '_areas_we_serve_data', wp_slash( $m['areas_we_serve'] ) );
		update_post_meta( $post_id, '_growth_areas_data', wp_slash( $m['growth_areas'] ) );
		update_post_meta( $post_id, '_why_choose_us_data', wp_slash( $m['why_choose'] ) );

		$results[] = array( 'name' => $c['name'], 'slug' => $c['slug'], 'action' => $action . 'd', 'id' => (int) $post_id );
	}
	return $results;
}

/**
 * Download a remote image into the Media Library once. Dedupes by source URL
 * stored in attachment meta '_grass_src_url'. Returns attachment ID or 0.
 */
function grass_sai_sideload( $url ) {
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
	if ( ! $name ) { $name = 'grass-image.jpg'; }
	$file_array = array( 'name' => $name, 'tmp_name' => $tmp );

	$id = media_handle_sideload( $file_array, 0 );
	if ( is_wp_error( $id ) ) { @unlink( $tmp ); return 0; }

	update_post_meta( $id, '_grass_src_url', $url );
	return (int) $id;
}

