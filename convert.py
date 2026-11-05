from pathlib import Path
import re

root = Path('resources/views')

def matching_end(text, start):
    opening = re.match(r'<(aside|div|header|main|nav)\b[^>]*>', text[start:], re.I | re.S)
    if not opening:
        return None
    tag = opening.group(1)
    depth = 1
    token = re.compile(r'<(/?)' + tag + r'\b[^>]*>', re.I | re.S)
    for match in token.finditer(text, start + opening.end()):
        if match.group(0).rstrip().endswith('/>'):
            continue
        depth += -1 if match.group(1) else 1
        if depth == 0:
            return start + opening.end() + match.end()
    return None

def remove_element(text, start):
    end = matching_end(text, start)
    return text[:start] + text[end:] if end else text

def strip_sidebars(text):
    while True:
        found = re.search(r'<(?:aside|div)\b[^>]*class=["\'][^"\']*\bsidebar\b[^"\']*["\'][^>]*>', text, re.I)
        if not found:
            return text
        text = remove_element(text, found.start())

def page_title(text):
    found = re.search(r'<title[^>]*>(.*?)</title>', text, re.I | re.S)
    return re.sub(r'\s+', ' ', found.group(1)).strip() if found else 'SI PS ONLINE'

for role in ('admin', 'pendamping', 'kelompok'):
    for path in (root / role).glob('*.blade.php'):
        source = path.read_text(encoding='utf-8')
        if '<body' not in source.lower() or not re.search(r'class=["\'][^"\']*sidebar', source, re.I):
            continue
        head = re.search(r'<head[^>]*>(.*?)</head>', source, re.I | re.S)
        head_content = head.group(1) if head else ''
        body = re.search(r'<body[^>]*>(.*?)</body>', source, re.I | re.S)
        if not body:
            continue
        content = strip_sidebars(body.group(1))
        main = re.search(r'<(?:main|div)\b[^>]*class=["\'][^"\']*\bmain\b[^"\']*["\'][^>]*>', content, re.I)
        if main:
            end = matching_end(content, main.start())
            content = content[main.end():end]
            content = re.sub(r'</(?:main|div)>\s*$', '', content, flags=re.I)
        header = re.search(r'<(?:header|div)\b[^>]*class=["\'][^"\']*\bheader\b[^"\']*["\'][^>]*>', content, re.I)
        if header:
            content = remove_element(content, header.start())
        scripts = '\n'.join(re.findall(r'<script(?:\s[^>]*)?>.*?</script>', content, re.I | re.S))
        content = re.sub(r'<script(?:\s[^>]*)?>.*?</script>', '', content, flags=re.I | re.S)
        styles = '\n'.join(re.findall(r'<style[^>]*>(.*?)</style>', head_content, re.I | re.S))
        styles = re.sub(r'[^{}]*\.sidebar[^{}]*\{[^{}]*\}', '', styles)
        styles = re.sub(r'[^{}]*\.sidebar-category[^{}]*\{[^{}]*\}', '', styles)
        external_styles = '\n'.join(re.findall(r'<link[^>]+(?:leaflet|\.css)[^>]*>', head_content, re.I))
        external_scripts = '\n'.join(re.findall(r'<script[^>]+src=[^>]+></script>', head_content, re.I))
        output = ["@extends('layouts.app')", "@section('title', '" + page_title(source).replace("'", "\\'") + "')"]
        if external_styles or styles.strip():
            output += ["@push('styles')", external_styles + '\n<style>\n' + styles + '\n</style>', '@endpush']
        output += ["@section('content')", content.strip(), '@endsection']
        if external_scripts or scripts.strip():
            output += ["@push('scripts')", external_scripts + '\n' + scripts, '@endpush']
        path.write_text('\n\n'.join(output) + '\n', encoding='utf-8')
        print(path)
