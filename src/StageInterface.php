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
 * @copyright 2018 Andrey Ryzhov
 */

namespace AndyDune\Pipeline;


interface StageInterface
{
    /**
     * Any object witch implements this interface may be used as pipeline stage.
     *
     * @param array|object $data
     * @param callable $next
     * @return mixed
     */
    public function execute($data, callable $next);
}