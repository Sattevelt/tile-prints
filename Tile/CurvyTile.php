<?php
namespace Oneway\TilePrints\Tile;

class CurvyTile extends AbstractTile
{
    private $curveSvgFormat = '<path d="M %1$s %2$s Q %3$s %4$s %5$s %6$s" />';

    public function renderZero(TileTheme $theme)
    {
        // Do nothing
        return '';
    }

    public function renderOne(TileTheme $theme)
    {
        $halfWidth = $theme->getTileWidth() / 2;
        $halfHeight = $theme->getTileHeight() / 2;
        $svg = sprintf(
            '<line x1="%1$s" y1="%2$s" x2="%3$s" y2="%4$s" />',
            $halfWidth,
            0,
            $halfWidth,
            $theme->getTileHeight() / 3
        );

        $svg .= sprintf(
            '<circle cx="%1$s" cy="%2$s" r="%3$s" />',
            $halfWidth,
            $halfHeight,
            $halfWidth / 3
        );

        return $svg;
    }

    public function renderTwoAngle(TileTheme $theme)
    {
        $svg = sprintf(
            $this->curveSvgFormat,
            $theme->getTileWidth() / 2,
            0,
            $theme->getTileWidth() / 2,
            $theme->getTileHeight() / 2,
            $theme->getTileWidth(),
            $theme->getTileHeight() / 2
        );
        $svg .= PHP_EOL;


        return $svg;
    }

    public function renderTwoStraight(TileTheme $theme)
    {
        $svg = sprintf(
            '<line x1="%1$s" y1="%2$s" x2="%3$s" y2="%4$s" />',
            $theme->getTileWidth() / 2,
            0,
            $theme->getTileWidth() / 2,
            $theme->getTileHeight()
        );

        return $svg;
    }

    public function renderThree(TileTheme $theme)
    {
        $svg = sprintf(
            $this->curveSvgFormat,
            $theme->getTileWidth() / 2,
            0,
            $theme->getTileWidth() / 2,
            $theme->getTileHeight() / 2,
            $theme->getTileWidth(),
            $theme->getTileHeight() / 2
        );
        $svg .= PHP_EOL;
        $svg .= sprintf(
            $this->curveSvgFormat,
            $theme->getTileWidth(),
            $theme->getTileHeight() / 2,
            $theme->getTileWidth() / 2,
            $theme->getTileHeight() / 2,
            $theme->getTileWidth() / 2,
            $theme->getTileHeight()
        );
        $svg .= PHP_EOL;

        return $svg;
    }

    public function renderFour(TileTheme $theme)
    {
        $svg = sprintf(
            $this->curveSvgFormat,
            0,
            $theme->getTileHeight() / 2,
            $theme->getTileWidth() / 2,
            $theme->getTileWidth() / 2,
            $theme->getTileHeight() / 2,
            0
        );
        $svg .= PHP_EOL;
        $svg .= sprintf(
            $this->curveSvgFormat,
            $theme->getTileWidth() / 2,
            0,
            $theme->getTileWidth() / 2,
            $theme->getTileHeight() / 2,
            $theme->getTileWidth(),
            $theme->getTileHeight() / 2
        );
        $svg .= PHP_EOL;
        $svg .= sprintf(
            $this->curveSvgFormat,
            $theme->getTileWidth(),
            $theme->getTileHeight() / 2,
            $theme->getTileWidth() / 2,
            $theme->getTileHeight() / 2,
            $theme->getTileWidth() / 2,
            $theme->getTileHeight()
        );
        $svg .= PHP_EOL;
        $svg .= sprintf(
            $this->curveSvgFormat,
            $theme->getTileWidth() / 2,
            $theme->getTileHeight(),
            $theme->getTileWidth() / 2,
            $theme->getTileHeight() / 2,
            0,
            $theme->getTileHeight() / 2
        );
        $svg .= PHP_EOL;

        return $svg;
    }
}
