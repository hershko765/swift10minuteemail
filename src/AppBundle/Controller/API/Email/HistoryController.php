<?php
namespace AppBundle\Controller\API\Email;

use AppBundle\Entities\Handler\Email\History;
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


class HistoryController extends Base\Controller {

	/**
	 * Get single Email,
	 *
	 * @Annotations\View(templateVar="History")
	 * @param int     $id      the page id
	 * @return array
	 *
	 * @throws NotFoundHttpException when page not exist
	 */
	public function getHistoryAction($id)
	{
        /**
         * @var $handler History\Get
         */
        $handler = $this->getHandler('Email:History', 'get');

		return $handler->setID($id)->execute();
	}

	/**
	 * Get single Email,
	 *
	 * @Annotations\View(templateVar="History")
     * @param Request $request the request object
	 * @param int     $id      the page id
	 *
	 * @return array
	 *
	 * @throws NotFoundHttpException when page not exist
	 */
	public function getHistoriesAction(Request $request)
	{
		$query    = $request->query->all();
		$paging   = Arr::extract($query, [ 'limit', 'offset',  'order', 'page', 'sort' ]);
		$filters  = Arr::extract($query, [  ]);
		$settings = Arr::extract($query, [ 'select', 'index', 'selectBox', 'group' ]);

        $filters = array_filter($filters);

        if ( ! $request->cookies->get('vid') ) throw new PreconditionFailedHttpException('No Permissions');

        /**
         * @var $handler History\Collect
         */
		$emails = $this->getHandler('Email:History', 'Collect')
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
     * @Annotations\View(templateVar="History")
     *
     * @param Request $request the request object
     * @return array
     */
    public function postHistoryAction(Request $request)
    {
        $post = $request->request->all();

        /**
         * @var $handler History\Create
         */
        $handler = $this->getHandler('Email:History', 'Create');

        return $handler->setData($post)->execute();
    }


    /**
     * Create a Email from the submitted data
     *
     * @Annotations\View(templateVar="History")
     *
     * @param Request $request the request object
     *
     * @return array
     */
    public function putHistoryAction(Request $request, $id)
    {
        $post = $request->request->all();

        /**
         * @var $handler History\Update
         */
        $handler = $this->getHandler('Email:History', 'Update');

        return $handler->setData($post, $id)->execute();
    }
    
    /**
     * Delete Email,
     *
     * @Annotations\View(templateVar="History")
     *
     * @param Request $request the request object
     * @param int     $id      the page id
     * @return array
     *
     * @throws NotFoundHttpException when page not exist
     */
    public function deleteHistoryAction(Request $request, $id)
    {
        /**
         * @var $handler History\Delete
         */
        $handler  = $this->getHandler('Email:History', 'Delete');

        return $handler->setID($id)->execute();
    }
}
