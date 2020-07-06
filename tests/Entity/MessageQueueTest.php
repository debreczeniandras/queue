<?php

namespace App\Tests\Entity;

use App\Entity\Message;
use App\Entity\MessageQueue;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Entity\MessageQueue
 */
class MessageQueueTest extends TestCase
{
    /**
     * @dataProvider provideForInsert
     * @testdox      If result is inserted
     *
     * @param Message $message
     * @param         $expPriority
     * @param         $expValue
     */
    public function testIfInserted(Message $message, $expPriority, $expValue)
    {
        $queue = new MessageQueue();
        $queue->insert($message, $message->getPriority());
        
        /** @var Message $value */
        $value = $queue->extract();
        
        $this->assertSame($expPriority, $value->getPriority());
        $this->assertSame($expValue, $value->getValue());
    }
    
    public function provideForInsert()
    {
        return [
            [
                (Message::getInstance())->setPriority(1)->setValue('Test'),
                1,
                'Test',
            ],
        ];
    }
    
    /**
     * @dataProvider provideForPop
     * @testdox      If the last element is returned in the queue
     *
     * @param Message[] $messages
     * @param           $expLastPriority
     * @param           $expLastValue
     */
    public function testIfLastIsReturned(iterable $messages, $expLastPriority, $expLastValue)
    {
        $queue = new MessageQueue();
        foreach ($messages as $message) {
            $queue->insert($message, $message->getPriority());
        }
        
        /** @var Message $lastElement */
        $lastElement = $queue->extract();
        
        $this->assertSame($expLastPriority, $lastElement->getPriority());
        $this->assertSame($expLastValue, $lastElement->getValue());
    }
    
    public function provideForPop()
    {
        return [
            'in a normal order' => [
                [
                    (Message::getInstance())->setPriority(1)->setValue('Test 1'),
                    (Message::getInstance())->setPriority(2)->setValue('Test 2'),
                    (Message::getInstance())->setPriority(3)->setValue('Test 3'),
                    (Message::getInstance())->setPriority(4)->setValue('Test 4'),
                    (Message::getInstance())->setPriority(5)->setValue('Test 5'),
                ],
                5,
                'Test 5',
            ],
            'in a random order' => [
                [
                    (Message::getInstance())->setPriority(5)->setValue('Test 5'),
                    (Message::getInstance())->setPriority(2)->setValue('Test 2'),
                    (Message::getInstance())->setPriority(1)->setValue('Test 1'),
                    (Message::getInstance())->setPriority(3)->setValue('Test 3'),
                    (Message::getInstance())->setPriority(4)->setValue('Test 4'),
                ],
                5,
                'Test 5',
            ],
            'with identical priorities' => [
                [
                    (Message::getInstance())->setPriority(5)->setValue('Test 5'),
                    (Message::getInstance())->setPriority(2)->setValue('Test 2'),
                    (Message::getInstance())->setPriority(2)->setValue('Test 2'),
                    (Message::getInstance())->setPriority(3)->setValue('Test 3'),
                    (Message::getInstance())->setPriority(4)->setValue('Test 4'),
                ],
                5,
                'Test 5',
            ],
        ];
    }
}
