<?php

namespace AppBundle\Entities\Handler\Email;

use AppBundle\Entities\Model\Email;
use SourceBundle\Base\HandlerManager;
use SourceBundle\Helpers\Arr;
use SourceBundle\Interfaces\Handler;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator;
use AppBundle\Entities\Repository;

class Update extends HandlerManager implements Handler {

	/**
	 * @var Registry
	 * @DI (alias=doctrine)
	 */
	protected $em;


	/**
	 * data to update
	 * @var array
	 */
	protected $data, $id;

	public function setData(array $data, $id = NULL)
	{
		$this->data = $data;
		$this->id = $id;
		return $this;
	}

	public function execute()
	{
        /**
         * Get repository and filter data to contain only allowed data
         * @var $repo Repository\Email
         */
        $repo = $this->em->getRepository('AppBundle:Model\Email');

		// Load model by id, throw exception if nothing found
		$email = $repo->find($this->id);

		if ( ! $email)
			throw new NotFoundHttpException('Email not found for id: '.$this->id);

		$repo->hydrate($this->data, $email, Repository\Email::PERM_UPDATE);

		// Save model into the database and return response
        return $repo->save($email);
	}
}