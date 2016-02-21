<?php
namespace Oneway\TilePrints\Tile;

interface TileInterface
{
    public function getType();

    public function getTypes();

    public function setType($type);

    public function getRotation();

    public function setRotation($rotation);

    public function getExits();

    public function getStandardExits($type);

    public function render($offsetX, $offsetY, TileTheme $theme);
}
