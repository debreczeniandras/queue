<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Swagger\Annotations as SWG;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class MessageQueue extends \SplPriorityQueue
{
    /**
     * @var Message[]
     */
    private iterable $messages;
    
}
