<?php

namespace AppBundle\Entities\Repository;

use SourceBundle\Base\Repository\Repository;
use SourceBundle\Helpers\Arr;
use Doctrine\ORM\QueryBuilder;

class Email extends Repository {

    protected $defaultOrder = 'ASC';
    protected $defaultSort = 'created';

    /**
     * Entity available filters
     *
     * [ <Filter Name>, <Related Column>, <Mysql Operator>
     * @var array
     */
    protected $filterMap = [
        [ 'visitor', 'visitor', '=' ]
    ];

    /**
     * List of table fields
     * @var array
     */
    protected $tableMap = [
        'id'         => self::PERM_NONE,
        'visitor' => self::PERM_CREATE,
        'headers'    => self::PERM_CREATE,
        'from'       => self::PERM_CREATE,
        'subject'    => self::PERM_CREATE,
        'content'    => self::PERM_CREATE,
        'created'    => self::PERM_CREATE
    ];

    /**
     * Model abilities
     *
     * @var array
     */
    protected $abilities = [];
}