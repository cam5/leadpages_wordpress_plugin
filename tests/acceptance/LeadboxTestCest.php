<?php


class LeadboxTestCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    protected function goToLeadboxPage(AcceptanceTester $I)
    {
        $I->amOnAdminPage('admin.php?page=Leadboxes');
        $I->see('Configure Leadboxes');
    }

    /**
     * @test
     * @group leadobx
     * @param \AcceptanceTester $I
     */
    public function setup_a_global_timed_leadbox(AcceptanceTester $I)
    {
        $I->wantTo('Test that a user can create a global timed Leadbox');
        $I->loginAsAdmin();
        baseAcceptanceTest::loginToLeadpages($I);
        $this->goToLeadboxPage($I);

        $I->selectOption('#leadboxesTime', '1418aa546639c5');
        $I->see('Test Timed Leadbox');
        $I->see('Time before it appears: 1 seconds');
        $I->see('Page views before it appears: 0 views');
        $I->see('Don\'t reshow for the next: 0 days');

        $I->click('#leadpages-save');
    }
}
