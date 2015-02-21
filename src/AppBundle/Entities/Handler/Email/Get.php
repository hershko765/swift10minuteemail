<?php

namespace AppBundle\Entities\Handler\Email;

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
		$email = $this->em->getRepository('AppBundle:Model\Email')->find($this->id);

		if ( ! $email)
			throw new NotFoundHttpException('Email Not Found!');

		return $email;
	}
}
 