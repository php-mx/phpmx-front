<?php

namespace PhpMx;

/** Controla layout, estado, metadados e alertas do frontend. */
abstract class Front
{
    protected static array $HEAD = [];
    protected static array $ALERT = [];
    protected static ?string $DOMAIN = null;
    protected static ?string $LAYOUT = null;
    protected static ?string $DOMAIN_STATE = null;
    protected static ?string $LAYOUT_STATE = null;

    /** Define o dominio frontend que deve ser utilizado */
    static function domain(?string $domain): void
    {
        self::$DOMAIN = $domain;
    }

    /** Define o layout frontend que deve ser utilizado */
    static function layout(?string $layout): void
    {
        self::$LAYOUT = $layout;
    }

    /** Define o estado do dominio frontend */
    static function domainState(?string $domainState): void
    {
        self::$DOMAIN_STATE = $domainState;
    }

    /** Define o estado do layout frontend */
    static function layoutState(?string $layoutState): void
    {
        self::$LAYOUT_STATE = $layoutState;
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
