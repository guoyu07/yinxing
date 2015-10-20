<?php
/**
 * @author    AlloVince
 * @copyright Copyright (c) 2015 EvaEngine Team (https://github.com/EvaEngine)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */


namespace Eva\EvaMovieTests;


use Eva\EvaMovie\Tasks\CrawlDmmTask;

class CrawlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CrawlDmmTask
     */
    protected $crawl;

    public function setUp()
    {
        $this->crawl = new CrawlDmmTask();
    }

    public function testNameParse()
    {
        list($name, $aka) = CrawlDmmTask::parseNameAndAka('中野美奈');
        $this->assertEquals('中野美奈', $name);
        $this->assertEquals([], $aka);

        list($name, $aka) = CrawlDmmTask::parseNameAndAka('酒井ちなみ（紫葵）');
        $this->assertEquals('酒井ちなみ', $name);
        $this->assertEquals(['紫葵'], $aka);

        list($name, $aka) = CrawlDmmTask::parseNameAndAka('黒木麻衣（花野真衣、SHIHO）');
        $this->assertEquals('黒木麻衣', $name);
        $this->assertEquals(['花野真衣', 'SHIHO'], $aka);

    }

}