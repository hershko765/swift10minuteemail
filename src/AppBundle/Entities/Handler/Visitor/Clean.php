<?php

namespace AppBundle\Entities\Handler\Visitor;

use AppBundle\Entities\Model\Email;
use SourceBundle\Base\HandlerManager;
use SourceBundle\Interfaces\Handler;
use Doctrine\Bundle\DoctrineBundle\Registry;

class Clean extends HandlerManager implements Handler {

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
		$visitor_repo = $this->em->getRepository('AppBundle:Model\Visitor');
		$email_history_repo = $this->em->getRepository('AppBundle:Model\Email\History');
		$email_repo = $this->em->getRepository('AppBundle:Model\Email');

        $to_clean = $visitor_repo->collect([ 'is_closed' => date('Y-m-d H:i:s') ]);

        foreach ($to_clean as $model)
        {
            $emails = $model->getEmails();

            if ($emails->count())
            {
                foreach($emails as $email)
                {
                    $email_history = new Email\History();
                    $email_history->set($email->asArray());

                    $email_history_repo->save($email_history);
                    $email_repo->delete($email);
                }
            }

            $visitor_repo->delete($model);
        }
	}
}
 