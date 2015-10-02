<?php

namespace Eva\EvaMovie\Entities;

use Eva\EvaEngine\Mvc\Model as BaseEntity;
use Swagger\Annotations as SWG;

/**
 * Class Makers
 *
 * @package Eva\EvaMovie\Entities *
 * @SWG\Model(id="Eva\EvaMovie\Entities\Makers")
 *
 */
class Makers extends BaseEntity
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
     *   description="制作商名称"
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
     *   description="制作商简介"
     * )
     *
     * @var text
     */
    public $summary;


    /**
     * Database table name (Not including prefix)
     * @var string
     */
    protected $tableName = 'movie_makers';
}
