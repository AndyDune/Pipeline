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