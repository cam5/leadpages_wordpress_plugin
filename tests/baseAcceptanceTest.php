<?php


class baseAcceptanceTest{

    public static function loginToLeadpages(AcceptanceTester $I)
    {
        //see if token is in database
        $isLoggedIn = $I->grabFromDatabase('wptests_options', 'option_value', ['option_name' => 'leadpages_security_token']);
        //if token is not in database login
        if(!$isLoggedIn) {
            //Ensure you see Leadpages to make sure plugin is active
            $I->see('You are not logged into Leadpages. Your pages will not work until you login');
            //go to login screen
            $I->click(['class' => 'notice_login_link']);
            //check to make sure the url is correct

            $I->seeCurrentUrlMatches('~(.)*?\?page=Leadpages~');

            //Fill out login form
            $I->fillField('username', getenv('leadpagesUsername'));
            $I->fillField('password', getenv('leadpagesPassword'));
            $I->click('form-submit');

            //ensure we are on the leadpages post type page
            $I->seeCurrentUrlMatches('~(.)*post_type=leadpages_post~');
        }else{
            //go to leadpage page if you are logged in
            $I->amOnAdminPage('edit.php?post_type=leadpages_post');
        }
    }

    public static function fillOutLeadpageDropdown(AcceptanceTester $I)
    {
        //we need to open the dropdown for the select box
        $I->executeJS("jQuery('#select_leadpages').select2('open');");
        $I->wait(1);
        //fill in the search field with part of the page
        $I->fillField('.select2-search__field', 'E2E');
        //press enter
        $I->pressKey('.select2-search__field', WebDriverKeys::ENTER);
    }

    public static function goToleadpagesPostTypePage(AcceptanceTester $I)
    {
        $I->click('.page-title-action');
        $I->seeCurrentUrlMatches('~(.)*post-new.php\?post_type=leadpages_post~');
        //wait till everything is loaded
        $I->waitForElementNotVisible('.ui-loading');
    }

}