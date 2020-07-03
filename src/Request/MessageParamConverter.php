<?php

namespace App\Request;

use App\Entity\Message;
use App\Manager\MessageManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class MessageParamConverter implements ParamConverterInterface
{
    /** @var MessageManager */
    private MessageManager $repository;
    
    /** @var SerializerInterface */
    private SerializerInterface $serializer;
    
    public function __construct(MessageManager $repository, SerializerInterface $serializer)
    {
        $this->repository = $repository;
        $this->serializer = $serializer;
    }
    
    public function apply(Request $request, ParamConverter $configuration)
    {
        $options = $configuration->getOptions();
        $param   = $options['requestParam'] ?? $configuration->getName() ?? 'id';
        
        if (!$request->attributes->has($param)) {
            return false;
        }
        
        $value = $request->attributes->get($param);
        
        if (!$value && $configuration->isOptional()) {
            $request->attributes->set($configuration->getName(), null);
            
            return true;
        }
        
        $message = $this->repository->find($value);
        $request->attributes->set($configuration->getName(), $message);
        
        return true;
    }
    
    public function supports(ParamConverter $configuration)
    {
        if (null === $configuration->getClass()) {
            return false;
        }
        
        return $configuration->getClass() === Message::class;
    }
}
