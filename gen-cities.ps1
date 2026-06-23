$ErrorActionPreference = 'Stop'
$jsonText = [System.IO.File]::ReadAllText("d:\Grass\post-type-help\original-cities-data.json", [System.Text.Encoding]::UTF8)
$all = $jsonText | ConvertFrom-Json

# Non-ASCII chars built from code points so this .ps1 stays pure-ASCII (encoding-safe).
$MDASH  = [char]0x2014   # em dash
$MIDDOT = [char]0x00B7   # middle dot

# "Why customers choose us" — company-wide value props (verbatim from Houston), same on every city.
$WHYCHOOSE = @(
  'Fully licensed and insured across Greater Houston',
  '2,400+ properties installed since 2014',
  'Residential, commercial, and acreage specialists',
  "Free estimates and consultative quotes $MDASH many quoted right over the phone",
  "In-house sod and hydroseeding crews $MDASH not subcontracted",
  'Coordinated with your builder, irrigation, and HOA',
  'Workmanship warranty on every install',
  'TXDOT and municipal-spec hydroseeding blends',
  'Written scope of work on every project'
)

function PhpEsc([string]$s) {
  if ($null -eq $s) { return '' }
  return ($s -replace '\\','\\\\' -replace "'","\'")
}

# Fixed boilerplate (exactly matches the live Houston page), {CITY} substituted per city.
function GrassHtml($soilNote) {
@"
<p class="text-stone-600 mb-5 leading-relaxed">$soilNote</p>

<ul class="space-y-3 mb-6">
	<li class="flex items-start gap-3 text-stone-700">Front yard, backyard, and full-property sod</li>
	<li class="flex items-start gap-3 text-stone-700">Hydroseeding for larger residential lots</li>
	<li class="flex items-start gap-3 text-stone-700">Lawn repair and full replacement</li>
	<li class="flex items-start gap-3 text-stone-700">Builder and remodel scheduling</li>
</ul>
"@
}
function SignatureHtml($city) {
@"
<p class="text-stone-200 mb-5 leading-relaxed">For half-acre-plus properties in $city, our hybrid model delivers a finished, magazine-worthy front yard with cost-efficient coverage across the rest of the lot.</p>

<ul class="space-y-3 mb-6">
	<li class="flex items-start gap-3 text-stone-100">Premium sod in front, around home, and patio</li>
	<li class="flex items-start gap-3 text-stone-100">Engineered hydroseeding across back acreage</li>
	<li class="flex items-start gap-3 text-stone-100">Smart property zoning for maximum impact</li>
	<li class="flex items-start gap-3 text-stone-100">Scalable coverage that scales with your budget</li>
</ul>
"@
}
function CommercialHtml($city) {
@"
<p class="text-stone-600 mb-5 leading-relaxed">Builders, developers, HOAs, schools, churches, and property managers across $city count on us for bonded, insured, on-schedule sod and lawn installs.</p>

<ul class="space-y-3">
	<li class="flex items-start gap-3 text-stone-700">New construction final-grade sod installation</li>
	<li class="flex items-start gap-3 text-stone-700">Builder &amp; developer scheduling</li>
	<li class="flex items-start gap-3 text-stone-700">HOA common-area sod and restoration</li>
	<li class="flex items-start gap-3 text-stone-700">Hydroseeding for large lots and slopes</li>
	<li class="flex items-start gap-3 text-stone-700">Erosion control and SWPPP compliance</li>
</ul>
"@
}

$sb = New-Object System.Text.StringBuilder
[void]$sb.AppendLine('<?php')
[void]$sb.AppendLine('/**')
[void]$sb.AppendLine(' * City dataset for the Grass Service Areas Importer.')
[void]$sb.AppendLine(' * AUTO-GENERATED from original-cities-data.json to mirror the live Houston')
[void]$sb.AppendLine(' * page field-for-field. Regenerate via gen-cities.ps1 instead of hand-editing.')
[void]$sb.AppendLine(' */')
[void]$sb.AppendLine('if ( ! defined( "ABSPATH" ) ) { exit; }')
[void]$sb.AppendLine('')
[void]$sb.AppendLine('function grass_sai_cities() {')
[void]$sb.AppendLine('	return array(')

