<?php

namespace SourceBundle\Base;

use SourceBundle\Exception\ValidationException;
use SourceBundle\Helpers\Arr;
use SourceBundle\Interfaces\Handler;
use Symfony\Component\HttpFoundation\Request;


abstract class HandlerManager implements Handler {

	/**
	 * @var HandlerGateway
	 * @DI(alias=handler_gateway)
	 */
	protected $handlerGateway;

    /**
     * @var Request
     * @DI(alias=request)
     */
    protected $request;

	public $_bundle;

    /**
     * Collect Handler
     * @var array
     */
    protected $filters  = [];
    protected $paging   = [];
    protected $settings = [];


    /**
     * Get Handler
     * @var array
     */
    protected $options = [];
    protected $id;

    /**
	 * Inject dependencies into class properties
	 */
	public function __construct()
	{
		$args = func_get_args();
		$DIarray = Arr::get($args, count(func_get_args()) - 1);
		foreach ($DIarray as $key => $DIClass)
		{
			$this->{$DIClass} = Arr::get($args, $key);
		}
		if(method_exists($this, 'initialize')) $this->initialize();
	}

	/**
	 * Get handler gateway instance
	 * @return HandlerGateway
	 */
	protected function getHandlerGateway()
	{
		return $this->handlerGateway;
	}

	/**
	 * Shortcut for loading handler
	 *
	 * @param      $entity
	 * @param      $handler
	 * @param bool $bundle
	 * @return HandlerManager
	 */
	protected function getHandler($entity, $handler, $bundle = FALSE)
	{
		return $this->handlerGateway->getHandler($entity, $handler, $bundle);
	}

	public function setFilters(array $filters)
	{
		$this->filters = $filters;
		return $this;
	}

    public function setID($id, array $options = [])
    {
        $this->options = $options;
        $this->id = $id;

        return $this;
    }

	public function setSettings(array $settings)
	{
		$this->settings = $settings;
		return $this;
	}

	public function setPaging(array $paging)
	{
		$this->paging = $paging;
		return $this;
	}

	public function clear()
	{
		$this->paging = [];
		$this->settings = [];
		$this->filters = [];
		return $this;
	}
}