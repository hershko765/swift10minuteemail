<?php
namespace AppBundle\Controller\API;

use AppBundle\Entities\Handler\Visitor;
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


class VisitorController extends Base\Controller {

	/**
	 * Get single Visitor,
	 *
	 * @Annotations\View(templateVar="Visitor")
	 * @param int     $id      the page id
	 * @return array
	 *
	 * @throws NotFoundHttpException when page not exist
	 */
	public function getVisitorAction($id)
	{
        /**
         * @var $handler Visitor\Get
         */
        $handler = $this->getHandler('Visitor', 'get');

		return $handler->setID($id)->execute();
	}

	/**
	 * Get single Visitor,
	 *
	 * @Annotations\View(templateVar="Visitor")
     * @param Request $request the request object
	 * @param int     $id      the page id
	 *
	 * @return array
	 *
	 * @throws NotFoundHttpException when page not exist
	 */
	public function getVisitorsAction(Request $request)
	{
		$query    = $request->query->all();
		$paging   = Arr::extract($query, [ 'limit', 'offset',  'order', 'page', 'sort' ]);
		$filters  = Arr::extract($query, [  ]);
		$settings = Arr::extract($query, [ 'select', 'index', 'selectBox', 'group' ]);
		
        $filters = array_filter($filters);

        if ( ! $request->cookies->get('vid') ) throw new PreconditionFailedHttpException('No Permissions');
        $filters['visitor'] = $request->cookies->get('vid');

        /**
         * @var $handler Visitor\Collect
         */
		$visitors = $this->getHandler('Visitor', 'Collect')
            ->setFilters($filters)
            ->setPaging($paging)
            ->setSettings($settings)
            ->execute();

        $visitors_arr = [];
        foreach($visitors->toArray() as $visitor)
        {
            $visitors_arr[] = $visitor->asArray();
        }

        return $visitors_arr;
	}

    /**
     * Create a Visitor from the submitted data
     *
     * @Annotations\View(templateVar="Visitor")
     *
     * @param Request $request the request object
     * @return array
     */
    public function postVisitorAction(Request $request)
    {
        $post = $request->request->all();

        /**
         * @var $handler Visitor\Create
         */
        $handler = $this->getHandler('Visitor', 'Create');

        return $handler->setData($post)->execute();
    }


    /**
     * Create a Visitor from the submitted data
     *
     * @Annotations\View(templateVar="Visitor")
     *
     * @param Request $request the request object
     *
     * @return array
     */
    public function putVisitorAction(Request $request, $id)
    {
        $post = $request->request->all();

        /**
         * @var $handler Visitor\Update
         */
        $handler = $this->getHandler('Visitor', 'Update');

        return $handler->setData($post, $id)->execute();
    }
    
    /**
     * Delete Visitor,
     *
     * @Annotations\View(templateVar="Visitor")
     *
     * @param Request $request the request object
     * @param int     $id      the page id
     * @return array
     *
     * @throws NotFoundHttpException when page not exist
     */
    public function deleteVisitorAction(Request $request, $id)
    {
        /**
         * @var $handler Visitor\Delete
         */
        $handler  = $this->getHandler('Visitor', 'Delete');

        return $handler->setID($id)->execute();
    }

    /**
     * Get single Visitor,
     *
     * @Annotations\View(templateVar="Visitor")
     * @get("/api/v1/visitors/extend/{id}")
     * @param Request $request the request object
     * @param int     $id      the page id
     * @return array
     *
     * @throws NotFoundHttpException when page not exist
     */
    public function extendVisitorAction(Request $request, $id)
    {
        /**
         * @var $handler Visitor\Extend
         */
        $handler  = $this->getHandler('Visitor', 'Extend');

        $visitor = $handler->setID($id)->execute();

        return $visitor->asArray();
    }

    /**
     * Change Email Address
     * @get("/api/v1/visitors/change_address/{address}")
     * @throws NotFoundHttpException when page not exist
     * @Annotations\View(templateVar="Visitor")
     */
    public function changeVisitorAddressAction(Request $request, $address = NULL)
    {
        /**
         * @var $handler Visitor\Change
         */
        $handler  = $this->getHandler('Visitor', 'Change');
        $visitor = $handler->setAddress($address)->execute();

        return $visitor->asArray();
    }
}
