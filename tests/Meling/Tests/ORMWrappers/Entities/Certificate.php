<?php
namespace Meling\Tests\ORMWrappers\Entities;

/**
 * Class Certificate
 * @package Meling\Tests\ORMWrappers\Entities
 */
class Certificate extends \Meling\Tests\ORMWrappers\Entity
{
    /**
     * @param int $flag
     * @return bool
     */
    public function priceFlag($flag)
    {
        return false;
    }

}