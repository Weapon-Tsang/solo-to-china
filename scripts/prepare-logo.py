#!/usr/bin/env python3
"""Create the transparent white SoloToChina web logo from the supplied JPG."""

from __future__ import annotations

import argparse
from pathlib import Path

import numpy as np
from PIL import Image


def prepare_logo(source: Path, destination: Path) -> None:
    image = Image.open(source).convert("RGB")
    pixels = np.asarray(image).astype(np.int16)
    red, green, blue = (pixels[:, :, index] for index in range(3))

    darkest_channel = np.minimum(np.minimum(red, green), blue)
    distance_from_white = (255 - darkest_channel).astype(np.float32)
    alpha = np.clip((distance_from_white - 8) * 255 / 100, 0, 255).astype(np.uint8)
    alpha[alpha < 5] = 0

    brightest_channel = np.maximum(np.maximum(red, green), blue)
    saturation = brightest_channel - darkest_channel
    warm_color = (
        (red > green + 18)
        & (red > blue + 24)
        & (saturation > 34)
    )

    height, width = alpha.shape
    y_grid, x_grid = np.ogrid[:height, :width]
    seal_source = (
        warm_color
        & (x_grid > width * 0.58)
        & (y_grid > height * 0.20)
        & (y_grid < height * 0.56)
        & (red > 145)
    )

    # Fill each red-seal scanline between its colored edges. This retains the
    # enclosed white Chinese characters while leaving letter counters and the
    # surrounding page background transparent.
    seal_fill = np.zeros_like(alpha, dtype=bool)
    seal_rows = np.flatnonzero(seal_source.any(axis=1))
    for y_position in seal_rows:
        x_positions = np.flatnonzero(seal_source[y_position])
        if x_positions.size:
            seal_fill[y_position, x_positions[0] : x_positions[-1] + 1] = True

    alpha[seal_fill] = 255

    output_rgb = np.full_like(pixels, 255, dtype=np.uint8)
    output_rgb[warm_color] = pixels[warm_color].astype(np.uint8)
    output_rgb[seal_fill] = pixels[seal_fill].astype(np.uint8)
    rgba = np.dstack((output_rgb, alpha))

    visible = np.argwhere(alpha > 5)
    if not visible.size:
        raise ValueError("No visible logo pixels were detected.")

    top, left = visible.min(axis=0)
    bottom, right = visible.max(axis=0)
    padding = 18
    left = max(0, int(left) - padding)
    top = max(0, int(top) - padding)
    right = min(width - 1, int(right) + padding)
    bottom = min(height - 1, int(bottom) + padding)

    result = Image.fromarray(rgba, "RGBA").crop((left, top, right + 1, bottom + 1))
    destination.parent.mkdir(parents=True, exist_ok=True)
    result.save(destination, format="PNG", optimize=True)


def main() -> None:
    parser = argparse.ArgumentParser()
    parser.add_argument("source", type=Path)
    parser.add_argument("destination", type=Path)
    args = parser.parse_args()
    prepare_logo(args.source, args.destination)


if __name__ == "__main__":
    main()
