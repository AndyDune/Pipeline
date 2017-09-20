<?php
/**
 * ----------------------------------------------
 * | Автор: Андрей Рыжов (Dune) <info@rznw.ru>   |
 * | Сайт: www.rznw.ru                           |
 * | Телефон: +7 (4912) 51-10-23                 |
 * | Дата: 20.09.2017                            |
 * -----------------------------------------------
 *
 * Class place for use with ZendFW3.
 * Don't look at this if you use pipeline standalone.
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