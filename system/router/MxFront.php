<?php

use PhpMx\Router;

Router::get('style.css', \Controller\MxFront\Style::class);
Router::get('script.js', \Controller\MxFront\Script::class);


Router::get('...', STS_NOT_FOUND, ['front']);
