<?php
echo "Blank Test Page !!! <br>";
if( class_exists("Imagick") )
{
    echo "ok";
}
if (!extension_loaded('imagick'))
    echo 'imagick not installed';

if( extension_loaded('imagick') || class_exists("Imagick") ){ echo "123"; }
?>