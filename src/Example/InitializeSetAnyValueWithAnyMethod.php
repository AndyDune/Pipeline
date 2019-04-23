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


class InitializeSetAnyValueWithAnyMethod
{

    protected $method;
    protected $value = null;

    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    public function __invoke($instance)
    {
        if(method_exists($instance, $this->method)) {
            call_user_func([$instance, $this->method], $this->value);
        }
    }
}