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
        $useCache = LeadPagesPostTypeModel::getMetaCache($post->ID);

        ?>
        <div class="leadpages-edit-wrapper">
            <div id="leadpages-header-wrapper" class="flex flex--xs-between flex--xs-middle">
                <div class="ui-title-nav" aria-controls="navigation">
                    <div class="ui-title-nav__img">
                        <i class="lp-icon lp-icon--alpha">leadpages_mark</i>
                    </div>
                    <div class="ui-title-nav__content">
                        Add New Leadpage
                    </div>
                </div>

                <button id="leadpages-save" class="ui-btn">
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
                <div class="flex">
                    <div class="flex__item--xs-7">
                        <p>
                            Maecenas quis ullamcorper enim. Morbi molestie metus eget ipsum suscipit, ut elementum dolor
                            vulputate.
                            Sed sed mauris euismod, finibus elit id, vulputate nunc. Interdum et malesuada fames ac ante
                            ipsum
                            primis in faucibus. Nam mattis viverra orci, eu blandit nisl imperdiet at.
                        </p>
                    </div>
                </div>
                <div class="select_a_leadpage flex">
                    <h3>Select a Leadpage</h3>
                    <div class="leadpages_search_container flex__item--xs-7">
                        <div id="leadpages_my_selected_page"></div>
                    </div>
                </div>
            </div>
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

        $items        = $leadpagesApp['pagesApi']->getAllUserPages();
        $size = sizeof($items['_items']);
        $optionString = '';
        $optionString .= '<select id="select_leadpages" class="leadpage_select_dropdown" name="leadpages_my_selected_page">';
        foreach ($items['_items'] as $page) {
            $pageId = number_format($page['id'], 0, '.', '');
            $optionString .= "<option value=\"{$page['_meta']['xor_hex_id']}:{$pageId}\" " . ($currentPage == $pageId ? 'selected="selected"' : '') . " >{$page['name']}</option>";
        }
        $optionString .= '</select>';
        echo $optionString;
        die();
    }


}