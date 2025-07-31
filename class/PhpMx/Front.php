<?php

namespace PhpMx;

/** Controla layout, estado, metadados e alertas do frontend. */
abstract class Front
{
    protected static array $HEAD = [];
    protected static array $ALERT = [];
    protected static ?string $CONTEXT = null;
    protected static ?string $CONTEXT_STATE = null;
    protected static ?string $LAYOUT = null;
    protected static ?string $LAYOUT_STATE = null;

    /** Define o contexto que deve ser utilizado */
    static function context(?string $context): void
    {
        self::$CONTEXT = $context;
        self::contextState(self::$CONTEXT_STATE);
    }

    /** Define o estado do contexto */
    static function contextState(?string $contextState): void
    {
        self::$CONTEXT_STATE = mx5([self::$CONTEXT ?? uuid(), $contextState]);
    }

    /** Define o layout que deve ser utilizado */
    static function layout(?string $layout): void
    {
        self::$LAYOUT = $layout;
        self::layoutState(self::$LAYOUT_STATE);
    }

    /** Define o estado do layout */
    static function layoutState(?string $layoutState): void
    {
        self::$LAYOUT_STATE = mx5([self::$LAYOUT ?? uuid(), $layoutState]);
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
