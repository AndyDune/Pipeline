<?php
/**
 * ----------------------------------------------
 * | Автор: Андрей Рыжов (Dune) <info@rznw.ru>   |
 * | Сайт: www.rznw.ru                           |
 * | Телефон: +7 (4912) 51-10-23                 |
 * | Дата: 29.09.2017                            |
 * -----------------------------------------------
 *
 */


namespace AndyDune\Pipeline\Example;


class PowerOfNumber
{
    public function __invoke($data, callable $next, $power = 2)
    {
        if (is_array($power)) {
            array_walk($power, function (&$value, $key) use ($data) {
                $value = pow($data, $value);
            });
            return $next($power);
        }
        $data = $this->handle($data, $power);
        return $next($data);
    }

    public function power3($data, callable $next)
    {
        $data = $this->handle($data, 3);
        return $next($data);
    }

    public function power4($data, callable $next)
    {
        $data = $this->handle($data, 4);
        return $next($data);
    }

    protected function handle($number, $power)
    {
        return pow($number, $power);
    }

}