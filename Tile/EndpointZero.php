<?php
namespace Oneway\TilePrints\Tile;

class EndpointZero extends AbstractTile
{
    public function render($offsetX, $offsetY, $rotation, TileTheme $tileTheme)
    {
        return $this->renderOuterSvg($offsetX, $offsetY, $rotation, '', $tileTheme);
    }
}
