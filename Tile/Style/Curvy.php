<?php
namespace Oneway\TilePrints\Tile\Style;

use Oneway\TilePrints\Tile\AbstractTile;
use Oneway\TilePrints\Tile\TileTheme;

class Curvy extends AbstractTile
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
            'stroke-width' => 8,
            'fill' => 'none'
        ),
        '.correctionCurve' => array(
            'stroke' => '#bgcolor#',
            'stroke-width' => 16,
            'fill' => 'none'
        )
    );

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
        $halfWidth = $theme->getTileSize() / 2;
        $halfHeight = $theme->getTileSize() / 2;
        $svg = sprintf(
            '<line x1="%1$s" y1="%2$s" x2="%3$s" y2="%4$s" class="curve" />',
            $halfWidth,
            0,
            $halfWidth,
            $theme->getTileSize() / 3
        );

        $svg .= sprintf(
            '<circle cx="%1$s" cy="%2$s" r="%3$s" class="curve" />',
            $halfWidth,
            $halfHeight,
            $halfWidth / 3
        );

        return $svg;
    }

    public function renderTwoAngle(TileTheme $theme)
    {
        $centerX = $theme->getTileSize();
        $centerY = 0;
        $radius = $theme->getTileSize() / 2;
        $svg = $this->getSvgCurveForAnglesAndCenter(-90, 0, $radius, $centerX, $centerY, 'curve') . PHP_EOL;

        return $svg;
    }

    public function renderTwoStraight(TileTheme $theme)
    {
        $svg = sprintf(
            '<line x1="%1$s" y1="%2$s" x2="%3$s" y2="%4$s" class="curve" />',
            $theme->getTileSize() / 2,
            0,
            $theme->getTileSize() / 2,
            $theme->getTileSize()
        );

        return $svg;
    }

    public function renderThree(TileTheme $theme)
    {
        $ts = $theme->getTileSize();
        $hs = $theme->getTileSize() / 2;
        $svg = '';
        $svg .= $this->getSvgCurveForAnglesAndCenter(-90, 0, $hs, $ts, 0, 'curve') . PHP_EOL;
        $svg .= $this->getSvgCurveForAnglesAndCenter(180,225, $hs, $ts, $ts, 'correctionCurve') . PHP_EOL;
        $svg .= $this->getSvgCurveForAnglesAndCenter(180, 270, $hs, $ts, $ts, 'curve') . PHP_EOL;

        return $svg;
    }

    public function renderFour(TileTheme $theme)
    {
        $ts = $theme->getTileSize();
        $hs = $theme->getTileSize() / 2;
        $svg = '';
        $svg .= $this->getSvgCurveForAnglesAndCenter(-45, 0, $hs, $ts, 0, 'curve') . PHP_EOL;
        $svg .= $this->getSvgCurveForAnglesAndCenter(180, 225, $hs, $ts, $ts, 'correctionCurve') . PHP_EOL;
        $svg .= $this->getSvgCurveForAnglesAndCenter(180, 270, $hs, $ts, $ts, 'curve') . PHP_EOL;
        $svg .= $this->getSvgCurveForAnglesAndCenter(90, 135, $hs, 0, $ts, 'correctionCurve') . PHP_EOL;
        $svg .= $this->getSvgCurveForAnglesAndCenter(90, 180, $hs, 0, $ts, 'curve') . PHP_EOL;
        $svg .= $this->getSvgCurveForAnglesAndCenter(0, 45, $hs, 0, 0, 'correctionCurve') . PHP_EOL;
        $svg .= $this->getSvgCurveForAnglesAndCenter(0, 90, $hs, 0, 0, 'curve') . PHP_EOL;
        $svg .= $this->getSvgCurveForAnglesAndCenter(-90, -45, $hs, $ts, 0, 'correctionCurve') . PHP_EOL;
        $svg .= $this->getSvgCurveForAnglesAndCenter(-90, -45, $hs, $ts, 0, 'curve') . PHP_EOL;

        return $svg;
    }
}
