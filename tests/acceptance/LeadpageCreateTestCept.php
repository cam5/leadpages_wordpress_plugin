<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('Test that a user can create a new page in the admin');
$I->loginAsAdmin();
//Ensure you see Leadpages to make sure plugin is active
$I->see('You are not logged into Leadpages. Your pages will not work until you login');
//go to login screen
$I->click(['class'=>'notice_login_link']);
//check to make sure the url is correct

$I->seeCurrentUrlMatches('~(.)*?\?page=Leadpages~');

//Fill out login form
$I->fillField('username', 'brandon.braner@ave81.com');
$I->fillField('password', 'cd007-01');
$I->click('form-submit');

//ensure we are on the leadpages post type page
$I->seeCurrentUrlMatches('~(.)*post_type=leadpages_post~');
$I->click('.page-title-action');
$I->seeCurrentUrlMatches('~(.)*post-new.php\?post_type=leadpages_post~');

//wait till everything is loaded
$I->waitForElementNotVisible('.ui-loading');

//we need to open the dropdown for the select box
$I->executeJS("jQuery('#select_leadpages').select2('open');");
$I->wait(1);
//fill in the search field with part of the page
$I->fillField('.select2-search__field', 'E2E');
//press enter
$I->pressKey('.select2-search__field', WebDriverKeys::ENTER);

//check to make sure that the new value is set
$I->see('E2E Test Page Do Not Remove');

//$I->selectOption('.leadpage-normal-page');


