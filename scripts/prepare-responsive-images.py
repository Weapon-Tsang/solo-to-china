"""Generate real responsive theme assets; originals remain unchanged. Requires Pillow."""
from pathlib import Path
from PIL import Image, ImageOps
import json

root = Path(__file__).resolve().parents[1] / 'wp-content/themes/solo-to-china/assets/images'
manifest = {}
for source in [root / 'hero-home.png', *root.glob('card-*-hd.webp')]:
    with Image.open(source) as original:
        original = ImageOps.exif_transpose(original).convert('RGB')
        widths = (640, 960, 1600) if source.stem == 'hero-home' else (320, 480, 720)
        for width in widths:
            size = (width, round(original.height * width / original.width))
            target = source.with_name(f'{source.stem}-{width}.webp')
            original.resize(size, Image.Resampling.LANCZOS).save(target, 'WEBP', quality=83, method=6)
            manifest[target.name] = {'width': size[0], 'height': size[1], 'bytes': target.stat().st_size}
(root / 'responsive-images.json').write_text(json.dumps(manifest, indent=2) + '\n', encoding='utf-8')
print(json.dumps({k: v for k, v in manifest.items() if k.startswith('hero')}))
