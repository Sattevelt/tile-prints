<?php
namespace Oneway\TilePrints\Tile;

interface TileInterface
{
    public function getType();

    public function setType($type);

    public function getRotation();

    public function setRotation($rotation);

    public function getExits();

    public function render($offsetX, $offsetY, TileTheme $theme);
}
