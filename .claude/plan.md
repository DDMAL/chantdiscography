# Chant Discography — Static Site Rebuild Plan

## Goal
Convert a 10-year-old PHP/MySQL site to a fully static site hostable on GitHub Pages,
preserving the original visual design exactly.

## Why it was broken
- Uses `mysql_*` PHP functions removed in PHP 7 (2015)
- Hardcoded server paths (`/library/webserver/ftl/chant/`)
- Dead SSL URLs for contact form
- Requires a live MySQL server

## Architecture

### Data
- Source: `chant_disc6-28-242024UTF-8BOM.sql` (~41K rows)
- Two tables: `record` (~3,100 rows) and `chant` (~38,500 rows)
- Encoding issue: data stored as UTF-8 bytes in a Latin-1 MySQL column → mojibake
  Fix: `s.encode('latin-1').decode('utf-8')` with fallback
- Conversion script: `scripts/sql_to_json.py`
- Output: `data/records.json` and `data/chants.json`

### Frontend
- Single `index.html` — hash-based SPA routing
- `#home`, `#search`, `#abbreviations`, `#tropes`, `#background`,
  `#print`, `#liber`, `#records-needed`, `#links`, `#contact`
- `#search?q=TEXT&type=all|record|chant|performer` — search results
- `#record/ID` — record detail with chant list
- Separate pages: `chant-index.html`, `record-index.html`, `performer-index.html`
- CSS: original `style.css` preserved verbatim
- Images: `logo.gif`, `pmms_logo.jpg`, `scan0001a.jpg` preserved

### Search (client-side, replaces MySQL FULLTEXT)
- Records: search across `record_title`, `issue_number`, `performers`,
  `director`, `solo`, `keywords` — split query into words, all must match
- Chants: search across `title_of_chant`, `page`
- Performer: filter records where `performers` contains query
- Sort: records then chants, no relevance rank needed

### Indexes
- Chant index: all distinct chant titles alphabetically, paginated 200/page
  with letter nav (A–Z)
- Record index: all records alphabetically, single page (~3K items)
- Performer index: all distinct performers alphabetically

### Contact form
- Replace dead `contact.php` POST with `mailto:` link (simple, no third-party)

### GitHub Pages
- `.nojekyll` at repo root to bypass Jekyll
- `index.html` at repo root — serves directly from main branch

## Commit Plan (~10–15 commits)
1. chore: add .claude folder with project plan
2. build: add SQL-to-JSON conversion script
3. feat: generate records.json and chants.json data files
4. feat: scaffold static site with original layout and CSS
5. feat: implement hash routing and all static content pages
6. feat: implement record and chant search
7. feat: implement record detail view with chant list
8. feat: add chant, record, and performer index pages
9. feat: add contact page and links
10. chore: add GitHub Pages config and .nojekyll

## File Layout (new)
```
/
├── index.html
├── chant-index.html
├── record-index.html
├── performer-index.html
├── css/style.css
├── js/app.js
├── data/
│   ├── records.json
│   └── chants.json
├── img/
│   ├── logo.gif
│   ├── pmms_logo.jpg
│   └── scan0001a.jpg
├── content/          ← .txt files cleaned up as HTML fragments
│   ├── home.html
│   ├── background.html
│   ├── abbreviations.html
│   ├── tropes.html
│   ├── print.html
│   ├── liber.html
│   ├── records-needed.html
│   ├── links.html
│   └── search-help.html
├── scripts/
│   └── sql_to_json.py
└── .claude/
    └── plan.md
```
