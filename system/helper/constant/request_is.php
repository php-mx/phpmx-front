<?php

use PhpMx\Request;

/** Se a requisição é uma solicitação de api */
define('IS_API', !IS_TERMINAL && Request::header('Request-Api'));

/** Se a requisição é uma solicitação página parcial */
define('IS_PARTIAL', !IS_TERMINAL && !IS_API && Request::header('Request-Partial'));

/** Se a requisição é uma solicitação aside */
define('IS_ASIDE', IS_PARTIAL && Request::header('Request-Aside'));

/** Se a requisição é uma solicitação submetida por formulário */
define('IS_SUBMITTING', IS_PARTIAL && Request::header('Request-Submitting'));
