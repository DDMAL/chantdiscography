# Chant Discography

A relational database of Western Plainchant on sound recordings, created by Fr. Jerome Weber.  
Transferred to the Sacred Music program at the University of Notre Dame, managed by Peter Jeffery (Michael P. Grace Professor of Medieval Studies, Professor of Musicology and Ethnomusicology).

Rebuilt as a fully static site (HTML + CSS + JavaScript + JSON) from the original PHP/MySQL codebase, hostable on GitHub Pages with no server required.

## What's here

| Path | Purpose |
|------|---------|
| `index.html` | Main SPA — handles all page routing and search |
| `chant-index.html` | Alphabetical index of all 7,200+ unique chant titles |
| `record-index.html` | Alphabetical index of all 2,918 records |
| `performer-index.html` | Alphabetical index of all 995 performers |
| `data/records.json` | All record data (converted from MySQL dump) |
| `data/chants.json` | All chant data (~38,500 entries) |
| `content/` | Static content pages (Home, Background, Abbreviations, etc.) |
| `css/style.css` | Original visual design, preserved |
| `img/` | Original images |
| `scripts/` | One-time data conversion scripts (Python) |
| `chant/` | Legacy PHP source files (kept for reference) |

## Hosting on GitHub Pages

1. Push this repo to GitHub
2. Go to **Settings → Pages**
3. Set source to **Deploy from a branch**, branch `main`, folder `/ (root)`
4. The site will be live at `https://<org>.github.io/<repo>/`

To use a custom domain (e.g. `chantdiscography.com`):
- Add a `CNAME` file at the repo root containing your domain name
- Point your DNS `A` records to GitHub Pages IPs (see GitHub docs)

## Regenerating the data

If the SQL dump is updated, re-run from the repo root:

```bash
python3 scripts/sql_to_json.py
```

This overwrites `data/records.json` and `data/chants.json`.

To regenerate content pages (Home, Background, etc.) from the legacy `.txt` files:

```bash
python3 scripts/txt_to_html.py
```

## How search works

All search is client-side — no server needed.

- **Records**: matches query words against `record_title`, `issue_number`, `performers`, `director`, `solo`, `keywords`
- **Chants**: matches query words against `title_of_chant` and `page` (edition page references)
- **Performers**: filters records where `performers`, `director`, or `solo` contain the query
- Diacritics are normalised (searching "solesmes" also matches "Solesmes")
- Results cap at 500 chants to keep rendering fast; narrow the query for more precision

## Contact form

The contact form uses `mailto:` — clicking Submit opens the user's email client with the message pre-filled. No server or third-party form service required.
