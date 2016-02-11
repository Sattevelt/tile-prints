<?php

// Yeah, not really nice. Gets the job done, though :)
function my_autoloader($class)
{
    $class = str_replace('Oneway\\TilePrints\\', '', $class);
    $class = str_replace('\\', '/', $class);
    require_once $class . '.php';
}
spl_autoload_register('my_autoloader');


// Define a theme used for rendering
$theme = new \Oneway\TilePrints\Tile\TileTheme();
$theme->setBackgroundColor('#185366')
      ->setStrokeColor('#2C9ABF')
      ->setStrokeWidth(4)
      ->setTileHeight(50)
      ->setTileWidth(50);

// Create a canvas of 10 by 10 tiles
$canvas = new \Oneway\TilePrints\Tile\TileCanvas(10, 10);

// Capture debug output in the buffer
ob_start();
$canvas->generate();
$debug = ob_get_contents();
ob_end_clean();
// Render ALL THE THINGS!!
$svg = $canvas->render($theme, true);

echo '<html><body>';
echo $svg . '<textarea rows="30" cols="80">' . $debug . '</textarea></body></html>';
