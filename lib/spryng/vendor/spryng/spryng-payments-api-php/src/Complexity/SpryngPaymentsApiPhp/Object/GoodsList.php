<?php

namespace SpryngPaymentsApiPhp\Object;

use SpryngPaymentsApiPhp\Object\Good;

/**
 * Class GoodsList
 * @package SpryngPaymentsApiPhp\Object
 */
class GoodsList
{
    /**
     * Array which holds the Good objects.
     *
     * @var array
     */
    protected $goods = array();

    /**
     * Adds a new good to the list.
     *
     * @param Good $good
     */
    public function add(Good $good)
    {
        array_push($this->goods, $good);
    }

    /**
     * Removes a good from the list.
     *
     * @param $key
     */
    public function remove($key)
    {
        unset($this->goods[$key]);
    }

    /**
     * Calculates the total amount for this order.
     *
     * @return int
     */
    public function getTotalAmount()
    {
        $total = 0;
        foreach ($this->goods as $good)
        {
            $total += $good->price;
        }

        return $total;
    }

    /**
     * Turns the goods list into a JSON string.
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->goods);
    }

    public function toArray()
    {
        $goods = array();

        foreach($this->goods as $good)
        {
            array_push($goods, get_object_vars($good));
        }

        return $goods;
    }
}