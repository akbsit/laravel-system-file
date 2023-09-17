<?php namespace Akbsit\SystemFile\Helper;

use function DusanKasan\Knapsack\getOrDefault;
use function DusanKasan\Knapsack\first;
use function DusanKasan\Knapsack\last;

use DusanKasan\Knapsack\Exceptions\ItemNotFound;
use ArrayAccess;
use Traversable;

/**
 * Class CollectionHelper
 * @package Akbsit\SystemFile\Helper
 */
class CollectionHelper
{
    /**
     * @param ArrayAccess|Traversable|array $array
     * @param string|int|null               $key
     * @param mixed                         $default
     *
     * @return mixed
     */
    public static function get($array, $key, $default = null)
    {
        return getOrDefault($array, $key, $default);
    }

    /**
     * @param ArrayAccess|Traversable|array $array
     * @param mixed                         $default
     *
     * @return mixed
     */
    public static function first($array, $default = null)
    {
        try {
            return first($array);
        } catch (ItemNotFound $oException) {
            return $default;
        }
    }

    /**
     * @param ArrayAccess|Traversable|array $array
     * @param mixed                         $default
     *
     * @return mixed
     */
    public static function last($array, $default = null)
    {
        try {
            return last($array);
        } catch (ItemNotFound $oException) {
            return $default;
        }
    }
}
