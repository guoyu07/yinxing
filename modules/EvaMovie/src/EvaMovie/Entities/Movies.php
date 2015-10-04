<?php

namespace Eva\EvaMovie\Entities;

use Eva\EvaEngine\Mvc\Model as BaseEntity;
use Swagger\Annotations as SWG;

/**
 * Class Movies
 *
 * @package Eva\EvaMovie\Entities *
 * @SWG\Model(id="Eva\EvaMovie\Entities\Movies")
 *
 */
class Movies extends BaseEntity
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
     *   name="title",
     *   type="string",
     *   description="名称"
     * )
     *
     * @var string
     */
    public $title;

    /**
     *
     * @SWG\Property(
     *   name="banngo",
     *   type="string",
     *   description="番号"
     * )
     *
     * @var string
     */
    public $banngo;

    /**
     *
     * @SWG\Property(
     *   name="subBanngo",
     *   type="string",
     *   description="番号别名"
     * )
     *
     * @var string
     */
    public $subBanngo;

    /**
     *
     * @SWG\Property(
     *   name="originalTitle",
     *   type="string",
     *   description="原名"
     * )
     *
     * @var string
     */
    public $originalTitle;

    /**
     *
     * @SWG\Property(
     *   name="aka",
     *   type="string",
     *   description="Array 又名"
     * )
     *
     * @var string
     */
    public $aka;

    /**
     *
     * @SWG\Property(
     *   name="alt",
     *   type="string",
     *   description="信息URL"
     * )
     *
     * @var string
     */
    public $alt;

    /**
     *
     * @SWG\Property(
     *   name="ratingsCount",
     *   type="integer",
     *   description="评分人数"
     * )
     *
     * @var integer
     */
    public $ratingsCount;

    /**
     *
     * @SWG\Property(
     *   name="wishCount",
     *   type="integer",
     *   description="想看人数"
     * )
     *
     * @var integer
     */
    public $wishCount;

    /**
     *
     * @SWG\Property(
     *   name="collectCount",
     *   type="integer",
     *   description="看过人数"
     * )
     *
     * @var integer
     */
    public $collectCount;

    /**
     *
     * @SWG\Property(
     *   name="doCount",
     *   type="integer",
     *   description="在看人数"
     * )
     *
     * @var integer
     */
    public $doCount;

    /**
     *
     * @SWG\Property(
     *   name="subtype",
     *   type="string",
     *   description="条目分类 movie | tv"
     * )
     *
     * @var string
     */
    public $subtype = 'movie';

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
     *   name="pubdate",
     *   type="string",
     *   description="上映日期"
     * )
     *
     * @var string
     */
    public $pubdate;

    /**
     *
     * @SWG\Property(
     *   name="year",
     *   type="integer",
     *   description="年代"
     * )
     *
     * @var integer
     */
    public $year;

    /**
     *
     * @SWG\Property(
     *   name="languages",
     *   type="string",
     *   description="语言"
     * )
     *
     * @var string
     */
    public $languages;

    /**
     *
     * @SWG\Property(
     *   name="genres",
     *   type="string",
     *   description="Array 影片类型"
     * )
     *
     * @var string
     */
    public $genres;

    /**
     *
     * @SWG\Property(
     *   name="durations",
     *   type="string",
     *   description="片长"
     * )
     *
     * @var string
     */
    public $durations;

    /**
     *
     * @SWG\Property(
     *   name="countries",
     *   type="string",
     *   description="Array 制作国家"
     * )
     *
     * @var string
     */
    public $countries;

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
     *   name="seasonsCount",
     *   type="integer",
     *   description="总季数"
     * )
     *
     * @var integer
     */
    public $seasonsCount;

    /**
     *
     * @SWG\Property(
     *   name="currentSeason",
     *   type="integer",
     *   description="当前季数"
     * )
     *
     * @var integer
     */
    public $currentSeason;

    /**
     *
     * @SWG\Property(
     *   name="tags",
     *   type="string",
     *   description="Array 标签"
     * )
     *
     * @var string
     */
    public $tags;

    /**
     *
     * @SWG\Property(
     *   name="episodesCount",
     *   type="integer",
     *   description="当前季的集数"
     * )
     *
     * @var integer
     */
    public $episodesCount;

    /**
     *
     * @SWG\Property(
     *   name="makerId",
     *   type="integer",
     *   description="制作厂商ID"
     * )
     *
     * @var integer
     */
    public $makerId;

    /**
     *
     * @SWG\Property(
     *   name="seriesId",
     *   type="integer",
     *   description="系列ID"
     * )
     *
     * @var integer
     */
    public $seriesId;

    /**
     *
     * @SWG\Property(
     *   name="images",
     *   type="string",
     *   description="Array 封面图片，尺寸从小到大"
     * )
     *
     * @var string
     */
    public $images;

    /**
     *
     * @SWG\Property(
     *   name="previews",
     *   type="string",
     *   description="Array 预览图片"
     * )
     *
     * @var string
     */
    public $previews;


    /**
     * Database table name (Not including prefix)
     * @var string
     */
    protected $tableName = 'movie_movies';


    public function initialize()
    {
        $this->belongsTo(
            'makerId',
            'Eva\EvaMovie\Entities\Makers',
            'id',
            array(
                'alias' => 'maker'
            )
        );

        $this->belongsTo(
            'seriesId',
            'Eva\EvaMovie\Entities\SeriesId',
            'id',
            array(
                'alias' => 'series'
            )
        );

        $this->hasMany(
            'id',
            'Eva\EvaMovie\Entities\MoviesCasts',
            'movieId',
            array('alias' => 'moviesCasts')
        );

        $this->hasManyToMany(
            'id',
            'Eva\EvaMovie\Entities\MoviesCasts',
            'movieId',
            'staffId',
            'Eva\EvaMovie\Entities\Staffs',
            'id',
            array('alias' => 'casts')
        );

        $this->hasMany(
            'id',
            'Eva\EvaMovie\Entities\MoviesDirectors',
            'movieId',
            array('alias' => 'moviesDirectors')
        );

        $this->hasManyToMany(
            'id',
            'Eva\EvaMovie\Entities\MoviesDirectors',
            'movieId',
            'staffId',
            'Eva\EvaMovie\Entities\Staffs',
            'id',
            array('alias' => 'directors')
        );
        parent::initialize();
    }

}
