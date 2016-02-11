<?php
namespace Oneway\TilePrints\Tile;

class EndpointTwoStraight extends AbstractTile
{
    public function render($offsetX, $offsetY, $rotation, TileTheme $tileTheme)
    {
        $svg = sprintf(
            '<line x1="%1$s" y1="%2$s" x2="%3$s" y2="%4$s" />',
            $tileTheme->getTileWidth() / 2,
            0,
            $tileTheme->getTileWidth() / 2,
            $tileTheme->getTileHeight()
        );

        return $this->renderOuterSvg($offsetX, $offsetY, $rotation, $svg, $tileTheme);

    }
}
