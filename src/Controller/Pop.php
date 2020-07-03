<?php

namespace App\Controller;

use App\Entity\Message;
use App\Manager\MessageManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

class Pop extends AbstractFOSRestController
{
    /**
     * POP a message from the queue.
     *
     * @param MessageManager $manager
     *
     * @return FormInterface|Response
     *
     * @Rest\Post("/queue/messages")
     * @SWG\Response(
     *     response=200,
     *     description="Last message from the queue is popped.",
     *     @Model(type=Message::class)
     * )
     * @SWG\Response(
     *     response=404,
     *     description="When the queue is empty."
     * )
     * @SWG\Tag(name="Queue")
     */
    public function __invoke(MessageManager $manager): Response
    {
        $message = $manager->pop();
        $view    = $this->view($message, 200)
                        ->setFormat('json');
        
        return $this->handleView($view);
    }
}
