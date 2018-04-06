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