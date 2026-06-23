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

for src_name, dst_name in FILES.items():
    src_path = os.path.join(TXT_DIR, src_name)
    dst_path = os.path.join(OUT_DIR, dst_name)

    if not os.path.exists(src_path):
        print(f"SKIP (not found): {src_path}")
        continue

    with open(src_path, "r", encoding="cp1252", errors="replace") as f:
        content = f.read()

    # Unescape backslash-quoted double-quotes
    content = content.replace('\\"', '"')

    # Strip leading/trailing whitespace
    content = content.strip()

    # Fix mojibake (cp1252 → UTF-8 round-trip) where it sneaked through
    def fix_str(m):
        s = m.group(0)
        try:
            return s.encode("cp1252").decode("utf-8")
        except Exception:
            return s

    # Apply fix to runs of non-ASCII characters
    content = re.sub(r'[^\x00-\x7F]+', fix_str, content)

    with open(dst_path, "w", encoding="utf-8") as f:
        f.write(content)

    print(f"  {src_name} → {dst_path}")

print("Done.")
