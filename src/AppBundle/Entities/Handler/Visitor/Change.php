<?php

namespace AppBundle\Entities\Handler\Visitor;

use AppBundle\Entities\Model\Visitor;
use Doctrine\Common\Collections\ArrayCollection;
use SourceBundle\Base\Repository\Repository;
use SourceBundle\Base\HandlerManager;
use SourceBundle\Interfaces\Handler;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;
use Symfony\Component\Validator\Validator;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entities\Handler\Visitor as VisitorHandler;

class Change extends HandlerManager implements Handler {

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
    protected $address;

    protected $not_allowed = [
        'admin', 'swiftmail', 'django', 'django576'
    ];

    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }

    public function execute()
    {
        // Get repository and filter data to contain only allowed data
        $repo = $this->em->getRepository('AppBundle:Model\Visitor');

        $visitor_id = $this->request->cookies->get('vid');

        if ( ! $visitor_id) throw new NotFoundHttpException('Visitor not found !');

        /**
         * @var $visitor Visitor
         */
        $visitor = $this->getHandler('Visitor', 'Get')
            ->setID($visitor_id)
            ->execute();

        /**
         * @var $register_handler VisitorHandler\Register
         */
        if ( ! $this->address)
        {
            $register_handler = $this->getHandler('Visitor', 'Register');
            $this->address = $register_handler->generateVisitor(10);
        }
        else
        {
            if (in_array($this->address, $this->not_allowed))
                throw new PreconditionFailedHttpException('Email address is not allowed! please select anther one');
        }
        /**
         * @var $exists_address ArrayCollection
         */
        $exists_address = $this->getHandler('Visitor', 'Collect')
            ->setFilters([ 'email' => $this->address ])
            ->execute();

        if ($exists_address->count())
        {
            throw new PreconditionFailedHttpException('email already exists, please select anther one');
        }

        $this->address = str_replace('@swift10minutemail', '', $this->address);

        if ( ! filter_var($this->address.'@swift10minutemail.com', FILTER_VALIDATE_EMAIL))
            throw new PreconditionFailedHttpException('Invalid Email Address');

        $visitor->set([
            'email' => $this->address,
        ]);

        // Save model and return data response with the new ID
        return $repo->save($visitor);
    }
}