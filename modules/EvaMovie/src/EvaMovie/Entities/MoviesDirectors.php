<?php

namespace Eva\EvaMovie\Entities;

use Eva\EvaEngine\Mvc\Model as BaseEntity;
use Swagger\Annotations as SWG;

/**
 * Class MoviesDirectors
 *
 * @package Eva\EvaMovie\Entities *
 * @SWG\Model(id="Eva\EvaMovie\Entities\MoviesDirectors")
 *
 */
class MoviesDirectors extends BaseEntity
{
    /**
     *
     * @SWG\Property(
     *   name="id",
     *   type="long",
     *   description=""
     * )
     *
     * @var long
     */
    public $id;

    /**
     *
     * @SWG\Property(
     *   name="movieId",
     *   type="long",
     *   description=""
     * )
     *
     * @var long
     */
    public $movieId;

    /**
     *
     * @SWG\Property(
     *   name="staffId",
     *   type="integer",
     *   description=""
     * )
     *
     * @var integer
     */
    public $staffId;


    /**
     * Database table name (Not including prefix)
     * @var string
     */
    protected $tableName = 'movie_movies_directors';
}
