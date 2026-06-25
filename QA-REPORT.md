# GrassHouston Migration QA — Source vs WordPress

**Source (truth):** grasshouston.com Replit/React build — content extracted from its JS bundle (`original-bundle.js`); the live site is an SPA so its rendered HTML only shows the homepage shell.
**Destination:** coral-hare-833726.hostingersite.com (WordPress + Elementor), audited via the WP REST API + front-end fetches.
**Date:** 2026-06-26

> Method note: `service` and `services-areas` posts return an **empty `content.rendered`** over REST — their content is rendered by Elementor templates from ACF/`_sa_*` meta. Depth checks for those were done against the **front-end** HTML, not REST.

---

## Scorecard

| Content type | Source | On WP | Gap |
|---|---:|---:|---|
| Services (service/market/hybrid) | 14 | 13 | **1 missing** (`hoa-common-area-turf`) |
| Grass-type pages | 3 | 3 | 0 (1 title bug) |
| Service-area (city) pages | 24 | 24 | 0 |
| Blog / resource articles | 13 | 13 (+1 stray) | 0 missing; 1 extra |
| Project / case-study cards | 8 | 4 | **4 missing** |

---

## 1. Services — 1 missing

The 14 source service pages map to a mix of the `service` CPT and top-level Pages:

| Source slug | On WP as | Status |
|---|---|---|
| sod-installation | service | ✅ |
| hydroseeding | service | ✅ |
| hydromulching | service | ✅ |
| erosion-control-turfing | service `erosion-control-sod-hydroseed` | ✅ (slug renamed) |
| grass-repair | service | ✅ |
| rye-grass-overseeding | service | ✅ |
| lawn-replacement | service | ✅ |
| residential-lawn-installation | **page** | ✅ |
| commercial-turf-installation | **page** | ✅ |
| hybrid-turf-solutions | **page** ("Hybrid Plans") | ✅ |
| sports-turf-installation | service | ✅ |
| new-construction-turf | service | ✅ |
| acreage-estate-turf-installation | service | ✅ |
| **hoa-common-area-turf** | — | ❌ **MISSING** (404 on front-end) |

**Finding 1 — `hoa-common-area-turf` is absent** from the `service` CPT, Pages, and front-end (verified 404). In the source it's a *stub* (hero + intro + 4 bullets, no rich body/FAQ), the same shape sports-turf/new-construction were before we authored copy for them. Recommended: decide whether to add it (as a service CPT entry, matching its siblings) with hand-written rich copy, or intentionally drop it.

---

## 2. Grass-type pages — present, 1 title bug

All three exist as Pages: `st-augustine-grass-houston`, `bermuda-grass-houston`, `zoysia-grass-houston`.

**Finding 2 — St. Augustine page title is wrong.** Title renders as **"Augustine Grass"** (missing "St."). Should be "St. Augustine Grass". Body content is present (~1k chars). Quick fix in the page title.

---

## 3. Service-area (city) pages — complete

All 24 source cities are present in the `services-areas` CPT (source `<city>-tx-grass-sod-installation` → WP `<city>-tx`). Front-end spot-check of **katy-tx** is fully localized: Katy-specific intro, 7 neighborhoods (Cinco Ranch, Cross Creek Ranch, Firethorne, Seven Meadows, Grand Lakes, Falcon Ranch, Cane Island), 8 FAQs, grass-type recs, no stale/duplicated content (Houston appears only as the parent region — intentional).

**Watch item:** `jersey-village-tx` exists on WP, but Jersey Village was a **stub with no real data in the source** (it was skipped in the earlier city import). Worth confirming that page isn't thin or carrying generic/duplicated content. (Not individually deep-checked: the other 22 cities — the importer covered them, and Katy verified clean, but a fuller pass is advisable if you want 100% confidence.)

---

## 4. Blog / resources — all present, 1 stray

All 13 source articles are present as `posts`. Two have lightly different slugs (harmless):
- `st-augustine-vs-bermuda-houston` → `st-augustine-vs-bermuda-for-houston`
- `how-much-sod-do-i-need` → `how-much-sod-do-i-need-houston-sod-calculator`

**Finding 3 — one extra/stray post: `care-maintenance`** ("Care & Maintenance") has no matching source article. This is the known stray (post #1394). Recommended: delete, or confirm it's intentional.

---

## 5. Project / case-study cards — 4 of 8 missing

The source has 8 example/case-study cards; the WP `project` CPT has 4.

| Source card | On WP | Status |
|---|---|---|
| energy-corridor-office-park | energy-corridor-office-park | ✅ |
| cinco-ranch-hoa-entry | cinco-ranch-hoa-entry-restoration | ✅ |
| fulshear-detention-pond | fulshear-detention-pond-stabilization | ✅ |
| magnolia-estate-acreage | magnolia-14-acre-estate | ✅ |
| **pearland-builder-closeout** | — | ❌ MISSING |
| **conroe-construction-hydroseed** | — | ❌ MISSING |
| **sh-99-slope-stabilization** | — | ❌ MISSING |
| **klein-isd-athletic-field** | — | ❌ MISSING (verified 404) |

**Finding 4 — 4 case-study projects not migrated.** In the source these are short teaser cards (no deep page content). They may have been intentionally curated down to 4, or dropped by accident. Recommended: confirm intent; if wanted, add the remaining 4 to the `project` CPT.

---

## 6. Other observations

- **Finding 5 — Acreage service hero image:** front-end fetch of `/service/acreage-estate-turf-installation/` showed the full rich content (6 key features, 8 FAQs, pricing) but **no prominent hero image** was detected. Verify the featured image is set/rendering on that page (the importer sideloads `_.hybridAerial`). Could be a template detail rather than a true gap — eyeball it.
- The `service` page content is fully Elementor/ACF-driven; `post_content` mirrors are not used for layout, so REST `content` looks empty even when pages render fine. Not a defect — just noted so future QA uses front-end checks for these.

---

## Recommended action list (priority order)

1. **Add `hoa-common-area-turf`** (or confirm intentional drop). — *missing page*
2. **Fix the St. Augustine page title** "Augustine Grass" → "St. Augustine Grass". — *quick win*
3. **Decide on the 4 missing case-study `project`s** (add or confirm curated). — *content*
4. **Delete/confirm the stray `care-maintenance` blog post (#1394).** — *quick win*
5. **Verify the acreage page hero image** renders. — *spot check*
6. **Optional:** deep-QA the remaining 22 city pages + `jersey-village-tx` for localization/leakage. — *thoroughness*
