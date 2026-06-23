#!/usr/bin/env python3
"""
Convert the Chant Discography MySQL dump to JSON files for the static site.

Produces:
  data/records.json  — list of record objects
  data/chants.json   — list of chant objects

Run from the repo root:
  python3 scripts/sql_to_json.py
"""

import re
import json
import os
import sys

SQL_FILE = "chant_disc6-28-242024UTF-8BOM.sql"
OUT_DIR = "data"


def fix_encoding(s):
    """Fix mojibake: data was stored as UTF-8 bytes in MySQL's 'latin1' column.
    MySQL's latin1 is Windows-1252 (cp1252), not strict ISO-8859-1, so bytes
    0x80-0x9F map to printable chars (€, ™, etc.) rather than control chars.
    Falls back to latin-1 for bytes 0x81/0x8D/0x8F/0x90/0x9D undefined in cp1252."""
    if s is None:
        return s
    for codec in ("cp1252", "latin-1"):
        try:
            return s.encode(codec).decode("utf-8")
        except (UnicodeEncodeError, UnicodeDecodeError):
            continue
    return s


def parse_values_line(line):
    """
    Parse one VALUES row like:
      (1, 2, 'hello world', NULL, 'it\\'s fine')
    Returns a list of Python values (int, str, or None).
    """
    line = line.strip().rstrip(",;")
    if not (line.startswith("(") and line.endswith(")")):
        return None
    line = line[1:-1]  # strip outer parens

    tokens = []
    i = 0
    while i < len(line):
        c = line[i]
        if c == " " or c == "\t":
            i += 1
            continue
        if c == ",":
            i += 1
            continue
        if line[i:i+4] == "NULL":
            tokens.append(None)
            i += 4
            continue
        # Integer
        if c == "-" or c.isdigit():
            j = i + 1
            while j < len(line) and (line[j].isdigit() or line[j] == "."):
                j += 1
            try:
                tokens.append(int(line[i:j]))
            except ValueError:
                tokens.append(float(line[i:j]))
            i = j
            continue
        # String
        if c == "'":
            i += 1
            buf = []
            while i < len(line):
                ch = line[i]
                if ch == "\\" and i + 1 < len(line):
                    nxt = line[i + 1]
                    if nxt == "'":
                        buf.append("'")
                    elif nxt == "\\":
                        buf.append("\\")
                    elif nxt == "n":
                        buf.append("\n")
                    elif nxt == "r":
                        buf.append("\r")
                    elif nxt == "t":
                        buf.append("\t")
                    else:
                        buf.append(nxt)
                    i += 2
                    continue
                if ch == "'":
                    i += 1
                    break
                buf.append(ch)
                i += 1
            tokens.append("".join(buf))
            continue
        # Unexpected — skip character
        i += 1

    return tokens


def parse_insert_block(sql, table_name):
    """
    Find all INSERT INTO `table_name` blocks and return list of dicts.
    """
    # Find the column list for this table's INSERT
    pattern = re.compile(
        r"INSERT INTO `" + re.escape(table_name) + r"`\s*\(([^)]+)\)\s*VALUES\s*\n(.*?);\n",
        re.DOTALL,
    )

    records = []
    for m in pattern.finditer(sql):
        cols_raw = m.group(1)
        values_block = m.group(2)

        # Parse column names
        cols = [c.strip().strip("`") for c in cols_raw.split(",")]

        # Each line in the values block is one row
        for line in values_block.splitlines():
            line = line.strip()
            if not line or line == "(":
                continue
            vals = parse_values_line(line)
            if vals is None or len(vals) != len(cols):
                continue
            row = {}
            for col, val in zip(cols, vals):
                if isinstance(val, str):
                    val = fix_encoding(val).strip()
                row[col] = val
            records.append(row)

    return records


def main():
    sql_path = os.path.join(os.path.dirname(__file__), "..", SQL_FILE)
    sql_path = os.path.normpath(sql_path)

    if not os.path.exists(sql_path):
        print(f"ERROR: SQL file not found: {sql_path}", file=sys.stderr)
        sys.exit(1)

    print(f"Reading {sql_path} …")
    with open(sql_path, "r", encoding="utf-8-sig", errors="replace") as f:
        sql = f.read()

    os.makedirs(OUT_DIR, exist_ok=True)

    print("Parsing records …")
    records = parse_insert_block(sql, "record")
    print(f"  {len(records)} records found")

    print("Parsing chants …")
    chants = parse_insert_block(sql, "chant")
    print(f"  {len(chants)} chants found")

    # Sort records by record_title for the index page
    records.sort(key=lambda r: (r.get("record_title") or "").lower())

    # Sort chants by title_of_chant for the chant index page
    chants.sort(key=lambda c: (c.get("title_of_chant") or "").lower())

    records_path = os.path.join(OUT_DIR, "records.json")
    chants_path = os.path.join(OUT_DIR, "chants.json")

    print(f"Writing {records_path} …")
    with open(records_path, "w", encoding="utf-8") as f:
        json.dump(records, f, ensure_ascii=False, separators=(",", ":"))

    print(f"Writing {chants_path} …")
    with open(chants_path, "w", encoding="utf-8") as f:
        json.dump(chants, f, ensure_ascii=False, separators=(",", ":"))

    rec_size = os.path.getsize(records_path) / 1024
    chant_size = os.path.getsize(chants_path) / 1024
    print(f"\nDone.")
    print(f"  records.json  {rec_size:.0f} KB")
    print(f"  chants.json   {chant_size:.0f} KB")


if __name__ == "__main__":
    main()
