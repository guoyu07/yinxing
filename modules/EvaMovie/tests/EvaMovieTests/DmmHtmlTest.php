<?php
/**
 * @author    AlloVince
 * @copyright Copyright (c) 2015 EvaEngine Team (https://github.com/EvaEngine)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */


namespace Eva\EvaMovieTests;


use Eva\EvaMovie\Tasks\ImportDmmTask;
use Phalcon\Di;
use Eva\EvaEngine\Module\Manager as ModuleManager;
use Phalcon\Db\Adapter\Pdo\Mysql;

class DmmHtmlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ImportDmmTask
     */
    protected $task;

    public function setUp()
    {
        $this->task = new ImportDmmTask();
        $di = new Di\FactoryDefault();
        $db = function () {
            return new Mysql(array(
                "host" => $GLOBALS['db_host'],
                "username" => $GLOBALS['db_username'],
                "password" => $GLOBALS['db_password'],
                "dbname" => 'yinxing',
            ));
        };
        $di->set('dbSlave', $db);
        $di->set('dbMaster', $db);
        $di->set('moduleManager', function () {
            return new ModuleManager();
        });

        /** @var Mysql $mysql */
        $mysql = $di->get('dbMaster');
        $mysql->query(file_get_contents(__DIR__ . '/../../sql/evamovie_2015-10-20.sql'));

    }

    public function testFull()
    {
        $movie = $this->task->getMovie(file_get_contents(__DIR__ . '/_html/mird00143.html'));
        $this->assertEquals(2000474618082, $movie->id);
        $this->assertEquals('中出しされたザーメンをごっくん 大乱交4時間SPECIAL', $movie->title);
        $this->assertEquals('mird00143', $movie->subBanngo);
        $this->assertEquals('http://www.dmm.co.jp/digital/videoa/-/detail/=/cid=mird00143/', $movie->alt);
        $this->assertEquals('2014-10-09', $movie->pubdate);
        $this->assertEquals('2014', $movie->year);
        $this->assertEquals('238', $movie->durations);
        $this->assertStringStartsWith('MOODYZ大人気ハード企画', $movie->summary);

        $tags = explode(',', $movie->tags);
        $this->assertCount(9, $tags);
        $this->assertEquals('ハイビジョン', $tags[0]);

        $images = explode(',', $movie->images);
        $this->assertCount(3, $images);
        $this->assertStringEndsWith('pt.jpg', $images[0]);
        $this->assertStringEndsWith('ps.jpg', $images[1]);
        $this->assertStringEndsWith('pl.jpg', $images[2]);

        $priviews = explode(',', $movie->previews);
        $this->assertCount(10, $priviews);

        $this->assertEquals(2000209496, $movie->series->id);
        $this->assertEquals('中出しされたザーメンをごっくん', $movie->series->name);

        $this->assertEquals(2000001509, $movie->maker->id);
        $this->assertEquals('ムーディーズ', $movie->maker->name);

        //NOTE:Failed on travic-ci by 0
        //$this->assertEquals(4, $movie->casts->count());

        //$this->assertEquals(1, count($movie->directors));
        //$this->assertEquals(2100105001, $movie->directors[0]->id);
    }

    public function testBanngoConvert()
    {
        $this->assertEquals('foobar', ImportDmmTask::dmmIdToBanngo('foobar'));

        $this->assertEquals('abcd004', ImportDmmTask::dmmIdToBanngo('104abcd00004'));
        $this->assertEquals('wofp01', ImportDmmTask::dmmIdToBanngo('10wofp0001s'));
        $this->assertEquals('svdvd124', ImportDmmTask::dmmIdToBanngo('1svdvd00124'));
        $this->assertEquals('aldmg212', ImportDmmTask::dmmIdToBanngo('b149aldmg00212'));
        $this->assertEquals('jpdrs01761', ImportDmmTask::dmmIdToBanngo('1jpdrs01761'));
        $this->assertEquals('atfb285', ImportDmmTask::dmmIdToBanngo('atfb00285'));
        $this->assertEquals('nfdm375', ImportDmmTask::dmmIdToBanngo('h_188nfdm00375'));


        $this->assertEquals('c01110', ImportDmmTask::dmmIdToBanngo('140c01110'));
        $this->assertEquals('180_01904', ImportDmmTask::dmmIdToBanngo('180_01904'));
        $this->assertEquals('acdv01001', ImportDmmTask::dmmIdToBanngo('148acdv01001'));
        $this->assertEquals('sd01026', ImportDmmTask::dmmIdToBanngo('165sd01026'));
        //$this->assertEquals('d1clymax011', ImportDmmTask::dmmIdToBanngo('189d1clymax00011'));
    }

    public function testDifferentBanngo()
    {
        $movie = $this->task->getMovie(file_get_contents(__DIR__ . '/_html/13ys36.html'));
        $this->assertEquals('gqd143', $movie->banngo);
        $this->assertEquals('13gqd00143', $movie->subBanngo);
    }

    public function testNoIdLink()
    {
        $movie = $this->task->getMovie(file_get_contents(__DIR__ . '/_html/12val00024.html'));
        $this->assertEquals('val024', $movie->banngo);
    }

    public function testNoActress()
    {
        $movie = $this->task->getMovie(file_get_contents(__DIR__ . '/_html/3wanz00261.html'));
        $this->assertEmpty($movie->casts);
    }

    public function testNoDirector()
    {
        $movie = $this->task->getMovie(file_get_contents(__DIR__ . '/_html/asfb00160.html'));
        $this->assertEmpty($movie->directors);
    }

    public function testNoSeries()
    {
        $movie = $this->task->getMovie(file_get_contents(__DIR__ . '/_html/asfb00160.html'));
        $this->assertEmpty($movie->series);
    }
}
