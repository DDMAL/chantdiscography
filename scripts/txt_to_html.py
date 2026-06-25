#!/usr/bin/env python3
"""
Convert the legacy .txt content files to clean HTML fragments.
The .txt files are single-line HTML with backslash-escaped quotes.

Run from the repo root:
  python3 scripts/txt_to_html.py
"""

import os
import re

TXT_DIR  = "chant"
OUT_DIR  = "content"

FILES = {
    "home.txt":         "home.html",
    "background.txt":   "background.html",
    "abbreviations.txt":"abbreviations.html",
    "tropes.txt":       "tropes.html",
    "print.txt":        "print.html",
    "liber.txt":        "liber.html",
    "records.txt":      "records-needed.html",
    "links.txt":        "links.html",
    "search.txt":       "search-help.html",
}

os.makedirs(OUT_DIR, exist_ok=True)

def fix_mojibake(m):
    s = m.group(0)
    for codec in ("cp1252", "latin-1"):
        try:
            return s.encode(codec).decode("utf-8")
        except Exception:
            continue
    return s

def clean(content):
    # Unescape backslash-escaped quotes (both single and double)
    content = content.replace('\\"', '"')
    content = content.replace("\\'", "'")

    # Strip leading/trailing whitespace
    content = content.strip()

    # Fix mojibake (cp1252/latin-1 bytes mis-read as UTF-8)
    content = re.sub(r'[^\x00-\x7F]+', fix_mojibake, content)

    # Remove <font> tags entirely (keep their inner content)
    content = re.sub(r'<font[^>]*>', '', content)
    content = re.sub(r'</font>', '', content)

    # Remove inline font-family style declarations (leave other styles intact)
    content = re.sub(r'\s*font-family\s*:[^;"\']+(;|(?=["\']))', '', content)

    # Remove leftover empty style="" attributes
    content = re.sub(r'\s*style\s*=\s*["\']["\']', '', content)

    # Remove font-size: medium inline styles (redundant — CSS handles it)
    content = re.sub(r'\s*font-size\s*:\s*medium\s*;?', '', content)

    # Auto-link bare URLs that are not already inside an <a href>
    # Match http(s):// URLs not preceded by href=" or src="
    def linkify(m):
        url = m.group(0).rstrip('.,;)')
        trail = m.group(0)[len(url):]
        return f'<a href="{url}" target="_blank" rel="noopener">{url}</a>{trail}'

    content = re.sub(
        r'(?<!["\'=>])(https?://[^\s<"\']+)',
        linkify,
        content
    )

    return content

for src_name, dst_name in FILES.items():
    src_path = os.path.join(TXT_DIR, src_name)
    dst_path = os.path.join(OUT_DIR, dst_name)

    if not os.path.exists(src_path):
        print(f"SKIP (not found): {src_path}")
        continue

    with open(src_path, "r", encoding="cp1252", errors="replace") as f:
        content = f.read()

    content = clean(content)

    with open(dst_path, "w", encoding="utf-8") as f:
        f.write(content)

    print(f"  {src_name} → {dst_path}")

print("Done.")
