<?php

namespace SourceBundle\Base\Repository;

use Doctrine\ORM\QueryBuilder;
use SourceBundle\Helpers\Arr;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;;
use Symfony\Component\DependencyInjection\ContainerBuilder;


abstract class Core extends EntityRepository {

	/**
	 * Define permissions
	 */
	const PERM_NONE   = 0;
	const PERM_CREATE = 1;
	const PERM_UPDATE = 2;
	const PERM_ALL    = 3;

	/**
	 * List of available filters, to be overwritten by the child repository
	 * @var array $filterMap
	 */
	protected $filterMap;

	/**
	 * Service container
	 * @var ContainerBuilder
	 */
	protected $services;

	/**
	 * @var string
	 */
	protected $tableName;

	/**
	 * List of table fields, permissions and covert types
	 * @var
	 */
	protected $tableMap;

	/**
	 * List of table joins for QB object
	 * @var array
	 */
	protected  $_joins = [];

	/**
	 * table primary key
	 * @var string
	 */
	protected $pk = 'id';

    /**
     * Default collect order
     * @var
     */
    protected $defaultOrder = 'ASC';

    /**
     * Default collect sorting
     * @var string
     */
    protected $defaultSort = 'id';

	public function __construct($em, Mapping\ClassMetadata $class)
	{
		$this->services = new ContainerBuilder();
		$this->services->register('Filters', 'SourceBundle\Base\Repository\Core\Filters');
		parent::__construct($em, $class);
	}

	/**
	 * @return \Doctrine\DBAL\Connection
	 */
	public function getConnection()
	{
		return $this->getEntityManager()->getConnection();
	}

	/**
	 * Add filters to query
	 *
	 * @param QueryBuilder $qb
	 * @param array        $filters
	 */
	protected function addFilters(QueryBuilder &$qb, array $filters)
	{
		$filtersIdx = Arr::Column($this->filterMap, NULL, 0);
		$filtersService = $this->services->get('filters');

		foreach($filters as $filterName => $value)
		{
            $filterMethod = str_replace(' ', '', ucwords(str_replace('_', ' ', $filterName)));
			if(method_exists($this, 'filter'.$filterMethod))
			{
				$this->{'filter'.$filterMethod}($qb, $value);
				continue;
			}
			if ( ! $this->isAllowedFilter($filterName)) continue;
			$column   = $filtersIdx[$filterName][1];
			$operator = $filtersIdx[$filterName][2];

			$filtersService->register($qb, $operator);
			$filtersService->addFilter([ 'column' => $column, 'value'  => $value, 'filterName' => $filterName ]);
		}
	}

	/**
	 * Add paging options to query
	 *
	 * @param QueryBuilder $qb
	 * @param array        $paging
	 */
	protected function addPaging(QueryBuilder &$qb, array $paging)
	{
		if (Arr::get($paging, 'page'))
		{
			list($page, $perPage) = $paging['page'];
			$paging['limit'] = $perPage;
			$paging['offset'] = ($page != 0 ? $page - 1 : 1) * $perPage;
		}

		// Add limit if given
		if (Arr::get($paging, 'limit'))
			$qb->setMaxResults($paging['limit']);

		// Add offset if given
		if (Arr::get($paging, 'offset'))
			$qb->setFirstResult($paging['offset']);

		// Adding order type ( ASC, DESC ) if exists and sort if exists
		$qb->orderBy(Arr::get($paging, 'sort')
            ? 'entity.'.$paging['sort']
            : 'entity.'.$this->defaultSort,
            Arr::get($paging, 'order') ? (Arr::get($paging, 'order') == 'ASC' ? 'ASC' : 'DESC') : $this->defaultOrder
        );
	}

	/**
	 * Add settings to query
	 *
	 * @param QueryBuilder $qb
	 * @param              $settings
	 */
	protected function addSettings(QueryBuilder &$qb, $settings)
	{
		if(Arr::get($settings, 'select') || Arr::get($settings, 'selectBox'))
		{
			// If select box chosen, apply selectBox parameters (Not merging in purpose)
			if (Arr::get($settings, 'selectBox'))
				$settings['select'] = $settings['selectBox'];
			
			// If not array given, trying to convert to array with "," as separator
			if ( ! is_array($settings['select']))
				$settings['select'] = explode(',', $settings['select']);

			// Checking if column exists and allowed and adding to query
			foreach ($settings['select'] as $idx => $select)
			{
				if ( ! $this->isColumnExists(trim($select))) continue;

				$full_select = 'entity.'.$select;
				$has_prefix = explode('.', $select);

				if (count($has_prefix) > 1)
				{
					$full_select = $select;
				}

				if($idx === 0) $qb->select($full_select);
				else $qb->addSelect($full_select);
			}
		}

		if (Arr::get($settings, 'group')) {
			$column = Arr::get($settings, 'group');
			$qb->addGroupBy('entity.'.$column);
		}
	}

	protected function joinOnce(QueryBuilder &$qb, $join, $alias, $conditionType = NULL, $condition = NULL)
	{
		if (in_array($join, $this->_joins)) return;

		$this->_joins[] = $join;
		$qb->innerJoin($join, $alias, $conditionType, $condition);
	}
	/**
	 * Check if a given filter is allowed by the filter map
	 * of the repository, create a silent report if not
	 *
	 * @param $filter
	 * @return bool
	 */
	protected function isAllowedFilter($filter)
	{
		$filters = Arr::Column($this->filterMap, 1, 0);
		if ( ! array_key_exists($filter, $filters))
		{
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Check if a given column is exists in the table map list
	 *
	 * @param $col
	 * @return bool
	 */
	protected function isColumnExists($col)
	{
		if ( ! Arr::get($this->tableMap, $col))
		{
			SilentLog::getInstance()->silentLog([
					'message'  => 'Column not exists on select settings',
					'metadata' => 'column='.$col.'&table='.$this->tableName
				]);
			return FALSE;
		}
		return TRUE;
	}

	protected function applyIdx(&$results, $idx)
	{
		$resultsIdx = [];
		foreach($results as $result)
		{
			if ( ! Arr::get($result, $idx)) break;

			$resultsIdx[$result[$idx]] = $result;
		}

		$results = $resultsIdx;
	}

	protected function createSelect(&$results, $selectCols)
	{
		// Extract Columns
		if ( ! is_array($selectCols))
			$selectCols = explode(',', $selectCols);

		if ( ! Arr::get($selectCols, 1)) return;
		list($idx, $val) = $selectCols;

		$resultsIdx = [];
		foreach($results as $result)
		{
			if ( ! Arr::get($result, $idx) ||  ! Arr::get($result, $val)) continue;
			$resultsIdx[$result[$idx]] = $result[$val];
		}

		$results = $resultsIdx;
	}

} // End Core