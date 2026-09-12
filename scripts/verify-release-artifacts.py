"""Verify packaged bytes match current owned source and the release manifest."""
import hashlib
import json
import zipfile
from pathlib import Path

root = Path(__file__).resolve().parents[1]
manifest = (root / 'dist/release-manifest.txt').read_text(encoding='utf-8-sig')
report = []
for archive, directory in [
    ('solo-to-china-theme.zip', 'wp-content/themes/solo-to-china'),
    ('solo-to-china-child-theme.zip', 'wp-content/themes/solo-to-china-child'),
    ('solo-to-china-tools-plugin.zip', 'wp-content/plugins/solo-to-china-tools'),
]:
    source = root / directory
    path = root / 'dist' / archive
    digest = hashlib.sha256(path.read_bytes()).hexdigest().upper()
    assert digest in manifest, 'Manifest mismatch: ' + archive
    expected = {source.name + '/' + p.relative_to(source).as_posix(): p for p in source.rglob('*') if p.is_file()}
    with zipfile.ZipFile(path) as package:
        actual = {n.replace('\\', '/'): n for n in package.namelist() if not n.endswith('/')}
        assert actual.keys() == expected.keys(), 'Missing or extra packaged files: ' + archive
        for name, local in expected.items():
            assert package.read(actual[name]) == local.read_bytes(), 'Source/package mismatch: ' + name
            assert not any(part in {'.env', 'wp-config.php', 'node_modules', 'mu-plugins', 'scripts'} for part in Path(name).parts), 'Unexpected deployment file: ' + name
    report.append({'archive': archive, 'bytes': path.stat().st_size, 'sha256': digest, 'matching_files': len(expected)})
evidence = root / 'docs/qa/evidence'
evidence.mkdir(parents=True, exist_ok=True)
(evidence / 'release-artifacts.json').write_text(json.dumps(report, indent=2) + '\n', encoding='utf-8')
print(json.dumps(report, indent=2))
