<?php
/**
 *
 * Stage for pipeline catch exception by default.
 * You can use it for your own.
 *
 * PHP version 5.6, 7.0 and 7.1
 *
 * @package andydune/pipeline
 * @link  https://github.com/AndyDune/Pipeline for the canonical source repository
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Andrey Ryzhov  <info@rznw.ru>
 * @copyright 2019 Andrey Ryzhov
 *
 */


namespace AndyDune\Pipeline\Stage;
use Exception;

class ExceptionCatch
{
    public function __invoke($context, $next)
    {
        try {
            return $next($context);
        } catch (Exception $e) {
            return $e;
        }
    }
}