<?php

return new class {

    function __invoke(Closure $next)
    {
        if (!IS_SUBMITTING && !IS_ASIDE)
            return STS_NOT_FOUND;

        return $next();
    }
};
