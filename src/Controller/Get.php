<?php

namespace App\Controller;

use App\Entity\Message;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Response;

class Get extends AbstractFOSRestController
{
    /**
     * Get one message.
     *
     * @param Message $message
     * @param string  $id
     *
     * @return Response
     *
     * @ParamConverter("message", options={"requestParam": "id"})
     * @Rest\Get("/queue/{id}", name="get_message")
     * @SWG\Response(
     *     response=200,
     *     description="Get One Message.",
     *     @Model(type=Message::class)
     * )
     * @SWG\Response(
     *     response=404,
     *     description="When a message with this id can not be found."
     * )
     * @SWG\Tag(name="Message")
     */
    public function find(Message $message): Response
    {
        $view = $this->view($message, 200)
                     ->setFormat('json');
        
        return $this->handleView($view);
    }
}
