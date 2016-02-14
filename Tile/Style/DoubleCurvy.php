<?php
namespace Oneway\TilePrints\Tile\Style;

use Oneway\TilePrints\Tile\AbstractTile;
use Oneway\TilePrints\Tile\TileTheme;

class DoubleCurvy extends AbstractTile
{
    protected $standardExits = array(
        'zero' => 0b0000,
        'one' => 0b0001,
        'twoAngle' => 0b0011,
        'twoStraight' => 0b0101,
        'three' => 0b0111,
        'four' => 0b1111
    );

    protected $styles = array(
        '.curve' => array(
            'stroke' => '#color#',
            'stroke-width' => 2,
            'fill' => 'none'
        ),
        '.centerCurve' => array(
            'stroke' => '#ff0000',
            'stroke-width' => 1,
            'fill' => 'none'
        )
    );

    private $centerOffsetFactor = 15;

    public function getStandardExits($type)
    {
        return $this->standardExits[$type];
    }

    public function renderZero(TileTheme $theme)
    {
        return '';
    }

    public function renderOne(TileTheme $theme)
    {
        $centerOffset = $theme->getTileSize() / $this->centerOffsetFactor;
        $halfSize = $theme->getTileSize() / 2;
        $radius = $theme->getTileSize() / 6;
        $radius1 = $radius + $centerOffset;
        $radius2 = $radius - $centerOffset;
        $lineFormat = '<line x1="%1$s" y1="%2$s" x2="%3$s" y2="%4$s" class="curve" />';

        $svg = '';
        $svg .= $this->getSvgCurveForAnglesAndCenter(-165, 0, $radius1, $halfSize, $halfSize, 'curve') . PHP_EOL;
        $svg .= $this->getSvgCurveForAnglesAndCenter(0, 165, $radius1, $halfSize, $halfSize, 'curve') . PHP_EOL;
        $svg .= $this->getSvgCurveForAnglesAndCenter(0, 180, $radius2, $halfSize, $halfSize, 'curve') . PHP_EOL;
        $svg .= $this->getSvgCurveForAnglesAndCenter(180, 360, $radius2, $halfSize, $halfSize, 'curve') . PHP_EOL;

        $svg .= sprintf(
            $lineFormat,
            $halfSize - $centerOffset,
            0,
            $halfSize - $centerOffset,
            $halfSize - $radius1
        );
        $svg .= sprintf(
            $lineFormat,
            $halfSize + $centerOffset,
            0,
            $halfSize + $centerOffset,
            $halfSize - $radius1
        );

        return $svg;
    }

    public function renderTwoAngle(TileTheme $theme)
    {
        $centerOffset = $theme->getTileSize() / $this->centerOffsetFactor;
        $centerX = $theme->getTileSize();
        $centerY = 0;
        $radius = $theme->getTileSize() / 2;
        $radius1 = $radius + $centerOffset;
        $radius2 = $radius - $centerOffset;
        $svg = '';
        $svg .= $this->getSvgCurveForAnglesAndCenter(-90, 0, $radius1, $centerX, $centerY, 'curve') . PHP_EOL;
        $svg .= $this->getSvgCurveForAnglesAndCenter(-90, 0, $radius2, $centerX, $centerY, 'curve') . PHP_EOL;

        return $svg;
    }

    public function renderTwoStraight(TileTheme $theme)
    {
        $format = '<line x1="%1$s" y1="%2$s" x2="%3$s" y2="%4$s" class="curve" />';
        $tileSize = $theme->getTileSize();
        $halfSize = $theme->getTileSize() / 2;
        $centerOffset = $theme->getTileSize() / $this->centerOffsetFactor;

        $svg = '';
        $svg .= sprintf($format, $halfSize - $centerOffset, 0, $halfSize - $centerOffset, $tileSize);
        $svg .= sprintf($format, $halfSize + $centerOffset, 0, $halfSize + $centerOffset, $tileSize);

        return $svg;
    }

    public function renderThree(TileTheme $theme)
    {
        $centerOffset = $theme->getTileSize() / $this->centerOffsetFactor;
        $tileSize = $theme->getTileSize();
        $radius = $theme->getTileSize() / 2;
        $radius1 = $radius + $centerOffset;
        $radius2 = $radius - $centerOffset;
        $svg = '';
        $svg .= $this->getSvgCurveForAnglesAndCenter(-90, -28, $radius1, $tileSize, 0, 'curve') . PHP_EOL;
        $svg .= $this->getSvgCurveForAnglesAndCenter(-90, 0, $radius2, $tileSize, 0, 'curve') . PHP_EOL;
        $svg .= $this->getSvgCurveForAnglesAndCenter(180, 270, $radius1, $tileSize, $tileSize, 'curve') . PHP_EOL;
        $svg .= $this->getSvgCurveForAnglesAndCenter(180, 270, $radius2, $tileSize, $tileSize, 'curve') . PHP_EOL;

        return $svg;
    }

    public function renderFour(TileTheme $theme)
    {
        $centerOffset = $theme->getTileSize() / $this->centerOffsetFactor;
        $tileSize = $theme->getTileSize();
        $radius = $theme->getTileSize() / 2;
        $radius1 = $radius + $centerOffset;
        $radius2 = $radius - $centerOffset;

        $svg = '';
        $svg .= $this->getSvgCurveForAnglesAndCenter(-90, -28, $radius1, $tileSize, 0, 'curve') . PHP_EOL;
        $svg .= $this->getSvgCurveForAnglesAndCenter(-90, 0, $radius2, $tileSize, 0, 'curve') . PHP_EOL;
        $svg .= $this->getSvgCurveForAnglesAndCenter(180, 242, $radius1, $tileSize, $tileSize, 'curve') . PHP_EOL;
        $svg .= $this->getSvgCurveForAnglesAndCenter(180, 270, $radius2, $tileSize, $tileSize, 'curve') . PHP_EOL;
        $svg .= $this->getSvgCurveForAnglesAndCenter(90, 152, $radius1, 0, $tileSize, 'curve') . PHP_EOL;
        $svg .= $this->getSvgCurveForAnglesAndCenter(90, 180, $radius2, 0, $tileSize, 'curve') . PHP_EOL;
        $svg .= $this->getSvgCurveForAnglesAndCenter(0, 62, $radius1, 0, 0, 'curve') . PHP_EOL;
        $svg .= $this->getSvgCurveForAnglesAndCenter(0, 90, $radius2, 0, 0, 'curve') . PHP_EOL;

        return $svg;
    }
}
