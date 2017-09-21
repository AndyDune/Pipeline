# AndyDune\Pipeline

[![Build Status](https://travis-ci.org/AndyDune/Pipeline.svg?branch=master)](https://travis-ci.org/AndyDune/Pipeline)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Packagist Version](https://img.shields.io/packagist/v/andydune/pipeline.svg?style=flat-square)](https://packagist.org/packages/andydune/pipeline)
[![Total Downloads](https://img.shields.io/packagist/dt/andydune/pipeline.svg?style=flat-square)](https://packagist.org/packages/andydune/pipeline)


This package provides a pipeline pattern implementation. It base on middleware approach.


## Usage

Operations in a pipeline, stages, can be anything that satisfies the `callable`
type-hint. So closures and anything that's invokable is good.

```php
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
