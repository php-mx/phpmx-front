<?php

use Controller\MxFront\Error;
use PhpMx\Log;
use PhpMx\Front;
use PhpMx\Request;
use PhpMx\Response;
use PhpMx\View;

return new class extends Front {

    function __invoke(Closure $next)
    {
        if (IS_API) return $next();

        try {
            self::title(env('FRONT_TITLE'));
            self::favicon(env('FRONT_FAVICON'));
            self::description(env('FRONT_DESCRIPTION'));
            self::domain(env('FRONT_DOMAIN'));
            self::layout(env('FRONT_LAYOUT'));

            $content = $next();
            if (is_httpStatus($content)) throw new Exception(env("STM_$content"), $content);
            $content = $this->renderize($content);
        } catch (Throwable $e) {
            $content = $this->renderizeThrowable($e);
        }

        if (env('DEV') && is_array($content))
            $content['log'] = Log::getArray();

        Response::content($content);
        Response::send();
    }

    protected function renderize($content): string|array
    {
        $content = $content ?? '';
        $domainState = mx5(['domain', self::$DOMAIN, self::$DOMAIN_STATE]);
        $layoutState = mx5(['layout', self::$LAYOUT, self::$LAYOUT_STATE]);

        if (is_array($content)) $content = implode('', $content);

        if (!IS_PARTIAL) {
            $content = $this->renderizeLayout($content);
            $content = $this->renderizeDomain($content);
            $content = $this->renderizeBase($content, $domainState, $layoutState);

            if (env('DEV')) $content = prepare("[#]\n<!--[#]-->", [$content, Log::getString()]);

            return $content;
        }

        if (!IS_ASIDE) {
            $changeDomain = Request::header('Domain-State') != $domainState;
            $changeLayout = $changeDomain || Request::header('Layout-State') != $layoutState;

            if ($changeLayout) $content = self::renderizeLayout($content);
            if ($changeDomain) $content = self::renderizeDomain($content);
        }

        return [
            'info' => [
                'mx' => true,
                'status' => Response::getStatus(),
                'error' => is_httpStatusError(Response::getStatus()),
                'message' => env('STM_' . Response::getStatus()),
                'alert' => self::$ALERT,
            ],
            'data' => [
                'head' => self::$HEAD,
                'state' => [
                    'domain' => $domainState,
                    'layout' => $layoutState
                ],
                'content' => $content
            ]
        ];
    }

    protected function renderizeBase($content, $domainState, $layoutState): string
    {
        $version = cache('front-version', fn() => [
            'script' => md5(View::render("front/base.js")),
            'style' => md5(View::render("front/base.css"))
        ]);

        $template = View::render('front/base.html', ['HEAD' => self::$HEAD]);

        return prepare($template, [
            'DOMAIN' => "<div id='DOMAIN'>\n$content\n</div>",
            'ALERT' => encapsulate(self::$ALERT),
            'STATE' => [
                'domain' => $domainState,
                'layout' => $layoutState
            ],
            'ASSETS' => [
                'script' => url('script.js', ['v' => $version['script']]),
                'style' => url('style.css', ['v' => $version['style']])
            ],
        ]);
    }

    protected function renderizeDomain($content = ''): string
    {
        $content = "<div id='LAYOUT'>\n$content\n</div>";

        $template = View::render("front/domain/" . self::$DOMAIN, ['HEAD' => self::$HEAD]);
        if ($template) $content = prepare($template, ['LAYOUT' => $content]);

        return $content;
    }

    protected function renderizeLayout($content = ''): string
    {
        $content = "<div id='CONTENT'>\n$content\n</div>";

        $template = View::render("front/layout/" . self::$LAYOUT, ['HEAD' => self::$HEAD]);
        if ($template) $content = prepare($template, ['CONTENT' => $content]);

        return $content;
    }

    protected function renderizeThrowable(Throwable $e)
    {
        $status = $e->getCode();
        $message = $e->getMessage();

        if ($status == STS_REDIRECT)
            $this->redirect($e);

        if (!is_httpStatus($status))
            $status = !is_class($e, Error::class) ? STS_BAD_REQUEST : STS_INTERNAL_SERVER_ERROR;

        if (empty($message))
            $message = env("STM_$status");

        if (is_json($message))
            $message = json_decode($message, true);

        if (!is_array($message) || !isset($message['message']))
            $message = ['message' => $message];

        Response::status($status);
        Response::header('Mx-Error-Message', $message['message']);
        Response::header('Mx-Error-Status', $status);

        if (env('DEV')) {
            Response::header('Mx-Error-File', $e->getFile());
            Response::header('Mx-Error-Line', $e->getLine());
        }

        if (IS_GET) {
            $content = Error::handlePageThrowable($e);
            $content = $this->renderize($content);
        }

        $content = $content ?? ['info' => ['mx' => true,], 'data' => null];

        if (is_array($content)) {
            $content['info']['status'] = $status;
            $content['info']['error'] =  is_httpStatusError($status);
            $content['info'] = [...$content['info'], ...$message];
            $content['info']['alert'] = self::$ALERT;
        }

        return $content;
    }

    protected function redirect(Throwable $e): never
    {
        if (IS_PARTIAL) {
            $url = !empty($e->getMessage()) ? url($e->getMessage()) : url('.');

            $scheme = [
                'info' => [
                    'mx' => true,
                    'status' => STS_REDIRECT,
                    'error' => false,
                    'location' => $url,
                    'alert' => self::$ALERT,
                ],
                'data' => null
            ];

            Response::header('Mx-Location', $url);
            Response::status(STS_OK);

            if (env('DEV')) $scheme['log'] = Log::getArray();

            Response::content($scheme);
        } else {
            Response::header('location', $e->getMessage());
            Response::status(STS_REDIRECT);
        }
        Response::send();
    }
};
