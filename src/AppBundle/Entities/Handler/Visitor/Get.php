<?php

namespace AppBundle\Entities\Handler\Visitor;

use SourceBundle\Base\HandlerManager;
use SourceBundle\Interfaces\Handler;
use Doctrine\Bundle\DoctrineBundle\Registry;
use SourceBundle\Base\Model;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Get extends HandlerManager implements Handler\Get {

	/**
	 * @var Registry
	 * @DI (alias=doctrine)
	 */
	protected $em;

	public function execute()
	{
		$visitor = $this->em->getRepository('AppBundle:Model\Visitor')->find($this->id);

		if ( ! $visitor)
			throw new NotFoundHttpException('Visitor Not Found!');

		return $visitor;
	}
}
 