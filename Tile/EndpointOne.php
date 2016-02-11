<?php
namespace Oneway\TilePrints\Tile;

class endpointOne extends AbstractTile
{
    public function render($offsetX, $offsetY, $rotation, TileTheme $tileTheme)
    {
        $halfWidth = $tileTheme->getTileWidth() / 2;
        $halfHeight = $tileTheme->getTileHeight() / 2;
        $svg = sprintf(
            '<line x1="%1$s" y1="%2$s" x2="%3$s" y2="%4$s" />',
            $halfWidth,
            0,
            $halfWidth,
            $tileTheme->getTileHeight() / 3
        );

        $svg .= sprintf(
            '<circle cx="%1$s" cy="%2$s" r="%3$s" />',
            $halfWidth,
            $halfHeight,
            $halfWidth / 3
        );

        return $this->renderOuterSvg($offsetX, $offsetY, $rotation, $svg, $tileTheme);
    }
}
