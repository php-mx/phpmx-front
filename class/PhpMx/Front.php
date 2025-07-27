<?php

namespace PhpMx;

abstract class Front
{
    protected static array $HEAD = [];
    protected static array $ALERT = [];
    protected static ?string $LAYOUT = null;
    protected static ?string $STATE = null;

    /** Define o layout que deve ser utilizado */
    static function layout(?string $layout): void
    {
        self::$LAYOUT = $layout;
        self::state(self::$STATE);
    }

    /** Define o estado do layout */
    static function state(?string $state): void
    {
        self::$STATE = mx5([self::$LAYOUT ?? uuid(), $state]);
    }

    /** Define um valor para uma subpropriedade da tag [#HEAD] */
    static function head(string $name, mixed $value): void
    {
        self::$HEAD[$name] = $value;
    }

    /** Define o valor para a propriedade [#HEAD.title] */
    static function title(string $title): void
    {
        self::head('title', $title);
    }

    /** Define o valor para a propriedade [#HEAD.favicon] */
    static function favicon(string $favicon): void
    {
        self::head('favicon', $favicon);
    }

    /** Define o valor para a propriedade [#HEAD.description] */
    static function description(string $description): void
    {
        self::head('description', $description);
    }

    /** Adiciona um alerta para o frontend */
    static function alert(string $content, ?bool $type = null): void
    {
        self::$ALERT[] = [$content, $type];
    }
}
