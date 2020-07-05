<?php

namespace App\Entity\Shop;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table("article")
 */
class Article
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column()
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $inStock;
}
