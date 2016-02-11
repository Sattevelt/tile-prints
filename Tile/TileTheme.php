<?php
namespace Oneway\TilePrints\Tile;

class TileTheme
{
    /** @var string */
    private $backgroundColor = '#FFFFFF';

    /** @var int */
    private $strokeWidth = '8';

    /** @var string */
    private $strokeColor = '#000000';

    /** @var int */
    private $tileWidth = 50;

    /** @var int */
    private $tileHeight = 50;

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
     * @return int
     */
    public function getStrokeWidth()
    {
        return $this->strokeWidth;
    }

    /**
     * @param $strokeWidth
     * @return $this
     */
    public function setStrokeWidth($strokeWidth)
    {
        $this->strokeWidth = $strokeWidth;
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
    public function getTileWidth()
    {
        return $this->tileWidth;
    }

    /**
     * @param int $tileWidth
     * @return TileTheme
     */
    public function setTileWidth($tileWidth)
    {
        $this->tileWidth = $tileWidth;
        return $this;
    }

    /**
     * @return int
     */
    public function getTileHeight()
    {
        return $this->tileHeight;
    }

    /**
     * @param int $tileHeight
     * @return TileTheme
     */
    public function setTileHeight($tileHeight)
    {
        $this->tileHeight = $tileHeight;
        return $this;
    }
}
