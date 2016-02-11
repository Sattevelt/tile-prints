<?php
namespace Oneway\TilePrints\Tile;

class EndpointThree extends AbstractTile
{
    public function render($offsetX, $offsetY, $rotation, TileTheme $tileTheme)
    {
        $svg = sprintf(
            $this->curveSvgFormat,
            $tileTheme->getTileWidth() / 2,
            0,
            $tileTheme->getTileWidth() / 2,
            $tileTheme->getTileHeight() / 2,
            $tileTheme->getTileWidth(),
            $tileTheme->getTileHeight() / 2
        );
        $svg .= PHP_EOL;
        $svg .= sprintf(
            $this->curveSvgFormat,
            $tileTheme->getTileWidth(),
            $tileTheme->getTileHeight() / 2,
            $tileTheme->getTileWidth() / 2,
            $tileTheme->getTileHeight() / 2,
            $tileTheme->getTileWidth() / 2,
            $tileTheme->getTileHeight()
        );
        $svg .= PHP_EOL;

        return $this->renderOuterSvg($offsetX, $offsetY, $rotation, $svg, $tileTheme);
    }
}
