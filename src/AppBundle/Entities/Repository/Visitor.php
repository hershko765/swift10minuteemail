<?php
namespace AppBundle\Entities\Repository;

use SourceBundle\Base\Repository\Repository;
use SourceBundle\Helpers\Arr;
use Doctrine\ORM\QueryBuilder;

class Visitor extends Repository {

    protected $defaultOrder = 'ASC';
    protected $defaultSort = 'id';

    /**
	 * Entity available filters
	 *
	 * [ <Filter Name>, <Related Column>, <Mysql Operator>
	 * @var array
	 */
	protected $filterMap = [
        [ 'email', 'email', '=' ],
        [ 'is_closed', 'close_date', '<' ],
        [ 'is_open', 'close_date', '>' ],
    ];

	/**
	 * List of table fields
	 * @var array
	 */
	protected $tableMap = [
		'id'         => self::PERM_NONE,
		'email'      => self::PERM_ALL,
		'ip'         => self::PERM_ALL,
		'created'    => self::PERM_ALL,
		'close_date' => self::PERM_ALL,
	];

	/**
	 * Model abilities
	 *
	 * @var array
	 */
	protected $abilities = [];

}