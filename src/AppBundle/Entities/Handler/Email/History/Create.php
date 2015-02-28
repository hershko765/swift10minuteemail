<?php
namespace AppBundle\Entities\Handler\Email\History;

use AppBundle\Entities\Model\Email;
use SourceBundle\Base\HandlerManager;
use SourceBundle\Helpers\Arr;
use SourceBundle\Interfaces\Handler;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator;
use AppBundle\Entities\Repository;

class Create extends HandlerManager implements Handler {

	/**
	 * @var Registry
	 * @DI (alias=doctrine)
	 */
	protected $em;

	/**
	 * @var Validator
	 * @DI (alias=validator)
	 */
	protected $validate;

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
		$email_history = new Email\History();

        /**
         * Get repository and filter data to contain only allowed data
         * @var $repo Repository\Email
         */
        $repo = $this->em->getRepository('AppBundle:Model\Email\History');
		$repo->hydrate($this->data, $email_history, Repository\Email\History::PERM_CREATE);

        return $repo->save($email_history);
	}
}