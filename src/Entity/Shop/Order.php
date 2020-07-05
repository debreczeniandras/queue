<?php

namespace App\Entity\Shop;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table("order")
 */
class Order
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @var OrderItem[]
     *
     * @ORM\OneToMany(targetEntity="Dbn\ProductBundle\Entity\OrderItem", mappedBy="order")
     */
    private $items;

    /**
     * @var Customer
     *
     * @ORM\ManyToMany(targetEntity="Dbn\ProductBundle\Entity\Customer", inversedBy="orders")
     */
    private $customer;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $total;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $discount;
}
