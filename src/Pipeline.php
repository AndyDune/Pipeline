<?php
/**
 * This package provides a pipeline pattern implementation. It base on middleware approach.
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
use Closure;
use Interop\Container\ContainerInterface;

class Pipeline
{
    /**
     * The object being passed through the pipeline.
     *
     * @var mixed
     */
    protected $passable;

    /**
     * The array of class pipes.
     *
     * @var array
     */
    protected $pipes = [];

    /**
     * The method to call on each pipe.
     * By default check object as callable.
     *
     * @var string
     */
    protected $method = null;

    /**
     * @var ContainerInterface|null
     */
    protected $container = null;

    protected $initializers = [];


    /**
     * Create a new class instance.
     *
     * @param  ContainerInterface|null  $container
     * @return void
     */
    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    /**
     * Set the object being sent through the pipeline.
     *
     * @param  mixed  $passable
     * @return $this
     */
    public function send($passable)
    {
        $this->passable = $passable;
        return $this;
    }

    /**
     * Add initializer for DI with interface.
     *
     * @param callable $initializer
     * @return $this
     */
    public function addInitializer(callable $initializer)
    {
        $this->initializers[] = $initializer;
        return $this;
    }

    protected function initialize($instance)
    {
        if (!$this->initializers) {
            return;
        }
        foreach ($this->initializers as $initializer) {
            $initializer($instance);
        }
        return;
    }

    /**
     * Set the array of pipes.
     *
     * @param  array|mixed  $pipes
     * @return $this
     */
    public function through($pipes)
    {
        $this->pipes = is_array($pipes) ? $pipes : func_get_args();

        return $this;
    }

    /**
     * Set the method to call on the pipes.
     *
     * @param  string  $method
     * @return $this
     */
    public function via($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Add stage for workflow.
     *
     * @param $pipe class name or service name for stage.
     * @param string $methodName method name for this object
     * @param string $params additional params
     * @return $this
     */
    public function pipe($pipe, $methodName = '', ...$params)
    {
        if ($pipe instanceof Closure) {
            $this->pipes[] = $pipe;
            return $this;
        }

        $pipe = [
            $pipe,
            $methodName,
            $params,
            false
        ];
        $this->pipes[] = $pipe;
        return $this;
    }


    /**
     * Add stage without middleware interface.
     *
     * @param $pipe description for stage
     * @param string $methodName
     * @param array ...$params
     * @return $this
     */
    public function pipeForContainer($pipe, $methodName = '', ...$params)
    {
        if ($pipe instanceof Closure) {
            $this->pipes[] = $pipe;
            return $this;
        }

        $pipe = [
            $pipe,
            $methodName,
            $params,
            true
        ];
        $this->pipes[] = $pipe;
        return $this;
    }

    /**
     * Run the pipeline with a final destination callback.
     *
     * @param  \Closure  $destination
     * @return mixed
     */
    public function then(Closure $destination)
    {
        $pipeline = array_reduce(
            array_reverse($this->pipes), $this->carry(), $this->prepareDestination($destination)
        );

        return $pipeline($this->passable);
    }


    /**
     * Run the pipeline without a final destination callback.
     *
     * @return mixed
     */
    public function execute()
    {
        $pipeline = array_reduce(
            array_reverse($this->pipes), $this->carry(), function($passable){ return $passable; }
        );

        return $pipeline($this->passable);
    }

    /**
     * Get the final piece of the Closure onion.
     *
     * @param  \Closure  $destination
     * @return \Closure
     */
    protected function prepareDestination(Closure $destination)
    {
        return function ($passable) use ($destination) {
            return $destination($passable);
        };
    }

    /**
     * Get a Closure that represents a slice of the application onion.
     *
     * @return \Closure
     */
    protected function carry()
    {
        return function ($stack, $pipe) {
            return function ($passable) use ($stack, $pipe) {
                $method = $this->method;
                if (is_callable($pipe)) {
                    // If the pipe is an instance of a Closure, we will just call it directly but
                    // otherwise we'll resolve the pipes out of the container and call it with
                    // the appropriate method and arguments, returning the results back out.
                    return $pipe($passable, $stack);
                } elseif (is_object($pipe)) {
                    // If the pipe is already an object we'll just make a callable and pass it to
                    // the pipe as-is. There is no need to do any extra parsing and formatting
                    // since the object we're given was already a fully instantiated object.
                    $parameters = [$passable, $stack];
                } else {
                    list($name, $methodFromString, $parameters, $needContainer) = $this->parsePipeData($pipe);
                    if($methodFromString) {
                        $method = $methodFromString;
                    }

                    if (! $parameters) {
                        $parameters = [];
                    }

                    // If the pipe is a string we will parse the string and resolve the class out
                    // of the dependency injection container. We can then build a callable and
                    // execute the pipe function giving in the parameters that are required.
                    if (is_string($name)) {
                        $pipe = $this->getContainer()->get($name);
                    } else {
                        $pipe = $name;
                    }

                    $parameters = array_merge([$passable, $stack], $parameters);
                    //
                    if ($needContainer) {
                        $this->initialize($pipe);
                        $pipeReturn = function ($data, $next) use ($pipe, $method) {
                            if ($method) {
                                $data = $pipe->{$method}($data);
                            } else {
                                $data = $pipe($data);
                            }
                            return $next($data);
                        };
                        return $pipeReturn(...$parameters);
                    }
                }

                $this->initialize($pipe);

                if ($method) {
                    return method_exists($pipe, $method)
                        ? $pipe->{$method}(...$parameters)
                        : $pipe(...$parameters);
                } else {
                    return $pipe(...$parameters);
                }
            };
        };
    }

    /**
     * Parse full pipe string to get name and parameters.
     *
     * @param  string $pipe
     * @return array
     */
    protected function parsePipeData($pipe)
    {
        if (is_array($pipe)) {
            if (! $pipe[0]) {
                throw  new Exception('I need to know name of service (class)');
            }
            $name       = $pipe[0];
            $method     = $pipe[1];
            $parameters = $pipe[2];
            $needContainer = $pipe[3];
        } else {
            list($name, $method, $parameters) = array_pad(explode(':', $pipe, 3), 3, null);
            $needContainer = null;
        }

        if (is_string($parameters)) {
            $parameters = explode(',', $parameters);
        }

        return [$name, $method, $parameters, $needContainer];
    }

    /**
     * Get the service locator instance.
     *
     * @return ContainerInterface
     * @throws \RuntimeException
     */
    protected function getContainer()
    {
        if (! $this->container) {
            $this->container = new PipeIsClassName();
        }

        return $this->container;
    }
}
