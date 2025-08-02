<?php

use PhpMx\File;
use PhpMx\Import;
use PhpMx\Path;
use PhpMx\Terminal;

return new class extends Terminal {

    function __invoke($errCode = null)
    {
        $errCode = strToCamelCase($errCode);
        $errCode = path('system/view/front/throw', $errCode);
        $errCode = "$errCode.html";

        if (File::check($errCode))
            return self::echo("[ignored] file [$errCode] already exists");

        $content = Path::seekForFile('library/template/terminal/front/error.txt');
        $content = Import::content($content);
        File::create($errCode, $content);
        self::echo("[created] error page [$errCode] created");
    }
};
