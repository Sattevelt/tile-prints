<?php
namespace Oneway\TilePrints\Tile;

abstract class AbstractTile implements TileInterface
{
    /** @var int */
    private $rotation = 0;
    /** @var string */
    private $type = 'zero';
    /**
     * Each tile type has exits defined as a 4 bit binary number.
     * The smallest bit is the top direction, and going clockwise with increasing bit values.
     * @var int[] */
    private $standardExits = array(
        'zero' => 0b0000,
        'one' => 0b0001,
        'twoAngle' => 0b0011,
        'twoStraight' => 0b0101,
        'three' => 0b0111,
        'four' => 0b1111
    );

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
        $this->rotation = $rotation;
    }

    /**
     * @return mixed
     */
    public function getExits()
    {
        // Normalise rotation steps. (6 rotation steps is equal to 1 full revolution and 2 steps)
        $rotationSteps = ((int)($this->getRotation() / 90)) % 4;
        $exits = $this->standardExits[$this->getType()];

        while ($rotationSteps > 0) {
            $exits = $exits << 1;
            if ($exits & 16) {
                $exits -= 15;
            }
            $rotationSteps--;
        }

        return $exits;
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

        $gProperties = array(
            'transform' => sprintf(
                'translate(%1$s, %2$s) rotate(%3$s, %4$s, %5$s)',
                $offsetX,
                $offsetY,
                $this->getRotation(),
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

    abstract public function renderZero(TileTheme $theme);

    abstract public function renderOne(TileTheme $theme);

    abstract public function renderTwoAngle(TileTheme $theme);

    abstract public function renderTwoStraight(TileTheme $theme);

    abstract public function renderThree(TileTheme $theme);

    abstract public function renderFour(TileTheme $theme);
}
