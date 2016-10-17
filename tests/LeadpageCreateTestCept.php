<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('Test that a user can create a new page in the admin');
$I->loginAsAdmin();

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
//$I->see('E2E Test Page Do Not Remove');
$I->selectOption('#leadpage-normal-page', 'lp');
$I->fillField('.leadpages_slug_input', 'test-leadpage');
$I->click('#publish');
$I->seeInDatabase('wptests_postmeta', ['meta_value' => 'test-leadpage'] );



