"""Synthetic, clearly labelled image fixtures. No actual traveler photos."""
from pathlib import Path
import sys
sys.path.insert(0, str(Path(__file__).resolve().parents[1] / '.tools/qa-python'))
from PIL import Image, ImageDraw

out = Path(__file__).resolve().parents[1] / 'output/playwright/fixture-images'
out.mkdir(parents=True, exist_ok=True)
for name, size, mode in [('photo', (1600, 900), 'RGB'), ('transparent', (320, 200), 'RGBA'),
                         ('long', (400, 6000), 'RGB'), ('lowtext', (240, 120), 'RGB'),
                         ('pixels', (12001, 10), 'RGB')]:
    im = Image.new(mode, size, (255, 255, 255, 0) if mode == 'RGBA' else 'white')
    ImageDraw.Draw(im).text((10, 10), 'TEST SIGN: CENTRAL STATION', fill='black')
    im.save(out / (name + '.png'))
im = Image.new('RGB', (32, 64), 'red')
exif = Image.Exif()
exif[274], exif[270] = 6, 'PRIVATE_METADATA_TEST'
im.save(out / 'rotated.jpg', exif=exif)
(out / 'corrupt.jpg').write_bytes(b'not a jpeg')
(out / 'unsupported.gif').write_bytes(b'GIF89a')
with (out / 'oversize.jpg').open('wb') as target:
    target.seek(21 * 1024 * 1024)
    target.write(b'0')
print('Prepared image fixtures in', out)
