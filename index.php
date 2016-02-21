<?php

require_once "bootstrap.php";

$colorSets = array(
    'basicBlue' => array(
        'color' => '#2C9ABF',
        'bgcolor' => '#185366'
    ),
    'neonBlueGreen' => array(
        'color' => '#24F8DF',
        'bgcolor' => '#04332E'
    ),
    'neonGreen' => array(
        'color' => '#46FC25',
        'bgcolor' => '#0B3304'
    ),
    'neonPurple' => array(
        'color' => '#D71E8A',
        'bgcolor' => '#330420'
    ),
    'neonOrange' => array(
        'color' => '#D0351D',
        'bgcolor' => '#330A04'
    ),
    'neonBlue' => array(
        'color' => '#173FAC',
        'bgcolor' => '#041033'
    ),
    'moodyGrey' => array(
        'color' => '#444444',
        'bgcolor' => '#dddddd'
    )
);
$colorSet = $colorSets['moodyGrey'];
// Define a theme used for rendering
$theme = new \Oneway\TilePrints\Tile\TileTheme();
$theme->setBackgroundColor($colorSet['bgcolor'])
      ->setStrokeColor($colorSet['color'])
      ->setTileSize(40);
$tileFactory = new \Oneway\TilePrints\Tile\TileFactory();
// Create a canvas of 10 by 10 tiles
$canvas = new \Oneway\TilePrints\Tile\TileCanvas(15, 15, $tileFactory);

// Capture debug output in the buffer
ob_start();
$canvas->generate('doubleCurvy');
$debug = ob_get_contents();
ob_end_clean();
// Render ALL THE THINGS!!
$svg = $canvas->render($theme, true);

echo '<html><body>';
echo $svg; // . '<textarea rows="30" cols="80">' . $debug . '</textarea></body></html>';
