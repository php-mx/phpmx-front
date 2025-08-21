<?php

use PhpMx\Front;
use PhpMx\View;

$status = $e->getCode();

if (!is_httpStatus($status))
    $status = STS_INTERNAL_SERVER_ERROR;

if (env('DEV'))
    $message = $e->getMessage();

$message = $message ?? env("STM_$status") ?? 'Erro desconhecido';

Front::title($message);
Front::context(env('FRONT_ERROR_CONTEXT'));
Front::layout(env('FRONT_ERROR_LAYOUT'));

$content = View::render("./$status", ['status' => $status, 'message' => $message]);

if (is_blank($content))
    $content = View::render("./default", ['status' => $status, 'message' => $message]);

return $content;
