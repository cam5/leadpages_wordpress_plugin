<?php

use GuzzleHttp\Client;

class LeadpagesLoginTest extends \Codeception\TestCase\WPTestCase
{

    public function setUp()
    {
        // before
        parent::setUp();

        // your set up methods here
    }

    public function tearDown()
    {
        // your tear down methods here

        // then
        parent::tearDown();
    }

    /**
     * @test
     */
    public function does_this_test_run()
    {
        activate_plugin('plugins/leadpages.php');
        $plugin_active = is_plugin_active('plugins/leadpages.php');
        $this->assertTrue($plugin_active);
    }

}