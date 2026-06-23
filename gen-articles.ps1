$ErrorActionPreference = 'Stop'
$articles = [System.IO.File]::ReadAllText("d:\Grass\post-type-help\articles-data.json", [System.Text.Encoding]::UTF8) | ConvertFrom-Json
$imgMap   = [System.IO.File]::ReadAllText("d:\Grass\post-type-help\image-map.json", [System.Text.Encoding]::UTF8) | ConvertFrom-Json

# Featured-image overrides for the 5 "simple" articles (no hero in source).
$heroOverride = @{
  'sod-cost-houston'            = 'home1'
  'sod-vs-hydroseeding'         = 'hydroseeding'
  'best-grass-for-houston'      = 'grass1'
  'when-to-install-sod-houston' = 'sodRoll'
  'sod-care-after-installation' = 'home2'
}

function HtmlEnc([string]$s) {
  if ($null -eq $s) { return '' }
  return ($s -replace '&','&amp;' -replace '<','&lt;' -replace '>','&gt;')
}
function PhpEsc([string]$s) {
  if ($null -eq $s) { return '' }
  return ($s -replace '\\','\\\\' -replace "'","\'")
}

$sb = New-Object System.Text.StringBuilder
[void]$sb.AppendLine('<?php')
[void]$sb.AppendLine('/**')
[void]$sb.AppendLine(' * Article dataset for the Grass Blog Importer.')
[void]$sb.AppendLine(' * AUTO-GENERATED from articles-data.json (original grasshouston.com bundle).')
[void]$sb.AppendLine(' * content_main -> post_content column; content_second -> post_content_second (ACF) + post_content meta.')
[void]$sb.AppendLine(' */')
[void]$sb.AppendLine('if ( ! defined( "ABSPATH" ) ) { exit; }')
[void]$sb.AppendLine('')
[void]$sb.AppendLine('function grass_blog_articles() {')
[void]$sb.AppendLine('	return array(')

foreach ($a in $articles) {
  # Build content chunks (one per section, or one per paragraph for simple articles).
  $chunks = @()
  if ($a.sections -and @($a.sections).Count -gt 0) {
    foreach ($s in $a.sections) {
      $c = ''
      if ($s.heading) { $c += '<h3>' + (HtmlEnc $s.heading) + '</h3>' + "`n" }
      foreach ($p in @($s.paragraphs)) { if ($p) { $c += '<p>' + (HtmlEnc $p) + '</p>' + "`n" } }
      if ($s.bullets -and @($s.bullets).Count -gt 0) {
        $c += '<ul>' + "`n"
        foreach ($bl in @($s.bullets)) { if ($bl) { $c += '<li>' + (HtmlEnc $bl) + '</li>' + "`n" } }
        $c += '</ul>' + "`n"
      }
      $chunks += $c.TrimEnd("`r", "`n")
    }
  } else {
    foreach ($p in @($a.body)) { if ($p) { $chunks += ('<p>' + (HtmlEnc $p) + '</p>') } }
  }

  # Split into two blocks at ~35% of total length (mirrors the live layout's CTA break).
  $main = ''; $second = ''
  if ($chunks.Count -le 1) {
    $main = ($chunks -join "`n`n")
  } else {
    $total = ($chunks | ForEach-Object { $_.Length } | Measure-Object -Sum).Sum
    $cum = 0; $splitIdx = 0
    for ($k = 0; $k -lt $chunks.Count; $k++) {
      $cum += $chunks[$k].Length
      if ($cum -ge 0.35 * $total) { $splitIdx = $k; break }
    }
    if ($splitIdx -ge ($chunks.Count - 1)) { $splitIdx = $chunks.Count - 2 }
    if ($splitIdx -lt 0) { $splitIdx = 0 }
    $main   = ($chunks[0..$splitIdx] -join "`n`n")
    $second = ($chunks[($splitIdx + 1)..($chunks.Count - 1)] -join "`n`n")
  }

  $hero = $a.heroImage
  if (-not $hero -and $heroOverride.ContainsKey($a.slug)) { $hero = $imgMap.($heroOverride[$a.slug]) }

  [void]$sb.AppendLine('		array(')
  [void]$sb.AppendLine("			'title' => '" + (PhpEsc $a.title) + "',")
  [void]$sb.AppendLine("			'slug' => '" + (PhpEsc $a.slug) + "',")
  [void]$sb.AppendLine("			'excerpt' => '" + (PhpEsc $a.excerpt) + "',")
  [void]$sb.AppendLine("			'category' => '" + (PhpEsc $a.category) + "',")
  [void]$sb.AppendLine("			'hero' => '" + (PhpEsc $hero) + "',")
  [void]$sb.AppendLine("			'content_main' => '" + (PhpEsc $main) + "',")
  [void]$sb.AppendLine("			'content_second' => '" + (PhpEsc $second) + "',")
  [void]$sb.AppendLine('		),')
}

[void]$sb.AppendLine('	);')
[void]$sb.AppendLine('}')

$utf8 = New-Object System.Text.UTF8Encoding($false)
$dir = "d:\Grass\post-type-help\grass-blog-importer"
[System.IO.File]::WriteAllText("$dir\articles-data.php", $sb.ToString(), $utf8)
Write-Output ("Generated articles-data.php (" + ((Get-Item "$dir\articles-data.php").Length) + " bytes), articles: " + @($articles).Count)
