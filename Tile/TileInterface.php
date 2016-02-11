<?php
namespace Oneway\TilePrints\Tile;

interface TileInterface
{
    /**
     * Render tile to an SVG string
     *
     * @param float $offsetX Offset in x direction from origin
     * @param float $offsetY Offset in y direction from origin
     * @param int $rotation Rotation of the tile in degrees
     * @param TileTheme $theme Tile theme
     * @return string Generated SVG of the tile.
     */
    public function render($offsetX, $offsetY, $rotation, TileTheme $theme);
}
