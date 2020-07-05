<?php

namespace App\Storage;

use App\Entity\Message;
use App\Entity\MessageQueue;

interface MessageStorageInterface
{
    public function insert(Message $message): MessageQueue;
    
    public function pop(): ?Message;
    
    public function remove(Message $message): bool;
    
    public function find(string $id): ?Message;
}
