<?php

namespace App\Test\TestCase\View\Helper;
use App\View\Helper\BreadcrumbHelper;
use Cake\TestSuite\TestCase;
use Cake\View\View;

class BreadcrumbHelperTest extends TestCase {
    public $helper = null;

    public function setUp() {
        parent::setUp();
        $View = new View();
        $this->helper = new BreadcrumbHelper($View);
    }

    // Testing the catfood function
    public function testCatfood() {
        $this->assertEquals('catfood', $this->helper->catfood());
    }
}
