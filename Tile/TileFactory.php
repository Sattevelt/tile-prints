<?php
namespace Oneway\TilePrints\Tile;

use ReflectionClass;

class TileFactory
{
    public static function getInstance($style)
    {
        $className = __NAMESPACE__ . '\\' . ucfirst($style) . 'Tile';
        $reflClass = new ReflectionClass($className);
        $tileObj = $reflClass->newInstance();

        return $tileObj;
    }
}
