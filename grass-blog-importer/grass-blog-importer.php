<?php
/**
 * Plugin Name:       Grass Blog Importer
 * Plugin URI:        https://grasshouston.com
 * Description:        Imports the 13 Resources/Guides articles from the original grasshouston.com as WordPress posts (content, excerpt, category, featured image). Tools -> Blog Import.
 * Version:           1.1.0
 * Author:            GrassHouston
 * License:           GPL-2.0+
 * Requires at least: 5.8
 * Requires PHP:      7.2
 *
 * Safe to re-run: matches by slug or title and UPDATES (no duplicates).
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'GRASS_BLOG_CAP', 'manage_options' );

require_once plugin_dir_path( __FILE__ ) . 'articles-data.php';

/* -------------------------------------------------------------------------
 *  Admin page
 * ---------------------------------------------------------------------- */
add_action( 'admin_menu', function () {
	add_management_page( 'Blog Import', 'Blog Import', GRASS_BLOG_CAP, 'grass-blog', 'grass_blog_render_page' );
} );

function grass_blog_render_page() {
	if ( ! current_user_can( GRASS_BLOG_CAP ) ) { wp_die( 'No permission.' ); }

	$results   = null;
	$dry_run   = true;
	$do_images = false;
	$inspect   = null;
	$img_note  = '';

	if ( isset( $_POST['grass_blog_action'] ) ) {
		check_admin_referer( 'grass_blog_run' );
		if ( 'inspect' === $_POST['grass_blog_action'] ) {
			$inspect = grass_blog_inspect();
		} else {
			$dry_run   = ( 'import' !== $_POST['grass_blog_action'] );
			$do_images = ! empty( $_POST['do_images'] );
			$results   = grass_blog_run_import( $dry_run, $do_images, $img_note );
		}
	}
	?>
	<div class="wrap">
		<h1>Grass Blog Importer</h1>
		<p>Imports the 13 Resources articles from the original site as standard posts. Matches by slug or title and updates in place (no duplicates), creating the missing ones and refreshing existing.</p>

		<form method="post">
			<?php wp_nonce_field( 'grass_blog_run' ); ?>
			<p><label><input type="checkbox" name="do_images" value="1" checked> Also import &amp; assign the featured (hero) images from the original.</label></p>
			<p>
				<button type="submit" name="grass_blog_action" value="preview" class="button">Preview (dry run — no changes)</button>
				<button type="submit" name="grass_blog_action" value="import" class="button button-primary" onclick="return confirm('Import all 13 articles now?');">Run Import</button>
				<button type="submit" name="grass_blog_action" value="inspect" class="button">Inspect existing posts</button>
			</p>
		</form>

		<?php if ( null !== $inspect ) : ?>
			<h2>Existing post structure</h2>
			<textarea readonly onclick="this.select()" style="width:100%;height:360px;font-family:monospace;font-size:12px;"><?php echo esc_textarea( $inspect ); ?></textarea>
		<?php endif; ?>

		<?php if ( is_array( $results ) ) : ?>
			<hr>
			<h2><?php echo $dry_run ? 'Preview (nothing saved)' : 'Import complete'; ?></h2>
			<?php if ( $img_note ) : ?><p><em><?php echo esc_html( $img_note ); ?></em></p><?php endif; ?>
			<table class="widefat striped" style="max-width:880px">
				<thead><tr><th>Title</th><th>Category</th><th>Action</th><th>Post</th></tr></thead>
				<tbody>
				<?php foreach ( $results as $r ) : ?>
					<tr>
						<td><?php echo esc_html( $r['title'] ); ?></td>
						<td><?php echo esc_html( $r['category'] ); ?></td>
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
 *  Import
 * ---------------------------------------------------------------------- */
function grass_blog_run_import( $dry_run, $do_images, &$img_note ) {
	$articles = grass_blog_articles();
	$results  = array();
	$imgcount = 0;

	foreach ( $articles as $a ) {
		$existing_id = grass_blog_find_post( $a['slug'], $a['title'] );
		$action      = $existing_id ? 'update' : 'create';

		if ( $dry_run ) {
			$results[] = array( 'title' => $a['title'], 'category' => $a['category'], 'action' => 'would ' . $action, 'id' => $existing_id );
			continue;
		}

		$postarr = array(
			'post_type'    => 'post',
			'post_status'  => 'publish',
			'post_title'   => $a['title'],
			'post_content' => $a['content_main'],   // first block -> the_content widget
			'post_excerpt' => $a['excerpt'],
		);
		if ( $existing_id ) {
			$postarr['ID'] = $existing_id;                 // keep existing slug/URL.
			$pid = wp_update_post( wp_slash( $postarr ), true );
		} else {
			$postarr['post_name'] = $a['slug'];
			$pid = wp_insert_post( wp_slash( $postarr ), true );
		}

		if ( is_wp_error( $pid ) || ! $pid ) {
			$results[] = array( 'title' => $a['title'], 'category' => $a['category'], 'action' => 'ERROR', 'id' => 0 );
			continue;
		}

		// Second block -> ACF "Post content second" + the mirror post_content meta the template reads.
		update_post_meta( $pid, 'post_content_second', wp_slash( $a['content_second'] ) );
		update_post_meta( $pid, '_post_content_second', 'field_6a304edb56d38' ); // ACF field key ref.
		update_post_meta( $pid, 'post_content', wp_slash( $a['content_second'] ) );

		$cat_id = grass_blog_ensure_category( $a['category'] );
		if ( $cat_id ) { wp_set_post_terms( $pid, array( $cat_id ), 'category' ); }

		if ( $do_images && $a['hero'] ) {
			$att = grass_blog_sideload( $a['hero'] );
			if ( $att ) { set_post_thumbnail( $pid, $att ); $imgcount++; }
		}

		$results[] = array( 'title' => $a['title'], 'category' => $a['category'], 'action' => $action . 'd', 'id' => (int) $pid );
	}

	if ( $do_images && ! $dry_run ) { $img_note = "Assigned {$imgcount} featured images."; }
	return $results;
}

/** Find an existing post by source slug, else by exact title. Returns ID or 0. */
function grass_blog_find_post( $slug, $title ) {
	$byslug = get_page_by_path( $slug, OBJECT, 'post' );
	if ( $byslug ) { return (int) $byslug->ID; }

	$q = new WP_Query( array(
		'post_type'      => 'post',
		'post_status'    => 'any',
		'title'          => $title,
		'posts_per_page' => 1,
		'fields'         => 'ids',
		'no_found_rows'  => true,
	) );
	return ! empty( $q->posts ) ? (int) $q->posts[0] : 0;
}

/** Ensure a category exists, matching by SLUG (avoids HTML-encoded-name duplicates). */
function grass_blog_ensure_category( $name ) {
	$clean = html_entity_decode( $name, ENT_QUOTES );   // "Care & Maintenance"
	$slug  = sanitize_title( $clean );                  // "care-maintenance"

	$existing = get_term_by( 'slug', $slug, 'category' );
	if ( $existing ) {
		if ( $existing->name !== $clean ) {             // fix legacy "Care &amp; Maintenance" names.
			wp_update_term( $existing->term_id, 'category', array( 'name' => $clean ) );
		}
		return (int) $existing->term_id;
	}
	$term = wp_insert_term( $clean, 'category', array( 'slug' => $slug ) );
	return is_wp_error( $term ) ? 0 : (int) $term['term_id'];
}

/** Sideload a remote image once (dedup by source URL). Returns attachment ID or 0. */
function grass_blog_sideload( $url ) {
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
	if ( ! $name ) { $name = 'grass-article.jpg'; }
	$file_array = array( 'name' => $name, 'tmp_name' => $tmp );

	$id = media_handle_sideload( $file_array, 0 );
	if ( is_wp_error( $id ) ) { @unlink( $tmp ); return 0; }
	update_post_meta( $id, '_grass_src_url', $url );
	return (int) $id;
}

/* -------------------------------------------------------------------------
 *  Inspector — existing posts + categories + post metaboxes
 * ---------------------------------------------------------------------- */
function grass_blog_inspect() {
	$out = "############ EXISTING 'post' POSTS ############\n";
	$posts = get_posts( array( 'post_type' => 'post', 'post_status' => 'any', 'numberposts' => 40, 'orderby' => 'date', 'order' => 'DESC' ) );
	foreach ( $posts as $p ) {
		$cats = wp_get_post_terms( $p->ID, 'category', array( 'fields' => 'names' ) );
		$thumb = has_post_thumbnail( $p->ID ) ? 'yes' : 'no';
		$out .= "\n#{$p->ID}  [{$p->post_status}]  \"{$p->post_title}\"\n";
		$out .= "   slug: {$p->post_name}\n";
		$out .= "   category: " . implode( ', ', (array) $cats ) . " | thumbnail: {$thumb} | content_len: " . strlen( $p->post_content ) . "\n";
		// non-standard meta keys
		$keys = array();
		foreach ( get_post_meta( $p->ID ) as $k => $v ) { if ( '_' !== substr( $k, 0, 1 ) || in_array( $k, array( '_thumbnail_id' ), true ) ) { $keys[] = $k; } }
		if ( $keys ) { $out .= "   meta: " . implode( ', ', $keys ) . "\n"; }
	}

	$out .= "\n############ CATEGORIES ############\n";
	foreach ( get_terms( array( 'taxonomy' => 'category', 'hide_empty' => false ) ) as $t ) {
		$out .= "  {$t->name}  (slug: {$t->slug}, count: {$t->count})\n";
	}

	// Where does the article body live? Dump the 3 content sources per post.
	$out .= "\n############ CONTENT SOURCES on existing posts ############\n";
	foreach ( $posts as $p ) {
		$col = (string) $p->post_content;
		$pcs = (string) get_post_meta( $p->ID, 'post_content_second', true );
		$pcm = (string) get_post_meta( $p->ID, 'post_content', true );
		$out .= "\n=== #{$p->ID}  {$p->post_title} ===\n";
		$out .= "  [post_content COLUMN] len=" . strlen( $col ) . "\n      " . mb_substr( trim( $col ), 0, 450 ) . "\n";
		$out .= "  [meta: post_content_second] len=" . strlen( $pcs ) . "\n      " . mb_substr( trim( $pcs ), 0, 450 ) . "\n";
		$out .= "  [meta: post_content] len=" . strlen( $pcm ) . "\n      " . mb_substr( trim( $pcm ), 0, 450 ) . "\n";
	}

	// Scan theme functions.php for post-screen metaboxes.
	$out .= "\n############ POST METABOXES in theme functions.php ############\n";
	$fn = get_stylesheet_directory() . '/functions.php';
	if ( is_file( $fn ) ) {
		$src = file_get_contents( $fn );
		if ( preg_match_all( "/add_meta_box\s*\(\s*['\"][^'\"]+['\"]\s*,\s*['\"]([^'\"]+)['\"][^;]*?['\"](post)['\"]/", $src, $m ) ) {
			$out .= "  " . implode( "\n  ", array_unique( $m[1] ) ) . "\n";
		} else {
			$out .= "  (none found targeting 'post')\n";
		}
	}
	return $out;
}
