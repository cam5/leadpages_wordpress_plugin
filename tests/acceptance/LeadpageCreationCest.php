<?php

class LeadpageCreationCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }


    /**
     * @test
     * @group create-page
     * @param \AcceptanceTester $I
     */
    public function a_normal_page_can_be_created(AcceptanceTester $I)
    {
        $I->wantTo('Test that a user can create a new normal page in the admin');
        $I->loginAsAdmin();
        //Ensure you see Leadpages to make sure plugin is active
        baseAcceptanceTest::loginToLeadpages($I);

        baseAcceptanceTest::goToleadpagesPostTypePage($I);
        baseAcceptanceTest::fillOutLeadpageDropdown($I);

        //check to make sure that the new value is set
        //$I->see('E2E Test Page Do Not Remove');
        $I->selectOption('#leadpage-normal-page', 'lp');
        $I->fillField('.leadpages_slug_input', 'test-leadpage');
        $I->click('#publish');
        $I->seeInDatabase('wptests_postmeta', ['meta_value' => 'test-leadpage']);
    }

    /**
     * @test
     *@group create-page
     * @param \AcceptanceTester $I
     */
    public function a_front_page_can_be_created(AcceptanceTester $I)
    {
        $I->wantTo('Test that a user can create a new front page in the admin');
        $I->loginAsAdmin();
        //Ensure you see Leadpages to make sure plugin is active
        baseAcceptanceTest::loginToLeadpages($I);
        baseAcceptanceTest::goToleadpagesPostTypePage($I);
        baseAcceptanceTest::fillOutLeadpageDropdown($I);

        //check to make sure that the new value is set
        //$I->see('E2E Test Page Do Not Remove');
        $I->selectOption('#leadpage-home-page', 'fp');
        $I->click('#publish');
        $I->wait(5);
        $I->seeInDatabase('wptests_options', ['option_name' => 'leadpages_front_page_id']);
        $frontPageId = $I->grabFromDatabase('wptests_options', 'option_value', ['option_name' => 'leadpages_front_page_id']);
        $I->seeInDatabase('wptests_postmeta', [
          'post_id' => $frontPageId,
          'meta_value' => 'fp',
        ]);
    }

    /**
     * @test
     * @group create-page
     * @param \AcceptanceTester $I
     */
    public function a_welcome_gate_page_can_be_created(AcceptanceTester $I)
    {
        $I->wantTo('Test that a user can create a new welcome gate page in the admin');
        $I->loginAsAdmin();
        //Ensure you see Leadpages to make sure plugin is active
        baseAcceptanceTest::loginToLeadpages($I);
        baseAcceptanceTest::goToleadpagesPostTypePage($I);
        baseAcceptanceTest::fillOutLeadpageDropdown($I);

        //check to make sure that the new value is set
        //$I->see('E2E Test Page Do Not Remove');
        $I->selectOption('#leadpage-welcome-page', 'wg');
        $I->click('#publish');
        $I->wait(5);
        $I->seeInDatabase('wptests_options', ['option_name' => 'leadpages_wg_page_id']);
        $wgId = $I->grabFromDatabase('wptests_options', 'option_value', ['option_name' => 'leadpages_wg_page_id']);
        $I->seeInDatabase('wptests_postmeta', [
          'post_id' => $wgId,
          'meta_value' => 'wg',
        ]);
    }

    /**
     * @test
     * @group create-page
     * @param \AcceptanceTester $I
     */
    public function a_404_page_can_be_created(AcceptanceTester $I)
    {
        $I->wantTo('Test that a user can create a new 404 page in the admin');
        $I->loginAsAdmin();
        //Ensure you see Leadpages to make sure plugin is active
        baseAcceptanceTest::loginToLeadpages($I);
        baseAcceptanceTest::goToleadpagesPostTypePage($I);
        baseAcceptanceTest::fillOutLeadpageDropdown($I);

        //check to make sure that the new value is set
        //$I->see('E2E Test Page Do Not Remove');
        $I->selectOption('#leadpage-404-page', 'nf');
        $I->click('#publish');
        $I->wait(5);
        $I->seeInDatabase('wptests_options', ['option_name' => 'leadpages_404_page_id']);
        $wgId = $I->grabFromDatabase('wptests_options', 'option_value', ['option_name' => 'leadpages_404_page_id']);
        $I->seeInDatabase('wptests_postmeta', [
          'post_id' => $wgId,
          'meta_value' => 'nf',
        ]);
    }
}
