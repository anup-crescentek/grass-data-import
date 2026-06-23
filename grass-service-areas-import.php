<?php
/**
 * GrassHouston — Service Areas bulk importer
 * -------------------------------------------------
 * Creates / updates the 14 missing "services-areas" location posts and
 * fills their ACF fields (field group: "Services Area Details").
 *
 * HOW TO RUN (no WP All Import / no CLI needed):
 *   1. Install the free "WPCode" or "Code Snippets" plugin.
 *   2. Add a new PHP snippet, paste this whole file (without the opening <?php
 *      line if the plugin adds its own), set it to "Run once" if available.
 *   3. Activate / Execute. You'll see a summary printed (and in debug.log).
 *   4. Check Service Areas in admin + the /service-areas/ listing.
 *   5. DELETE / deactivate the snippet afterwards so it can't run again.
 *
 * Re-running is SAFE: it matches on slug and UPDATES instead of duplicating.
 *
 * IMAGES: the 3 image fields are left untouched by default. To reuse the
 * Houston page's images on every city, put their media-library attachment IDs
 * in the $shared_images map below. Find an ID by opening the image in
 * Media Library and reading the post=NNN number in the URL.
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

add_action( 'admin_init', function () {

	// Guard: only run when explicitly triggered to avoid accidental re-runs.
	if ( ! current_user_can( 'manage_options' ) ) { return; }
	if ( ! isset( $_GET['run_grass_import'] ) ) { return; } // visit /wp-admin/?run_grass_import=1

	if ( ! function_exists( 'update_field' ) ) {
		wp_die( 'ACF is not active — update_field() unavailable.' );
	}

	$post_type = 'services-areas';

	// OPTIONAL: reuse shared images across all cities. Leave 0 to skip.
	$shared_images = array(
		'grass_installation_image'          => 0, // e.g. 123
		'signature_method_image'            => 0,
		'commercial_sod_installation_image' => 0,
	);

	// --- Shared brand copy (identical on every page) -----------------------
	$shared = array(
		'short_description'   => 'Anyone can lay sod. We engineer successful lawn establishment — soil prep, drainage, the right grass, and long-term root development.',
		'tagline'             => 'Sod where appearance matters most. Hydroseeding where scale matters most.',
		'installation_done'   => '2,400+ properties installed',
	);

	$cities = grass_service_area_cities();

	$created = array();
	$updated = array();

	foreach ( $cities as $c ) {

		// Find existing post by slug within this CPT.
		$existing = get_page_by_path( $c['slug'], OBJECT, $post_type );

		$postarr = array(
			'post_type'   => $post_type,
			'post_status' => 'publish',
			'post_title'  => 'Sod Installation & Grass Establishment in ' . $c['name'] . ', TX',
			'post_name'   => $c['slug'],
		);

		if ( $existing ) {
			$postarr['ID'] = $existing->ID;
			$post_id = wp_update_post( $postarr, true );
			$updated[] = $c['name'];
		} else {
			$post_id = wp_insert_post( $postarr, true );
			$created[] = $c['name'];
		}

		if ( is_wp_error( $post_id ) || ! $post_id ) {
			error_log( 'Grass import FAILED for ' . $c['name'] . ': ' . print_r( $post_id, true ) );
			continue;
		}

		// --- ACF fields (by field NAME) -----------------------------------
		update_field( 'short_heading',                  $c['name'] . ', TX', $post_id );
		update_field( 'heading_text',                   'Sod Installation & Grass Establishment in ' . $c['name'] . ', TX', $post_id );
		update_field( 'short_description_listing_view',  $c['listing'], $post_id );
		update_field( 'short_description',               $shared['short_description'], $post_id );
		update_field( 'tagline',                         $shared['tagline'], $post_id );
		update_field( 'installation_done',               $shared['installation_done'], $post_id );

		update_field( 'lawn_establishment_heading',      'What makes ' . $c['name'] . ' lawn installation different', $post_id );
		update_field( 'lawn_establishment_sub_heading',  $c['lawn_sub'], $post_id );
		update_field( 'lawn_establishment_content',      $c['lawn_content'], $post_id );

		update_field( 'deep_dive_heading',               'Neighborhood deep dive · ' . $c['name'] . ', TX', $post_id );
		update_field( 'deep_dive_content',               $c['deep_dive'], $post_id );

		update_field( 'grass_installation_conetent',     $c['grass_install'], $post_id ); // NOTE: ACF field name is misspelled "conetent"
		update_field( 'signature_method_content',        $c['signature'], $post_id );
		update_field( 'commercial_sod_installation_content', $c['commercial'], $post_id );

		update_field( 'why_choose_us_heading_text',      'Why ' . $c['name'] . ', TX customers choose Grass', $post_id );

		// Optional shared images.
		foreach ( $shared_images as $field => $att_id ) {
			if ( $att_id ) { update_field( $field, (int) $att_id, $post_id ); }
		}
	}

	$msg  = "GRASS SERVICE AREAS IMPORT COMPLETE\n";
	$msg .= 'Created (' . count( $created ) . '): ' . implode( ', ', $created ) . "\n";
	$msg .= 'Updated (' . count( $updated ) . '): ' . implode( ', ', $updated ) . "\n";
	error_log( $msg );
	wp_die( nl2br( esc_html( $msg ) ) . '<p>Done. Now delete this snippet.</p>' );
} );


/**
 * City dataset. Source facts (neighborhoods + soil line) scraped from
 * grasshouston.com; narrative copy written to match the Houston template voice.
 */
