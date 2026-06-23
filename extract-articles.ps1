$ErrorActionPreference = 'Stop'
$BT = [char]96  # backtick
$b = [System.IO.File]::ReadAllText("d:\Grass\post-type-help\original-bundle.js", [System.Text.Encoding]::UTF8)

function Skip-Str($s, $i) {        # $s[$i] is a quote (" or `). Returns index past closing quote.
  $q = $s[$i]; $i++
  while ($i -lt $s.Length) {
    if ($s[$i] -eq '\') { $i += 2; continue }
    if ($s[$i] -eq $q) { return $i + 1 }
    $i++
  }
  return $i
}
function Match-Bracket($s, $i, $open, $close) {   # $s[$i] is $open. Returns index of matching close.
  $depth = 0
  while ($i -lt $s.Length) {
    $ch = $s[$i]
    if ($ch -eq '"' -or $ch -eq $BT) { $i = Skip-Str $s $i; continue }
    if ($ch -eq $open) { $depth++ }
    elseif ($ch -eq $close) { $depth--; if ($depth -eq 0) { return $i } }
    $i++
  }
  return -1
}
function Unescape($s) {
  $s -replace '\\"','"' -replace ('\\' + $BT), $BT -replace '\\n',"`n" -replace '\\\\','\'
}
function Get-Strings($region) {    # tokenize all "..." and `...` strings in order
  $out = @(); $i = 0
  while ($i -lt $region.Length) {
    $ch = $region[$i]
    if ($ch -eq '"' -or $ch -eq $BT) {
      $end = Skip-Str $region $i
      $out += (Unescape $region.Substring($i + 1, $end - $i - 2))
      $i = $end
    } else { $i++ }
  }
  return @($out)
}
function Split-Objects($region) {  # top-level { ... } objects within an array region
  $objs = @(); $i = 0
  while ($i -lt $region.Length) {
    if ($region[$i] -eq '{') { $j = Match-Bracket $region $i '{' '}'; $objs += $region.Substring($i, $j - $i + 1); $i = $j + 1 }
    else { $i++ }
  }
  return @($objs)
}
function Get-FieldString($obj, $field) {   # value of field that is a "..." or `...` string
  $m = [regex]::Match($obj, [regex]::Escape($field) + ':')
  if (-not $m.Success) { return '' }
  $i = $m.Index + $m.Length
  while ($i -lt $obj.Length -and $obj[$i] -eq ' ') { $i++ }
  if ($obj[$i] -ne '"' -and $obj[$i] -ne $BT) { return '' }
  $end = Skip-Str $obj $i
  return (Unescape $obj.Substring($i + 1, $end - $i - 2))
}
function Get-ArrayRegion($obj, $field) {   # inner text of field:[ ... ]
  $m = [regex]::Match($obj, [regex]::Escape($field) + ':\[')
  if (-not $m.Success) { return $null }
  $open = $m.Index + $m.Length - 1
  $close = Match-Bracket $obj $open '[' ']'
  return $obj.Substring($open + 1, $close - $open - 1)
}

# --- image map: _={hero:"url",grass1:"url",...,articleShade:"url",...}
$imgMap = @{}
$mi = $b.IndexOf('_={hero:"')
if ($mi -ge 0) {
  $open = $b.IndexOf('{', $mi)
  $close = Match-Bracket $b $open '{' '}'
  $mapText = $b.Substring($open, $close - $open + 1)
  foreach ($m in [regex]::Matches($mapText, '(\w+):"(https://[^"]+)"')) { $imgMap[$m.Groups[1].Value] = $m.Groups[2].Value }
}
Write-Output ("image map entries: " + $imgMap.Count)

# --- articles array: Cr=[{slug:"...",title:"...",excerpt:...}]
$ci = $b.IndexOf('Cr=[{slug:"best-grass-for-houston-shade"')
$open = $b.IndexOf('[', $ci)
$close = Match-Bracket $b $open '[' ']'
$crRegion = $b.Substring($open + 1, $close - $open - 1)
$artObjs = Split-Objects $crRegion
Write-Output ("article objects: " + $artObjs.Count)

$articles = @()
foreach ($obj in $artObjs) {
  $slug = Get-FieldString $obj 'slug'
  if (-not $slug) { continue }
  $heroKey = [regex]::Match($obj, 'heroImage:_\.(\w+)').Groups[1].Value
  $secRegion = Get-ArrayRegion $obj 'sections'
  $sections = @()
  if ($secRegion) {
    foreach ($so in (Split-Objects $secRegion)) {
      $pr = Get-ArrayRegion $so 'paragraphs'
      $bl = Get-ArrayRegion $so 'bullets'
      $sections += [pscustomobject]@{
        heading    = (Get-FieldString $so 'heading')
        paragraphs = if ($pr) { Get-Strings $pr } else { @() }
        bullets    = if ($bl) { Get-Strings $bl } else { @() }
      }
    }
  }
  # Simple articles use a flat body:[ ... ] of paragraph strings instead of sections.
  $bodyRegion = Get-ArrayRegion $obj 'body'
  $body = if ($bodyRegion) { Get-Strings $bodyRegion } else { @() }

  $articles += [pscustomobject]@{
    slug       = $slug
    title      = (Get-FieldString $obj 'title')
    excerpt    = (Get-FieldString $obj 'excerpt')
    category   = (Get-FieldString $obj 'category')
    readMinutes = [int]([regex]::Match($obj, 'readMinutes:(\d+)').Groups[1].Value)
    updated    = (Get-FieldString $obj 'updated')
    heroImage  = if ($imgMap.ContainsKey($heroKey)) { $imgMap[$heroKey] } else { '' }
    sections   = $sections
    body       = $body
  }
}

$json = $articles | ConvertTo-Json -Depth 8
[System.IO.File]::WriteAllText("d:\Grass\post-type-help\articles-data.json", $json, (New-Object System.Text.UTF8Encoding($false)))
Write-Output ("extracted: " + $articles.Count)
$articles | ForEach-Object { "{0,-38} [{1,-18}] secs:{2} body:{3} hero:{4} read:{5}" -f $_.slug, $_.category, $_.sections.Count, $_.body.Count, [bool]$_.heroImage, $_.readMinutes }
