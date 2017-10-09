<?php
/**
 *
 * Default service manager.
 *
 * PHP version 5.6, 7.0 and 7.1
 *
 * @package andydune/pipeline
 * @link  https://github.com/AndyDune/Pipeline for the canonical source repository
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Andrey Ryzhov  <info@rznw.ru>
 * @copyright 2017 Andrey Ryzhov
 */


namespace AndyDune\Pipeline;
use Interop\Container\ContainerInterface;

class PipeIsClassName implements ContainerInterface
{
    /**
     * Create instance by its class name and returns it.
     *
     * @param string $className Class name for create.
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get($className)
    {
        $object = new $className;
        return $object;
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($className)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $className Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has($className)
    {
        return class_exists($className);
    }

}