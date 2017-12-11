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

    protected $braceLeft = '(';
    protected $braceRight = ')';

    /**
     * @param string $braceLeft
     */
    public function setBraceLeft($braceLeft)
    {
        $this->braceLeft = $braceLeft;
    }

    /**
     * @param string $braceRight
     */
    public function setBraceRight($braceRight)
    {
        $this->braceRight = $braceRight;
    }

    public function addBraceLeft($string)
    {
        return $this->braceLeft . $string;
    }

    public function addBraceRight($string, callable $next)
    {
        $string =  $string . $this->braceRight;
        return $next($string);
    }

}