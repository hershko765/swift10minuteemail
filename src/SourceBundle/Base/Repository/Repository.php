<?php
namespace SourceBundle\Base\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use SourceBundle\Base\Repository\Core;
use SourceBundle\Helpers\Arr;
use Doctrine\ORM\Mapping;;
use SourceBundle\Base;
use DateTime;

abstract class Repository extends Core {

	/**
	 * Abilities array
	 *
	 * @var array
	 */
	protected $abilities = [];

	/**
	 * -- [ Creatable Model Ability ] --
	 * Fill a created column in the database every
	 * time that record is added
	 * - Requires Column Created with type = datetime
	 */
	const CREATABLE = 1;

	/**
	 * -- [ Creatable Model Ability ] --
	 * Fill updated column in the database every time
	 * that a record is updated
	 * - Requires Column updated with type = datetime
	 */
	const UPDATABLE = 2;

	/**
	 * -- [ Creatable Model Ability ] --
	 * Allow soft delete of a record,
	 * soft delete will fill deleted column in a date deleted
	 * - Requires Column updated with type = datetime
	 */
	const SOFT_DELETABLE = 3;

    /**
     * -- [ Multi Database ] --
     * This ability will reflect all changes to this entity
     * in binarybonus
     */
    const MULTI_DB = 4;

	protected $_joins = [];

	/**
	 * Generic finding records by custom options
	 * ## Example
	 * filters  - [ 'name'   => 'some name'       ]
	 * paging   - [ 'limit'  => 5, 'offset' => 10 ]
	 * settings - [ 'select' => [ 'id', 'title'   ]
	 *
	 * @param array $filters
	 * @param array $paging
	 * @param array $settings
	 */
	public function collect(array $filters = [], array $paging = [], array $settings = [])
	{
		$qb = $this->createQueryBuilder('entity');
        $total = NULL;

		// Reset joins
		$this->_joins = [];

		$this->addFilters($qb, $filters);

		$this->addPaging($qb, $paging);
		$this->addSettings($qb, $settings);

		set_time_limit(300);
		
		$result = $qb->getQuery()->execute();

		return new ArrayCollection($result);
	}

	/**
	 * Save entity object
	 *
	 * @param $model
	 * @return mixed
	 */
	public function save($model)
	{
		$em = $this->getEntityManager();
		$em->persist($model);
		$em->flush();

        if (in_array(self::MULTI_DB, $this->abilities))
        {
            $em = $this->getEntityManager();
            $em->persist($model);
            $em->flush();
        }

		return $model;
	}

	/**
	 * Delete a record
	 *
	 * @param $id
	 */
	public function delete($entity)
	{
		$em = $this->getEntityManager();
        $em->remove($entity);
        $em->flush();

		return $entity;
	}

	/**
	 * Fill model by permissions
	 *
	 * @param $data
	 * @param $permission
	 * @return array
	 */
	public function hydrate($data, Base\Model &$model, $permission)
	{
		// Filter by permission
		$convertedValues = array_intersect($this->tableMap, [ $permission, self::PERM_ALL ]);
		$convertedData   = array_intersect_key($data, $convertedValues);


		// Set abilities if exists
		$this->setAbilities($model);
		
		// Set Model with the filtered values
		$model->set($convertedData);
	}

	/**
	 * Set Created at value
	 *
	 * @ORM\PrePersist
	 */
	public function setAbilities(Base\Model &$model)
	{
		if(in_array(self::CREATABLE, $this->abilities) && method_exists($model, 'setCreated'))
			$model->setCreated(new DateTime());

		if(in_array(self::UPDATABLE, $this->abilities) && method_exists($model, 'setUpdated'))
			$model->setUpdated(new DateTime());

		if(in_array(self::SOFT_DELETABLE, $this->abilities) && method_exists($model, 'setDeleted'))
			$model->setDeleted(new Datetime());
	}
}

/**
 * Filter classes
 * Filter Converters
 */