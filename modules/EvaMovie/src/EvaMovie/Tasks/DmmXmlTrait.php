<?php
/**
 * @author    AlloVince
 * @copyright Copyright (c) 2015 EvaEngine Team (https://github.com/EvaEngine)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */


namespace Eva\EvaMovie\Tasks;

use Eva\EvaMovie\Entities\Makers;
use Eva\EvaMovie\Entities\Movies;
use Eva\EvaMovie\Entities\Series;
use Eva\EvaMovie\Entities\Staffs;
use JpnForPhp\Transliterator\Romaji;
use Phalcon\Config;

trait DmmXmlTrait
{
    /**
     * @var string
     */
    private static $dmmMovieId;

    /**
     * @var int
     */
    private static $yinxingMovieId;

    /**
     * @var Romaji
     */
    protected $romaji;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var string
     */
    private $lastDbMessage;

    /**
     * @return Config
     */
    protected function getConfig()
    {
        return $this->config ?: $this->config = $this->getDI()->get("config");
    }

    /**
     * @param \SimpleXMLElement $item
     * @return boolean | Movies
     */
    protected function checkItemExist(\SimpleXMLElement $item)
    {
        return self::checkDmmIdInDb((string)$item->product_id);
    }

    /**
     * @param $dmmId
     * @return bool | Movies
     */
    public function checkDmmIdInDb($dmmId)
    {
        return Movies::findFirstById(self::dmmMovieIDToYinxingID($dmmId));
    }

    /**
     * @param \SimpleXMLElement $item
     * @return bool
     */
    public function saveDmmItem(\SimpleXMLElement $item)
    {
        $item = json_decode(json_encode($item));
        $movie = new Movies();
        $movie->id = self::dmmMovieIDToYinxingID($item->product_id);
        $movie->title = $item->title;
        $movie->banngo = $item->product_id;
        $movie->subBanngo = $item->content_id;
        $movie->alt = $item->URL;
        $movie->subtype = $item->service_name;
        $movie->pubdate = $item->date;
        $movie->year = $item->date;
        $movie->images = implode(',', (array)$item->imageURL);
        $movie->previews = implode(',', (array)$item->sampleImageURL->sample_s->image);

        if (!empty($item->iteminfo->keyword)) {
            $tags = [];
            foreach ($item->iteminfo->keyword as $keyword) {
                if (empty($keyword->name)) {
                    continue;
                }
                $tags[] = $keyword->name;
            }
            $movie->tags = implode(',', $tags);
        }

        if (!empty($item->iteminfo->series->id)) {
            $series = new Series();
            $series->id = self::dmmOtherIDConvert($item->iteminfo->series->id);
            $series->name = $item->iteminfo->series->name;
            $movie->series = $series;
        }

        if (!empty($item->iteminfo->maker->id)) {
            $maker = new Makers();
            $maker->id = self::dmmOtherIDConvert($item->iteminfo->maker->id);
            $maker->name = $item->iteminfo->maker->name;
            $movie->maker = $maker;
        }

        if (!empty($item->iteminfo->actress)) {
            $casts = $this->getStaffs($item->iteminfo->actress);
            $casts = $this->processActress($casts);
            if ($casts) {
                $movie->casts = $casts;
            }
        }


        /*
        //Not correct, id convert need fix
        if (!empty($item->iteminfo->director)) {
            $directors = $this->getStaffs($item->iteminfo->director);
            $directors = $this->processDirectors($directors);
            if ($directors) {
                $movie->directors = $directors;
            }
        }
        */

        $res = $movie->save();
        if (!$res) {
            $this->lastDbMessage = $movie->getMessages();
        }
        return $res;
    }

    /**
     * @return Romaji
     */
    public function getRomajiConvertor()
    {
        return $this->romaji ?: $this->romaji = new Romaji('nihon');
    }

    /**
     * @param $dmmId
     * @return int
     */
    public static function dmmMovieIDToYinxingID($dmmId)
    {
        self::$dmmMovieId = $dmmId;
        return self::$yinxingMovieId = 2000000000000 + crc32($dmmId);
    }

    /**
     * @param $dmmId
     * @return int
     */
    public static function dmmDirectorIDConvert($dmmId)
    {
        if (!is_numeric($dmmId)) {
            return false;
        }
        return 2100000000 + (int)$dmmId;
    }

    /**
     * @param $dmmId
     * @return int
     */
    public static function dmmOtherIDConvert($dmmId)
    {
        if (!is_numeric($dmmId)) {
            return false;
        }
        return 2000000000 + (int)$dmmId;
    }

    /**
     * @param $name
     * @return array
     */
    public function parseNameAndAka($name)
    {
        //array_filter to remove empty strings
        $nameArray = array_filter(preg_split("/（|、|）/", $name));

        if (count($nameArray) <= 1) {
            return [$name, []];
        }

        return [array_shift($nameArray), $nameArray];
    }

    /**
     * @param array $casts
     * @return array
     */
    private function processActress(array $casts)
    {
        foreach ($casts as $key => $cast) {
            $cast->gender = 'female';
            $casts[$key] = $cast;
        }
        return $casts;
    }

    /**
     * @param array $directors
     * @return array
     */
    private function processDirectors(array $directors)
    {
        foreach ($directors as $key => $director) {
            $director->isDirector = 1;
            $directors[$key] = $director;
        }
        return $directors;
    }

    /**
     * @param array $people
     * @return array
     */
    private function getStaffs(array $people)
    {
        $staffs = [];
        foreach ($people as $key => $person) {
            if (!is_numeric($person->id)) {
                continue;
            }
            $staff = new Staffs();
            $staff->id = self::dmmOtherIDConvert($person->id);
            list($name, $aka) = $this->parseNameAndAka($person->name);
            $staff->name = $name;
            $staff->aka = implode(',', $aka) ?: null;
            $nameRuby = $this->findNameRuby($person->id, $people);
            if ($nameRuby) {
                list($name, $aka) = $this->parseNameAndAka($nameRuby);
                $staff->nameRuby = $name;
                $staff->nameEn = $this->getRomajiConvertor()->transliterate($name);
                $staff->akaRuby = implode(',', $aka);
            }
            $staffs[] = $staff;
        }
        return $staffs;
    }

    /**
     * @param $id
     * @param array $people
     * @return null | string
     */
    private function findNameRuby($id, array $people)
    {
        foreach ($people as $key => $person) {
            if ($person->id == $id . '_ruby') {
                return $person->name;
            }
        }
        return null;
    }
}
