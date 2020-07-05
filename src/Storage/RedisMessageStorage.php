<?php

namespace App\Storage;

use App\Entity\Message;
use App\Entity\MessageQueue;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use SymfonyBundles\RedisBundle\Redis\ClientInterface;

class RedisMessageStorage implements MessageStorageInterface
{
    private ClientInterface       $redis;
    private SerializerInterface   $serializer;
    
    public function __construct(ClientInterface $redis, SerializerInterface $serializer)
    {
        $this->redis      = $redis;
        $this->serializer = $serializer;
    }
    
    public function insert(Message $message): MessageQueue
    {
        $serialized = $this->serializer->serialize($message, 'json');
        
        $this->redis->set($message->getId(), $serialized);
        
        return $this->getQueue();
    }
    
    public function pop(): ?Message
    {
        $queue = $this->getQueue();
        
        return $queue->isEmpty() ? null : $queue->extract();
    }
    
    public function remove(Message $message): bool
    {
        return (bool)$this->redis->remove($message->getId());
    }
    
    public function find(string $id): ?Message
    {
        $data = $this->redis->get($id);
        
        if (!$data) {
            return null;
        }
        
        /** @var Message $message */
        $message = $this->serializer->deserialize($data, Message::class, 'json');
        
        return $message;
    }
    
    public function getQueue(): MessageQueue
    {
        $messageQueue = new MessageQueue();
        $keys         = $this->redis->keys('*');
        
        foreach ($keys as $key) {
            $message = $this->find($key);
            $messageQueue->insert($message, $message->getPriority());
        }
        
        return $messageQueue;
    }
}
