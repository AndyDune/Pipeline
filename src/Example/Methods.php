<?php
/**
 * This package provides a pipeline pattern implementation. It base on middleware approach.
 *
 * PHP version => 5.6
 *
 * @package andydune/pipeline
 * @link  https://github.com/AndyDune/Pipeline for the canonical source repository
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Andrey Ryzhov  <info@rznw.ru>
 * @copyright 2017 Andrey Ryzhov
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

    public function addBraceLeftPipe($string, callable $next)
    {
        $string =  $this->braceLeft . $string;
        return $next($string);
    }


    public function addBraceRight($string, callable $next)
    {
        $string =  $string . $this->braceRight;
        return $next($string);
    }

}