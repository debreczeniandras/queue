<?php

namespace App\Entity\Shop;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table("customer")
 */
class Customer
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Order[]
     * @ORM\OneToMany(targetEntity="Order", mappedBy="customer")
     */
    private $orders;

    /**
     * @var string
     * @ORM\Column()
     */
    private $name;
}
