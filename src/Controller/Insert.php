<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\Type\MessageType;
use App\Manager\MessageManager;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Swagger\Annotations as SWG;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Insert extends AbstractFOSRestController
{
    /**
     * INSERT message to the queue.
     *
     * @param Message        $message
     * @param Request        $request
     * @param MessageManager $manager
     *
     * @return FormInterface|Response
     *
     * @ParamConverter("message", converter="fos_rest.request_body")
     * @Rest\Post("/queue")
     * @SWG\Parameter(name="message",
     *     in="body",
     *     required=true,
     *     @SWG\Schema(
     *          type="object",
     *          ref=@Model(type=MessageType::class, )
     *     )
     * )
     * @SWG\Response(
     *     response=201,
     *     description="Message inserted.",
     *     headers={
     *      @SWG\Header(header="Location", description="Link to created message", type="string"),
     *      @SWG\Header(header="X-Count", description="The size of the queue after insert", type="integer"),
     * },
     *     @Model(type=Message::class)
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="When a validation error has occured."
     * )
     * @SWG\Tag(name="Queue")
     */
    public function __invoke(Message $message, Request $request, MessageManager $manager): Response
    {
        $form = $this->createForm(MessageType::class, $message);
        $form->submit(null, false);
        
        if (!$form->isValid()) {
            return $this->handleView($this->view($form)->setFormat('json'));
        }
        
        $queue = $manager->insert($message);
        
        $view = $this->view($message, 201)
                     ->setHeader('Location', $this->generateUrl('get_message', ['id' => $message->getId()]))
                     ->setHeader('X-Count', $queue->count())
                     ->setFormat('json');
        
        return $this->handleView($view);
    }
}
