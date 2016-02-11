<?php
namespace Oneway\TilePrints\Tile;

abstract class AbstractTile implements TileInterface
{
    protected $curveSvgFormat = '<path d="M %1$s %2$s Q %3$s %4$s %5$s %6$s" />';

    /**
     * @inheritdoc
     */
    abstract public function render($offsetX, $offsetY, $rotation, TileTheme $theme);

    /**
     * Method that wraps the inner render output into a SVG group (<g>) element that does the following things:
     * - define a local coordinate system so all coordinate are relative to the top left of this tile
     * - translate (move) the tile itself to the desired offset (position in the grid)
     * - rotate the tile to the desired angle
     * - set style properties for the inner tile SVG elements.
     * A rectangle element is also added to provide a background color.
     *
     * @param int $offsetX X Location in the SVG
     * @param int $offsetY Y Location in the SVG
     * @param int $rotation Desired rotation of the tile (in degrees. Should use 90 degree increments)
     * @param string $innerSvg output of the tile internal SVG string
     * @param TileTheme $theme Theme to be used for this tile
     * @return string Rendered SVG string
     */
    protected function renderOuterSvg($offsetX, $offsetY, $rotation, $innerSvg, TileTheme $theme)
    {
        $gProperties = array(
            'transform' => sprintf(
                'translate(%1$s, %2$s) rotate(%3$s, %4$s, %5$s)',
                $offsetX,
                $offsetY,
                $rotation,
                $theme->getTileWidth() / 2,
                $theme->getTileHeight() / 2
            ),
            'stroke' => $theme->getStrokeColor(),
            'stroke-width' => $theme->getStrokeWidth(),
            'fill' => 'none'
        );
        $rectProperties = array(
            'width' => $theme->getTileWidth(),
            'height' => $theme->getTileHeight(),
            'fill' => $theme->getBackgroundColor(),
            'stroke-width' => 1 // Debug. Set to 0 for 'pretty' output
        );

        $outerFormat = '<g ';
        foreach ($gProperties as $key => $value) {
            $outerFormat .= $key . '="' . $value . '" ';
        }
        $outerFormat .= '>' . PHP_EOL;

        $outerFormat .= '    <rect ';
        foreach ($rectProperties as $key => $value) {
            $outerFormat .= $key . '="' . $value . '" ';
        }
        $outerFormat .= '/>' . PHP_EOL;

        return $outerFormat . $innerSvg . PHP_EOL . '</g>' . PHP_EOL;
    }
}
