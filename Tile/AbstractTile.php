<?php
namespace Oneway\TilePrints\Tile;

use Oneway\TilePrints\Tile\Exception\IllegalRotation;

abstract class AbstractTile implements TileInterface
{
    protected $styles = [];
    /** @var int */
    private $rotation = 0;
    /** @var string */
    private $type = 'zero';
    /**
     * Each tile type has exits defined as a 4 bit binary number.
     * The smallest bit is the top direction, and going clockwise with increasing bit values.
     * @var int[] */

    abstract public function getStandardExits($type);

    abstract public function getTypes();

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getRotation()
    {
        return $this->rotation;
    }

    /**
     * @param int $rotation
     */
    public function setRotation($rotation)
    {
        if ($rotation % 90 != 0) {
            throw new IllegalRotation('Invalid tile rotation value: ' . (int)$rotation);
        }
        $this->rotation = $rotation;
    }

    /**
     * @return mixed
     */
    public function getExits()
    {
        // Normalise rotation steps. (6 rotation steps is equal to 1 full revolution and 2 steps)
        $rotationSteps = ((int)($this->getRotation() / 90)) % 4;
        $exits = $this->getStandardExits($this->getType());

        while ($rotationSteps > 0) {
            $exits = $exits << 1;
            if ($exits & 16) {
                $exits -= 15;
            }
            $rotationSteps--;
        }

        return $exits;
    }

    public function getStyles(TileTheme $theme)
    {
        $svg = '<defs><style type="text/css"><![CDATA[' . PHP_EOL;
        foreach ($this->styles as $name => $style) {
            $svg .= $name . ' {';
            foreach ($style as $key => $value) {
                $value = str_replace('#color#', $theme->getStrokeColor(), $value);
                $value = str_replace('#bgcolor#', $theme->getBackgroundColor(), $value);
                if ($key == 'stroke-width') {
                    $value = ($value / 100) * $theme->getTileSize();
                }
                $svg .= $key . ':' . $value . ';';
            }
            $svg .= '}' . PHP_EOL;
        }

        $svg .= ']]></style></defs>';

        return $svg;
    }

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
     * @param TileTheme $theme Theme to be used for this tile
     * @return string Rendered SVG string
     */
    public function render($offsetX, $offsetY, TileTheme $theme)
    {
        $method = new \ReflectionMethod($this, 'render' . ucfirst($this->getType()));
        $innerSvg = $method->invoke($this, $theme);
        $tileSize = $theme->getTileSize();

        $gProperties = array(
            'transform' => sprintf(
                'translate(%1$s, %2$s) rotate(%3$s, %4$s, %5$s)',
                $offsetX,
                $offsetY,
                $this->getRotation(),
                $tileSize / 2,
                $tileSize / 2
            ),
        );
        $rectProperties = array(
            'width' => $tileSize,
            'height' => $tileSize,
            'fill' => $theme->getBackgroundColor(),
            'stroke' => $theme->getStrokeColor(),
            'stroke-width' => 0 // Debug. Set to 0 for 'pretty' output
        );

        $outerFormat = '<g ';
        foreach ($gProperties as $key => $value) {
            $outerFormat .= $key . '="' . $value . '" ';
        }
        $outerFormat .= '>' . PHP_EOL;

        $outerFormat .= '<rect ';
        foreach ($rectProperties as $key => $value) {
            $outerFormat .= $key . '="' . $value . '" ';
        }
        $outerFormat .= '/>' . PHP_EOL;

        return $outerFormat
                . $innerSvg
                . PHP_EOL
                . '</g>'
                . PHP_EOL;
    }

    protected function getSvgCurveForAnglesAndCenter(
        $angleStart,
        $angleEnd,
        $radius,
        $centerX,
        $centerY,
        $className
    ) {
        $coords = $this->getCoordinatesForAnglesAndCenter(
            $angleStart,
            $angleEnd,
            $radius,
            $centerX,
            $centerY
        );

        $svg = sprintf(
            '<path d="M %1$s %2$s A%3$s,%4$s 0 0,0 %5$s,%6$s" class="%7$s"/>',
            $coords['x1'],
            $coords['y1'],
            $radius,
            $radius,
            $coords['x2'],
            $coords['y2'],
            $className
        );

        return $svg;
    }

    protected function getCoordinatesForAnglesAndCenter(
        $angleStart,
        $angleEnd,
        $radius,
        $centerX,
        $centerY
    ) {

        $angleStartRad = deg2rad($angleStart);
        $angleEndRad = deg2rad($angleEnd);
        $coords = [
            'x1' => sin($angleStartRad) * $radius + $centerX,
            'y1' => cos($angleStartRad) * $radius + $centerY,
            'x2' => sin($angleEndRad) * $radius + $centerX,
            'y2' => cos($angleEndRad) * $radius +  $centerY,
        ];

        return $coords;
    }
}
