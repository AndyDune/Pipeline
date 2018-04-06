<?php
/**
 *
 * Class place for use with ZendFW3.
 * Don't look at this if you use pipeline standalone.
 *
 * PHP version => 5.6
 *
 * @package andydune/pipeline
 * @link  https://github.com/AndyDune/Pipeline for the canonical source repository
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Andrey Ryzhov  <info@rznw.ru>
 * @copyright 2017 Andrey Ryzhov
 *
 */


namespace AndyDune\Pipeline;
use Zend\ServiceManager\Factory\FactoryInterface;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;


class ZendFactory implements FactoryInterface
{
    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     * @return object
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $manager = new Pipeline($container);
        return $manager;
    }
}