# AndyDune\Pipeline

[![Build Status](https://travis-ci.org/AndyDune/Pipeline.svg?branch=master)](https://travis-ci.org/AndyDune/Pipeline)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Packagist Version](https://img.shields.io/packagist/v/andydune/pipeline.svg?style=flat-square)](https://packagist.org/packages/andydune/pipeline)
[![Total Downloads](https://img.shields.io/packagist/dt/andydune/pipeline.svg?style=flat-square)](https://packagist.org/packages/andydune/pipeline)


This package provides a pipeline pattern implementation. It base on middleware approach.

Installation
------------

Installation using composer:

```
composer require andydune/pipeline 
```
Or if composer doesn't install globa:
```
php composer.phar require andydune/pipeline
```
Or edit your `composer.json`:
```
"require" : {
     "andydune/pipeline": "^0"
}

```
And execute command:
```
php composer.phar update
```


## Usage

Operations in a pipeline, stages, can be anything that satisfies the `callable`
type-hint. So closures and anything that's invokable is good.

```php
use AndyDune\Pipeline\Pipeline;
 
$pipeline = new Pipeline();

$stages = [
    function($contest, $next) {
        $contest += 100;
        return $next($contest);    
    },
    function($contest, $next) {
        $contest += 10;
        return $next($contest);    
    }
];
$result = $pipeline->through($stages)->send(1)
->then(function ($context) {
            $context += 1000;
            return $context;});
$result // 1111
```

## Types of stages

Basically each stage can be `Closure`:
```php
$pipeline = new Pipeline();
$pipeline->pipe(function ($context, $next) {
    $context['closure'] = 'was here';
    return $next($context); 
});
```

It can be an instance of class with callable interface:
```php
$instance = new class() {
    public function __invoke($context, $next) 
    {
        $context['invoke'] = 'was here';
        return $next($context); 
    }
}

$pipeline = new Pipeline();
$pipeline->pipe($instance);
```

It can be a class name with method __invoke or any method you describe:
```php
class Trim
{
    public function __invoke($context, $next) 
    {
        $context['class_invoke'] = 'was here';
        return $next($context); 
    }
}

$pipeline = new Pipeline();
$pipeline->pipe(Trim::class);

class Trim
{
    public function handle($context, $next) 
    {
        $context['class_invoke'] = 'was here';
        return $next($context); 
    }
}

$pipeline = new Pipeline();
$pipeline->pipe(Trim::class,  'handle');
```


## Container provider (service provider)

String can de passed as pipeline stage. By default it is a class name. 
It's make possible by using default provider: `AndyDune\Pipeline\PipeIsClassName`  
You can set your own provider, with injections you need. Your class must implements interface: `Interop\Container\ContainerInterface`

Use your provider as parameter for pipeline constructor:
```php
use Interop\Container\ContainerInterface;

class SpecialPipelineContainer implements ContainerInterface
{
    /**
     * @var  \Rzn\ObjectBuilder
     */
    protected $objectBuilder;

    public function get($name)
    {
        /*
        do anything to build object using $name
        */
        return $object;
    }
    
    public function has($name)
    {
        /*
        do anything to check required object can be build  
        */
        return $exist;
    }
}

$container = new SpecialPipelineContainer();
$pipeLine = new Pipeline($container);

$pipeLine->pipe('any string to get stage from container');

```

## Additional parameters for stage

Sometimes you need to pass any number of additional parameters to pipe stage during description. 
It needs for test and it very good for more flexibility of stage. It increases reuse of class.

Lets look at the example. It is very simple example.

Here is example class for stage:  
```php
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
    protected function handle($number, $power)
    {
        return pow($number, $power);
    }
}
``` 

Lets use it:

```php
    use use AndyDune\Pipeline\Pipeline;
    use AndyDune\Pipeline\Example;
    
    $pipeline = new Pipeline();
    $pipeline->send(2);
    $pipeline->pipe(PowerOfNumber::class);
    $result = $pipeline->execute(); // == 4

    $pipeline = new Pipeline();
    $pipeline->send(2);
    $pipeline->pipe(PowerOfNumber::class, null, 3);
    $result = $pipeline->execute(); // == 8
    
    $pipeline = new Pipeline();
    $pipeline->send(2);
    $pipeline->pipe(PowerOfNumber::class, null, 4);
    $result = $pipeline->execute(); // == 16
```   

## Exceptions

Package has not integrated exception catch support. It is simple for you  to include exception _try-catch_ block into one of pipeline stages.
```php
    use AndyDune\Pipeline\Pipeline;
    $pipeline = new Pipeline();
    $pipeline->send(['zub' => 'kovoy']);
    $pipeline->pipe(function ($context, $next) {
        try {
            return $next($context);
        } catch (Exception $e) {
            $context['exception'] = 'caught';
        }
        return $context;
    });

    $pipeline->pipe(function ($context, $next) {
        $context['action'] = 'before_exception';
        throw new Exception();
        return $next($context); // it will be never execute
    });
     
    // This stage will never execute
    $pipeline->pipe(function ($context, $next) {
        $context['after_exception'] = 'ignored';
        return $next($context);
    });

    $result = $pipeline->execute();
    array_key_exists('zub', $result);       // true
    array_key_exists('exception', $result); // true
    array_key_exists('action', $result);    // false
    array_key_exists('after_exception', $result); // false

```  

There is a class you may use as stage for your pipeline for catch exception  in this package.
```php
 
    $pipeline = new Pipeline();
    $pipeline->send(['zub' => 'kovoy']);
    $pipeline->pipe(AndyDune\Pipeline\Stage\ExceptionCatch::class);
    $pipeline->pipe(function ($context, $next) {
        $context['action'] = 'before_exception';
        throw new Exception('jump');
    });
    $result = $pipeline->execute();
    
    $result instancheof \Exception // true
```
