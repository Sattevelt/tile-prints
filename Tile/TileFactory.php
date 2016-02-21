<?php
namespace Oneway\TilePrints\Tile;

use ReflectionClass;

class TileFactory
{
    /**
     * @param $style
     * @return TileInterface
     */
    public static function getInstance($style)
    {
        $className = __NAMESPACE__ . '\\Style\\' . ucfirst($style);
        $reflClass = new ReflectionClass($className);
        $tileObj = $reflClass->newInstance();

        return $tileObj;
    }
}
