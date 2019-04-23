<?php
/**
 * This package provides a pipeline pattern implementation. It base on middleware approach.
 *
 * PHP version >= 7.1
 *
 * @package andydune/pipeline
 * @link  https://github.com/AndyDune/Pipeline for the canonical source repository
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Andrey Ryzhov  <info@rznw.ru>
 * @copyright 2017 Andrey Ryzhov
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