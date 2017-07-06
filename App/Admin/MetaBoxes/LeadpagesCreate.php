<?php


namespace LeadpagesWP\Admin\MetaBoxes;

use LeadpagesWP\Admin\CustomPostTypes\LeadpagesPostType;
use LeadpagesWP\models\LeadPagesPostTypeModel;
use TheLoop\Contracts\MetaBox;


class LeadpagesCreate extends LeadpagesPostType implements MetaBox
{

    /**
     * @var \LeadpagesWP\models\LeadPagesPostTypeModel
     */
    private $postTypeModel;
    /**
     * @var \Leadpages\Pages\LeadpagesPages
     */
    private $pagesApi;

    public function __construct()
    {
        global $leadpagesApp;

        $this->pagesApi      = $leadpagesApp['pagesApi'];
        $this->postTypeModel = $leadpagesApp['lpPostTypeModel'];
        $this->splitTestApi  = $leadpagesApp['splitTestApi'];
        add_action('wp_ajax_get_pages_dropdown', array($this, 'generateSelectList'));
        add_action('wp_ajax_nopriv_get_pages_dropdown', array($this, 'generateSelectList'));
    }


    public static function getName()
    {
        return get_called_class();
    }

    public function defineMetaBox()
    {
        add_meta_box("leadpage-create", "Leadpages Create", array($this, 'callback'), $this->postTypeName, "normal",
          "high", null);
    }

    public function callBack($post, $box)
    {
        $useCache    = LeadPagesPostTypeModel::getMetaCache($post->ID);
        $currentType = LeadPagesPostTypeModel::getMetaPageType($post->ID);
        $slug        = LeadPagesPostTypeModel::getMetaPagePath($post->ID);
        $action      = (isset($_GET['action']) && $_GET['action'] == 'edit') ? 'Edit' : 'Add New';

        ?>
        <div class="leadpages-edit-wrapper">
        <div id="leadpages-header-wrapper" class="flex flex--xs-between flex--xs-middle">
            <div class="ui-title-nav" aria-controls="navigation">
                <div class="ui-title-nav__img">
                    <i class="lp-icon lp-icon--alpha">leadpages_mark</i>
                </div>
                <div class="ui-title-nav__content">
                    <?= $action; ?> Leadpage
                </div>
            </div>

            <button id="publish" name="publish" class="ui-btn">
                Publish
                <!-- Loading icons-->
                <div class="ui-loading ui-loading--sm ui-loading--inverted">
                    <div class="ui-loading__dots ui-loading__dots--1"></div>
                    <div class="ui-loading__dots ui-loading__dots--2"></div>
                    <div class="ui-loading__dots ui-loading__dots--3"></div>
                </div>
                <!-- End Loading Icons-->
            </button>
        </div>

        <!-- Body Start -->
        <div class="leadpages-edit-body">
            <div class="flex leadpages-loading">
                <div class="ui-loading">
                    <div class="ui-loading__dots ui-loading__dots--1"></div>
                    <div class="ui-loading__dots ui-loading__dots--2"></div>
                    <div class="ui-loading__dots ui-loading__dots--3"></div>
                </div>
            </div>
            <div class="flex">
                <div class="flex__item--xs-12">
                    <p class="header_text">
                        Welcome to the Leadpages admin. Select your Leadpage below, which page type you would like it
                        to be, and give it a slug below.
                    </p>
                </div>
            </div>
            <div class="select_a_leadpage flex">
                <h3>Select a Leadpage</h3>

                <p>Please select your desired Leadpage below. Have a lot of Leadpages? Feel free to use the
                search box to quickly find your Leadpage by name.</p>

                <div class="leadpages_search_container flex__item--xs-7">
                    <div id="leadpages_my_selected_page"></div>
                </div>
                <div class="flex__item--xs-1">
                    <i class="sync-leadpages lp-icon lp-icon--xsm lp-icon-sync"></i>
                </div>
            </div>
            <div class="select_a_leadpage_type flex">
                <h3 class="flex__item--xs-12">Select a Page Type</h3>

                <p class="flex__item--xs-12"> Please select a Leadpage display type below.</p>

                <div class="leadpage_type_container flex">
                    <div class="leadpage_type_box">
                        <h3 class="header">Normal Page</h3>

                        <p class="section_description">
                            This display type will allow you to direct people to this leadpage by using the
                            slug below.
                        </p>
                        <input id="leadpage-normal-page" type="radio" name="leadpages-post-type" class="leadpages-post-type leadpage-normal-page"
                               value="lp" <?php echo $currentType == "lp" ? 'checked=checked"' : ""; ?> >
                    </div>
                    <div class="leadpage_type_box">
                        <h3 class="header">Home Page</h3>

                        <p>
                            This will take over your home page on your blog. Anytime someone goes to
                            your home page it will show this page.
                        </p>
                        <input id="leadpage-home-page" type="radio" name="leadpages-post-type" class="leadpages-post-type leadpage-home-page"
                               value="fp" <?php echo $currentType == "fp" ? 'checked=checked"' : ""; ?> >
                    </div>
                    <div class="leadpage_type_box">
                        <h3 class="header">Welcome Gate &trade;</h3>

                        <p>
                            A Welcome Gate &trade; page will be the first page any new visitor to your site sees.
                        </p>
                        <input id="leadpage-welcome-page" type="radio" name="leadpages-post-type" class="leadpages-post-type leadpage-welcomegate-page"
                               value="wg" <?php echo $currentType == "wg" ? 'checked=checked"' : ""; ?> >
                    </div>
                    <div class="leadpage_type_box">
                        <h3 class="header">404 Page</h3>

                        <p>
                            This will allow you to put a Leadpage as your 404
                            page to ensure you are not missing out on any conversions.
                        </p>
                        <input id="leadpage-404-page" type="radio" name="leadpages-post-type" class="leadpages-post-type leadpage-404-page"
                               value="nf" <?php echo $currentType == "nf" ? 'checked=checked"' : ""; ?> >
                    </div>
                </div>
            </div>
            <div id="leadpage-slug" class="leadbox_slug flex">
                <h3 class="flex__item--xs-12">Set a Custom Slug</h3>

                <p class="flex__item--xs-12">
                    Enter a custom slug for your Leadpage. This will be the url someone will go to to see your Leadpage.
                    <br />
                    Instructions:
                </p>
                <ul class="ui-list ui-list--bulleted">
                    <li>You may enter multi-part slugs such as parent/child/grand-child</li>
                    <li>Omit / at the start and end of slug(they will be trimed off upon saving)
                        <ul class="ui-list ui-list--bulleted">
                            <li>Good: my-wonderful-page</li>
                            <li>Bad: /my-wonderful-page/</li>
                        </ul>
                    </li>

                </ul>


                <div class="flex__item--xs-12 leadpage_slug_container">
                    <span class="lp_site_main_url"><?php echo $this->leadpages_permalink(); ?></span>
                    <input type="text" name="leadpages_slug" class="leadpages_slug_input" value="<?php echo $slug; ?>">
                </div>
            </div>
            <div id="leadpage-cache" class="leadbox_slug flex">
                <h3 class="flex__item--xs-12">Set Page Cache</h3>

                <p class="flex__item--xs-12">
                    Choose whether or not you would like to cache your page html locally.
                    This will create faster page loads, however if a page is split tested, the split tested version
                    will not load.
                </p>

                <div class="flex__item--xs-12 leadpage_cache_container">
                    <input type="radio" id="cache_this_true" name="cache_this" value="true"  <?php echo ($useCache == 'true') ? 'checked="checked"': ''; ?>> Yes, cache for improved performance. <br />
                    <input type="radio" id="cache_this_false" name="cache_this" value="false"  <?php echo ($useCache != 'true') ? 'checked="checked"': ''; ?>> No, re-fetch on each visit; slower, but required for split testing.
                </div>
            </div>
            <input type="hidden" name="leadpages_name" id="leadpages_name">
            <input type="hidden" name="leadpage_type" id="leadpageType">
        </div>
        <div id="leadpages-footer-wrapper" class="flex flex--xs-end flex--xs-middle">

            <button id="publish" name="publish" class="ui-btn">
                Publish
                <!-- Loading icons-->
                <div class="ui-loading ui-loading--sm ui-loading--inverted">
                    <div class="ui-loading__dots ui-loading__dots--1"></div>
                    <div class="ui-loading__dots ui-loading__dots--2"></div>
                    <div class="ui-loading__dots ui-loading__dots--3"></div>
                </div>
                <!-- End Loading Icons-->
            </button>
        </div>
        <?php
    }


