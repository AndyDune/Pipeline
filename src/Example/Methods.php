<?php
/**
 * ----------------------------------------------
 * | Author: Андрей Рыжов (Dune) <info@rznw.ru>  |
 * | Site: www.rznw.ru                           |
 * | Phone: +7 (4912) 51-10-23                   |
 * | Date: 10.10.2017                               |
 * -----------------------------------------------
 *
 */


namespace AndyDune\Pipeline\Example;


class Methods
{
    public function addBraceLeft($string)
    {
        return '(' . $string;
    }

    public function addBraceRight($string, callable $next)
    {
        $string =  $string . ')';
        return $next($string);
    }

}