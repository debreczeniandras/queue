<?php

namespace App\Controller;

use App\Entity\Message;
use App\Manager\MessageManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Response;

class All extends AbstractFOSRestController
{
    /**
     * Get the queue
     *
     * @param MessageManager $manager
     *
     * @return Response
     *
     * @Rest\Get("/queue", name="get_messages")
     * @SWG\Response(
     *     response=200,
     *     description="Returns the queue",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Message::class))
     *     )
     * )
     * @SWG\Response(
     *     response=404,
     *     description="When no messages in the queue."
     * )
     * @SWG\Tag(name="Message")
     */
    public function __invoke(MessageManager $manager): Response
    {
        $messageQueue = $manager->findAll();
        $view         = $this->view($messageQueue, $messageQueue->count() ? 200 : 404)
                             ->setHeader('X-Count', $messageQueue->count())
                             ->setFormat('json');
        
        return $this->handleView($view);
    }
}
