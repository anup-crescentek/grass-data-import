# GrassHouston Migration QA — Source vs WordPress (Round 2)

**Source (truth):** grasshouston.com Replit/React build — content from its JS bundle (`original-bundle.js`); the live site is an SPA so its HTML only shows the homepage shell.
**Destination:** coral-hare-833726.hostingersite.com (WordPress + Elementor), via WP REST API + front-end fetches.
**Date:** 2026-06-26 (round 2 — after the HOA import)

> **FINAL SWEEP (pre-client): ALL CHECKS PASSED ✅** — automated verification of post-type coverage (service 11, pages, services-areas 24, blog 13, project 8, grass pages 3), grass-type titles, all 24 city pages (grass varieties + content-leak heading check + Houston-leak scan), key service/project front-end renders, single-page disable, and stray-post removal. Zero failures.

> Method note: `service` / `services-areas` posts return empty `content.rendered` over REST (Elementor/ACF-rendered). Depth + grass-variety checks were done against the **front-end HTML**.

---

## Scorecard

| Content type | Source | On WP | Status |
|---|---:|---:|---|
| Services | 14 | **14** | ✅ complete (HOA added this round) |
| Grass-type pages | 3 | 3 | ⚠ 1 title bug |
| Service-area (city) pages | 24 | 24 | ✅ present |
| **City "Recommended grass varieties"** | 24 | 24 | ✅ **24/24 correct (Jersey Village fixed)** |
| Blog / articles | 13 | 13 | ✅ clean (stray removed) |
| Project / case-study cards | 8 | 8 | ✅ all 8 (4 added, verified live) |

---

## 1. Recommended grass varieties (the focus of this round)

**Important:** in the source, `recommendedGrass` is a **per-CITY field on the 24 service-area pages — not a service field.** None of the 14 service pages define recommended grass. So the "Recommended grass varieties" cards shown on a *service* page (e.g. all three on acreage/HOA) are a **template default**, not source-driven — there is nothing per-service to validate against the source.

The real, source-driven comparison is on the **24 city pages**, and they were checked one by one:

**Result: 23 of 24 match the source exactly. 1 is wrong.**

| City | Source | WordPress | |
|---|---|---|---|
| ❌ **jersey-village** | **St. Augustine** | **St. Augustine, Bermuda, Zoysia** | **MISMATCH** |
| baytown | St. Augustine, Bermuda | St. Augustine, Bermuda | ok |
| clear-lake | St. Augustine | St. Augustine | ok |
| conroe | St. Augustine, Bermuda | St. Augustine, Bermuda | ok |
| cypress | St. Augustine, Bermuda | St. Augustine, Bermuda | ok |
| friendswood | St. Augustine | St. Augustine | ok |
| fulshear | St. Augustine, Zoysia | St. Augustine, Zoysia | ok |
| hockley | St. Augustine, Bermuda | St. Augustine, Bermuda | ok |
| houston | St. Augustine, Bermuda, Zoysia | St. Augustine, Bermuda, Zoysia | ok |
| humble | St. Augustine, Zoysia | St. Augustine, Zoysia | ok |
| katy | St. Augustine, Bermuda | St. Augustine, Bermuda | ok |
| kingwood | St. Augustine | St. Augustine | ok |
| league-city | St. Augustine, Bermuda | St. Augustine, Bermuda | ok |
| magnolia | St. Augustine, Zoysia | St. Augustine, Zoysia | ok |
| missouri-city | St. Augustine, Bermuda | St. Augustine, Bermuda | ok |
| pasadena | St. Augustine, Bermuda | St. Augustine, Bermuda | ok |
| pearland | St. Augustine, Bermuda | St. Augustine, Bermuda | ok |
| richmond | St. Augustine, Bermuda | St. Augustine, Bermuda | ok |
| rosenberg | St. Augustine, Bermuda | St. Augustine, Bermuda | ok |
| spring | St. Augustine, Zoysia | St. Augustine, Zoysia | ok |
| sugar-land | St. Augustine, Bermuda, Zoysia | St. Augustine, Bermuda, Zoysia | ok |
| the-woodlands | St. Augustine, Zoysia | St. Augustine, Zoysia | ok |
| tomball | St. Augustine, Zoysia | St. Augustine, Zoysia | ok |
| willis | St. Augustine, Bermuda | St. Augustine, Bermuda | ok |

**Finding A — Jersey Village shows the wrong grass varieties. ✅ FIXED (verified live).** It displayed all three (the Houston/default set) instead of the source's **St. Augustine only**, and the whole page body was leaked Houston content. Root cause: Jersey Village was a stub in the source and was skipped in the earlier city import, so the page stayed a Houston duplicate. Fix: a full `jersey-village-tx` entry was added to the service-areas importer (v1.7.0) and imported. Live page now reads "Recommended grass for Jersey Village: St. Augustine" with city-specific content (White Oak Bayou, Jersey Meadow, Carverdale, mature canopy) and no Houston leak.

---

## 2. Services — complete (14/14)

All 14 source services now exist. `hoa-common-area-turf` was added this round and renders fully (hero, body, 6 Key Features, generic How-It-Works, 6 FAQs, grass cards) at `/service/hoa-common-area-turf/`. (residential / commercial / hybrid live as Pages; the other 11 as the `service` CPT.)

- **Minor, likely template:** service pages don't show a large featured/hero image (seen on acreage + HOA). Consistent across pages → probably by-design template behavior, not a per-page defect. Eyeball if a hero image is expected.

---

## 3. Still-open items from Round 1 (not yet addressed)

- **Finding B — St. Augustine page title bug. ✅ FIXED (verified live).** `st-augustine-grass-houston` now titles correctly as **"St. Augustine Grass"**.
- **Finding C — stray blog post `care-maintenance`** (#1394). ✅ FIXED (verified live) — trashed; it was a mislabeled truncated duplicate of the "St. Augustine vs. Bermuda for Houston" article (#1). Blog now 13/13, matching source; `/care-maintenance/` 404s.
- **Finding D — 4 of 8 case-study `project`s were missing. ✅ FIXED (verified live).** Added `pearland-builder-closeout`, `conroe-construction-hydroseed`, `sh-99-slope-stabilization`, `klein-isd-athletic-field` via the grass-projects-importer plugin (ACF Project Details fields: location/area/services_name/short_description + project-category + featured image). The homepage Loop Grid posts-per-page was bumped 6→8. All 8 cards now render with correct data; single-page view disabled (Publicly Queryable off → /project/ URLs redirect home).

---

## Action list — ALL COMPLETE ✅

1. ✅ **Jersey Village grass varieties** → fixed to St. Augustine only + full city content (verified live).
2. ✅ **St. Augustine page title** → "St. Augustine Grass" (verified live).
3. ✅ **Stray `care-maintenance` post** → trashed; blog now 13/13 (verified live).
4. ✅ **4 missing case-study projects** → added; all 8 cards render with correct data; single-page view disabled (verified live).
5. *Optional / open:* service pages don't show a large hero image — appears to be intended template behavior, not a defect. Eyeball if a hero is expected.

## What's healthy ✅
- 14/14 services present and populated; HOA verified live.
- **24/24** city grass-variety lists exactly match the source.
- All 24 city pages present; all 13 source articles present; Katy & others spot-checked as fully localized.
- 8/8 project cards present with correct ACF data.
