<?php
namespace AppBundle\Entities\Handler\Visitor;

use AppBundle\Entities\Model\Visitor;
use JMS\Serializer\Tests\Serializer\DateIntervalFormatTest;
use SourceBundle\Base\Repository\Repository;
use SourceBundle\Base\HandlerManager;
use SourceBundle\Interfaces\Handler;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Validator\Validator;
use Symfony\Component\HttpFoundation\Request;

class Extend extends HandlerManager implements Handler {

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
    protected $id;

    public function setID($id, array $options = [])
    {
        $this->id = $id;

        return $this;
    }

    public function execute()
    {
        // Get repository and filter data to contain only allowed data
        $repo = $this->em->getRepository('AppBundle:Model\Visitor');

        /**
         * @var $visitor Visitor
         */
        $visitor = $repo->find($this->id);

        /**
         * @var $close_date \DateTime
         */
        $close_date = $visitor->getCloseDate();
        $time = $close_date->getTimestamp();
        $time = $time + 600;
        $new_date = new \DateTime();
        $new_date->setTimestamp($time);

        $visitor->setCloseDate($new_date);

        $repo->save($visitor);

        // Save model and return data response with the new ID
        return  $visitor;
    }
}