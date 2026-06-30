/* Chant Discography — single-page app
   Hash-based routing, all data loaded from JSON files. */

const CONTENT_PAGES = ['home','search-help','abbreviations','tropes','background','print','liber','records-needed','links'];

let records = null;   // array of record objects
let chants  = null;   // array of chant objects

// ── Data loading ────────────────────────────────────────────────────────────

async function loadRecords() {
  if (records) return records;
  const r = await fetch('data/records.json');
  records = await r.json();
  return records;
}

async function loadChants() {
  if (chants) return chants;
  const r = await fetch('data/chants.json');
  chants = await r.json();
  return chants;
}

// ── Router ───────────────────────────────────────────────────────────────────

function getRoute() {
  const hash = location.hash.slice(1) || 'home';
  const [path, qs] = hash.split('?');
  const params = new URLSearchParams(qs || '');
  return { path, params };
}

async function route() {
  const { path, params } = getRoute();
  const el = document.getElementById('results');

  // Static content pages
  if (CONTENT_PAGES.includes(path)) {
    await renderContentPage(path, el);
    return;
  }

  // Search results
  if (path === 'search') {
    const q    = params.get('q') || '';
    const type = params.get('type') || 'all';
    document.getElementById('searchInput').value = q;
    await renderSearch(q, type, el);
    return;
  }

  // Record detail
  if (path.startsWith('record/')) {
    const id = parseInt(path.split('/')[1], 10);
    await renderRecord(id, el);
    return;
  }

  // Contact
  if (path === 'contact') {
    renderContact(el);
    return;
  }

  // Default → home
  await renderContentPage('home', el);
}

// ── Static content pages ─────────────────────────────────────────────────────

const contentCache = {};

async function renderContentPage(name, el) {
  if (!contentCache[name]) {
    try {
      const r = await fetch(`content/${name}.html`);
      if (!r.ok) throw new Error(r.status);
      contentCache[name] = await r.text();
    } catch (e) {
      contentCache[name] = '<p><em>Page not found.</em></p>';
    }
  }
  el.innerHTML = contentCache[name];
}

// ── Search ───────────────────────────────────────────────────────────────────

function normalise(s) {
  return (s || '').toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');
}

function matchesAll(target, words) {
  const t = normalise(target);
  return words.every(w => t.includes(w));
}

function matchesAny(target, words) {
  const t = normalise(target);
  return words.some(w => t.includes(w));
}

// Split normalised text into whole-word tokens (mirrors MySQL FULLTEXT tokenisation).
function tokenize(text) {
  return normalise(text).split(/[^a-z0-9]+/).filter(Boolean);
}

// OR whole-word match — any query word must appear as a standalone token.
function matchesAnyWord(target, words) {
  const tokens = new Set(tokenize(target));
  return words.some(w => tokens.has(w));
}

// Count matched whole-word tokens — used for FULLTEXT score ordering.
function scoreText(text, words) {
  const tokens = tokenize(text);
  return tokens.reduce((n, t) => n + (words.includes(t) ? 1 : 0), 0);
}

async function renderSearch(q, type, el) {
  if (!q) {
    el.innerHTML = '<p>Enter a search term above.</p>';
    return;
  }

  el.innerHTML = '<div id="loading">Searching…</div>';

  const words = normalise(q).split(/\s+/).filter(Boolean);
  // MySQL FULLTEXT: words shorter than ft_min_word_len are ignored. The original server used 3.
  const ftWords = words.filter(w => w.length >= 3);

  await loadRecords();
  await loadChants();

  let html = '';

  // ── Record results (shown for all, record, and performer searches) ───────
  if (type !== 'chant') {
    let matchedRecords;
    if (type === 'performer') {
      // Original PHP: simple LIKE match on performers field only, no ranking
      matchedRecords = records.filter(r =>
        normalise([r.performers, r.director, r.solo].join(' ')).includes(normalise(q))
      );
    } else {
      // Original PHP: FULLTEXT boolean mode, sorted by score DESC then serial_num
      const fields = r => [r.record_title, r.issue_number, r.performers, r.director, r.solo, r.keywords].join(' ');
      matchedRecords = ftWords.length === 0 ? [] : records
        .filter(r => matchesAnyWord(fields(r), ftWords))
        .map(r => ({ r, score: scoreText(fields(r), ftWords) }))
        .sort((a, b) => b.score - a.score || (a.r.serial_num || 0) - (b.r.serial_num || 0))
        .map(x => x.r);
    }

    if (matchedRecords.length > 0) {
      html += `<h3>Records (${matchedRecords.length}):</h3>`;
      matchedRecords.forEach(r => {
        html += `<div class="result-entry">
          <span id="title"><a href="#record/${r.id}">${esc(r.record_title)}</a></span><br>
          <span id="details">Format: ${esc(r.format_code)}
          <br>Country Code: ${esc(r.country_code)}
          <br>Label: ${esc(r.label_name)}</span>
          <br><span id="date">Date: ${esc(r.date_of_recording)}</span>
          <hr></div>`;
      });
    } else if (type === 'record' || type === 'performer') {
      html += '<h3>Records:</h3><p>No records found.</p>';
    }
  }

  // ── Chant results (shown for all and chant searches) ────────────────────
  if (type !== 'record' && type !== 'performer') {
    const recMap = {};
    records.forEach(r => { recMap[r.id] = r; });

    let matchedChants;
    if (type === 'chant') {
      // Original PHP: simple LIKE match on title_of_chant only, no ranking
      matchedChants = chants.filter(c => normalise(c.title_of_chant).includes(normalise(q)));
    } else {
      // Original PHP: FULLTEXT on title_of_chant + page, sorted by score DESC then serial_num
      const fields = c => [c.title_of_chant, c.page].join(' ');
      matchedChants = ftWords.length === 0 ? [] : chants
        .filter(c => matchesAnyWord(fields(c), ftWords))
        .map(c => ({ c, score: scoreText(fields(c), ftWords), serial_num: (recMap[c.record_id] || {}).serial_num || 0 }))
        .sort((a, b) => b.score - a.score || a.serial_num - b.serial_num || (a.c.item_num || 0) - (b.c.item_num || 0))
        .map(x => x.c);
    }

    if (matchedChants.length > 0) {
      html += `<h3>Chants (${matchedChants.length}):</h3>`;
      matchedChants.slice(0, 500).forEach(c => {
        const rec = recMap[c.record_id] || {};
        const performers = [rec.performers, rec.director, rec.solo]
          .filter(Boolean).join(', ');
        html += `<div class="result-entry">
          <span id="title">${esc(c.title_of_chant)}</span>
          <span id="details"> ${esc(c.page)}<br>
          <span id="date">${esc(performers)}${performers ? ', ' : ''}[${esc(c.time)}] – ${esc(c.comments)}</span><br>
          Record Title: <a href="#record/${c.record_id}">${esc(rec.record_title || '')}</a>
          </span><hr></div>`;
      });
      if (matchedChants.length > 500) {
        html += `<p><em>Showing first 500 of ${matchedChants.length} chants. Narrow your search for more specific results.</em></p>`;
      }
    } else if (type === 'chant') {
      html += '<h3>Chants:</h3><p>No chants found.</p>';
    }
  }

  if (!html) html = '<p>No results found.</p>';
  el.innerHTML = html;
}