function grass_service_area_cities() {
	return array(

		array(
			'name' => 'League City', 'slug' => 'league-city-tx',
			'listing' => 'Coastal-clay communities near Clear Lake with high-water-table drainage planning baked into every install.',
			'lawn_sub' => 'Inside the League City, TX soil and drainage realities we build around',
			'lawn_content' => '<p>League City sits on heavy coastal clay with a naturally high water table, so the difference between a lawn that thrives and one that stays soggy is almost always what happens <em>below</em> the sod. In master-planned communities like Tuscan Lakes and Mar Bella, builder fill is often compacted clay that sheds water instead of absorbing it.</p><p>Our standard League City install corrects grade to a minimum 2% fall away from the foundation, opens up surface drainage, and amends with a 2–3 inch topsoil layer so new roots can actually establish before the next heavy rain. The result is St. Augustine that roots deep and holds up through both flood season and August heat.</p>',
			'deep_dive' => '<p>Inside the League City, TX neighborhoods we install in:</p><ul><li><strong>Tuscan Lakes</strong> — established master-planned lots where drainage correction makes or breaks a re-sod.</li><li><strong>Mar Bella</strong> — newer construction on heavy fill that benefits from full grade and topsoil work.</li><li><strong>Westover Park</strong> — family lots where St. Augustine repair and replacement are the everyday work.</li><li><strong>Magnolia Creek</strong> — golf-community properties wanting a manicured, consistent finish.</li></ul>',
			'grass_install' => '<p>Residential sod, hydroseeding, repair, and full replacement across League City — front yards, back yards, and whole-property installs handled by our own crews.</p><ul><li>Front yard, backyard, and full-property sod</li><li>Hydroseeding for larger residential lots</li><li>Lawn repair and full replacement</li><li>Builder and remodel scheduling</li></ul>',
			'signature' => '<p>For half-acre-plus properties around League City, our hybrid model delivers a finished, magazine-worthy front yard with cost-efficient coverage across the rest of the lot.</p><ul><li>Premium sod in front areas</li><li>Engineered hydroseeding across back acreage</li><li>Smart property zoning</li><li>Scalable coverage</li></ul>',
			'commercial' => '<p>Builders, developers, HOAs, schools, churches, and property managers across League City count on us for bonded, insured, on-schedule sod and lawn installs.</p><ul><li>New construction final-grade sod</li><li>Builder &amp; developer scheduling</li><li>HOA common-area work</li><li>Hydroseeding for large lots</li><li>Erosion control and SWPPP compliance</li></ul>',
		),

		array(
			'name' => 'Humble', 'slug' => 'humble-tx',
			'listing' => 'Sandy loam with clay pockets near Lake Houston — lake-adjacent suburbs from Atascocita to Eagle Springs.',
			'lawn_sub' => 'Inside the Humble, TX soil and drainage realities we build around',
			'lawn_content' => '<p>Humble runs on sandy loam with clay pockets near Lake Houston, which means soil can change character within a single property. Lake-adjacent neighborhoods like Atascocita and Walden on Lake Houston drain quickly in places and hold water in others, so a one-size install rarely takes.</p><p>We map sun, shade, and drainage on every Humble lot before we lay a single piece of sod, amend the clay pockets, and match the grass variety to the conditions — St. Augustine under canopy, Bermuda in full sun — so the whole lawn establishes evenly.</p>',
			'deep_dive' => '<p>Inside the Humble, TX neighborhoods we install in:</p><ul><li><strong>Atascocita</strong> — large master-planned community with mixed sun and shade lots.</li><li><strong>Eagle Springs</strong> — family lots that reward proper soil prep and variety selection.</li><li><strong>Fall Creek</strong> — golf-community properties wanting a clean, uniform finish.</li><li><strong>Walden on Lake Houston</strong> — lake-adjacent lots with fast-draining sandy soil.</li></ul>',
			'grass_install' => '<p>Residential sod, hydroseeding, repair, and full replacement across Humble — front yards, back yards, and whole-property installs handled by our own crews.</p><ul><li>Front yard, backyard, and full-property sod</li><li>Hydroseeding for larger residential lots</li><li>Lawn repair and full replacement</li><li>Builder and remodel scheduling</li></ul>',
			'signature' => '<p>For half-acre-plus properties around Humble, our hybrid model delivers a finished, magazine-worthy front yard with cost-efficient coverage across the rest of the lot.</p><ul><li>Premium sod in front areas</li><li>Engineered hydroseeding across back acreage</li><li>Smart property zoning</li><li>Scalable coverage</li></ul>',
			'commercial' => '<p>Builders, developers, HOAs, schools, churches, and property managers across Humble count on us for bonded, insured, on-schedule sod and lawn installs.</p><ul><li>New construction final-grade sod</li><li>Builder &amp; developer scheduling</li><li>HOA common-area work</li><li>Hydroseeding for large lots</li><li>Erosion control and SWPPP compliance</li></ul>',
		),

		array(
			'name' => 'Kingwood', 'slug' => 'kingwood-tx',
			'listing' => 'The Livable Forest — St. Augustine country under heavy pine canopy where shade tolerance is everything.',
			'lawn_sub' => 'Inside the Kingwood, TX soil and drainage realities we build around',
			'lawn_content' => '<p>Kingwood earned its "Livable Forest" name honestly — mature pine canopy shades most of the community, and that changes everything about lawn establishment. Under heavy shade, sun-loving grasses fail; shade-tolerant St. Augustine is the standard, and even then variety selection matters.</p><p>On every Kingwood lot we map the canopy and choose St. Augustine cultivars built for low light, correct any drainage that pine litter and root competition create, and prep soil so new sod establishes despite the shade. It is detailed work, and it is the work this community needs.</p>',
			'deep_dive' => '<p>Inside the Kingwood, TX neighborhoods we install in:</p><ul><li><strong>Kings Forest</strong> — established, heavily shaded lots where cultivar choice is critical.</li><li><strong>Forest Cove</strong> — mature-canopy properties needing shade-tuned St. Augustine.</li><li><strong>Kings River</strong> — riverside lots balancing shade and drainage.</li><li><strong>Bear Branch</strong> — family lots under dense pine.</li><li><strong>Kingwood Greens</strong> — golf-community properties wanting a manicured finish.</li></ul>',
			'grass_install' => '<p>Residential sod, hydroseeding, repair, and full replacement across Kingwood — front yards, back yards, and whole-property installs handled by our own crews.</p><ul><li>Front yard, backyard, and full-property sod</li><li>Hydroseeding for larger residential lots</li><li>Lawn repair and full replacement</li><li>Builder and remodel scheduling</li></ul>',
			'signature' => '<p>For half-acre-plus properties around Kingwood, our hybrid model delivers a finished, magazine-worthy front yard with cost-efficient coverage across the rest of the lot.</p><ul><li>Premium sod in front areas</li><li>Engineered hydroseeding across back acreage</li><li>Smart property zoning</li><li>Scalable coverage</li></ul>',
			'commercial' => '<p>Builders, developers, HOAs, schools, churches, and property managers across Kingwood count on us for bonded, insured, on-schedule sod and lawn installs.</p><ul><li>New construction final-grade sod</li><li>Builder &amp; developer scheduling</li><li>HOA common-area work</li><li>Hydroseeding for large lots</li><li>Erosion control and SWPPP compliance</li></ul>',
		),

		array(
			'name' => 'Missouri City', 'slug' => 'missouri-city-tx',
			'listing' => 'Expansive swelling clay with strict HOA review across master-planned Sienna, Riverstone, and First Colony.',
			'lawn_sub' => 'Inside the Missouri City, TX soil and drainage realities we build around',
			'lawn_content' => '<p>Missouri City is expansive-clay country — soil that swells when wet and shrinks when dry, putting constant stress on roots, slabs, and irrigation. In master-planned communities like Sienna and Riverstone, that is compounded by strict HOA architectural review that expects a polished, conforming result.</p><p>Our Missouri City installs are built for both realities: grade and drainage corrections that manage the clay, topsoil amendment that gives roots a stable layer, and a finish that clears HOA standards the first time. We coordinate directly with your HOA and builder so approval is never the bottleneck.</p>',
			'deep_dive' => '<p>Inside the Missouri City, TX neighborhoods we install in:</p><ul><li><strong>Sienna</strong> — large master-planned community with strict HOA expectations.</li><li><strong>Riverstone</strong> — premium lots on expansive clay needing careful drainage work.</li><li><strong>First Colony</strong> — established neighborhoods where re-sods are the everyday job.</li><li><strong>Quail Valley</strong> — golf-community properties wanting a manicured finish.</li></ul>',
			'grass_install' => '<p>Residential sod, hydroseeding, repair, and full replacement across Missouri City — front yards, back yards, and whole-property installs handled by our own crews.</p><ul><li>Front yard, backyard, and full-property sod</li><li>Hydroseeding for larger residential lots</li><li>Lawn repair and full replacement</li><li>Builder and remodel scheduling</li></ul>',
			'signature' => '<p>For half-acre-plus properties around Missouri City, our hybrid model delivers a finished, magazine-worthy front yard with cost-efficient coverage across the rest of the lot.</p><ul><li>Premium sod in front areas</li><li>Engineered hydroseeding across back acreage</li><li>Smart property zoning</li><li>Scalable coverage</li></ul>',
			'commercial' => '<p>Builders, developers, HOAs, schools, churches, and property managers across Missouri City count on us for bonded, insured, on-schedule sod and lawn installs.</p><ul><li>New construction final-grade sod</li><li>Builder &amp; developer scheduling</li><li>HOA common-area work</li><li>Hydroseeding for large lots</li><li>Erosion control and SWPPP compliance</li></ul>',
		),

		array(
			'name' => 'Rosenberg', 'slug' => 'rosenberg-tx',
			'listing' => 'Brazos-influenced loamy soils in a fast-growing suburb — Seabourne Creek, Walnut Creek, and Summer Lakes.',
			'lawn_sub' => 'Inside the Rosenberg, TX soil and drainage realities we build around',
			'lawn_content' => '<p>Rosenberg benefits from Brazos-influenced loamy soils — generally more forgiving than the heavy gumbo clay closer to the bay, but still variable lot to lot in this fast-growing corridor. New construction in Seabourne Creek and Summer Lakes often leaves compacted builder fill that needs loosening before sod will root.</p><p>We assess each Rosenberg lot, correct grade and drainage where the loam gives way to clay, and amend soil so establishment is fast and even. With the right prep, Rosenberg lawns root in quickly and hold a clean, full look year-round.</p>',
			'deep_dive' => '<p>Inside the Rosenberg, TX neighborhoods we install in:</p><ul><li><strong>Seabourne Creek</strong> — newer construction on builder fill that rewards real soil prep.</li><li><strong>Walnut Creek</strong> — family lots wanting fast, even establishment.</li><li><strong>Summer Lakes</strong> — master-planned lots with mixed loam and clay.</li></ul>',
			'grass_install' => '<p>Residential sod, hydroseeding, repair, and full replacement across Rosenberg — front yards, back yards, and whole-property installs handled by our own crews.</p><ul><li>Front yard, backyard, and full-property sod</li><li>Hydroseeding for larger residential lots</li><li>Lawn repair and full replacement</li><li>Builder and remodel scheduling</li></ul>',
			'signature' => '<p>For half-acre-plus properties around Rosenberg, our hybrid model delivers a finished, magazine-worthy front yard with cost-efficient coverage across the rest of the lot.</p><ul><li>Premium sod in front areas</li><li>Engineered hydroseeding across back acreage</li><li>Smart property zoning</li><li>Scalable coverage</li></ul>',
			'commercial' => '<p>Builders, developers, HOAs, schools, churches, and property managers across Rosenberg count on us for bonded, insured, on-schedule sod and lawn installs.</p><ul><li>New construction final-grade sod</li><li>Builder &amp; developer scheduling</li><li>HOA common-area work</li><li>Hydroseeding for large lots</li><li>Erosion control and SWPPP compliance</li></ul>',
		),

		array(
			'name' => 'Pasadena', 'slug' => 'pasadena-tx',
			'listing' => 'Coastal clay with industrial-corridor compaction — durable installs built for tougher soil conditions.',
			'lawn_sub' => 'Inside the Pasadena, TX soil and drainage realities we build around',
			'lawn_content' => '<p>Pasadena lawns deal with two challenges at once: coastal clay that drains slowly and industrial-corridor compaction that leaves soil dense and root-hostile. Together they defeat sod that is simply rolled out over existing grade.</p><p>Our Pasadena installs start by breaking up compaction, correcting fall away from the foundation, and laying down an amended topsoil layer so roots have somewhere to go. We match grass to exposure — tough Bermuda for full-sun, traffic-heavy areas; St. Augustine where shade allows — for a lawn that actually lasts in these conditions.</p>',
			'deep_dive' => '<p>Inside the Pasadena, TX neighborhoods we install in:</p><ul><li><strong>Strawberry Park</strong> — established lots where compaction relief is step one.</li><li><strong>Genoa-area</strong> — properties needing durable, full-sun grass selection.</li><li><strong>West of Deer Park</strong> — family lots on coastal clay wanting reliable establishment.</li></ul>',
			'grass_install' => '<p>Residential sod, hydroseeding, repair, and full replacement across Pasadena — front yards, back yards, and whole-property installs handled by our own crews.</p><ul><li>Front yard, backyard, and full-property sod</li><li>Hydroseeding for larger residential lots</li><li>Lawn repair and full replacement</li><li>Builder and remodel scheduling</li></ul>',
			'signature' => '<p>For half-acre-plus properties around Pasadena, our hybrid model delivers a finished, magazine-worthy front yard with cost-efficient coverage across the rest of the lot.</p><ul><li>Premium sod in front areas</li><li>Engineered hydroseeding across back acreage</li><li>Smart property zoning</li><li>Scalable coverage</li></ul>',
			'commercial' => '<p>Builders, developers, HOAs, schools, churches, and property managers across Pasadena count on us for bonded, insured, on-schedule sod and lawn installs. The industrial corridor makes erosion control and SWPPP compliance a regular part of our work here.</p><ul><li>New construction final-grade sod</li><li>Builder &amp; developer scheduling</li><li>HOA common-area work</li><li>Hydroseeding for large lots</li><li>Erosion control and SWPPP compliance</li></ul>',
		),

		array(
			'name' => 'Clear Lake', 'slug' => 'clear-lake-tx',
			'listing' => 'Bay-area properties with salt-influenced soil and a high water table — El Lago, Nassau Bay, Bay Oaks.',
			'lawn_sub' => 'Inside the Clear Lake, TX soil and drainage realities we build around',
			'lawn_content' => '<p>Clear Lake sits right on the bay, and that brings salt-influenced soil and a high water table that few inland installers plan for. Neighborhoods like Nassau Bay and El Lago need grass and prep chosen specifically for these coastal conditions, or new sod struggles to root.</p><p>We build Clear Lake lawns around the water table — improving drainage, amending soil to buffer salinity, and selecting salt-tolerant, water-loving varieties — so the finished lawn establishes strong and stays green through the bay-area summer.</p>',
			'deep_dive' => '<p>Inside the Clear Lake, TX neighborhoods we install in:</p><ul><li><strong>El Lago</strong> — bay-adjacent lots with salt-influenced soil.</li><li><strong>Nassau Bay</strong> — waterfront properties needing drainage-first installs.</li><li><strong>Bay Oaks</strong> — premium, manicured lawns in a country-club setting.</li><li><strong>University Park</strong> — family lots wanting clean, even establishment.</li></ul>',
			'grass_install' => '<p>Residential sod, hydroseeding, repair, and full replacement across Clear Lake — front yards, back yards, and whole-property installs handled by our own crews.</p><ul><li>Front yard, backyard, and full-property sod</li><li>Hydroseeding for larger residential lots</li><li>Lawn repair and full replacement</li><li>Builder and remodel scheduling</li></ul>',
			'signature' => '<p>For half-acre-plus properties around Clear Lake, our hybrid model delivers a finished, magazine-worthy front yard with cost-efficient coverage across the rest of the lot.</p><ul><li>Premium sod in front areas</li><li>Engineered hydroseeding across back acreage</li><li>Smart property zoning</li><li>Scalable coverage</li></ul>',
			'commercial' => '<p>Builders, developers, HOAs, schools, churches, and property managers across Clear Lake count on us for bonded, insured, on-schedule sod and lawn installs.</p><ul><li>New construction final-grade sod</li><li>Builder &amp; developer scheduling</li><li>HOA common-area work</li><li>Hydroseeding for large lots</li><li>Erosion control and SWPPP compliance</li></ul>',
		),

		array(
			'name' => 'Baytown', 'slug' => 'baytown-tx',
			'listing' => 'Industrial-corridor compaction with coastal clay — durable, drainage-first installs from Eagle Pointe to Goose Creek.',
			'lawn_sub' => 'Inside the Baytown, TX soil and drainage realities we build around',
			'lawn_content' => '<p>Baytown lawns face coastal clay and industrial-corridor compaction — a tough combination that leaves soil dense, slow-draining, and hard for roots to penetrate. Along the SH-146 corridor especially, builder and industrial fill make proper prep non-negotiable.</p><p>We start every Baytown install by relieving compaction and correcting grade, then lay an amended topsoil layer and match grass to exposure so the lawn establishes and holds up. Durable Bermuda for full-sun, high-traffic zones and St. Augustine where there is shade give the most reliable long-term result.</p>',
			'deep_dive' => '<p>Inside the Baytown, TX neighborhoods we install in:</p><ul><li><strong>Eagle Pointe</strong> — golf-community lots wanting a clean, uniform finish.</li><li><strong>Goose Creek</strong> — established properties where compaction relief comes first.</li><li><strong>Brunson Estates</strong> — family lots on coastal clay.</li><li><strong>SH-146 corridor</strong> — newer construction on heavy industrial fill.</li></ul>',
			'grass_install' => '<p>Residential sod, hydroseeding, repair, and full replacement across Baytown — front yards, back yards, and whole-property installs handled by our own crews.</p><ul><li>Front yard, backyard, and full-property sod</li><li>Hydroseeding for larger residential lots</li><li>Lawn repair and full replacement</li><li>Builder and remodel scheduling</li></ul>',
			'signature' => '<p>For half-acre-plus properties around Baytown, our hybrid model delivers a finished, magazine-worthy front yard with cost-efficient coverage across the rest of the lot.</p><ul><li>Premium sod in front areas</li><li>Engineered hydroseeding across back acreage</li><li>Smart property zoning</li><li>Scalable coverage</li></ul>',
			'commercial' => '<p>Builders, developers, HOAs, schools, churches, and property managers across Baytown count on us for bonded, insured, on-schedule sod and lawn installs. The industrial corridor makes erosion control and SWPPP compliance a regular part of our work here.</p><ul><li>New construction final-grade sod</li><li>Builder &amp; developer scheduling</li><li>HOA common-area work</li><li>Hydroseeding for large lots</li><li>Erosion control and SWPPP compliance</li></ul>',
		),

		array(
			'name' => 'Pearland', 'slug' => 'pearland-tx',
			'listing' => 'One of the most expansive clay markets in Houston — Shadow Creek Ranch, Silverlake, Southern Trails, Pomona.',
			'lawn_sub' => 'Inside the Pearland, TX soil and drainage realities we build around',
			'lawn_content' => '<p>Pearland is one of the most expansive-clay markets in all of Greater Houston. The soil swells dramatically when wet and contracts when dry, stressing roots and irrigation and quickly defeating any lawn that was installed without real ground prep. Large master-planned communities like Shadow Creek Ranch sit squarely on it.</p><p>Our Pearland installs are engineered for that clay: minimum 2% slope correction away from the foundation, active drainage solutions, and a 2–3 inch amended topsoil layer that gives new roots a stable medium. Done right, even Pearland\'s heavy clay supports a deep-rooted, resilient St. Augustine lawn.</p>',
			'deep_dive' => '<p>Inside the Pearland, TX neighborhoods we install in:</p><ul><li><strong>Shadow Creek Ranch</strong> — large master-planned community on heavy expansive clay.</li><li><strong>Silverlake</strong> — established lots where drainage correction is essential.</li><li><strong>Southern Trails</strong> — premium properties wanting a manicured finish.</li><li><strong>Pomona</strong> — newer construction on builder fill that needs full prep.</li></ul>',
			'grass_install' => '<p>Residential sod, hydroseeding, repair, and full replacement across Pearland — front yards, back yards, and whole-property installs handled by our own crews.</p><ul><li>Front yard, backyard, and full-property sod</li><li>Hydroseeding for larger residential lots</li><li>Lawn repair and full replacement</li><li>Builder and remodel scheduling</li></ul>',
			'signature' => '<p>For half-acre-plus properties around Pearland, our hybrid model delivers a finished, magazine-worthy front yard with cost-efficient coverage across the rest of the lot.</p><ul><li>Premium sod in front areas</li><li>Engineered hydroseeding across back acreage</li><li>Smart property zoning</li><li>Scalable coverage</li></ul>',
			'commercial' => '<p>Builders, developers, HOAs, schools, churches, and property managers across Pearland count on us for bonded, insured, on-schedule sod and lawn installs.</p><ul><li>New construction final-grade sod</li><li>Builder &amp; developer scheduling</li><li>HOA common-area work</li><li>Hydroseeding for large lots</li><li>Erosion control and SWPPP compliance</li></ul>',
		),

		array(
			'name' => 'The Woodlands', 'slug' => 'the-woodlands-tx',
			'listing' => 'Premium standard under heavy pine canopy — Carlton Woods, Grogan\'s Mill, Creekside Park and beyond.',
			'lawn_sub' => 'Inside the The Woodlands, TX soil and drainage realities we build around',
			'lawn_content' => '<p>The Woodlands is built around its forest, and that heavy pine canopy sets the standard for lawn work here: deep shade across most properties, premium expectations, and demanding HOA and community standards. Sun-loving grasses simply do not perform under this much canopy.</p><p>We build Woodlands lawns around shade-tolerant St. Augustine cultivars, manage the drainage and root competition that mature pines create, and deliver the polished, magazine-worthy finish these neighborhoods expect. From Carlton Woods estates to Creekside Park family lots, the prep is detailed and the result is consistent.</p>',
			'deep_dive' => '<p>Inside the The Woodlands, TX neighborhoods we install in:</p><ul><li><strong>Carlton Woods</strong> — luxury estates demanding a flawless, manicured lawn.</li><li><strong>Grogan\'s Mill</strong> — the original village, heavily shaded and established.</li><li><strong>Cochran\'s Crossing</strong> — family lots under dense pine canopy.</li><li><strong>Indian Springs</strong> — wooded properties needing shade-tuned grass.</li><li><strong>Creekside Park</strong> — newer construction balancing shade and drainage.</li></ul>',
			'grass_install' => '<p>Residential sod, hydroseeding, repair, and full replacement across The Woodlands — front yards, back yards, and whole-property installs handled by our own crews.</p><ul><li>Front yard, backyard, and full-property sod</li><li>Hydroseeding for larger residential lots</li><li>Lawn repair and full replacement</li><li>Builder and remodel scheduling</li></ul>',
			'signature' => '<p>For half-acre-plus properties around The Woodlands, our hybrid model delivers a finished, magazine-worthy front yard with cost-efficient coverage across the rest of the lot.</p><ul><li>Premium sod in front areas</li><li>Engineered hydroseeding across back acreage</li><li>Smart property zoning</li><li>Scalable coverage</li></ul>',
			'commercial' => '<p>Builders, developers, HOAs, schools, churches, and property managers across The Woodlands count on us for bonded, insured, on-schedule sod and lawn installs.</p><ul><li>New construction final-grade sod</li><li>Builder &amp; developer scheduling</li><li>HOA common-area work</li><li>Hydroseeding for large lots</li><li>Erosion control and SWPPP compliance</li></ul>',
		),

		array(
			'name' => 'Conroe', 'slug' => 'conroe-tx',
			'listing' => 'Lake Conroe estates and an industrial hydroseed corridor — April Sound, Bentwater, Grand Central Park.',
			'lawn_sub' => 'Inside the Conroe, TX soil and drainage realities we build around',
			'lawn_content' => '<p>Conroe spans two very different jobs: polished Lake Conroe estate lawns in communities like Bentwater and April Sound, and large-scale industrial and commercial hydroseed work along its growing corridors. The soil and the goals shift accordingly.</p><p>For estate properties we focus on premium sod, drainage, and a manicured finish; for acreage and commercial sites we engineer hydroseeding for fast, cost-efficient coverage and erosion control. Either way, every Conroe install begins with a soil assessment and a written scope so the right method goes on the right ground.</p>',
			'deep_dive' => '<p>Inside the Conroe, TX neighborhoods we install in:</p><ul><li><strong>April Sound</strong> — gated Lake Conroe community wanting a refined finish.</li><li><strong>Bentwater</strong> — premium lakeside estates with high standards.</li><li><strong>Grand Central Park</strong> — newer master-planned lots on builder fill.</li><li><strong>Graystone Hills</strong> — family properties wanting even, durable establishment.</li></ul>',
			'grass_install' => '<p>Residential sod, hydroseeding, repair, and full replacement across Conroe — front yards, back yards, and whole-property installs handled by our own crews.</p><ul><li>Front yard, backyard, and full-property sod</li><li>Hydroseeding for larger residential lots</li><li>Lawn repair and full replacement</li><li>Builder and remodel scheduling</li></ul>',
			'signature' => '<p>For half-acre-plus and lakeside properties around Conroe, our hybrid model delivers a finished, magazine-worthy front yard with cost-efficient coverage across the rest of the lot.</p><ul><li>Premium sod in front areas</li><li>Engineered hydroseeding across back acreage</li><li>Smart property zoning</li><li>Scalable coverage</li></ul>',
			'commercial' => '<p>Builders, developers, HOAs, schools, churches, and property managers across Conroe count on us for bonded, insured, on-schedule sod and lawn installs — including large-scale hydroseed work along the industrial corridor.</p><ul><li>New construction final-grade sod</li><li>Builder &amp; developer scheduling</li><li>HOA common-area work</li><li>Hydroseeding for large lots</li><li>Erosion control and SWPPP compliance</li></ul>',
		),

		array(
			'name' => 'Sugar Land', 'slug' => 'sugar-land-tx',
			'listing' => 'Strict HOA review across expansive clay — Telfair, Riverstone, Sweetwater, Greatwood, Avalon, First Colony.',
			'lawn_sub' => 'Inside the Sugar Land, TX soil and drainage realities we build around',
			'lawn_content' => '<p>Sugar Land pairs expansive clay with some of the strictest HOA architectural review in the metro. Premier master-planned communities like Telfair, Riverstone, and Avalon expect a flawless, conforming lawn — and the swelling clay underneath works against that unless the ground is prepped properly.</p><p>Our Sugar Land installs manage the clay with grade correction, drainage solutions, and topsoil amendment, then finish to a standard that clears HOA review the first time. We coordinate directly with your HOA and builder so the approval process never slows the project down.</p>',
			'deep_dive' => '<p>Inside the Sugar Land, TX neighborhoods we install in:</p><ul><li><strong>Telfair</strong> — premier master-planned community with strict standards.</li><li><strong>Riverstone</strong> — premium lots on expansive clay.</li><li><strong>Sweetwater</strong> — established, manicured neighborhoods.</li><li><strong>Greatwood</strong> — family estates wanting a clean finish.</li><li><strong>Avalon</strong> — luxury lots demanding a flawless lawn.</li><li><strong>First Colony</strong> — established community where re-sods are routine.</li></ul>',
			'grass_install' => '<p>Residential sod, hydroseeding, repair, and full replacement across Sugar Land — front yards, back yards, and whole-property installs handled by our own crews.</p><ul><li>Front yard, backyard, and full-property sod</li><li>Hydroseeding for larger residential lots</li><li>Lawn repair and full replacement</li><li>Builder and remodel scheduling</li></ul>',
			'signature' => '<p>For half-acre-plus properties around Sugar Land, our hybrid model delivers a finished, magazine-worthy front yard with cost-efficient coverage across the rest of the lot.</p><ul><li>Premium sod in front areas</li><li>Engineered hydroseeding across back acreage</li><li>Smart property zoning</li><li>Scalable coverage</li></ul>',
			'commercial' => '<p>Builders, developers, HOAs, schools, churches, and property managers across Sugar Land count on us for bonded, insured, on-schedule sod and lawn installs.</p><ul><li>New construction final-grade sod</li><li>Builder &amp; developer scheduling</li><li>HOA common-area work</li><li>Hydroseeding for large lots</li><li>Erosion control and SWPPP compliance</li></ul>',
		),

		array(
			'name' => 'Cypress', 'slug' => 'cypress-tx',
			'listing' => 'Prairie clay with Cypress Creek watershed influence — Bridgeland, Towne Lake, Coles Crossing, Fairfield.',
			'lawn_sub' => 'Inside the Cypress, TX soil and drainage realities we build around',
			'lawn_content' => '<p>Cypress sits on heavy prairie clay, and the Cypress Creek watershed adds a real drainage dimension to nearly every install. Master-planned giants like Bridgeland and Towne Lake are built around water features, which makes proper grading and runoff management essential rather than optional.</p><p>We engineer Cypress lawns to move water the right way — correcting slope, building in drainage, and amending the prairie clay with topsoil so roots establish deep. The payoff is a St. Augustine lawn that survives both the heavy clay and the watershed\'s wet-season swings.</p>',
			'deep_dive' => '<p>Inside the Cypress, TX neighborhoods we install in:</p><ul><li><strong>Bridgeland</strong> — large master-planned community built around water and drainage.</li><li><strong>Towne Lake</strong> — lakeside lots where runoff management is key.</li><li><strong>Coles Crossing</strong> — established family neighborhoods on prairie clay.</li><li><strong>Fairfield</strong> — mature lots wanting reliable re-sods.</li><li><strong>Blackhorse Ranch</strong> — golf-community properties wanting a manicured finish.</li></ul>',
			'grass_install' => '<p>Residential sod, hydroseeding, repair, and full replacement across Cypress — front yards, back yards, and whole-property installs handled by our own crews.</p><ul><li>Front yard, backyard, and full-property sod</li><li>Hydroseeding for larger residential lots</li><li>Lawn repair and full replacement</li><li>Builder and remodel scheduling</li></ul>',
			'signature' => '<p>For half-acre-plus properties around Cypress, our hybrid model delivers a finished, magazine-worthy front yard with cost-efficient coverage across the rest of the lot.</p><ul><li>Premium sod in front areas</li><li>Engineered hydroseeding across back acreage</li><li>Smart property zoning</li><li>Scalable coverage</li></ul>',
			'commercial' => '<p>Builders, developers, HOAs, schools, churches, and property managers across Cypress count on us for bonded, insured, on-schedule sod and lawn installs.</p><ul><li>New construction final-grade sod</li><li>Builder &amp; developer scheduling</li><li>HOA common-area work</li><li>Hydroseeding for large lots</li><li>Erosion control and SWPPP compliance</li></ul>',
		),

		array(
			'name' => 'Hockley', 'slug' => 'hockley-tx',
			'listing' => 'FM 2920 corridor acreage, equestrian properties, and working ranches — sandy loam hybrid sod-plus-hydroseed country.',
			'lawn_sub' => 'Inside the Hockley, TX soil and drainage realities we build around',
			'lawn_content' => '<p>Hockley is acreage country — FM 2920 corridor estates, equestrian properties, and working ranches on sandy loam that drains fast. The challenge here is rarely a quarter-acre lot; it is covering large areas affordably while still getting a premium look where it matters.</p><p>That makes Hockley textbook hybrid territory: premium sod across the areas you see and use every day, engineered hydroseeding across the back acreage and pasture for fast, cost-efficient establishment and erosion control. We assess the whole property and zone the work so every acre gets the right method.</p>',
			'deep_dive' => '<p>Inside the Hockley, TX areas we install in:</p><ul><li><strong>FM 2920 corridor acreage</strong> — large estate lots needing zoned sod-and-hydroseed plans.</li><li><strong>Equestrian properties</strong> — pasture and paddock establishment with erosion control.</li><li><strong>Working ranches</strong> — large-scale hydroseed coverage on sandy loam.</li></ul>',
			'grass_install' => '<p>Residential and acreage sod, hydroseeding, repair, and full replacement across Hockley — handled by our own crews.</p><ul><li>Front yard, backyard, and full-property sod</li><li>Hydroseeding for larger residential and acreage lots</li><li>Lawn repair and full replacement</li><li>Builder and remodel scheduling</li></ul>',
			'signature' => '<p>For the acreage and estate properties that define Hockley, our hybrid model delivers a finished, magazine-worthy front yard with cost-efficient coverage across the rest of the land.</p><ul><li>Premium sod in front areas</li><li>Engineered hydroseeding across back acreage</li><li>Smart property zoning</li><li>Scalable coverage</li></ul>',
			'commercial' => '<p>Builders, developers, HOAs, ranches, schools, churches, and property managers across Hockley count on us for bonded, insured, on-schedule sod and large-scale hydroseed installs.</p><ul><li>New construction final-grade sod</li><li>Builder &amp; developer scheduling</li><li>HOA common-area work</li><li>Hydroseeding for large lots and pasture</li><li>Erosion control and SWPPP compliance</li></ul>',
		),

	);
}
