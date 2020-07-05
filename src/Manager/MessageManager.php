<?php

namespace App\Manager;

use App\Entity\Message;
use App\Entity\MessageQueue;
use App\Storage\RedisMessageStorage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MessageManager
{
    private RedisMessageStorage $storage;
    
    public function __construct(RedisMessageStorage $storage)
    {
        $this->storage = $storage;
    }
    
    public function insert(Message $message): MessageQueue
    {
        return $this->storage->insert($message);
    }
    
    public function pop(): Message
    {
        $lastMessage = $this->storage->pop();
        
        if (!$lastMessage) {
            throw new NotFoundHttpException('The Queue is empty');
        }
        
        $this->remove($lastMessage);
        
        return $lastMessage;
    }
    
    public function remove(Message $message): bool
    {
        return $this->storage->remove($message);
    }
    
    public function find(string $id): Message
    {
        $message = $this->storage->find($id);
    
        if (is_null($message)) {
            throw new NotFoundHttpException('This Message does not exist');
        }
        
        return $message;
    }
    
    public function findAll(): MessageQueue
    {
        return $this->storage->getQueue();
    }
}
