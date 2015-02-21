<?php
namespace AppBundle\Controller\API;

use AppBundle\Entities\Handler\Email;
use SourceBundle\Base;


use SourceBundle\Helpers\Arr;

use SourceBundle\Helpers\Dir;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\Annotations;

// Annotations dependency
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Component\Form\FormTypeInterface;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;


class EmailController extends Base\Controller {

	/**
	 * Get single Email,
	 *
	 * @Annotations\View(templateVar="Email")
	 * @param int     $id      the page id
	 * @return array
	 *
	 * @throws NotFoundHttpException when page not exist
	 */
	public function getEmailAction($id)
	{
        /**
         * @var $handler Email\Get
         */
        $handler = $this->getHandler('Email', 'get');

		return $handler->setID($id)->execute();
	}

	/**
	 * Get single Email,
	 *
	 * @Annotations\View(templateVar="Email")
     * @param Request $request the request object
	 * @param int     $id      the page id
	 *
	 * @return array
	 *
	 * @throws NotFoundHttpException when page not exist
	 */
	public function getEmailsAction(Request $request)
	{
		$query    = $request->query->all();
		$paging   = Arr::extract($query, [ 'limit', 'offset',  'order', 'page', 'sort' ]);
		$filters  = Arr::extract($query, [  ]);
		$settings = Arr::extract($query, [ 'select', 'index', 'selectBox', 'group' ]);
		
        $filters = array_filter($filters);

        if ( ! $request->cookies->get('vid') ) throw new PreconditionFailedHttpException('No Permissions');
        $filters['visitor'] = $request->cookies->get('vid');

        /**
         * @var $handler Email\Collect
         */
		$emails = $this->getHandler('Email', 'Collect')
            ->setFilters($filters)
            ->setPaging($paging)
            ->setSettings($settings)
            ->execute();

        $emails_arr = [];
        foreach($emails->toArray() as $email)
        {
            $emails_arr[] = $email->asArray();
        }

        return $emails_arr;
	}

    /**
     * Create a Email from the submitted data
     *
     * @Annotations\View(templateVar="Email")
     *
     * @param Request $request the request object
     * @return array
     */
    public function postEmailAction(Request $request)
    {
        $post = $request->request->all();

        /**
         * @var $handler Email\Create
         */
        $handler = $this->getHandler('Email', 'Create');

        return $handler->setData($post)->execute();
    }


    /**
     * Create a Email from the submitted data
     *
     * @Annotations\View(templateVar="Email")
     *
     * @param Request $request the request object
     *
     * @return array
     */
    public function putEmailAction(Request $request, $id)
    {
        $post = $request->request->all();

        /**
         * @var $handler Email\Update
         */
        $handler = $this->getHandler('Email', 'Update');

        return $handler->setData($post, $id)->execute();
    }
    
    /**
     * Delete Email,
     *
     * @Annotations\View(templateVar="Email")
     *
     * @param Request $request the request object
     * @param int     $id      the page id
     * @return array
     *
     * @throws NotFoundHttpException when page not exist
     */
    public function deleteEmailAction(Request $request, $id)
    {
        /**
         * @var $handler Email\Delete
         */
        $handler  = $this->getHandler('Email', 'Delete');

        return $handler->setID($id)->execute();
    }

    /**
     * Get single Email,
     *
     * @Annotations\View(templateVar="Email")
     * @post("/api/v1/emails/log")
     * @param Request $request the request object
     * @param int     $id      the page id
     * @return array
     *
     * @throws NotFoundHttpException when page not exist
     */
    public function logEmailAction(Request $request)
    {
        $post = $request->request->all();

        /**
         * @var $handler Email\Log
         */
        $handler  = $this->getHandler('Email', 'Log');

        $email = $handler->setData($post)->execute();

        return $email->asArray();
    }
}
