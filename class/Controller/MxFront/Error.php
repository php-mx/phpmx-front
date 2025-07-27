<?php

namespace Controller\MxFront;

use Exception;
use PhpMx\Front;
use PhpMx\View;
use Throwable;

class Error
{
    function __invoke(int $errCode = STS_NOT_FOUND)
    {
        return self::handlePageThrowable(new Exception('Erro personalizado', $errCode));
    }

    static function handlePageThrowable(Throwable $e)
    {
        $status = $e->getCode();

        if (!is_httpStatus($status))
            $status = STS_INTERNAL_SERVER_ERROR;

        if (env('DEV'))
            $message = $e->getMessage();

        $message = $message ?? env("STM_$status") ?? 'Erro desconhecido';

        Front::title($message);
        Front::layout(env('FRONT_ERROR_LAYOUT'));

        return View::render('_front/error', ['status' => $status, 'message' => $message]);
    }
}
