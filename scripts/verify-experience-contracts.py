"""Semantic checks for bundled destination data, responsive images and CMS examples."""
from pathlib import Path
import sys
sys.path.insert(0, str(Path(__file__).resolve().parents[1] / '.tools/qa-python'))
import json
import re
from datetime import date
from PIL import Image
from jsonschema import Draft202012Validator, FormatChecker

root = Path(__file__).resolve().parents[1]
def read(path):
    return json.loads((root / path).read_text(encoding='utf-8-sig'))

schema = read('contracts/destination-catalog.schema.json')
catalog = read('wp-content/plugins/solo-to-china-tools/data/destinations-v1.json')
Draft202012Validator(schema, format_checker=FormatChecker()).validate(catalog)
assert schema == read('wp-content/plugins/solo-to-china-tools/data/destination-catalog.schema.json')
keys = [p['entity_key'] for p in catalog['places']]
assert len(keys) == len(set(keys)) == 9
for place in catalog['places']:
    for field, evidence in place['evidence'].items():
        assert date.fromisoformat(evidence['checked_at']) <= date.today()
        assert evidence['source_url'].startswith('https://')
    for field in ('entrance', 'dropoff', 'viewpoint'):
        assert place['sources'][field] == 'UNKNOWN', 'Unreviewed arrival precision was promoted'

folder = root / 'wp-content/themes/solo-to-china/assets/images'
manifest = json.loads((folder / 'responsive-images.json').read_text(encoding='utf-8'))
for name, expected in manifest.items():
    with Image.open(folder / name) as image:
        assert image.size == (expected['width'], expected['height']), name
    assert (folder / name).stat().st_size == expected['bytes'], name
assert (folder / 'hero-home-640.webp').stat().st_size < 100_000
assert (folder / 'hero-home-1600.webp').stat().st_size < 200_000

cms_notes = (root / 'docs/CMS_UPGRADE_1_4.md').read_text(encoding='utf-8')
examples = json.loads(re.search(r'```json\n(.*?)\n```', cms_notes, re.S).group(1))
page_schema = read('contracts/page-schema.json')
# Select the schema's actual block union, preserving its local references.
block_schema = dict(page_schema)
block_schema.pop('required', None)
block_schema.pop('properties', None)
block_schema.pop('additionalProperties', None)
block_schema.update(page_schema['properties']['blocks']['items'])
for block in examples:
    Draft202012Validator(block_schema).validate(block)
print(f'Experience contracts passed: {len(keys)} sourced destinations, {len(manifest)} responsive images, {len(examples)} CMS examples.')
