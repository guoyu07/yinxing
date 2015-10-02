<?php

namespace Eva\EvaMovie\Entities;

use Eva\EvaEngine\Mvc\Model as BaseEntity;
use Swagger\Annotations as SWG;

/**
 * Class Series
 *
 * @package Eva\EvaMovie\Entities *
 * @SWG\Model(id="Eva\EvaMovie\Entities\Series")
 *
 */
class Series extends BaseEntity
{
    /**
     *
     * @SWG\Property(
     *   name="id",
     *   type="integer",
     *   description=""
     * )
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @SWG\Property(
     *   name="name",
     *   type="string",
     *   description="系列名称"
     * )
     *
     * @var string
     */
    public $name;

    /**
     *
     * @SWG\Property(
     *   name="summary",
     *   type="text",
     *   description=""
     * )
     *
     * @var text
     */
    public $summary;


    /**
     * Database table name (Not including prefix)
     * @var string
     */
    protected $tableName = 'movie_series';
}