foreach ($c in $all) {
  $liText = (@($c.longIntro) | Where-Object { $_ -and $_.ToString().Trim() }) -join ''
  if (-not $liText) { continue }  # skip stubs (Jersey Village)

  $name = $c.city
  $slug = $c.slug -replace '-grass-sod-installation$',''
  $grasses = @($c.recommendedGrass)

  $introJoined = (@($c.intro) -join "`n`n")
  $recLine = "Recommended grass for ${name}: " + ($grasses -join ', ') + " $MIDDOT Soil profile: " + $c.soilNote
  $lawnContent = $introJoined + "`n`n" + $recLine
  $deepContent = (@($c.longIntro) -join "`n`n")
  $subHeading = "Whether you're a homeowner upgrading the front yard, a builder finishing a custom home, or an HOA restoring entry-feature lawns $MDASH GrassHouston brings the right mix of sod, hydroseeding, and hybrid solutions to ${name}, Texas."
  $installDone = "$($c.installs) installed $MIDDOT Since $($c.founded)"

  $fields = [ordered]@{
    short_heading                       = $name
    heading_text                        = "Sod Installation & Grass Establishment in ${name}, TX"
    short_description_listing_view      = $c.blurb
    short_description                   = $c.blurb
    tagline                             = '"Sod where appearance matters most. Hydroseeding where scale matters most."'
    installation_done                   = $installDone
    lawn_establishment_heading          = "Premium sod & lawn establishment for ${name}, TX properties"
    lawn_establishment_sub_heading      = $subHeading
    lawn_establishment_content          = $lawnContent
    deep_dive_heading                   = "What makes ${name} lawn installation different"
    deep_dive_content                   = $deepContent
    grass_installation_conetent         = (GrassHtml $c.soilNote).TrimEnd("`r","`n")
    signature_method_content            = (SignatureHtml $name).TrimEnd("`r","`n")
    commercial_sod_installation_content = (CommercialHtml $name).TrimEnd("`r","`n")
    why_choose_us_heading_text          = "Why ${name} customers choose GrassHouston"
  }

  [void]$sb.AppendLine('		array(')
  [void]$sb.AppendLine("			'name' => '" + (PhpEsc $name) + "', 'slug' => '" + (PhpEsc $slug) + "',")
  [void]$sb.AppendLine('			''fields'' => array(')
  foreach ($k in $fields.Keys) {
    [void]$sb.AppendLine("				'$k' => '" + (PhpEsc ([string]$fields[$k])) + "',")
  }
  [void]$sb.AppendLine('			),')
  $st = if ($grasses -contains 'St. Augustine') { '1' } else { '0' }
  $be = if ($grasses -contains 'Bermuda') { '1' } else { '0' }
  $zo = if ($grasses -contains 'Zoysia') { '1' } else { '0' }
  [void]$sb.AppendLine("			'show' => array('st_augustine'=>'$st','bermuda'=>'$be','zoysia'=>'$zo'),")
  [void]$sb.AppendLine('			''faqs'' => array(')
  foreach ($f in $c.localFaqs) {
    if (-not $f.q) { continue }
    [void]$sb.AppendLine("				array('question'=>'" + (PhpEsc $f.q) + "','answer'=>'" + (PhpEsc $f.a) + "'),")
  }
  [void]$sb.AppendLine('			),')

  # Custom metabox repeaters (theme functions.php).
  $resItems = @(@($c.whoItsFor) | Select-Object -First 4)
  $comItems = @(@($c.whoItsFor) | Select-Object -Skip 4)
  [void]$sb.AppendLine('			''meta'' => array(')
  [void]$sb.AppendLine('				''residential'' => array(')
  foreach ($w in $resItems) { [void]$sb.AppendLine("					array('heading'=>'" + (PhpEsc $w.audience) + "','content'=>'" + (PhpEsc $w.description) + "'),") }
  [void]$sb.AppendLine('				),')
  [void]$sb.AppendLine('				''commercial'' => array(')
  foreach ($w in $comItems) { [void]$sb.AppendLine("					array('heading'=>'" + (PhpEsc $w.audience) + "','content'=>'" + (PhpEsc $w.description) + "'),") }
  [void]$sb.AppendLine('				),')
  [void]$sb.AppendLine('				''lawn_challenges'' => array(' + ((@($c.challenges) | ForEach-Object { "'" + (PhpEsc $_) + "'" }) -join ',') + '),')
  [void]$sb.AppendLine('				''we_install_in'' => array(')
  foreach ($n in $c.neighborhoodDeepDive) { [void]$sb.AppendLine("					array('title'=>'" + (PhpEsc $n.name) + "','content'=>'" + (PhpEsc $n.note) + "'),") }
  [void]$sb.AppendLine('				),')
  [void]$sb.AppendLine('				''areas_we_serve'' => array(' + ((@($c.neighborhoods) | ForEach-Object { "'" + (PhpEsc $_) + "'" }) -join ',') + '),')
  [void]$sb.AppendLine('				''growth_areas'' => array(' + ((@($c.landmarks) | ForEach-Object { "'" + (PhpEsc $_) + "'" }) -join ',') + '),')
  [void]$sb.AppendLine('				''why_choose'' => array(' + (($WHYCHOOSE | ForEach-Object { "'" + (PhpEsc $_) + "'" }) -join ',') + '),')
  [void]$sb.AppendLine('			),')
  [void]$sb.AppendLine('		),')
}

[void]$sb.AppendLine('	);')
[void]$sb.AppendLine('}')

$utf8 = New-Object System.Text.UTF8Encoding($false)
[System.IO.File]::WriteAllText("d:\Grass\post-type-help\grass-service-areas-importer\cities-data.php", $sb.ToString(), $utf8)
$included = (@($all | Where-Object { (@($_.longIntro) | Where-Object { $_ -and $_.ToString().Trim() }) -join '' })).Count
Write-Output ("Generated cities-data.php  (" + ((Get-Item 'd:\Grass\post-type-help\grass-service-areas-importer\cities-data.php').Length) + " bytes), cities: " + $included)
