<?php
/**
 * ----------------------------------------------
 * | Автор: Андрей Рыжов (Dune) <info@rznw.ru>   |
 * | Сайт: www.rznw.ru                           |
 * | Телефон: +7 (4912) 51-10-23                 |
 * | Дата: 21.09.2017                            |
 * -----------------------------------------------
 *
 */


namespace AndyDune\Pipeline\Example;

class Trim
{
    public function __invoke($context, $next)
    {
        return $this->handle($context, $next);
    }

    public function handle($context, $next, $symbols = null)
    {
        if (! $symbols) {
            $symbols = " \t\n\r\0\x0B";
        }
        if (is_string($context)) {
            $context = trim($context, $symbols);
        } else if (is_array($context)) {
            array_walk($context, function(&$value, $key) use($symbols) {
                if (is_string($value)) {
                    $value = trim($value, $symbols);
                }
            });
        }
        return $next($context);
    }
}