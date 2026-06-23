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

async function renderSearch(q, type, el) {
  if (!q) {
    el.innerHTML = '<p>Enter a search term above.</p>';
    return;
  }

  el.innerHTML = '<div id="loading">Searching…</div>';

  const words = normalise(q).split(/\s+/).filter(Boolean);

  await loadRecords();
  await loadChants();

  let html = '';

  // ── Record results (shown for all, record, and performer searches) ───────
  if (type !== 'chant') {
    const matchedRecords = (type === 'performer')
      ? records.filter(r => matchesAll([r.performers, r.director, r.solo].join(' '), words))
      : records.filter(r => matchesAll(
          [r.record_title, r.issue_number, r.performers, r.director, r.solo, r.keywords].join(' '),
          words));

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
    const matchedChants = (type === 'chant')
      ? chants.filter(c => normalise(c.title_of_chant).includes(normalise(q)))
      : chants.filter(c => matchesAll([c.title_of_chant, c.page].join(' '), words));

    if (matchedChants.length > 0) {
      const recMap = {};
      records.forEach(r => { recMap[r.id] = r; });

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
    <p>For questions or contributions regarding the Chant Discography, please send an email:</p>
    <p><a href="mailto:chantdisc@gmail.com" id="title">chantdisc@gmail.com</a></p>`;
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
