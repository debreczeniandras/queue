<?php

namespace App\Entity\Shop;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table("order_item")
 */
class OrderItem
{
    /**
     * @var Order
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="Dbn\ProductBundle\Entity\Order", inversedBy="orders")
     */
    private $order;

    /**
     * @var Article
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="Dbn\ProductBundle\Entity\Article")
     */
    private $article;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $count;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $subtotal;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $discount;
}
