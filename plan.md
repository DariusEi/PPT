# Plan: Revert Course Content Pages to Default Tutor LMS Layout

## Problem
The theme (`functions.php`) contains **massive CSS/JS overrides** targeting Tutor LMS course content, lesson player, and dashboard pages. These override Tutor's native templates, styling, and structure — producing the custom dark layout shown in the screenshot instead of Tutor's default design.

## What Needs to Change

### 1. Remove/disable Tutor LMS CSS overrides in `functions.php`

The following `add_action` blocks inject custom CSS/JS that override Tutor's native look on course content pages. Each needs to be removed or disabled:

| Lines | ID / Description | Action |
|-------|-----------------|--------|
| 2042–2131 | `pt101-enrolled-ux-polish` — Lesson UX polish (already has `if (true) return;` guard — **already disabled**) | No change needed |
| 2134–2191 | Lesson micro-UX JS (already has `if (true) return;` guard — **already disabled**) | No change needed |
| 2511–3796 | `pt101-tutor-overrides` — **HUGE block**: dark theme vars, dashboard dark skin, course player/lesson page "Turing College-inspired" layout, sidebar, nav, tabs, accordion, video, quiz, scrollbars. ~1300 lines of CSS. **This is the main offender.** | **Disable** by adding `return;` guard, or remove entirely |
| 3799–3929 | JS `tutorFix()` — Force-overrides Tutor elements via DOM manipulation (tabs, accordion, hide reviews) | **Disable** |
| 3934–4088 | `pt101-course-polish` — More accordion/tab/sidebar/card styling | **Disable** |
| 4090–4203 | `pt101-dashboard-polish` — Dashboard card/stats/progress/sidebar styling | **Keep or disable** (user said "keep other pages unchanged" — dashboard is debatable; it's a Tutor page) |
| 4205–4233 | Lesson sidebar JS force-override (already has `if (true) return;` — **already disabled**) | No change needed |
| 4236–4291 | Lesson completion bridge JS (already has `if (true) return;` — **already disabled**) | No change needed |
| 4293–4365 | `pt101-course-info-placement-refine-v1` — Course info text/heading sizing | **Disable** |
| 4368–4432 | `pt101-lesson-default-dark-skin` (already has `if (true) return;` — **already disabled**) | No change needed |
| 4435–4471 | `pt101-lesson-default-dark-skin-v2` (already has `if (true) return;` — **already disabled**) | No change needed |
| 4474–4554 | `pt101-lesson-default-dark-skin-safe-v3` — Lesson-only dark coloring (active, no guard) | **Disable** |

### 2. Review template overrides in `tutor/` directory

| File | Current behavior | Action |
|------|-----------------|--------|
| `tutor/single/lesson.php` | Passthrough — loads Tutor's default template via `include`. **Already correct.** | No change needed |
| `tutor/dashboard.php` | Wraps Tutor dashboard in theme header/footer. Reasonable. | No change needed |
| `tutor/single/course/lead-info.php` | Wraps Tutor's default in a custom `<aside>` wrapper | **Consider removing** (the wrapper adds custom classes that may attract custom CSS) |
| `tutor/single/course/course.php` | Wraps Tutor's `tutor_course_content()` in custom container classes (`pt101-tutor-page`, `pt101-tutor-shell`) | **Consider removing** or simplifying to just call Tutor's default |

### 3. Keep untouched
- Course redirect logic (lines 2307–2333) — functional, not visual
- Login page CSS — different page
- WooCommerce checkout/thank-you code — different pages
- Homepage, blog, landing page templates

## Execution Steps

1. **Disable the active Tutor CSS override blocks** by adding `if (true) return;` at the top of each callback (same pattern already used for disabled blocks), for these blocks:
   - Lines 2515–3796 (`pt101-tutor-overrides` + course player CSS)
   - Lines 3801–3929 (`tutorFix` JS)
   - Lines 3934–4088 (`pt101-course-polish`)
   - Lines 4294–4365 (`pt101-course-info-placement-refine-v1`)
   - Lines 4475–4554 (`pt101-lesson-default-dark-skin-safe-v3`)

2. **Decide on dashboard styling** (lines 4090–4203): disable it too if you want pure Tutor defaults on the dashboard, or keep if you want the dark dashboard theme.

3. **Optionally simplify** `tutor/single/course/course.php` and `tutor/single/course/lead-info.php` to pass through to Tutor defaults (like `lesson.php` already does).

4. **Test** that course content/lesson pages now render with Tutor's native white/default theme, while homepage and other pages remain unchanged.

## Risk
- Tutor's default theme is light/white. Your site is dark-themed. The course content pages will look white/light — which will contrast with your dark nav header and footer. You may want to keep a minimal dark-skin-only CSS block (colors only, no layout changes) to avoid a jarring contrast.
