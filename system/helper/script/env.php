<?php

use PhpMx\Env;

Env::default('COOKIE_LIFE', '+30 days');

Env::default('FRONT_TITLE', 'PHP-MX');
Env::default('FRONT_FAVICON', '/favicon.ico');
Env::default('FRONT_DESCRIPTION', 'Simplesmente PHP');

Env::default('FRONT_DOMAIN', 'default');
Env::default('FRONT_LAYOUT', 'default');

Env::default('FRONT_ERROR_DOMAIN', env('FRONT_DOMAIN'));
Env::default('FRONT_ERROR_LAYOUT', env('FRONT_LAYOUT'));
