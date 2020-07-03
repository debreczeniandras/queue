<?php

namespace App\Storage;

use App\Entity\Message;

interface MessageStorageInterface
{
    public function insert(Message $message): bool;
    
    public function pop(): Message;
    
    public function remove(Message $message): bool;
    
    public function find(string $id): Message;
}
