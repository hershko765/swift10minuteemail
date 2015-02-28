<?php

namespace AppBundle\Entities\Handler\Visitor;

use AppBundle\Entities\Model\Visitor;
use SourceBundle\Base\Repository\Repository;
use SourceBundle\Base\HandlerManager;
use SourceBundle\Interfaces\Handler;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Validator\Validator;
use Symfony\Component\HttpFoundation\Request;

class Register extends HandlerManager implements Handler {

    /**
     * @var Registry
     * @DI (alias=doctrine)
     */
    protected $em;

    /**
     * @var Request
     * @DI (alias=request)
     */
    protected $request;

    /**
     * Outsource data
     * @var array
     */
    protected $data;

    public function setData(array $data, $id = NULL)
    {
        $this->data = $data;
        return $this;
    }

    public function execute()
    {
        $visitor = new Visitor();

        $visitor->set([
            'email' => $this->generateVisitor(),
            'ip'    => $this->request->getClientIp()
        ]);

        // Get repository and filter data to contain only allowed data
        $repo = $this->em->getRepository('AppBundle:Model\Visitor');

        // Save model and return data response with the new ID
        return $repo->save($visitor);
    }

    public function generateVisitor($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}