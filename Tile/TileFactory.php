<?php
namespace Oneway\TilePrints\Tile;

use ReflectionClass;

class TileFactory
{
    public static function getInstance($style)
    {
        $className = __NAMESPACE__ . '\\Style\\' . ucfirst($style);
        $reflClass = new ReflectionClass($className);
        $tileObj = $reflClass->newInstance();

        return $tileObj;
    }
}
