<?php
/**
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
use Interop\Container\ContainerInterface;

class StageContainer implements ContainerInterface {
    protected $stages = [];

    public function setStage($name, $instance)
    {
        $this->stages[$name] = $instance;
    }

    public function get($name)
    {
        return $this->stages[$name];
    }

    public function has($name)
    {
        return array_key_exists($name, $this->stages);
    }
}