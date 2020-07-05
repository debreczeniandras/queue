<?php

namespace App\Manager;

use App\Entity\Message;
use App\Entity\MessageQueue;
use App\Storage\RedisMessageStorage;

class MessageManager
{
    private RedisMessageStorage $storage;
    
    public function __construct(RedisMessageStorage $storage)
    {
        $this->storage = $storage;
    }
    
    public function insert(Message $message): bool
    {
        return $this->storage->insert($message);
    }
    
    public function pop(): Message
    {
        $lastMessage = $this->storage->pop();
        
        $this->remove($lastMessage);
        
        return $lastMessage;
    }
    
    public function remove(Message $message): bool
    {
        return $this->storage->remove($message);
    }
    
    public function find(string $id): Message
    {
        return $this->storage->find($id);
    }
    
    public function findAll(): MessageQueue
    {
        return $this->storage->getQueue();
    }
}
