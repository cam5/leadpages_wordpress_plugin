<?php

namespace LeadpagesWP\Front\Controllers;

use Leadpages\Pages\LeadpagesPages;
use LeadpagesWP\Helpers\LeadpageType;
use LeadpagesMetrics\LeadpagesErrorEvent;
use LeadpagesWP\Helpers\PasswordProtected;
use LeadpagesWP\models\LeadPagesPostTypeModel;

class LeadpageController
{
    public $postPassword;


    /**
     * @var \LeadpagesWP\models\LeadPagesPostTypeModel
     */
    private $leadpagesModel;
    /**
     * @var \LeadpagesWP\Front\Controllers\NotFoundController
     */
    private $notfound;
    /**
     * @var \LeadpagesWP\Front\Controllers\WelcomeGateController
     */
    private $welcomeGate;
    /**
     * @var \Leadpages\Pages\LeadpagesPages
     */
    private $pagesApi;
    /**
     * @var \LeadpagesWP\Helpers\PasswordProtected
     */
    private $passwordChecker;

    public function __construct(NotFoundController $notfound, WelcomeGateController $welcomeGate ,LeadPagesPostTypeModel $leadpagesModel, LeadpagesPages $pagesApi, PasswordProtected $passwordChecker)
    {

        $this->leadpagesModel = $leadpagesModel;
        $this->notfound = $notfound;
        $this->welcomeGate = $welcomeGate;
        $this->pagesApi = $pagesApi;
        $this->passwordChecker = $passwordChecker;
    }

    /**
     * check to see if current page is front page and if so see if a front
     * leadpage exists to display it
     *
     * @param $posts
     *
     * @return
     */
    public function isFrontPage($posts)
    {
        global $leadpagesApp;

        if (is_home() || is_front_page()) {

            //see if a front page exists
            $post = LeadpageType::get_front_lead_page();
            //see if the post actually exists
            $postExists = self::checkLeadpagePostExists($post);
            //if the post does not exist remove the option from the db
            if(!$postExists){
                self::deleteOrphanPost('leadpages_front_page_id');
                return $posts;
            }

            //if $post is > 0 that means one exists and we need to display it
            if ($post > 0) {
                $pageId = $this->leadpagesModel->getLeadpagePageId($post);
                if($pageId == '') {
                    return $posts;
                }
                //check for cache
                $getCache = get_post_meta($post, 'cache_page', true);
                if($getCache == 'true'){
                    $html = $this->leadpagesModel->getCacheForPage($pageId);
                    if(empty($html)){
                        $apiResponse = $this->pagesApi->downloadPageHtml($pageId);
                        $html = $apiResponse['response'];
                        $this->leadpagesModel->setCacheForPage($pageId);
                    }
                }else {
                    //no cache download html
                    $apiResponse = $this->pagesApi->downloadPageHtml($pageId);
                    if(isset($apiResponse['error'])){
                        $leadpagesApp['errorEventsHandler']->reportError($apiResponse, ['pageId' => $pageId]);
                        //output error to screen
                        return $posts;
                    }
                    $html = $apiResponse['response'];
                }
                echo $html;
                die();
            }
        }
        return $posts;
    }

    /**
     *Display WelcomeGate Page
     */
    public function displayWelcomeGate($posts)
    {
        return $this->welcomeGate->displayWelcomeGate($posts);
    }

    /**
     * display a normal lead page if page type is a leadpage
     *
     * @param $post
     */

    public function displayNFPage()
    {

        $this->notfound->displaynfPage();
    }

    /**
     * Echos a normal Leadpage type html if the post type is leadpages_post
     * @param $post
     */
    public function normalPage()
    {
        global $leadpagesApp;
        //get page uri
        $requestedPage = $this->parse_request();

        if ( false == $requestedPage ) {
            return false;
        }
        //get post from database including meta data
        $post = LeadPagesPostTypeModel::get_all_posts($requestedPage[0]);

        if($post == false) return false;

        //ensure we have the leadpages page id
        if(isset($post['leadpages_page_id'])){
            $pageId = $post['leadpages_page_id'];
        }elseif(isset($post['leadpages_my_selected_page'])){
            $pageId = $this->leadpagesModel->getPageByXORId($post['post_id'], $post['leadpages_my_selected_page']);
        }else{
            return false;
        }

        if(empty($pageId)){
            return false;
        }

        //check cache
        $getCache = get_post_meta($post['post_id'], 'cache_page', true);

        if($getCache == 'true'){
            $html = $this->leadpagesModel->getCacheForPage($pageId);
            //failsafe incase the cache is not set for some reason
            //get html and set cache
            if(empty($html)){
                $apiResponse = $this->pagesApi->downloadPageHtml($pageId);
                $html = $apiResponse['response'];
                $this->leadpagesModel->setCacheForPage($pageId);
            }
        }else {
            $apiResponse = $this->pagesApi->downloadPageHtml($pageId);
            if(isset($apiResponse['error'])){
                $leadpagesApp['errorEventsHandler']->reportError($apiResponse, ['pageId' => $pageId]);
                //output error to screen
                return;
            }
            $html = $apiResponse['response'];
        }

        if(ob_get_length() > 0){
            ob_clean();
        }
        ob_start();//start output buffer
        status_header( '200' );
        print $html;
        ob_end_flush();
        die();
    }

    function parse_request() {
        // get current url
        $current = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        // calculate the path
        $part = substr( $current, strlen( site_url() ) );
        if ( $part[0] == '/' ) {
            $part = substr( $part, 1 );
        }
        // strip parameters
        $real   = explode( '?', $part );
        $tokens = explode( '/', $real[0] );
        foreach($tokens as $index => $token){
            //decode url enteities such as %20 for space
            $tokens[$index] = urldecode($token);
        }
        return $tokens;
    }

    public static function checkLeadpagePostExists($postId)
    {
        $exists = get_post($postId);
        if(empty($exists)){
            return false;
        }
        return true;
    }

    /**
     * @param $postId
     * @param $postType
     * postType should be the Leadpages Post type of leadpages_front_page_id or welcome gate ect
     */
    public static function deleteOrphanPost($postType)
    {
        delete_option($postType);
    }

}