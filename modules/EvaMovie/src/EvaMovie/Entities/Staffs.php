<?php

namespace Eva\EvaMovie\Entities;

use Eva\EvaEngine\Mvc\Model as BaseEntity;
use Swagger\Annotations as SWG;

/**
 * Class Staffs
 *
 * @package Eva\EvaMovie\Entities *
 * @SWG\Model(id="Eva\EvaMovie\Entities\Staffs")
 *
 */
class Staffs extends BaseEntity
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
     *   description="影人姓名"
     * )
     *
     * @var string
     */
    public $name;

    /**
     *
     * @SWG\Property(
     *   name="nameRuby",
     *   type="string",
     *   description="姓名标注"
     * )
     *
     * @var string
     */
    public $nameRuby;

    /**
     *
     * @SWG\Property(
     *   name="nameEn",
     *   type="string",
     *   description="英文名"
     * )
     *
     * @var string
     */
    public $nameEn;

    /**
     *
     * @SWG\Property(
     *   name="alt",
     *   type="string",
     *   description="URL"
     * )
     *
     * @var string
     */
    public $alt;

    /**
     *
     * @SWG\Property(
     *   name="avatars",
     *   type="string",
     *   description="Array 头像"
     * )
     *
     * @var string
     */
    public $avatars;

    /**
     *
     * @SWG\Property(
     *   name="summary",
     *   type="text",
     *   description="简介"
     * )
     *
     * @var text
     */
    public $summary;

    /**
     *
     * @SWG\Property(
     *   name="aka",
     *   type="string",
     *   description="Array 别名"
     * )
     *
     * @var string
     */
    public $aka;

    /**
     *
     * @SWG\Property(
     *   name="akaRuby",
     *   type="string",
     *   description="Array 别名标注"
     * )
     *
     * @var string
     */
    public $akaRuby;

    /**
     *
     * @SWG\Property(
     *   name="akaEn",
     *   type="string",
     *   description="Array 别名英文名"
     * )
     *
     * @var string
     */
    public $akaEn;

    /**
     *
     * @SWG\Property(
     *   name="website",
     *   type="string",
     *   description="官方网站"
     * )
     *
     * @var string
     */
    public $website;

    /**
     *
     * @SWG\Property(
     *   name="gender",
     *   type="string",
     *   description="性别"
     * )
     *
     * @var string
     */
    public $gender;

    /**
     *
     * @SWG\Property(
     *   name="birthday",
     *   type="date",
     *   description="出生日期"
     * )
     *
     * @var date
     */
    public $birthday;

    /**
     *
     * @SWG\Property(
     *   name="bornPlace",
     *   type="string",
     *   description="出生地"
     * )
     *
     * @var string
     */
    public $bornPlace;

    /**
     *
     * @SWG\Property(
     *   name="professions",
     *   type="string",
     *   description="职业"
     * )
     *
     * @var string
     */
    public $professions;

    /**
     *
     * @SWG\Property(
     *   name="constellation",
     *   type="string",
     *   description="星座"
     * )
     *
     * @var string
     */
    public $constellation;

    /**
     *
     * @SWG\Property(
     *   name="threeSizes",
     *   type="string",
     *   description="三围"
     * )
     *
     * @var string
     */
    public $threeSizes;

    /**
     *
     * @SWG\Property(
     *   name="cup",
     *   type="string",
     *   description="罩杯"
     * )
     *
     * @var string
     */
    public $cup;

    /**
     *
     * @SWG\Property(
     *   name="hobby",
     *   type="string",
     *   description="爱好"
     * )
     *
     * @var string
     */
    public $hobby;

    /**
     *
     * @SWG\Property(
     *   name="isDirector",
     *   type="integer",
     *   description="导演身份"
     * )
     *
     * @var integer
     */
    public $isDirector = 0;

    /**
     *
     * @SWG\Property(
     *   name="isWriter",
     *   type="integer",
     *   description="编剧身份"
     * )
     *
     * @var integer
     */
    public $isWriter = 0;


    /**
     * Database table name (Not including prefix)
     * @var string
     */
    protected $tableName = 'movie_staffs';
}
