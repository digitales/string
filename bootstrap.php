<?php

Autoloader::add_core_namespace('String');

Autoloader::add_classes(array(
	'String\\String'                => __DIR__.'/classes/string.php',
	'String\\StringException'       => __DIR__.'/classes/string.php',
    'String\\String_Date'           => __DIR__.'/classes/date.php',
    'String\\String_DateException'  => __DIR__.'/classes/date.php',
));
