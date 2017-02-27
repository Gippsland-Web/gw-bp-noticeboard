<?php
/*
 Plugin Name: GW BP Notice Board
 Plugin URI: 
 Description: Adds [gw-hotlist] shortcode for a Notice Board system
 Author: GippslandWeb
 Version: 1.1
 Author URI: https://wordpress.org/
 GitHub Plugin URI: gippslandweb/gw-bp-noticeboard
 */

class GW_NoticeBoard {


    public function __construct() {
        add_action("init", array($this,"InitPostTypes"));
        add_shortcode("gw-hotlist",array($this,'RenderNoticeBoard'));
        add_action('wp_ajax_gw_new_notice', 	 array($this, 'AjaxNotice'));

    }
    function AjaxNotice() {
        $title = $_POST['title'];
        $contents = $_POST['content'];
        if( !wp_verify_nonce( $_POST['_wpnonce'], 'new-notice' ) ) die();

        
        $response = array(
            'result' => true,
            'errors' => array()
        );

        if(!is_user_logged_in()){
            $response['result'] = false;
            $response['errors'][] = __('You must be logged in to post a Notice.', 'bp-user-reviews');
        }
        
            
        if(!isset($contents) || strlen($contents) < 50)
        {
            $response['result'] = false;
            $response['errors'][] = sprintf(__('Notice must be at least %s characters', 'bp-user-reviews'), 50);
        }


        if($response['result'] === true){
            $noticeType = 'host-notice';
            if(bp_get_member_type(get_current_user_id()) == "wwoofer")
                $noticeType = 'wwoofer-notice';
            $newNotice = array(
                'post_title' => esc_attr($title),
                'post_type' => "gwnotice",
                'post_content' => esc_attr($contents),
                'post_author' => get_current_user_id(),
                'post_status' => 'publish',
                'tax_input' => array('notice_type' => $noticeType)
            ); 
            $postID = wp_insert_post($newNotice,$err);
            if(isset($err) || $postID == 0){
                $response['result'] = false;
                $response['errors'][] = "Error posting Notice".$err;
            }
        }

        wp_send_json($response);
        die();
    }

    function InitPostTypes() {
        $labels = array(
            'name' => _x('Notices', 'Notices'),
            'singular_name' => _x('Notice','Notice'),
            
        );

        register_post_type("gwnotice", array(
            'labels' => $labels,
            'description' => 'Notice Board AKA HotList',
            'public' => true,
            'menu_position' => 10,
            'supports' => array('title','editor','thumbnail','excerpt'),
        ));

        $taxLabels = array(
        'name' => _x("Notice Types",""),
        'singular_name' => _x("Notice Type",""),
        );
        register_taxonomy('notice_type','gwnotice',array(
            'labels' => $taxLabels,
        ));
    }


    function RenderNoticeBoard($atts) {
        $type = "host-notice";
        if(isset($_GET['t']) && $_GET['t'] == 'w')
            $type = "wwoofer-notice";

        $args = array(
            'post_type' => 'gwnotice',
            'orderby' => 'date',
            'tax_query' => array(
            array(
                'taxonomy' => 'notice_type',
                'field' => 'slug',
                'terms' => $type
            )
        ),
        'date_query' => array(array('after' => '2 weeks ago')) );
        $context = Timber::get_context();
        
        $context['data'] = Timber::get_posts($args);
        switch($type) {
            case 'host-notice':
                $context['type'] = "Host";
                break;
            case 'wwoofer-notice';
                $context['type'] = "WWOOFER";
                break;
        }
        
        Timber::render('noticeboard.twig', $context,false,TimberLoader::CACHE_NONE);

    }
}
$GWNoticeBoard = new GW_NoticeBoard();

//Left public to make it easy to call from twig - Sorry
function gw_nb_getMessageURL($id) {
        $author = bp_core_get_user_displayname($id);
        return '<a class="btn" href="'.wp_nonce_url( bp_loggedin_user_domain() . 'bp-' . bp_get_messages_slug() . '/?new-message&to=' . $author ) .'title="Response%20to%20Notice">Private Message</a>';
        //return '<a class="btn" href="'.wp_nonce_url( bp_loggedin_user_domain() . bp_get_messages_slug() . '/compose/?r=' . $author ) .'title="Response to Notice">Private Message</a>';

}














