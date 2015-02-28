<?php

namespace AppBundle\Entities\Handler\Email\History;

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
		$email = $this->em->getRepository('AppBundle:Model\Email\History')->find($this->id);

		if ( ! $email)
			throw new NotFoundHttpException('Email History Not Found!');

		return $email;
	}
}
 