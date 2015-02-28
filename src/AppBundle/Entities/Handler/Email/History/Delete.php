<?php

namespace AppBundle\Entities\Handler\Email\History;

use SourceBundle\Base\HandlerManager;
use SourceBundle\Interfaces\Handler;
use Doctrine\Bundle\DoctrineBundle\Registry;

class Delete extends HandlerManager implements Handler {

	/**
	 * @var Registry
	 * @DI (alias=doctrine)
	 */
	protected $em;

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
		$repository = $this->em->getRepository('AppManagerBundle:Model\Email\History');

        return $repository->delete($this->id);
	}
}
 