<?php
namespace AppBundle\Entities\Handler\Email;

use AppBundle\Entities\Model\Email;
use SourceBundle\Base\HandlerManager;
use SourceBundle\Helpers\Arr;
use SourceBundle\Interfaces\Handler;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;
use Symfony\Component\Validator\Validator;
use AppBundle\Entities\Repository;

class Log extends HandlerManager implements Handler {

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

    /**
     * @return Email
     * @throws \Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException
     */
    public function execute()
	{
        /**
         * Get repository and filter data to contain only allowed data
         * @var $repo Repository\Email
         * @var $repo_visitor Repository\Visitor
         */
        $repo = $this->em->getRepository('AppBundle:Model\Email');
        $repo_visitor = $this->em->getRepository('AppBundle:Model\Visitor');

        $visitor_email = Arr::get(Arr::get(Arr::get($this->data, 'to'), 0), 'address');
        $visitor_email = preg_replace('/(.*)(\@swift10minutemail\.com)/', '$1', $visitor_email);

        if ( ! $visitor_email) throw new PreconditionFailedHttpException('Missing "To" Address');

		$email = new Email();

        $visitor = $repo_visitor->collect([ 'email' => $visitor_email ]);
        $visitor = $visitor->first();

        $email->setVisitor($visitor);

        $from = Arr::get(Arr::get($this->data, 'from'), 0);
        $email->set([
            'headers'      => Arr::get($this->data, 'headers', []),
            'from_address' => Arr::get($from, 'address'),
            'from_name'    => Arr::get($from, 'name'),
            'subject'      => Arr::get($this->data, 'subject', 'No Subject'),
            'content'      => Arr::get($this->data, 'html', Arr::get($this->data, 'text', ''))
        ]);

        return $repo->save($email);
	}
}