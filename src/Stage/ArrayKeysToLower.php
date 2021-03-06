<?php
/**
 *
 * It get input data as context and if it is array put keys to lower case.
 *
 *  Source array;
        $array = [
            'id' => 12,
            'ID' => 13,
            'aRRay' => [
                'RU' => 'RU',
                'EN' => 'EN',
            ]
        ];
 *
 *
 *  Result array;
        $array = [
            'id' => 13,
            'array' => [
                'ru' => 'RU',
                'en' => 'EN',
            ]
        ];
 *
 * PHP version >= 7.1
 *
 * @package andydune/pipeline
 * @link  https://github.com/AndyDune/Pipeline for the canonical source repository
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Andrey Ryzhov  <info@rznw.ru>
 * @copyright 2019 Andrey Ryzhov
 */



namespace AndyDune\Pipeline\Stage;

class ArrayKeysToLower
{
    /**
     * @param $context
     * @param callable $next
     * @param bool $recursive is recursive, default if true
     * @param null|array|string $keysToCheck keys in $context to process
     * @return mixed
     */
    public function __invoke($context, callable $next, $recursive = true, $keysToCheck = null)
    {
        if (! is_array($context)) {
            return $next($context);
        }
        if ($keysToCheck) {
            if (! is_array($keysToCheck)) {
                $keysToCheck = [$keysToCheck];
            }
            $function = function(&$value, $key) use($keysToCheck, $recursive){
                if (in_array($key, $keysToCheck)) {
                    $value = $this->handleArray($value, $recursive);
                }
            };
            $function->bindTo($this);
            array_walk($context, $function);
            return $next($context);
        }
        return $next($this->handleArray($context, $recursive));

    }

    protected function handleArray($array, $recursive)
    {
        if (! is_array($array)) {
            return $array;
        }

        $resultArray = [];
        foreach($array as $key => $value) {
            $key = $this->toLower($key);
            if ($recursive and is_array($value)) {
                $value = $this->handleArray($value, $recursive);
            }
            $resultArray[$key] = $value;
        }

        return $resultArray;
    }

    protected function toLower($string)
    {
        return strtolower($string);
    }
}