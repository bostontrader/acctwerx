<?php

namespace App\Test\TestCase\View\Helper;
use App\View\Helper\FinStatHelper;
use Cake\TestSuite\TestCase;
use Cake\View\View;

class FinStatHelperTest extends TestCase {
    public $helper = null;

    public function setUp() {
        parent::setUp();
        $View = new View();
        $this->helper = new FinStatHelper($View);
    }

    public function testGetStartPeriod() {
        $this->assertEquals(array('year'=>2015, 'month'=>6), $this->helper->getStartPeriod());
    }

    public function testGetStopPeriod() {
        $this->assertEquals(array('year'=>2016, 'month'=>3), $this->helper->getStopPeriod());
    }
}
