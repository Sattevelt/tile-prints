<?php
namespace Oneway\TilePrints\Tile;

use Exception;
use ReflectionClass;

class TileFactory
{
    private static $typeMap = array(
        'zero' => 'EndpointZero',
        'one' => 'EndpointOne',
        'twoAngle' => 'EndpointTwoAngle',
        'twoStraight' => 'EndpointTwoStraight',
        'three' => 'EndpointThree',
        'four' => 'EndpointFour'
    );

    public static function getTypes()
    {
        return array_keys(self::$typeMap);
    }

    public static function getInstanceByType($type)
    {
        if (isset(self::$typeMap[$type])) {
            $className = self::$typeMap[$type];
            $refl = new ReflectionClass(__NAMESPACE__ . '\\' . $className);

            $class = $refl->newInstance();
        } else {
            throw new Exception('Wrong type!');
        }

        return $class;
    }
}
