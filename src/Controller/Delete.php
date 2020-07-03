<?php

namespace App\Controller;

use App\Entity\Message;
use App\Manager\MessageManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Swagger\Annotations as SWG;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

class Delete extends AbstractFOSRestController
{
    /**
     * Delete One Message from the Queue
     *
     * @param Message        $message
     * @param MessageManager $manager
     *
     * @return FormInterface|Response
     *
     * @ParamConverter("message", options={"requestParam": "id"})
     * @Rest\Delete("/queue/{id}")
     * @SWG\Response(
     *     response=204,
     *     description="The Message is deleted."
     * )
     *
     * @SWG\Tag(name="Message")
     */
    public function __invoke(Message $message, MessageManager $manager): Response
    {
        $manager->remove($message);
        
        $view = $this->view(null, 204)
                     ->setFormat('json');
        
        return $this->handleView($view);
    }
}