// ── Record detail ─────────────────────────────────────────────────────────────

async function renderRecord(id, el) {
  el.innerHTML = '<div id="loading">Loading record…</div>';
  await loadRecords();
  await loadChants();

  const rec = records.find(r => r.id === id);
  if (!rec) {
    el.innerHTML = '<p>Record not found.</p>';
    return;
  }

  const label = [rec.format_code, rec.country_code, rec.label_name,
                 rec.prefix_to_number, rec.issue_number, rec.suffix]
                 .filter(Boolean).join(' ');

  let html = `<h3>${esc(rec.record_title)}</h3>
    <p>
      <span id="title">${esc(label)}</span><br>
      <span id="title">Title:</span> <span id="details">${esc(rec.record_title)}</span><br>
      <span id="title">Also issued as:</span> ${esc(rec.alternate_num)}<br>
      <span id="title">Performers:</span> ${esc(rec.performers)}<br>
      <span id="title">Director:</span> ${esc(rec.director)}<br>
      <span id="title">Solo:</span> ${esc(rec.solo)}<br>
      <span id="title">Date:</span> <span id="date">${esc(rec.date_of_recording)}</span><br>
      <span id="title">Comments:</span> ${esc(rec.comments)}
    </p><hr>
    <span id="title">Chant List:</span>`;

  const recChants = chants
    .filter(c => c.record_id === id)
    .sort((a, b) => (a.item_num || 0) - (b.item_num || 0));

  html += '<p><span id="details">';
  recChants.forEach(c => {
    html += `${esc(c.track_num)}. ${esc(c.title_of_chant)} ${esc(c.page)} [${esc(c.time)}] -- ${esc(c.comments)}<br>`;
  });
  html += '</span></p>';

  el.innerHTML = html;
}

// ── Contact page ──────────────────────────────────────────────────────────────

function renderContact(el) {
  el.innerHTML = `
    <strong>Contact Us:</strong>
    <form class="contactform" id="contactForm" style="margin-top:12px">
      <table border="0" cellspacing="2" cellpadding="2" width="700">
        <tr><td colspan="2">COMPLETE INFORMATION BELOW - *Required</td></tr>
        <tr>
          <td width="130" align="right"><label>Name*</label></td>
          <td><input type="text" id="cName" size="30" required /></td>
        </tr>
        <tr>
          <td width="130" align="right"><label>Email*</label></td>
          <td><input type="email" id="cEmail" size="30" required /></td>
        </tr>
        <tr>
          <td width="130" valign="top" align="right"><label>Message*</label></td>
          <td><textarea id="cMsg" rows="5" cols="40" required></textarea></td>
        </tr>
        <tr>
          <td colspan="2" style="padding-left:130px">
            <input type="submit" value="Submit" />
          </td>
        </tr>
      </table>
    </form>`;

  document.getElementById('contactForm').addEventListener('submit', e => {
    e.preventDefault();
    const name = document.getElementById('cName').value.trim();
    const email = document.getElementById('cEmail').value.trim();
    const msg  = document.getElementById('cMsg').value.trim();
    const body = encodeURIComponent(`Name: ${name}\n\n${msg}`);
    window.location.href = `mailto:chantdiscography@gmail.com?subject=Chant%20Discography%20Contact&body=${body}`;
  });
}

// ── Search form ───────────────────────────────────────────────────────────────

document.getElementById('searchForm').addEventListener('submit', e => {
  e.preventDefault();
  const q = document.getElementById('searchInput').value.trim();
  if (q) location.hash = `#search?q=${encodeURIComponent(q)}&type=all`;
});

// ── Utilities ─────────────────────────────────────────────────────────────────

function esc(s) {
  if (s === null || s === undefined) return '';
  return String(s)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');
}

// ── Boot ──────────────────────────────────────────────────────────────────────

window.addEventListener('hashchange', route);
route();
