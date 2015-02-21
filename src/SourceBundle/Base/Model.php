<?php

namespace SourceBundle\Base;
use SourceBundle\Helpers\Arr;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use DateTime;

/**
 * Class Model
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @package SourceBundle\Base
 */
abstract class Model {

	/**
	 * set multiple values into the model
	 *
	 * @param $data
	 */
	public function set($data)
	{
		foreach($data as $col => $val)
		{
			$method = 'set'.str_replace(' ', '', ucwords(str_replace('_', ' ', $col)));
			if(method_exists($this, $method))
			{
				$this->{$method}($val);
			}
		}
	}

	/**
	 * Convert entity values as array
	 * @return array
	 */
	public function asArray()
	{
		return $this->_asArray();
	}

	/**
	 * Convert entity values as JSON
	 * @return string
	 */
	public function asJSON()
	{
		return json_encode($this->_asArray());
	}

	public function isNew()
	{
		return ! (bool) $this->getId();
	}

	/**
	 * Doing all the convert work,
	 * convert the entity to array
	 *
	 * @return array
	 */
	private function _asArray()
	{
		return get_object_vars($this);
	}
}