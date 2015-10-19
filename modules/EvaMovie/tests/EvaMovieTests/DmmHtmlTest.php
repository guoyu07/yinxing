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
        $this->assertEquals('2014-10-13', $movie->pubdate);
        $this->assertEquals('2014', $movie->year);
        $this->assertEquals('238', $movie->durations);
        $this->assertStringStartsWith('MOODYZ大人気ハード企画', $movie->summary);

        $this->assertCount(9, $movie->tags);
        $this->assertEquals('ハイビジョン', $movie->tags[0]);

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

        #$this->assertEquals(4, count($movie->casts));
        #$this->assertEquals(2001012295, $movie->casts[0]->id);
        #$this->assertEquals('枢木みかん', $movie->casts[0]->name);
    }

    public function testNoActress()
    {
        $movie = $this->task->getMovie(file_get_contents(__DIR__ . '/_html/3wanz00261.html'));
        $this->assertEmpty($movie->casts);
    }

    public function testNoDirector()
    {
        $movie = $this->task->getMovie(file_get_contents(__DIR__ . '/_html/asfb00160.html'));
    }

    public function testNoSeries()
    {
        $movie = $this->task->getMovie(file_get_contents(__DIR__ . '/_html/asfb00160.html'));
        $this->assertEmpty($movie->series);
    }
}
