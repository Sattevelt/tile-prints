<?php
namespace Oneway\TilePrints\Tile;

class TileTheme
{
    /** @var string */
    private $backgroundColor = '#FFFFFF';

    /** @var string */
    private $strokeColor = '#000000';

    /** @var int */
    private $tileSize = 50;

    /**
     * @return string
     */
    public function getBackgroundColor()
    {
        return $this->backgroundColor;
    }

    /**
     * @param $backgroundColor
     * @return $this
     */
    public function setBackgroundColor($backgroundColor)
    {
        $this->backgroundColor = $backgroundColor;
        return $this;
    }

    /**
     * @return string
     */
    public function getStrokeColor()
    {
        return $this->strokeColor;
    }

    /**
     * @param $strokeColor
     * @return $this
     */
    public function setStrokeColor($strokeColor)
    {
        $this->strokeColor = $strokeColor;
        return $this;
    }

    /**
     * @return int
     */
    public function getTileSize()
    {
        return $this->tileSize;
    }

    /**
     * @param int $tileSize
     * @return TileTheme
     */
    public function setTileSize($tileSize)
    {
        $this->tileSize = $tileSize;
        return $this;
    }
}
