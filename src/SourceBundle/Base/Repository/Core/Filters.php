<?php

namespace SourceBundle\Base\Repository\Core;

use SourceBundle\Helpers\Arr;
use Symfony\Component\Config\Definition\Exception\Exception;
use Doctrine\DBAL\Query\QueryBuilder;

class Filters {

	/**
	 * Filter type
	 * @var
	 */
	private $type;

	/**
	 * @var QueryBuilder
	 */
	private $qb;

	/**
	 * Available filters and there callback
	 * @var array
	 */
	private $map = [
		'='       => 'filterEqual',
		'!='       => 'filterNotEqual',
		'>'       => 'filterBigger',
		'<'       => 'filterSmaller',
		'>='       => 'filterBiggerEqual',
		'LIKE'    => 'filterLike',
		'BETWEEN' => 'filterBetween',
		'IS TRUE' => 'filterTrue'
	];

	public function __construct() {}

	public function register(&$qb, $type)
	{
		if ( ! $this->isValidType($type))
			throw new Exception('Filter type "'.$type.'" is invalid, null or not supported');

		$this->qb = $qb;
		$this->type = $type;
	}

	public function addFilter($params)
	{
		$this->{$this->map[$this->type]}($params);
	}

	private function isValidType($type)
	{
		return array_key_exists($type, $this->map);
	}

	private function filterNotEqual($params)
	{
		$this->qb->andWhere("entity.".$params['column'].' != :'.$params['filterName']);
		$this->qb->setParameter($params['filterName'], $params['value']);
	}

    private function filterEqual($params)
    {
	    if ($params['value'] === NULL)
	    {
		    $this->qb->andWhere("entity.".$params['column'].' IS NULL');
	    }
	    else if ($params['value'] === TRUE)
	    {
		    $this->qb->andWhere($this->qb->expr()->isNotNull('entity.'.$params['column']));
	    }
	    else
	    {
		    $this->qb->andWhere("entity.".$params['column'].' = :'.$params['filterName']);
		    $this->qb->setParameter($params['filterName'], $params['value']);
	    }
    }

	private function filterTrue($params)
	{
		$this->qb->andWhere("entity.".$params['column'].' >= :'.$params['filterName']);
		$this->qb->setParameter($params['filterName'], 1);
	}

	private function filterLike($params)
	{
        if(is_array($params['column'])) {
            foreach($params['column'] as $idx => $col)
            {
                $this->qb->orWhere("entity.".$col.' LIKE :'.$params['filterName'].$idx);
                $this->qb->setParameter($params['filterName'].$idx, '%'.$params['value'].'%');
            }

            return;
        }
		$this->qb->andWhere("entity.".$params['column'].' LIKE :'.$params['filterName']);
		$this->qb->setParameter($params['filterName'], '%'.$params['value'].'%');
	}

	private function filterBigger($params)
	{
		$this->qb->andWhere("entity.".$params['column'].' > :'.$params['filterName']);
		$this->qb->setParameter($params['filterName'], $params['value']);
	}

	private function filterBiggerEqual($params)
	{
		$this->qb->andWhere("entity.".$params['column'].' >= :'.$params['filterName']);
		$this->qb->setParameter($params['filterName'], $params['value']);
	}

	private function filterSmaller($params)
	{
		$this->qb->andWhere("entity.".$params['column'].' < :'.$params['filterName']);
		$this->qb->setParameter($params['filterName'], $params['value']);
	}

	private function filterBetween($params)
	{
		$range = $params['value'];
		$param_1 = $params['filterName'].'_1';
		$param_2 = $params['filterName'].'_2';

		if ( ! Arr::get($range, 0) || ! Arr::get($range, 1))
			throw new Exception('Between filter excpet array with 2 values');
		
		$this->qb->andWhere("entity.".$params['column'].' BETWEEN :'.$param_1.' AND :'.$param_2);
		$this->qb->setParameter($param_1, $range[0]);
		$this->qb->setParameter($param_2, $range[1]);
	}

} // End Filter 