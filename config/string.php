<?php

/**
 * NOTICE:
 *
 * If you need to make modifications to the default configuration, copy
 * this file to your app/config folder, and make them in there.
 *
 * This will allow you to upgrade without losing your custom config.
 */

return array(
	'active' => Fuel::$env,

	'development' => array(
        'salt' => 'put_your_salt_here',
	),
	'production' => array(
		'salt' => 'put_your_salt_here',
	),
    
    
);