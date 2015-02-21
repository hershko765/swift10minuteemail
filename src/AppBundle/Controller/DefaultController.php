<?php

namespace AppBundle\Controller;

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
        $cookies = $request->cookies->all();
        $visitor = NULL;
        $renewed = FALSE;

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
            }
            catch(NotFoundHttpException $e)
            {
                $renewed = Arr::get($cookies, 'vid');
            }
        }

        if ( ! $visitor)
        {
            $visitor = $this->getHandler('Visitor', 'Register')->execute();

            $response = new Response();
            $response->headers->setCookie(new Cookie('vid', $visitor->getId()));
            $response->sendHeaders();
        }

        return [
            'visitor' => $visitor,
            'renewed' => $renewed
        ];
    }
}
