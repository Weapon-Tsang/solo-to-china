"""Collect actual CLI JSON results and unmodified screenshots; never synthesize passes."""
import json
import shutil
import subprocess
from pathlib import Path

root = Path(__file__).resolve().parents[1]
source = root / 'output/playwright'
target = root / 'docs/qa/evidence'
target.mkdir(parents=True, exist_ok=True)
for name in ['experience-browser', 'tool-interactions', 'webkit-tool-interactions', 'experience-edges', 'final-states', 'editor-browser', 'release-boundaries']:
    raw = (source / (name + '.log')).read_text(encoding='utf-8-sig')
    if '### Error' in raw or '### Result' not in raw:
        raise RuntimeError('Missing or failed test: ' + name)
    data = json.JSONDecoder().raw_decode(raw.split('### Result', 1)[1].lstrip())[0]
    (target / (name + '.json')).write_text(json.dumps(data, ensure_ascii=False, indent=2) + '\n', encoding='utf-8')
invariants = {}
for port in [9411, 9412, 9413]:
    response = subprocess.run(['curl.exe', '-fsS', '--noproxy', '*', '--max-time', '45', '-b', 'playground_auto_login_already_happened=1', '-X', 'POST', f'http://127.0.0.1:{port}/wp-json/stc-test/v1/recheck'], check=True, capture_output=True)
    invariants[str(port)] = json.loads(response.stdout)
(target / 'invariants.json').write_text(json.dumps(invariants, ensure_ascii=False, indent=2) + '\n', encoding='utf-8')
names = ['home', 'cities', 'article', 'finder', 'taxi']
for name in names:
    for width in [390, 1440]:
        filename = f'upgrade-{name}-{width}.png'
        shutil.copy2(source / filename, target / filename)
for name in ['upgrade-article-header-1440.png', 'upgrade-article-media-1440.png', 'upgrade-steps-390.png', 'upgrade-driver-long-landscape.png', 'upgrade-share-fallback-390.png', 'upgrade-editor-1440.png', 'upgrade-fallback-9412-390.png', 'upgrade-fallback-9413-390.png']:
    if (source / name).exists():
        shutil.copy2(source / name, target / name)
print('Saved actual test JSON, fresh WordPress invariants and unmodified screenshots:', target)
