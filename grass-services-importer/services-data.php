<?php
/**
 * Service dataset for the Grass Services Importer.
 * AUTO-GENERATED from grasshouston.com's bundled siteData (services array "Ir").
 *
 * Field/destination map:
 *   title                -> post_title
 *   image                -> Featured Image          (source hero image, sideloaded)
 *   heading              -> ACF heading            (source hero)
 *   short_description    -> ACF short_description   (source intro)
 *   content              -> post_content           (source longIntro)
 *   what_drives_the_cost -> ACF what_drives_the_cost (source pricingNotes)
 *   whats_included       -> meta _sa_whats_included_data (source bullets, flat strings)
 *   key_features         -> meta _sa_key_features_data   (source whoItsFor: heading/content)
 *   how_it_works         -> meta _sa_how_it_works_data   (source process: heading/content; empty = plugin uses generic)
 *   faqs                 -> meta _sa_services_faq_data    (source faqs: question/answer)
 *
 * In the source bundle, hydroseeding and acreage-estate-turf-installation carry
 * their own key_features/how_it_works/faqs. Every other service was a stub in the
 * source (heading + intro + bullets only); those have hand-written body, pricing,
 * key_features, and faqs added here so each page matches the rest. how_it_works is
 * left empty on the hand-written services so it falls back to the shared generic
 * steps (only hydroseeding and acreage carry their own source process steps).
 *
 * NOTE: which of these services the importer actually writes is gated by
 * grass_svc_active_slugs() in the main plugin file — see that function.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function grass_svc_services() {
	return array(
		array(
			'slug'                 => 'hydroseeding',
			'title'                => 'Hydroseeding',
			'image'                => 'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779247788720_779e09c1.png',
			'heading'              => 'Houston Hydroseeding — Scalable, Engineered Coverage',
			'short_description'    => '<p>Hydroseeding is a precision-applied slurry of seed, fiber mulch, tackifier, and nutrients — engineered for uniform germination across large properties, sloped terrain, and acreage where rolled sod is inefficient.</p>',
			'content'              => '<p>Hydroseeding is the right tool for the job whenever the project is too big, too steep, or too cost-sensitive for rolled sod. Instead of laying a finished lawn one pallet at a time, our truck-mounted hydroseeders spray a precision-mixed slurry of premium seed, wood-fiber mulch, organic tackifier, fertilizer, and a soil-conditioning polymer across the prepared surface — giving you uniform germination across acreage in a fraction of the time and cost of sod.</p>

<p>What separates engineered hydroseeding from cheap broadcast seeding is the slurry chemistry. The mulch holds moisture against the soil surface during the critical 14-day germination window. The tackifier glues the slurry to the slope so it does not wash off in the first Houston thunderstorm. The fertilizer feeds the seedlings during establishment. The polymer holds water in sandy soils and improves penetration in clay. We mix every load to your specific site — clay versus sand, sun versus shade, slope versus flat, summer versus winter.</p>

<p>Seed selection is the other half of the equation. For full-sun acreage and pastures we lead with Bermuda blends (Sahara, Princess, or custom common-Bermuda mixes). For erosion-control work we add quick-cover annuals like cereal rye or annual rye to germinate within 5–7 days while the warm-season grasses establish over 21–30 days. For commercial and municipal work we run TXDOT-spec and municipal-spec blends that meet the bid documents.</p>

<p>We hydroseed jobs from a quarter-acre to 100+ acres across Greater Houston, including residential acreage in Fulshear, Magnolia, and Conroe, commercial sites along the I-10 and SH-99 corridors, and erosion-control work for civil engineers and general contractors on construction sites under SWPPP compliance.</p>',
			'what_drives_the_cost' => '<p>Hydroseeding is priced primarily by total area, but the per-acre rate varies meaningfully based on access (can our truck reach the application zone, or do we need long hose runs?), surface prep (raw clean grade versus weeded, debris-strewn surface), seed blend (basic Bermuda versus a custom 4-species native mix), and erosion-control add-ons (blanket overlay, bonded fiber matrix, BMP coordination).</p>

<p>On large jobs the per-acre rate drops substantially — economies of scale on slurry batching, truck staging, and crew time. Small jobs (under half an acre) usually do not pencil out for hydroseeding versus sod or even broadcast seed; we will tell you that honestly when it applies.</p>

<p>As a planning reference: typical hydroseeding projects in the Houston market range from roughly $0.10–$0.35 per square foot on standard residential and commercial work, with bonded-fiber-matrix slope work and TXDOT-spec blends running higher. We quote every job after a site walk — generic per-acre estimates from a phone call will always be wrong by enough to matter.</p>',
			'whats_included'       => array(
				'Custom seed blends tuned to your property and soil',
				'Uniform germination across 1 to 100+ acres',
				'Integrated erosion control and moisture retention',
				'Significantly faster than broadcast seeding',
			),
			'key_features'         => array(
				array( 'heading' => 'Acreage and estate homeowners', 'content' => 'Hydroseed the back 2–95% of a large property where sod is wasteful, while we sod the premium zones near the home. The hybrid model that built our business.' ),
				array( 'heading' => 'Ranch and pasture owners', 'content' => 'Bermuda or custom forage blends established uniformly across pasture, paddocks, and turn-out zones — engineered for grazing as well as appearance.' ),
				array( 'heading' => 'Rural and country-property new builds', 'content' => 'Fresh hydroseed across the disturbed construction footprint outside the immediate home zone, with quick-cover annuals to lock down soil during establishment.' ),
				array( 'heading' => 'General contractors and developers', 'content' => 'Bid-document-compliant hydroseed coverage for raw-grade industrial pads, lot closeouts, and large commercial frontages where speed and scale matter.' ),
				array( 'heading' => 'Civil engineers and SWPPP-permitted sites', 'content' => 'Erosion-control hydroseeding with bonded fiber matrix, blanket overlay, and BMP coordination on detention ponds, slopes, and channel banks.' ),
				array( 'heading' => 'Municipalities, schools, and parks', 'content' => 'TXDOT- and municipal-spec hydroseed blends installed by an insured, bondable crew with clear documentation and warranty walks.' ),
			),
			'how_it_works'         => array(
				array( 'heading' => 'Site walk and soil assessment', 'content' => 'We walk the property, evaluate slope and soil, identify drainage and access issues, and document any erosion-control or SWPPP requirements before quoting.' ),
				array( 'heading' => 'Custom seed blend and slurry mix', 'content' => 'Seed varieties, mulch type, tackifier, fertilizer, and any soil conditioners are specified to your site conditions and goals — not a generic one-size-fits-all batch.' ),
				array( 'heading' => 'Surface prep and grading', 'content' => 'Existing weeds and debris cleared, surface lightly disced or harrowed for seed-to-soil contact, drainage corrections made before slurry application.' ),
				array( 'heading' => 'Hydroseed application', 'content' => 'Truck-mounted hydroseeder applies the engineered slurry in uniform overlapping passes, with on-site mix tuning for slope, sun exposure, and access constraints.' ),
				array( 'heading' => 'Erosion blankets where needed', 'content' => 'On slopes steeper than 3:1 or in SWPPP-regulated zones, we overlay erosion-control blankets to lock the slurry and seedlings down through the first heavy rain.' ),
				array( 'heading' => 'Establishment walk and follow-up', 'content' => 'We come back at germination (day 7–14) and again at 30 days to verify uniform stand establishment, identify any thin spots, and address them under warranty.' ),
			),
			'faqs'                 => array(
				array( 'question' => 'How long does hydroseed take to germinate in Houston?', 'answer' => 'Quick-cover annuals (ryegrass) germinate in 5–7 days. Warm-season Bermuda germinates in 14–21 days at full soil temperature (above 70°F), longer in cool weather. Full uniform stand establishment takes 6–10 weeks depending on season and watering.' ),
				array( 'question' => 'Can I hydroseed in the winter?', 'answer' => 'Yes — but warm-season grasses (Bermuda) will sit dormant until soil temperatures climb in March–April. Winter hydroseeding is most effective when paired with a cool-season nurse crop like cereal rye that germinates and locks down the soil through the off-season.' ),
				array( 'question' => 'Do I need irrigation for hydroseed to work?', 'answer' => 'You need some watering — either an irrigation system, a portable sprinkler setup, or reliable rainfall — for the first 21 days. Hydroseed has more drought tolerance than fresh sod once germinated, but the seed itself needs consistent moisture during the germination window.' ),
				array( 'question' => 'Will the mulch wash off in a rainstorm?', 'answer' => 'Properly applied hydroseed with the right tackifier stays put through normal Houston rainstorms. On slopes steeper than 3:1 or in heavy-runoff zones, we overlay erosion-control blankets to bond the slurry through the first heavy rain.' ),
				array( 'question' => 'Is hydroseed cheaper than sod?', 'answer' => 'On a per-square-foot basis, yes — typically 60–80% less than sod. But hydroseed takes weeks to establish, is not walkable during that window, and is best for larger lower-visibility zones. Most properties over a half-acre benefit from a hybrid approach: sod where appearance matters, hydroseed everywhere else.' ),
				array( 'question' => 'What seed do you use for Houston acreage?', 'answer' => 'For full-sun acreage we lead with Bermuda (common, Sahara, or premium TifTuf where the budget supports it). For shaded acreage we sometimes blend in tall fescue varieties, though shaded acreage is often better served by sod or selective tree thinning. We tune the blend to your specific sun, soil, and use case.' ),
				array( 'question' => 'Do you handle SWPPP and erosion-control compliance?', 'answer' => 'Yes. We work routinely with civil engineers, GCs, and municipal inspectors on SWPPP-permitted sites — bonded fiber matrix, blanket overlay, BMP coordination, and the documentation packages your inspector wants to see.' ),
				array( 'question' => 'Can hydroseed cover bare construction soil?', 'answer' => 'Yes — vegetative cover on raw construction grade is one of the most common uses of hydroseeding in Houston. We use TXDOT-spec quick-cover blends with high tackifier loading to lock down disturbed soil within 5–7 days of application.' ),
			),
		),
		array(
			'slug'                 => 'hydromulching',
			'title'                => 'Hydromulching',
			'image'                => 'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779247788720_779e09c1.png',
			'heading'              => 'Hydromulching for Slopes, Construction Sites & Large Properties',
			'short_description'    => '<p>Hydromulching combines wood-fiber mulch, tackifier, and seed in a single bonded application — ideal for difficult terrain, steep slopes, and large-scale construction sites that demand fast vegetative cover.</p>',
			'content'              => '<p>Hydromulching is the heavy-duty cousin of hydroseeding — the same spray-applied approach, but built around a thicker, higher-loading wood-fiber mulch and a stronger tackifier designed to hold a slope or a raw construction grade through the worst a Houston storm can throw at it. Where standard hydroseeding is about uniform germination across open acreage, hydromulching is about locking down disturbed soil fast and keeping it locked down while seed establishes.</p>

<p>The difference is in the slurry. A hydromulch mix carries substantially more wood fiber — and on the toughest jobs a bonded fiber matrix — per gallon than a standard hydroseed batch, so it builds a continuous protective blanket over the soil instead of just a seed-and-moisture layer. That blanket is what stops a 4-inch downpour from sheeting across a fresh slope and carrying your seed, and your topsoil, into the storm drain. For Houston construction sites under SWPPP review, that erosion performance is not a nice-to-have — it is the requirement.</p>

<p>We tune every hydromulch job to the site: fiber loading and tackifier rate scale up with slope steepness and runoff exposure, and the seed blend is matched to sun, soil, and whether the client needs permanent turf or temporary stabilization cover. On the steepest banks and channel sides we move from standard hydromulch to a bonded fiber matrix that cures into a reinforced mat — the highest-protection option short of hard armoring.</p>

<p>We run hydromulching across Greater Houston for general contractors, civil engineers, and developers — industrial pads along I-10 and the Beltway, detention ponds and channel banks in master-planned communities, and large lot closeouts where bare graded soil has to be stabilized and vegetated on a construction calendar, not a gardening one.</p>',
			'what_drives_the_cost' => '<p>Hydromulching is priced by area and by how much protection the site demands. The two biggest cost levers are fiber loading — a standard hydromulch blanket versus a bonded fiber matrix for steep or regulated slopes — and access, meaning whether our truck can reach the application zone directly or we have to run long hose lays. Surface prep and any drainage corrections add to that, and erosion-control add-ons like blanket overlay on the steepest sections move the number up further.</p>

<p>Like hydroseeding, the per-area rate drops on bigger jobs as batching, staging, and crew time spread across more square footage. The premium over standard hydroseeding comes almost entirely from the extra mulch and tackifier — you are paying for the protective blanket, so the steeper and more runoff-exposed the site, the more that blanket is worth.</p>

<p>As a planning reference, hydromulching in the Houston market generally runs a step above standard hydroseeding per square foot, with bonded-fiber-matrix slope work and TXDOT-spec jobs higher still. Because the right specification depends entirely on slope, soil, and regulatory requirements, we quote every hydromulch project after a site walk rather than over the phone.</p>',
			'whats_included'       => array(
				'Premium bonded fiber matrix for slope retention',
				'Fast cover establishment on raw construction grade',
				'TXDOT and municipal specification compliant blends',
				'Engineered for Houston rainfall and soil profiles',
			),
			'key_features'         => array(
				array( 'heading' => 'General contractors and site developers', 'content' => 'Fast vegetative cover on raw graded pads and lot closeouts, sequenced to your construction schedule and inspection deadlines.' ),
				array( 'heading' => 'Civil engineers on SWPPP-permitted sites', 'content' => 'Bonded fiber matrix and high-loading hydromulch with the erosion performance and documentation your stormwater inspector requires.' ),
				array( 'heading' => 'Slope and embankment stabilization', 'content' => 'Steep banks, retention-pond sides, and channel slopes locked down with a reinforced mulch mat that survives the first heavy rain.' ),
				array( 'heading' => 'Detention ponds and drainage infrastructure', 'content' => 'Uniform, erosion-resistant cover on pond banks and channels where standard broadcast seeding simply washes out.' ),
				array( 'heading' => 'Large acreage and rural property owners', 'content' => 'Cost-efficient, heavy-protection cover across big disturbed footprints where rolled sod is impractical.' ),
				array( 'heading' => 'Municipal, school, and parks projects', 'content' => 'TXDOT- and municipal-spec hydromulch installed by an insured, bondable crew with clean documentation.' ),
			),
			'how_it_works'         => array(),
			'faqs'                 => array(
				array( 'question' => 'What is the difference between hydromulching and hydroseeding?', 'answer' => 'Both spray a seed-and-slurry mix, but hydromulching uses a much heavier wood-fiber load — and often a bonded fiber matrix — to build a protective blanket over the soil. Hydroseeding is about uniform germination across open ground; hydromulching is about erosion control and holding disturbed or sloped soil in place while seed establishes.' ),
				array( 'question' => 'Is hydromulching good for steep slopes?', 'answer' => 'Yes — it is one of the best options short of hard armoring. On slopes steeper than about 3:1 we move from standard hydromulch to a bonded fiber matrix that cures into a reinforced mat, locking the slurry and seedlings down through heavy Houston rain.' ),
				array( 'question' => 'Will it pass SWPPP and erosion-control inspection?', 'answer' => 'That is exactly what it is built for. We routinely coordinate with civil engineers and municipal inspectors on permitted sites, matching fiber loading, blanket overlay, and BMP details to the stormwater plan and providing the documentation inspectors want to see.' ),
				array( 'question' => 'How fast does it establish?', 'answer' => 'Quick-cover annuals germinate in about 5–7 days to start locking down the surface, while warm-season grasses fill in over 21–30 days at full soil temperature. The mulch blanket protects the soil from day one, before anything has even germinated.' ),
				array( 'question' => 'Can you hydromulch raw construction soil?', 'answer' => 'Yes. Stabilizing and vegetating bare graded pads is one of the most common uses. We use high-tackifier, TXDOT-spec blends to bond disturbed soil quickly so it does not erode before the grass takes.' ),
				array( 'question' => 'Do you handle large commercial and municipal jobs?', 'answer' => 'Yes — we are insured and bondable, run dedicated crews for bid-document work, and handle industrial pads, detention basins, road frontages, and master-planned community infrastructure across Greater Houston.' ),
			),
		),
		array(
			'slug'                 => 'erosion-control-sod-hydroseed',
			'title'                => 'Erosion Control Sod & Hydroseed',
			'image'                => 'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779247810390_08d59034.png',
			'heading'              => 'Erosion Control Sod & Hydroseed — Engineered Soil Stabilization',
			'short_description'    => '<p>Combine sod, hydroseeding, blankets, and BMP-compliant techniques to lock down disturbed soil, prevent washouts, and pass SWPPP inspections on residential and commercial sites.</p>',
			'content'              => '<p>Erosion control is where soil stabilization, the right vegetation, and engineered products come together to keep a slope, channel, or disturbed site from washing away. In Houston\'s flat-but-flood-prone landscape, bare soil does not stay put — a single 4-inch downpour will rill a fresh grade, undercut a pond bank, and carry sediment straight into the storm system. Our erosion-control work combines sod, hydroseeding, hydromulch, erosion blankets, and BMP-compliant techniques into one plan matched to the site and, where applicable, to the stormwater permit.</p>

<p>There is no single right product for erosion control — there is the right product for the slope angle, the soil, the flow concentration, and the timeline. Gentle, low-flow areas may need nothing more than hydroseed with a tackifier. Moderate slopes get hydromulch or a blanket overlay. Steep banks and concentrated-flow channels get bonded fiber matrix or staked sod for instant, fully-rooted protection. We specify the mix, not a one-size-fits-all product.</p>

<p>On permitted construction sites we work as part of the SWPPP team. That means coordinating with the civil engineer and general contractor, installing the specified BMPs, sequencing stabilization against the grading and construction calendar, and documenting the work so it holds up at inspection. We have delivered this on detention ponds, channel banks, road and utility corridors, and large commercial pads across the metro.</p>

<p>For homeowners and HOAs, the same engineering applies at smaller scale — a back-slope that erodes into the yard after every storm, a detention easement the HOA is responsible for maintaining, a drainage swale that will not hold grass. We diagnose why the soil is moving and build a fix that actually stays, instead of re-seeding the same washout every spring.</p>',
			'what_drives_the_cost' => '<p>Erosion-control pricing is driven by the severity of the problem, not just the area. A gentle slope that needs hydroseed plus a tackifier is a very different number from a steep channel bank that needs bonded fiber matrix or staked sod. Slope angle, flow concentration, soil condition, access, and any SWPPP or BMP requirements all move the price — because they all change which products and how much labor the job actually needs.</p>

<p>Sod is the most expensive option per square foot but gives instant, fully-rooted protection where you cannot wait for establishment. Hydroseed and hydromulch cost far less and suit larger or lower-stakes areas. Most real erosion-control plans are a hybrid — sod or matrix on the critical zones, hydromulch or blanket across the rest — and that blend is the single biggest lever on total cost.</p>

<p>Because the right specification depends entirely on the site and, on permitted jobs, on the engineer\'s plan, we never quote erosion control over the phone. We walk the site, identify where and why soil is moving, and price the specific combination of products that will actually hold.</p>',
			'whats_included'       => array(
				'SWPPP-compliant erosion and sediment control',
				'Slope-specific seed and blanket selection',
				'Detention pond, channel, and bank stabilization',
				'Coordination with civil engineers and builders',
			),
			'key_features'         => array(
				array( 'heading' => 'Civil engineers and SWPPP-permitted sites', 'content' => 'BMP-compliant stabilization — bonded fiber matrix, blankets, staked sod — installed and documented to satisfy your stormwater plan and inspector.' ),
				array( 'heading' => 'General contractors and developers', 'content' => 'Erosion and sediment control sequenced to your grading and construction calendar, keeping the site compliant from clearing through closeout.' ),
				array( 'heading' => 'Detention ponds and channel banks', 'content' => 'Steep, concentrated-flow zones stabilized with the right mix of sod, matrix, and blanket so they stop washing out.' ),
				array( 'heading' => 'HOAs and master-planned communities', 'content' => 'Maintenance and repair of drainage easements, detention basins, and common-area slopes the association is responsible for.' ),
				array( 'heading' => 'Homeowners with eroding slopes', 'content' => 'Back-slopes, swales, and bare spots that wash out every storm — diagnosed and rebuilt so the fix actually holds.' ),
				array( 'heading' => 'Municipal and infrastructure projects', 'content' => 'Road, utility, and public-works corridors stabilized to TXDOT and municipal spec by an insured, bondable crew.' ),
			),
			'how_it_works'         => array(),
			'faqs'                 => array(
				array( 'question' => 'What does erosion control actually involve?', 'answer' => 'It is a combination of stabilizing the soil and establishing vegetation, using whatever products the site demands — hydroseed, hydromulch, erosion blankets, bonded fiber matrix, and sod — usually in a hybrid plan. The goal is to stop soil from moving while grass roots take over the job permanently.' ),
				array( 'question' => 'Do you work on SWPPP-permitted construction sites?', 'answer' => 'Yes, routinely. We coordinate with the civil engineer and GC, install the specified BMPs, sequence stabilization with the construction schedule, and provide the documentation your stormwater inspector needs.' ),
				array( 'question' => 'Sod or hydroseed for erosion control — which is better?', 'answer' => 'Sod gives instant, fully-rooted protection and is best on steep or high-flow zones where you cannot wait for establishment. Hydroseed and hydromulch are far more cost-effective for larger or lower-stakes areas. Most plans use both.' ),
				array( 'question' => 'My backyard slope erodes every time it rains. Can you fix it?', 'answer' => 'Almost always. The key is diagnosing why the soil is moving — flow concentration, soil type, slope angle — and matching the fix to it, rather than just re-seeding the same washout. We build slope fixes that hold.' ),
				array( 'question' => 'What about detention ponds and channels?', 'answer' => 'Those are core work for us. Pond banks and channels see concentrated flow and need the stronger end of the toolkit — bonded fiber matrix, blankets, or staked sod — which we install to spec.' ),
				array( 'question' => 'How soon is the soil protected?', 'answer' => 'Sod and erosion blankets protect immediately on install. Hydromulch builds a protective layer the day it is sprayed, with vegetation filling in over the following weeks. We stage the work so the critical zones are covered first.' ),
			),
		),
		array(
			'slug'                 => 'grass-repair',
			'title'                => 'Grass Repair',
			'image'                => 'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779247753177_210d3ce5.png',
			'heading'              => 'Grass Repair & Lawn Restoration in Houston',
			'short_description'    => '<p>From brown patch and chinch bug damage to construction scarring, drought stress, and pet wear — we diagnose the cause and rebuild the area with the right grass, soil, and care plan.</p>',
			'content'              => '<p>Grass repair is about diagnosis before treatment. A thinning, patchy, or browning lawn in Houston can come from a dozen causes — chinch bugs, brown patch and take-all root rot, grub damage, drought and heat stress, dog-urine burn, compaction, drainage problems, construction scarring, or simply the wrong grass for the light the yard actually gets. Patching over the symptom without fixing the cause just buys you a few months before it comes back. We start by figuring out why the lawn failed.</p>

<p>Houston\'s climate makes lawn problems both common and fast-moving. The same heat, humidity, and heavy rainfall that grow grass quickly also drive fungal disease and pest outbreaks, and our heavy clay soils compact and drain poorly, stressing roots until a thin spot becomes a bare patch. Many of the lawns we are called to repair are fundamentally healthy and just need the underlying issue corrected — a drainage fix, an aeration and topdress, a pest treatment, or the right shade-tolerant variety in the spots under the trees.</p>

<p>Once we know the cause, we rebuild the affected area to match the surrounding lawn — removing dead turf and thatch, correcting the soil and grade where needed, and patching with fresh sod or, for larger areas, hydroseed in the same grass type so it blends in as it establishes. Where the existing variety is simply wrong for the conditions, we will say so and recommend the change rather than reinstalling something that will fail again.</p>

<p>We repair residential lawns, HOA common areas, and commercial frontages across Greater Houston — from a single chinch-bug-killed front yard in a Bermuda neighborhood to shaded St. Augustine patches under mature live oaks to high-traffic wear in family back yards and commercial entries.</p>',
			'what_drives_the_cost' => '<p>Grass repair pricing depends on the size of the damaged area and, more importantly, on the cause. A straightforward sod patch on healthy soil is inexpensive; a repair that requires drainage correction, soil amendment, aeration, or pest treatment to keep it from recurring costs more because it is solving the real problem, not just covering it.</p>

<p>Grass type matters too — matching a repair to an existing St. Augustine, Bermuda, or Zoysia lawn means sourcing the same variety so it blends. Larger restoration areas often pencil out better as hydroseed than sod, and we will quote whichever makes sense for the size and visibility of the area.</p>

<p>As a planning reference, small patch jobs are billed at a modest visit-plus-materials rate, while full-area restorations are priced like a scaled-down install. Because the durable fix depends on diagnosing the cause, we assess the lawn on-site before quoting — a cheap patch that fails in six months is not a savings.</p>',
			'whats_included'       => array(
				'Diagnosis of disease, pest, and soil issues',
				'Targeted sod patching and seed restoration',
				'Soil amendment and aeration where needed',
				'Care plan tailored to your existing grass type',
			),
			'key_features'         => array(
				array( 'heading' => 'Homeowners with patchy or thinning lawns', 'content' => 'Bare spots, thinning turf, and discoloration diagnosed and repaired with the underlying cause corrected, not just covered over.' ),
				array( 'heading' => 'Pest and disease damage', 'content' => 'Chinch bug, grub, brown patch, and take-all damage treated and the killed areas rebuilt with matching grass.' ),
				array( 'heading' => 'Pet-damaged and high-traffic yards', 'content' => 'Urine-burn spots and worn play areas repaired, with durable grass choices for households with dogs and kids.' ),
				array( 'heading' => 'Construction and remodel scarring', 'content' => 'Lawn areas torn up by builders, trenching, or equipment restored to match the surrounding turf.' ),
				array( 'heading' => 'Drainage and compaction problems', 'content' => 'Lawns failing from standing water or compacted clay fixed at the soil level so the repair actually holds.' ),
				array( 'heading' => 'HOA and commercial property managers', 'content' => 'Common-area and frontage touch-ups and restorations that keep properties looking maintained.' ),
			),
			'how_it_works'         => array(),
			'faqs'                 => array(
				array( 'question' => 'Why does my grass keep dying in the same spot?', 'answer' => 'A recurring dead spot almost always means an unaddressed cause — poor drainage, compaction, a pest colony, disease in the soil, or too much shade for that grass type. Repairing the turf without fixing the cause just resets the clock. We diagnose the root issue first.' ),
				array( 'question' => 'Can you match my existing grass?', 'answer' => 'Yes. We identify your variety — St. Augustine (Raleigh, Palmetto), Bermuda, or Zoysia — and source the same type so the repair blends with the surrounding lawn as it establishes.' ),
				array( 'question' => 'Is it brown patch, chinch bugs, or just heat?', 'answer' => 'Those look similar but are treated very differently, which is why diagnosis matters. We check the pattern, the soil, and the roots on-site to identify the actual cause before recommending a fix.' ),
				array( 'question' => 'Should I repair or replace the whole lawn?', 'answer' => 'If most of the lawn is healthy and the problem is localized, repair is the smart call. If the turf is more than roughly half gone, or the wrong variety throughout, a full replacement usually costs less in the long run. We will give you an honest read.' ),
				array( 'question' => 'Do you fix pet damage?', 'answer' => 'Yes — we repair urine-burn spots and worn paths, and can recommend more durable grass and layout choices for yards with dogs.' ),
				array( 'question' => 'How long until the repair blends in?', 'answer' => 'Sod patches knit in over 2–3 weeks and blend over a full growing season. Hydroseeded repairs take a few weeks longer to establish but blend well once mature. Matching the grass type is what makes the seam disappear.' ),
			),
		),
		array(
			'slug'                 => 'rye-grass-overseeding',
			'title'                => 'Rye Grass Overseeding',
			'image'                => 'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779247750460_0f39badc.png',
			'heading'              => 'Winter Rye Grass Overseeding in Houston',
			'short_description'    => '<p>Annual or perennial ryegrass overseeding keeps your Bermuda or Zoysia lawn lush and green through Houston winters, with a clean spring transition back to your base turf.</p>',
			'content'              => '<p>Rye grass overseeding is how you keep a green lawn through a Houston winter. Our warm-season grasses — Bermuda, Zoysia, and to a lesser extent St. Augustine — go dormant and tan when soil temperatures drop, typically from December through February. Overseeding with annual or perennial ryegrass lays a temporary cool-season lawn over the top: it germinates fast, greens up while the base grass sleeps, and then transitions out in spring as the warm-season turf wakes back up.</p>

<p>Timing is everything. The ideal window in Houston is roughly mid-October to mid-November — late enough that the heat will not cook the young ryegrass, early enough that it establishes before the first real cold. Seed too early and the warm-season grass competes while the heat stresses the seedlings; too late and germination stalls. We schedule overseeding to the season and to your specific lawn\'s condition.</p>

<p>The grass you choose changes the result. Annual ryegrass is the economical option — fast, bright green, and gone by late spring. Perennial ryegrass is finer-bladed, darker, and more durable through the season, with a cleaner look that suits higher-end properties. Either way, the work is in the prep and the transition: proper mowing height, seed-to-soil contact, the right starter fertility, and a managed spring transition so the rye fades cleanly instead of fighting your Bermuda or Zoysia as it greens up.</p>

<p>We overseed residential lawns, HOA entries and common areas, and commercial frontages across Greater Houston for clients who want curb appeal year-round — and we manage the full cycle, from the fall seeding to the spring transition back to the base turf.</p>',
			'what_drives_the_cost' => '<p>Overseeding is priced by area and by seed choice. Perennial ryegrass costs more than annual ryegrass per pound but lasts longer and looks finer, so the right pick depends on how the lawn is used and how it should look. Seeding rate, any aeration or dethatching to improve seed-to-soil contact, and starter fertility round out the number.</p>

<p>It is an annual program, not a one-time install — the rye is temporary by design and the lawn needs overseeding each fall to stay green through winter. Many residential and commercial clients set it up as a recurring seasonal service so the timing and transition are handled for them.</p>

<p>As a planning reference, overseeding is one of the more affordable lawn services per square foot since there is no demolition or grading involved — it is seed, prep, and timing. We confirm the rate and the right blend after looking at your lawn\'s grass type and condition.</p>',
			'whats_included'       => array(
				'October–November ideal application window',
				'Premium rye blends with rapid germination',
				'Coordinated mowing and fertility transition',
				'Residential and commercial property programs',
			),
			'key_features'         => array(
				array( 'heading' => 'Homeowners who want a green winter lawn', 'content' => 'Annual or perennial ryegrass over dormant Bermuda or Zoysia so the yard stays green from fall through spring.' ),
				array( 'heading' => 'HOA entries and common areas', 'content' => 'Year-round curb appeal on the entries, medians, and amenity areas that represent the community through winter.' ),
				array( 'heading' => 'Commercial frontages and retail', 'content' => 'Green, maintained-looking frontage through the off-season when competitors\' lawns go tan.' ),
				array( 'heading' => 'Bermuda and Zoysia lawns', 'content' => 'Warm-season turf that goes dormant in winter is the ideal candidate for cool-season overseeding.' ),
				array( 'heading' => 'Properties needing a clean spring transition', 'content' => 'A managed transition so the rye fades out cleanly instead of competing with the base grass as it greens up.' ),
				array( 'heading' => 'Recurring seasonal-program clients', 'content' => 'Set-and-forget fall overseeding handled on schedule every year, including the spring transition.' ),
			),
			'how_it_works'         => array(),
			'faqs'                 => array(
				array( 'question' => 'When should I overseed in Houston?', 'answer' => 'Mid-October to mid-November is the sweet spot — warm enough for fast germination, cool enough that the heat will not stress the seedlings and the dormant warm-season grass will not out-compete them. We schedule to the season and your lawn\'s condition.' ),
				array( 'question' => 'Annual or perennial ryegrass — which should I use?', 'answer' => 'Annual rye is cheaper, bright green, and gone by late spring — great for budget winter color. Perennial rye is finer, darker, and more durable through the season, with a more refined look for higher-end lawns. We will match it to your goals.' ),
				array( 'question' => 'Will overseeding hurt my Bermuda or Zoysia?', 'answer' => 'Not when it is done and transitioned correctly. The rye lives over the top while the base grass is dormant. The key is a managed spring transition — proper mowing and timing — so the rye fades as the warm-season turf wakes up, instead of competing with it.' ),
				array( 'question' => 'How soon does it green up?', 'answer' => 'Ryegrass germinates fast — you will typically see green in 5–10 days and a full stand within a few weeks, carrying the lawn through the winter months.' ),
				array( 'question' => 'Do I need to overseed every year?', 'answer' => 'Yes — rye is a temporary cool-season grass by design. To keep a green winter lawn you overseed each fall. Many clients set it up as a recurring seasonal service.' ),
				array( 'question' => 'Does overseeding work on St. Augustine?', 'answer' => 'It is most effective on Bermuda and Zoysia, which go fully dormant. St. Augustine holds color longer and is more easily damaged by the overseeding and transition process, so we usually recommend it selectively — we will advise based on your lawn.' ),
			),
		),
		array(
			'slug'                 => 'lawn-replacement',
			'title'                => 'Lawn Replacement',
			'image'                => 'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779247846886_7f420ca0.jpg',
			'heading'              => 'Full Lawn Replacement & Reinstallation',
			'short_description'    => '<p>When repair won\'t cut it, a full lawn replacement gives you a clean slate — old turf removed, soil corrected, drainage addressed, and fresh sod or hydroseed installed to spec.</p>',
			'content'              => '<p>Lawn replacement is the full-reset option: when a yard is too far gone to repair, we remove the old turf entirely, fix what made it fail, and install a fresh lawn built to last. It is the right call when more than roughly half the lawn is dead or weed-choked, when the existing grass is simply wrong for the conditions, or when underlying soil and drainage problems mean patching would just fail again. Done properly, a replacement is not just new grass — it is a corrected foundation.</p>

<p>What separates a replacement that thrives from one that repeats the old lawn\'s fate is the work underneath. We strip the failing turf, weeds, and thatch, then assess what actually went wrong: compacted or contaminated soil, poor drainage, the wrong grade, too much shade for the old variety. Houston\'s heavy gumbo clay and violent rainfall punish lawns installed straight onto raw, uncorrected ground — so where the soil tests poor we amend it, and where water ponds we correct the drainage and regrade before any new grass goes down.</p>

<p>Then we install the right lawn for the lot. That usually means fresh sod — St. Augustine for shade, Bermuda for full sun and traffic, Zoysia as a premium upgrade — and for larger properties, often a hybrid of sod in the high-visibility zones and engineered hydroseed across the rest. Grass selection is matched to your actual sun, soil, and how you use the yard, not to whatever was there before.</p>

<p>We replace lawns for homeowners, HOAs, and commercial properties across Greater Houston — tired front yards that never recovered, post-construction yards left in ruins, and properties where years of patching finally stopped being worth it.</p>',
			'what_drives_the_cost' => '<p>Replacement pricing is driven mostly by the prep, not the grass. Removing and hauling old turf, correcting soil, fixing drainage, and regrading are what separate a replacement from a simple sod drop — and they vary widely depending on what shape the existing yard is in. The grass variety matters, but on most jobs it is only a fraction of the total; the foundation work is the rest.</p>

<p>Grass choice and method affect the number: Bermuda is the most affordable, St. Augustine sits in the middle, and premium Zoysia is a step up. On larger properties a hybrid sod-plus-hydroseed plan can cut the cost substantially versus all-sod while keeping the high-visibility zones premium — we quote both side by side when the lot is big enough to matter.</p>

<p>As a planning reference, a full replacement is priced like a new install plus removal and any corrective work — meaningfully more than a repair, but the right investment when the old lawn was never going to recover. Because so much depends on the prep the specific yard needs, we always assess on-site before quoting.</p>',
			'whats_included'       => array(
				'Removal of failing turf and thatch',
				'Soil testing, amendment, and re-grading',
				'Drainage correction where required',
				'Fresh install with full establishment plan',
			),
			'key_features'         => array(
				array( 'heading' => 'Homeowners with failed front yards', 'content' => 'Tired, weed-choked, or mostly-dead lawns torn out and rebuilt on a corrected foundation — the lawn that should have been there.' ),
				array( 'heading' => 'Post-construction and remodel properties', 'content' => 'Yards left as rutted, compacted, debris-strewn dirt after a build, brought to final grade and installed fresh.' ),
				array( 'heading' => 'Lawns with the wrong grass', 'content' => 'Shade-failing Bermuda or sun-starved St. Augustine replaced with the variety the lot\'s light and soil actually call for.' ),
				array( 'heading' => 'Properties with drainage or soil problems', 'content' => 'Yards that fail no matter what is planted, fixed at the soil and grade level before new turf goes in.' ),
				array( 'heading' => 'HOAs and commercial properties', 'content' => 'Common areas and frontages past the point of repair restored to a clean, maintained standard.' ),
				array( 'heading' => 'Large and acreage properties', 'content' => 'Full resets at scale using a hybrid sod-plus-hydroseed plan to balance curb appeal and cost.' ),
			),
			'how_it_works'         => array(),
			'faqs'                 => array(
				array( 'question' => 'Repair or full replacement — how do I know?', 'answer' => 'If most of the lawn is healthy and the problem is localized, repair is smarter. If more than about half is dead or weed-choked, the grass is wrong for the conditions, or soil and drainage problems keep killing it, replacement costs less over time. We will give you an honest assessment.' ),
				array( 'question' => 'Do you remove the old lawn?', 'answer' => 'Yes — full replacement includes stripping and hauling the old turf, weeds, and thatch. Skipping that step is why re-sod jobs fail, so it is built into the process.' ),
				array( 'question' => 'Will you fix what killed the old lawn?', 'answer' => 'That is the whole point of a replacement. We diagnose the cause — compaction, drainage, grade, soil, or shade — and correct it before installing, so the new lawn is not set up to fail the same way.' ),
				array( 'question' => 'Sod or hydroseed for a replacement?', 'answer' => 'Sod gives an instant finished lawn and suits most residential yards. On larger properties a hybrid — sod in the visible zones, hydroseed across the rest — often makes more sense on cost. We will quote what fits your lot.' ),
				array( 'question' => 'How long does a replacement take?', 'answer' => 'Most residential replacements are completed in a few days depending on removal and prep needs; larger or drainage-heavy jobs take longer. New sod is walkable in about three weeks and fully established in around six.' ),
				array( 'question' => 'What grass should the new lawn be?', 'answer' => 'Matched to your lot: St. Augustine for meaningful shade, Bermuda for full sun and heavy traffic, Zoysia as a premium upgrade. We recommend based on your actual sun, soil, and use — not on what was there before.' ),
			),
		),
		array(
			'slug'                 => 'acreage-estate-turf-installation',
			'title'                => 'Acreage & Estate Sod Installation',
			'image'                => 'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779247810390_08d59034.png',
			'heading'              => 'Acreage & Estate Sod Installation Across Greater Houston',
			'short_description'    => '<p>The high-end acreage segment is where our hybrid sod-and-hydroseed model shines. Premium sod near the home and amenities, engineered hydroseeding across outer pasture and low-traffic zones, all from one experienced team.</p>',
			'content'              => '<p>Acreage and estate properties are the segment GrassHouston was built to serve, and it is where our hybrid sod-and-hydroseed model delivers the most value. A 5-acre Fulshear estate, a 14-acre Magnolia ranch, an 80-acre Conroe horse property — at this scale, full-sod installs almost never pencil out, and full-hydroseed installs do not deliver the curb appeal these properties require. The right answer is engineered zoning: premium sod where the eye and the lifestyle actually land, hydroseeding everywhere else.</p>

<p>Acreage installs require a different kind of expertise than suburban lots. Equipment access matters — can we get a hydroseeder truck within 200 feet of every zone, or do we need long hose runs and bigger pumps? Soil varies dramatically across a single property — the front three acres of a Magnolia estate can sit on sandy loam while the back eleven acres sit on clay-influenced bottomland. Drainage matters at scale — water that pools on a quarter-acre lot is annoying; water that pools across two acres of pasture is a turf killer. We bring the equipment, the experience, and the planning discipline these properties demand.</p>

<p>Beyond the lawn itself, acreage projects almost always require coordination with adjacent crews: land-clearing teams that come in before we do, grading contractors who set the final elevations, irrigation companies that pull water to outer zones, and (on equestrian and ranch properties) fence and paddock installers whose timelines interlock with ours. We have run these projects often enough to know how to sequence them — and how to absorb the inevitable schedule slips from upstream crews without throwing the lawn timeline off.</p>

<p>We have delivered acreage and estate installs across Fulshear, Magnolia, Conroe, Willis, Hockley, Cypress, Richmond, and Tomball — properties ranging from 1-acre custom homesites to 100+ acre working ranches. The crew, the equipment, and the warranty are constant; the seed and sod blends and the prep discipline get tuned to each property.</p>',
			'what_drives_the_cost' => '<p>Acreage and estate pricing is always custom — the per-acre rate on a 2-acre Fulshear estate is meaningfully higher than the per-acre rate on a 60-acre Hockley ranch, because mobilization, prep, and crew time amortize differently at different scales. We do not quote acreage projects from a phone call or a per-square-foot rate; the only honest quote is one written after a site walk.</p>

<p>The sod-to-hydroseed ratio is the biggest cost lever, and it is calibrated during the walk. A 5-acre estate might split 0.5 acre sod / 4.5 acres hydroseed (~10/90); a luxury 2-acre custom home might split 1 acre sod / 1 acre hydroseed (50/50). Other meaningful drivers: equipment access to outer zones, the amount of land-clearing or grading needed before our crew arrives, drainage corrections, and any erosion-control or SWPPP requirements on slopes and pond banks.</p>

<p>As a planning reference: typical acreage and estate projects in the Houston market range from roughly $5,000 on smaller 1-acre hybrid hometis to $75,000+ on large multi-acre estate and ranch projects, with working-ranch and commercial-acreage installs quoted as custom bids. The dollars sound large until you compare them to a full-sod approach on the same property — at which point most owners write the check happily.</p>',
			'whats_included'       => array(
				'Properties from 1 acre to 100+ acres',
				'Hybrid zoning to maximize appearance and budget',
				'Ranch entries, equestrian areas, and pond banks',
				'Coordinated with land-clearing and grading crews',
			),
			'key_features'         => array(
				array( 'heading' => 'Estate homeowners (1–20+ acres)', 'content' => 'Hybrid sod-and-hydroseed plan tuned to your property — premium sod near the home, engineered hydroseed across outer pasture and outer-fence-line acreage.' ),
				array( 'heading' => 'Custom-build estate projects', 'content' => 'Coordinated with your builder, landscape architect, and irrigation contractor at the final-grade stage. We come in as the closing-week lawn install on multi-million-dollar custom builds.' ),
				array( 'heading' => 'Ranch and equestrian properties', 'content' => 'Sodded entries, paddock perimeters, turn-out gates, and barn surrounds; hydroseeded pasture and rotation zones with grazing-ready Bermuda blends.' ),
				array( 'heading' => 'Lake-adjacent and pond-front estates', 'content' => 'Sod near the home and dock approach, hydroseed across outer slopes, bonded fiber matrix on pond banks. We coordinate with our erosion-control crews on shoreline work.' ),
				array( 'heading' => 'Master-planned community common areas at scale', 'content' => 'Entry features, monument zones, and amenity centers get sod for review-clearing curb appeal; medians and outer parks get hydroseed for sustainable HOA budgets.' ),
				array( 'heading' => 'Land-development and acreage subdivision closeouts', 'content' => 'Multi-lot hybrid programs for developers turning rural acreage into subdivided estate lots — coordinated grass install across an entire phase or filing.' ),
			),
			'how_it_works'         => array(
				array( 'heading' => 'Property walk-through and aerial review', 'content' => 'We walk the property with the owner (or property manager), pull the aerial, and dig soil samples in 3–6 representative zones to map variability before quoting.' ),
				array( 'heading' => 'Zone map and hybrid plan', 'content' => 'A property map with sod zones, hydroseed zones, drainage corrections, and access notes — quoted line-item so you see exactly where the dollars go.' ),
				array( 'heading' => 'Coordination with adjacent crews', 'content' => 'We sequence with your land-clearing, grading, irrigation, and fencing contractors so the lawn install lands at the right moment in the project calendar.' ),
				array( 'heading' => 'Multi-phase site prep', 'content' => 'Demo, soil amendment, grading, and drainage corrections in the sod zones; light tillage, weed clearance, and surface prep across hydroseed zones — all in coordinated phases.' ),
				array( 'heading' => 'Sod and hydroseed install', 'content' => 'Premium sod zones installed first for instant finished appearance; truck-mounted hydroseed application across the outer acreage. Bonded fiber matrix or blanket overlay on any slope or pond bank that requires it.' ),
				array( 'heading' => 'Establishment walks and 90-day warranty', 'content' => 'Establishment walks at day 14, day 30, and day 90 — uniform stand verification, thin-spot re-application under warranty, and a custom long-term care calendar for the owner or property manager.' ),
			),
			'faqs'                 => array(
				array( 'question' => 'Is hybrid sod + hydroseed always the right answer for acreage?', 'answer' => 'For 90%+ of properties over an acre, yes. The exceptions are: (1) very small acreage where the back zones are also visible and high-traffic, in which case full sod might be justified, and (2) raw working ranches where the entire property is functionally pasture, in which case full hydroseed is the right answer. We will tell you honestly which bucket your property falls in.' ),
				array( 'question' => 'How long does an acreage install take to complete?', 'answer' => 'Most 1–5 acre hybrid projects complete in 3–7 working days, weather permitting. Larger 10–20+ acre projects run 2–4 weeks depending on prep and coordination with adjacent crews. The sod component installs in 1–3 days; the hydroseed component completes in 1–2 days; the rest is prep, grading, drainage, and clean-up.' ),
				array( 'question' => 'Can you coordinate with my builder, irrigation, and fence contractor?', 'answer' => 'Yes — and we expect to. Acreage projects almost always involve sequencing with 2–4 other trades. We work directly with builder superintendents, landscape architects, irrigation contractors, and fence installers to slot the lawn install at the right moment without delaying other work.' ),
				array( 'question' => 'How do you handle drainage on a multi-acre property?', 'answer' => 'We map low spots and water flow during the walk-through, and we coordinate corrections with civil-engineering and earthwork crews when the property needs them. On smaller drainage issues we install French drains, channel drains, and surface re-grading directly. On larger drainage redesigns we partner with engineering firms.' ),
				array( 'question' => 'Do you handle pond banks, slopes, and erosion-control zones?', 'answer' => 'Yes. Pond banks, drainage easements, and slopes steeper than 3:1 typically get bonded fiber matrix hydromulch with erosion-control blanket overlay, sequenced as part of the broader install. We are bondable and routinely work with civil engineers on SWPPP-permitted acreage.' ),
				array( 'question' => 'What seed and sod varieties do you use for acreage?', 'answer' => 'Sod zones near the home almost always get St. Augustine (Palmetto or ProVista for shade tolerance) or Zoysia (Zeon for premium estates). Hydroseed zones across pasture and outer acreage get Bermuda blends (Sahara, Princess, common) — sometimes paired with annual rye as a quick-cover nurse crop. We tune the blend to your sun, soil, and use case.' ),
				array( 'question' => 'Do you do equestrian properties?', 'answer' => 'Regularly. Equestrian work has its own quirks — paddock perimeters need traffic-tolerant Bermuda, turn-out zones need grazing-safe seed blends (no toxic species), and barn surrounds often need premium sod for the show-property look. We have installed across enough equestrian properties to plan for all of it.' ),
				array( 'question' => 'Is there a warranty on acreage installs?', 'answer' => 'Yes — every acreage project includes establishment walks at day 14, day 30, and day 90, plus a workmanship warranty on the install itself. Thin spots in the hydroseed zones are re-applied under warranty; sod that fails to establish from anything other than clear neglect is replaced.' ),
			),
		),
		array(
			'slug'                 => 'sports-turf-installation',
			'title'                => 'Sports Field Sod Installation',
			'image'                => 'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779247828949_b4bf2d2f.jpg',
			'heading'              => 'Sports Field Sod Installation for Houston Fields & Facilities',
			'short_description'    => '<p>Bermuda-dominant athletic sod installs for schools, leagues, parks, and private facilities — including base prep, laser grading, and post-install grow-in protocols.</p>',
			'content'              => '<p>An athletic field is a different discipline than a lawn. It has to absorb cleats, pivots, and concentrated traffic, shed a Houston downpour fast enough to stay playable, and present a uniform, safe surface every time it is used. We build Bermuda-dominant fields engineered for play and safety first — not just for appearance — for schools, leagues, municipal parks, and private facilities across Greater Houston.</p>

<p>The base is everything on a field. We laser-grade the surface to precise planes with the right crown or slope so water sheets off instead of ponding, prepare and amend the root zone, and finish-grade out the low spots that hold water and the high spots that scalp under a mower. Get the base and drainage right and the field plays true and recovers fast; get it wrong and no grass variety will save it.</p>

<p>Variety selection is matched to the sport, the budget, and the maintenance capacity. Tifway 419 Bermuda is the workhorse for Houston athletic fields — dense, aggressive in recovery, and highly traffic tolerant. Where a facility wants faster wear recovery or better drought performance, we step up to premium cultivars like TifTuf or Celebration. Every field is specified to how it will actually be used.</p>

<p>Grow-in is where fields are won or lost, so every install comes with a grow-in fertility and irrigation program to bring the new sod to a mature, knitted, game-ready stand. For warm-season Bermuda fields we also offer winter rye overseeding to hold color and playability through the dormant months, with a managed spring transition back to the Bermuda base.</p>',
			'what_drives_the_cost' => '<p>Field pricing is driven far more by base preparation than by the sod itself. Laser grading, root-zone work, and drainage corrections are the real cost variables — a flat, well-drained existing base is a fraction of the cost of a field that needs regrading, a rebuilt crown, or a new root zone before any grass goes down.</p>

<p>Grass variety and field size move the number next: Tifway 419 versus premium cultivars, and a full field versus a practice area or a renovation-and-overseed program. Irrigation condition and whether the grow-in fertility program is bundled in also factor into the quote.</p>

<p>Because a field lives or dies on its base and drainage design, we never quote one over the phone. We walk the site, review the drainage plan, and price the specific grading, prep, and sod the field actually needs to play safe and last.</p>',
			'whats_included'       => array(
				'Tifway 419 and premium athletic Bermuda varieties',
				'Laser grading and base preparation',
				'Grow-in fertility and irrigation guidance',
				'Renovation and overseed programs',
			),
			'key_features'         => array(
				array( 'heading' => 'Schools and ISD athletic departments', 'content' => 'Game and practice fields built to a safe, uniform, durable standard and scheduled around the school calendar and season.' ),
				array( 'heading' => 'Youth and adult sports leagues', 'content' => 'High-traffic league fields with the wear tolerance and fast Bermuda recovery a full season of play demands.' ),
				array( 'heading' => 'Municipal parks and recreation', 'content' => 'Public multi-use athletic fields installed by an insured, bondable crew with clean documentation and warranty walks.' ),
				array( 'heading' => 'Private and club sports facilities', 'content' => 'Premium Bermuda playing surfaces for academies, training centers, and club programs that need a show-quality field.' ),
				array( 'heading' => 'Field renovation and overseed', 'content' => 'Tired or worn fields re-leveled, re-sodded in the worn zones, and overseeded back to a playable standard without a full rebuild.' ),
				array( 'heading' => 'Multi-field complexes', 'content' => 'Phased installs across multiple fields, sequenced around your play schedule and maintenance windows.' ),
			),
			'how_it_works'         => array(),
			'faqs'                 => array(
				array( 'question' => 'What grass do you use for Houston sports fields?', 'answer' => 'Tifway 419 Bermuda is the default — dense, traffic tolerant, and aggressive in recovery. Where budget and performance goals justify it we step up to premium cultivars like TifTuf or Celebration. Bermuda is the right family for athletic play in our climate.' ),
				array( 'question' => 'Why is laser grading important for a field?', 'answer' => 'A field has to drain and play uniformly. Laser grading sets precise surface planes and the correct crown or slope so water sheets off instead of ponding, and so there are no low or high spots that affect play, mowing, or player safety.' ),
				array( 'question' => 'How long before a new field is playable?', 'answer' => 'New Bermuda sod knits in over about 2–3 weeks and is typically game-ready in roughly 4–8 weeks depending on season, reaching full maturity over a growing season. We time installs so the field is ready for your season opener.' ),
				array( 'question' => 'Can you renovate an existing field instead of a full rebuild?', 'answer' => 'Often, yes. If the base and drainage are sound, we can re-level, re-sod the worn zones, and overseed the field back to standard for far less than a full rebuild. We assess the base and drainage first and tell you honestly which the field needs.' ),
				array( 'question' => 'Do you keep fields green through winter?', 'answer' => 'Yes. Warm-season Bermuda goes dormant and tan in winter, so we offer rye overseeding to hold color and playability through the off-season, followed by a managed spring transition back to the Bermuda base.' ),
				array( 'question' => 'Do you install for schools and municipalities?', 'answer' => 'Yes. We are licensed, insured, and bondable, run dedicated crews for bid-document work, and provide the scheduling discipline and documentation that public and institutional projects require.' ),
			),
		),
		array(
			'slug'                 => 'new-construction-turf',
			'title'                => 'New Construction Sod Installation',
			'image'                => 'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779247846886_7f420ca0.jpg',
			'heading'              => 'New Construction Sod Installation',
			'short_description'    => '<p>We come in at final grade with the equipment, manpower, and scheduling discipline production and custom builders need to close out lots cleanly and on time.</p>',
			'content'              => '<p>New-construction lawns live or die on timing and final grade. We are built to come in at the closeout stage of a build — production or custom — with the crews, equipment, and scheduling discipline to take a lot from rough, compacted construction dirt to a finished lawn without holding up the closing.</p>

<p>The hard part of a new build is the soil. After construction the yard is compacted, often stripped of its topsoil, littered with debris, and rarely at a finished grade that drains. We handle final-grade soil prep, debris clean-up, topsoil and amendment where the spec calls for it, and the drainage corrections that keep the new lawn — and the builder\'s warranty — out of trouble down the road.</p>

<p>Then we install whatever the plan and lot call for: sod for instant finished curb appeal on the visible lot, engineered hydroseed for larger or acreage lots, or a hybrid of both. Grass variety is matched to the lot\'s sun exposure and the builder\'s spec — St. Augustine for shade, Bermuda for full sun and traffic, Zoysia as a premium upgrade.</p>

<p>We run production-builder programs — repeatable, scheduled lot closeouts across an entire community — as well as one-off custom-home installs, coordinating with the superintendent, irrigation contractor, and landscape crews so the lawn drops into the right slot in the closing schedule. Across Greater Houston, on the builder\'s calendar, not a gardening one.</p>',
			'what_drives_the_cost' => '<p>New-construction pricing is driven mostly by the shape the lot is left in. A lot delivered at clean final grade with topsoil is a straightforward sod drop; a compacted, debris-strewn, off-grade lot needs clean-up, soil prep, amendment, and drainage work before any grass goes down — and that prep is the real cost variable.</p>

<p>Method and variety move the number next: sod versus hydroseed versus a hybrid plan, and St. Augustine versus Bermuda versus premium Zoysia. On larger and acreage lots a hybrid sod-plus-hydroseed plan cuts cost substantially versus all-sod. Lot size and irrigation readiness round it out.</p>

<p>For production builders we set up program pricing across a community for predictable, repeatable closeouts; custom homes are quoted per lot. Either way we confirm the number after seeing the grade the lot is in and the spec it has to meet.</p>',
			'whats_included'       => array(
				'Production builder and custom builder programs',
				'Final-grade soil prep and clean-up',
				'Quick-turn scheduling around closings',
				'Sod, hydroseed, or hybrid per plan',
			),
			'key_features'         => array(
				array( 'heading' => 'Production builders', 'content' => 'Repeatable, scheduled lot-closeout programs across a community, with consistent quality and predictable, pre-agreed pricing.' ),
				array( 'heading' => 'Custom-home builders', 'content' => 'Final-grade lawn installs coordinated with your superintendent, irrigation, and landscape crews to land cleanly on the closing date.' ),
				array( 'heading' => 'Final-grade soil prep and clean-up', 'content' => 'Compaction relief, construction-debris removal, topsoil and amendment, and finish grading so the new lawn actually establishes.' ),
				array( 'heading' => 'Drainage correction at closeout', 'content' => 'Low spots and grade problems fixed before the sod goes down — protecting both the new lawn and the builder\'s warranty.' ),
				array( 'heading' => 'Sod, hydroseed, or hybrid per plan', 'content' => 'The right method for the lot, from instant-curb-appeal sod to engineered hydroseed across larger and acreage lots.' ),
				array( 'heading' => 'Move-in-ready handoff', 'content' => 'A finished, watered-in lawn with a simple care plan ready for the new homeowner on day one.' ),
			),
			'how_it_works'         => array(),
			'faqs'                 => array(
				array( 'question' => 'Can you hit our closing dates?', 'answer' => 'Yes — quick-turn scheduling around closings is the core of our builder programs. We slot the install into the closeout sequence and size the crew to finish on time, so the lawn never holds up a closing.' ),
				array( 'question' => 'The lot is compacted construction dirt — can you still install?', 'answer' => 'That is the normal starting condition on a new build. We relieve compaction, clear construction debris, bring in topsoil and amendment where needed, and set a finished, draining grade before any sod or seed goes down.' ),
				array( 'question' => 'Sod or hydroseed for a new build?', 'answer' => 'Sod for instant finished curb appeal on standard lots; engineered hydroseed for larger or acreage lots; and often a hybrid of both. We match the method to the lot size, visibility, and your spec.' ),
				array( 'question' => 'Do you offer program pricing for production builders?', 'answer' => 'Yes. For repeatable lot closeouts across a community we set up program pricing and a standing schedule, so every lot is predictable on both cost and timing.' ),
				array( 'question' => 'What grass will you install?', 'answer' => 'Matched to the lot and your spec: St. Augustine for shade, Bermuda for full sun and traffic, and Zoysia as a premium upgrade. We recommend per lot based on sun, soil, and use.' ),
				array( 'question' => 'Who handles watering after install?', 'answer' => 'We water in at install and leave a clear watering and care plan for the homeowner or your warranty crew. On builder programs we can coordinate the establishment hand-off so nothing falls through the cracks.' ),
			),
		),
		array(
			'slug'                 => 'hoa-common-area-turf',
			'title'                => 'HOA Common Area Sod',
			'image'                => 'https://d64gsuwffb70l.cloudfront.net/6a0d29e1debea06021d065ea_1779247828949_b4bf2d2f.jpg',
			'heading'              => 'HOA Common Area Sod Installation & Restoration',
			'short_description'    => '<p>Master-planned communities and HOAs trust us to keep entries, medians, parks, and amenity-center lawns looking like the marketing photos year after year.</p>',
			'content'              => '<p>The common areas are the face of a community — the entry monument, the boulevard medians, and the amenity-center and pool lawns are what residents drive past every day and what prospective buyers see first. We install and restore HOA common-area turf so those spaces look like the marketing photos year after year, working on the schedule and within the approval process that community management actually runs on.</p>

<p>HOA work is as much about coordination as it is about grass. Boards approve on a cycle, budgets run by fiscal year, and the common areas have to stay presentable while the work happens. We are built for it — we quote in a board-ready format, schedule around community events and irrigation windows, stage the work so entries and amenity areas are never all torn up at once, and keep the property manager in the loop from the first walk-through to the final establishment walk.</p>

<p>The right grass for a common area depends on the zone. High-visibility entries and monument beds often justify premium sod — Zoysia or a clean St. Augustine — for instant, manicured curb appeal, while large medians, parks, and outer common areas are better served by Bermuda sod or engineered hydroseed where the budget has to stretch across a lot of square footage. We map the community zone by zone and recommend where to spend and where to save.</p>

<p>We also run storm and drought recovery programs for associations. After a freeze, flood, or drought stretch the common areas are usually the first thing residents complain about — so we diagnose what failed, restore the affected zones to match the rest, and can put the community on a predictable maintenance-and-replacement cadence so the board is not reacting to an emergency every season. Across Greater Houston\'s master-planned communities, on the manager\'s schedule.</p>',
			'what_drives_the_cost' => '<p>HOA common-area pricing is driven by total area and by how many separate zones the work touches — a single entry monument is a small, premium job; a community-wide refresh across entries, medians, and amenity lawns is a staged, larger one. The sod-versus-hydroseed mix across those zones is the biggest cost lever, the same way it is on acreage.</p>

<p>Grass selection and access matter next: premium Zoysia or St. Augustine on the showcase zones versus Bermuda or hydroseed on the broad outer areas, plus how easily crews and equipment can reach medians and islands without disrupting traffic or residents. Any drainage correction, irrigation coordination, or phasing needed to keep the community presentable during the work also factors in.</p>

<p>For associations we quote in a board-ready, line-item format and can structure larger refreshes in phases to fit a fiscal-year budget. Because the right plan depends on the community\'s zones and standards, we walk the property with the manager before quoting rather than pricing common-area work over the phone.</p>',
			'whats_included'       => array(
				'Entry features, monuments, and medians',
				'Amenity center and pool-area lawns',
				'Storm and drought recovery programs',
				'Predictable scheduling for property managers',
			),
			'key_features'         => array(
				array( 'heading' => 'HOA and community association boards', 'content' => 'Board-ready, line-item proposals and a finish that keeps entries and amenities looking like the community\'s marketing photos.' ),
				array( 'heading' => 'Property and community managers', 'content' => 'Predictable scheduling, single-point coordination, and staged work that never leaves the community torn up all at once.' ),
				array( 'heading' => 'Entry features, monuments, and medians', 'content' => 'High-visibility showcase zones installed in premium sod for instant, manicured curb appeal at the front door of the community.' ),
				array( 'heading' => 'Amenity centers, pools, and parks', 'content' => 'Gathering-space lawns built to take foot traffic and stay presentable through the season and the events calendar.' ),
				array( 'heading' => 'Master-planned community developers', 'content' => 'Common-area and amenity turf across new phases, coordinated with builders, irrigation, and landscape architects.' ),
				array( 'heading' => 'Storm and drought recovery programs', 'content' => 'Post-freeze, flood, and drought restoration of common areas, with a predictable replacement cadence the board can budget around.' ),
			),
			'how_it_works'         => array(),
			'faqs'                 => array(
				array( 'question' => 'Do you provide board-ready proposals?', 'answer' => 'Yes. We quote HOA work in a clear, line-item format the board can review and approve, and we can break a larger common-area refresh into phases that fit a fiscal-year budget.' ),
				array( 'question' => 'Can you schedule around our community events and residents?', 'answer' => 'That is the norm for us. We sequence the work so entries and amenity areas are never all torn up at once, schedule around events and irrigation windows, and keep the property manager updated throughout.' ),
				array( 'question' => 'What grass do you use for community common areas?', 'answer' => 'It depends on the zone: premium Zoysia or St. Augustine on high-visibility entries and monuments for an instant manicured look, and Bermuda sod or engineered hydroseed across large medians, parks, and outer areas where the budget has to stretch.' ),
				array( 'question' => 'Do you handle just the entry, or the whole community?', 'answer' => 'Either. We do single entry-monument restorations and community-wide refreshes across entries, medians, amenity lawns, and parks — and everything in between.' ),
				array( 'question' => 'Can you restore common areas after a freeze, flood, or drought?', 'answer' => 'Yes — storm and drought recovery is a core program for us. We diagnose what failed, restore the affected zones to match the rest, and can set up a predictable maintenance-and-replacement cadence so the board is not reacting to an emergency each season.' ),
				array( 'question' => 'Do you coordinate with our irrigation and landscape vendors?', 'answer' => 'Yes. We work alongside the community\'s existing irrigation contractor and landscape maintenance crews so the new turf is watered in correctly and handed off cleanly.' ),
			),
		),
	);
}
