<?php

namespace SourceBundle\Base;

use Symfony\Bundle\FrameworkBundle;
use SourceBundle\Base;
use SourceBundle\Helpers\Arr;
use Symfony\Component\Config\Definition\Exception\Exception;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use SourceBundle\Base\HandlerManager;
use Symfony\Component\HttpFoundation\JsonResponse;

class Controller extends FOSRestController {

	/**
	 * @param $entity
	 * @param bool $handler
	 * @param bool $bundle
	 * @return mixed
	 * @throws \Symfony\Component\Config\Definition\Exception\Exception
	 * @throws \Symfony\Component\Form\Exception\InvalidArgumentException
	 * @return HandlerManager
	 */
	public function getHandler($entity, $handler = FALSE, $bundle = 'App')
	{
		$gateway = $this->container->get('handler_gateway');
		return $gateway->getHandler($entity, $handler, $bundle);
	}

    protected function sqlQuery($query){
        $conn = $this->getDoctrine()->getManager()->getConnection();
        return $conn->fetchAll($query);
    }

    protected function sqlCmd($cmd){
        $conn = $this->getDoctrine()->getManager()->getConnection();
        return $conn->query($cmd);

    }

	/**
	 * Create JSON response from exception
	 * error
	 *
	 * @param $code
	 * @param array $content
	 */
	public function PipeException(\Exception $e, $additionalInfo = [], $rewriteError = NULL)
	{
		$status_code = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
		$response = new JsonResponse();
		$response->setStatusCode($status_code);
		$response->setContent(json_encode(array_merge([
			'error' => [
				'code' => $status_code,
				'message' => trim($rewriteError ?: $e->getMessage(), '"')
			]
		], $additionalInfo)));

		$response->send();
	}

}