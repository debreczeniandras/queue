<?php

namespace App\Controller;

use App\Entity\Battle;
use App\Entity\GameOptions;
use App\Entity\Player;
use App\Entity\Shot;
use App\Form\Type\MessageType;
use App\Form\Type\GameOptionsType;
use App\Form\Type\ShotType;
use App\Service\BattleWorkflowService;
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
     * Set up game options and prepare for setting up ships.
     *
     * @param GameOptions    $options
     * @param Request        $request
     * @param MessageManager $manager
     *
     * @return FormInterface|Response
     *
     * @ParamConverter("options", converter="fos_rest.request_body")
     * @Rest\Post()
     * @SWG\Parameter(nameresoptions",
     *     in="body",
     *     required=true,
     *     @SWG\Schema(
     *          type="object",
     *          ref=@Model(type=GameOptions::class, groups={"Init"})
     *     )
     * )
     * @SWG\Response(
     *     response=201,
     *     description="The Battle is set.",
     *     headers={@SWG\Header(header="Location", description="Link to created battle", type="string")},
     *     @Model(type=Battle::class, groups={"Init"})
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="When a validation error has occured."
     * )
     * @SWG\Tag(name="Battle")
     */
    public function Insert(GameOptions $options, Request $request, MessageManager $manager): Response
    {
        $form = $this->createForm(GameOptionsType::class, $options);
        $form->submit($request->request->all(), false);
        
        if (!$form->isValid()) {
            return $this->handleView($this->view($form)->setFormat('json'));
        }
        
        $battle = $manager->create($options);
        
        $view = $this->view($battle, 201)
                     ->setContext((new Context())->setGroups(['Init']))
                     ->setHeader('Location', $this->generateUrl('get_battle', ['id' => $battle->getId()]))
                     ->setFormat('json');
        
        return $this->handleView($view);
    }
}
