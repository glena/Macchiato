<?php namespace MacchiatoPHP\Macchiato\Example\Models;

/** @Entity */
class Car
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    private $id;
    /** @Column(length=140) */
    public $name;
}
