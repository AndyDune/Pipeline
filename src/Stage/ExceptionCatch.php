<?php
/**
 * ----------------------------------------------
 * | Автор: Андрей Рыжов (Dune) <info@rznw.ru>   |
 * | Сайт: www.rznw.ru                           |
 * | Телефон: +7 (4912) 51-10-23                 |
 * | Дата: 28.09.2017                            |
 * -----------------------------------------------
 *
 * Stage for pipeline catch exception by default.
 * You can use it for your own.
 */


namespace AndyDune\Pipeline\Stage;
use Exception;

class ExceptionCatch
{
    public function __invoke($context, $next)
    {
        try {
            return $next($context);
        } catch (Exception $e) {
            return $e;
        }
    }
}