<?php
/**
 * ----------------------------------------------
 * | Author: Andrey Ryzhov (Dune) <info@rznw.ru> |
 * | Site: www.rznw.ru                           |
 * | Phone: +7 (4912) 51-10-23                   |
 * | Date: 05.03.2018                            |
 * -----------------------------------------------
 *
 */


namespace AndyDune\Pipeline\Example;


use AndyDune\Pipeline\StageInterface;

class InterfaceMethods implements StageInterface
{
    public function execute($data, callable $next)
    {
        return $next($data + 10);
    }

    public function handle($data, callable $next)
    {
        return $next($data + 100);
    }

}