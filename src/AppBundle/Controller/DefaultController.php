<?php

namespace AppBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SourceBundle\Base\Controller;
use SourceBundle\Helpers\Arr;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entities\Model\Visitor;

class DefaultController extends Controller
{

    /**
     * @Route("/", name="homepage")
     * @Template("index.html.twig")
     */
    public function indexAction(Request $request)
    {
        return [
            'page' => 'home'
        ];
    }

    /**
     * @Route("/10-minute-mail", name="10-minute-mail")
     * @Route("/20-minute-mail", name="20-minute-mail")
     */
    public function swiftMailAction(Request $request)
    {
        $route_name = $request->get('_route');
        $cookies = $request->cookies->all();
        $visitor = NULL;
        $status = 'new';
        if (Arr::get($cookies, 'vid'))
        {
            try {
                /**
                 * @var $visitor Visitor
                 */
                $visitor = $this->getHandler('Visitor', 'Get')
                    ->setID(Arr::get($cookies, 'vid'))
                    ->execute();

                if ($visitor->getCloseDate()->getTimestamp() <= time())
                {
                    $visitor = NULL;
                    throw new NotFoundHttpException('Visitor is out of date');
                }

                $status = 'visitor';
            }
            catch(NotFoundHttpException $e)
            {
                $status = 'renewed';
            }
        }
        else
        {
            /**
             * @var $visitors ArrayCollection
             */
            $visitors = $this->getHandler('Visitor', 'Collect')
                ->setFilters([ 'ip' => $request->getClientIp(), 'is_open' => date('Y-m-d H:i:s') ])
                ->execute();

            if ($visitors->first())
            {
                $visitor = $visitors->first();
                $response = new Response();
                $response->headers->setCookie(new Cookie('vid', $visitor->getId()));
                $response->sendHeaders();
                $status = 'visitor_ip';
            }
        }
        if ( ! $visitor)
        {
            $visitor = $this->getHandler('Visitor', 'Register')->execute();

            $response = new Response();
            $response->headers->setCookie(new Cookie('vid', $visitor->getId()));
            $response->sendHeaders();
        }

        $data = [
            'page' => 'home',
            'visitor' => $visitor,
            'status' => $status
        ];

        return $this->render($route_name.'.html.twig', $data, new Response());
    }

    /**
     * @Route("/about", name="about")
     * @Template("about.html.twig")
     */
    public function aboutAction()
    {
        return [
            'page' => 'about'
        ];
    }
}
