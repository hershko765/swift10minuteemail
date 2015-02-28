<?php

namespace AppBundle\Entities\Handler\Email\History;

use SourceBundle\Base\HandlerManager;
use SourceBundle\Interfaces\Handler;
use Doctrine\Bundle\DoctrineBundle\Registry;

class Collect extends HandlerManager implements Handler\Collect {

	/**
	 * @var Registry
	 * @DI (alias=doctrine)
	 */
	protected $em;

	public  function execute()
	{
		return $this->em->getRepository('AppBundle:Model\Email\History')
			->collect($this->filters, $this->paging, $this->settings);
	}
}
 