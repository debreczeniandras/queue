<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Swagger\Annotations as SWG;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class Message
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Valid()
     */
    private string $id;
    
    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @Assert\Valid()
     * @Assert\Type("integer")
     */
    private int $priority;
    
    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    private string $value;
    
    /**
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }
    
    /**
     * @param int $priority
     *
     * @return Message
     */
    public function setPriority(int $priority): Message
    {
        $this->priority = $priority;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
    
    /**
     * @param string $id
     *
     * @return Message
     */
    public function setId(string $id): Message
    {
        $this->id = $id;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
    
    /**
     * @param string $value
     *
     * @return Message
     */
    public function setValue(string $value): Message
    {
        $this->value = $value;
        
        return $this;
    }
    
    /**
     * create a hashed id for storage
     *
     * @return Message
     */
    public static function getInstance(): Message
    {
        $message = new static();
        $message->setId(hash("crc32b", hash('sha256', uniqid(mt_rand(), true), true)));
        
        return $message;
    }
}