    public function registerMetaBox()
    {
        add_action('add_meta_boxes', array($this, 'defineMetaBox'));
    }

    public function generateSelectList()
    {
        global $leadpagesApp;

        $id          = sanitize_text_field($_POST['id']);
        $currentPage = LeadPagesPostTypeModel::getMetaPageId($id);

        if (!$currentPage) {
            $currentPage = $leadpagesApp['lpPostTypeModel']->getPageByXORId($id);
        }

        $pages = $leadpagesApp['pagesApi']->getAllUserPages();

        $splitTest    = $leadpagesApp['splitTestApi']->getActiveSplitTests();
        $items['_items'] = array_merge($pages['_items'], $splitTest);
        $items = $leadpagesApp['pagesApi']->sortPages($items);
        $size         = sizeof($items['_items']);
        $optionString = '';
        $optionString .= '<select id="select_leadpages" class="leadpage_select_dropdown" name="leadpages_my_selected_page">';
        foreach ($items['_items'] as $page) {
            if (isset($page['splitTestId'])) {
                continue;
            }

            $pageId = number_format($page['id'], 0, '.', '');
            $optionString .= "<option value=\"{$page['_meta']['xor_hex_id']}:{$pageId}\" " . ($currentPage == $pageId ? 'selected="selected"' : '') . " >{$page['name']}</option>";
        }
        $optionString .= '</select>';
        echo $optionString;
        die();
    }

    //replace with get_permalink
    public function leadpages_permalink()
    {
        global $post;
        if($post->post_status !='publish'){
            $permalink = 'Publish to see full url';
        }else{
            $permalink = home_url() .'/';
        }
        $permalink = str_replace('/leadpages_post/', '', $permalink);
        $permalink = str_replace('/'.$post->post_name.'/', '/', $permalink);
        return $permalink;
    }


}
