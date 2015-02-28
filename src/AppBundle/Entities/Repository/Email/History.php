<?php

namespace AppBundle\Entities\Repository\Email;

use SourceBundle\Base\Repository\Repository;
use SourceBundle\Helpers\Arr;
use Doctrine\ORM\QueryBuilder;

class History extends Repository {

    protected $defaultOrder = 'ASC';
    protected $defaultSort = 'created';

    /**
     * Entity available filters
     *
     * [ <Filter Name>, <Related Column>, <Mysql Operator>
     * @var array
     */
    protected $filterMap = [ ];

    /**
     * List of table fields
     * @var array
     */
    protected $tableMap = [
        'id'         => self::PERM_NONE,
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