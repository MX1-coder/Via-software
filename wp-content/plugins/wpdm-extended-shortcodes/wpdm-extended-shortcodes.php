<?php
/**
 * Plugin Name: WPDM - Extended Short-codes
 * Plugin URI: http://www.wpdownloadmanager.com/download/wpdm-extended-short-codes/
 * Description: WordPress Download Manager Pro Extended Short-Codes
 * Author: Shaon
 * Version: 2.8.7
 * Text Domain: wpdm-extended-shortcodes
 * Domain Path: /languages
 * Author URI: http://www.wpdownloadmanager.com/
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

if (defined('WPDM_Version')) {

    /**
     * @param $params
     * @return mixed
     * @usage Handle output for [wpdm_tree category=id download_link=0/1 orderby=title/modified/date order=asc/desc ] shrotcode
     */

    function wpdm_tree($params = array()){
        $treejs = plugins_url('/wpdm-extended-shortcodes/js/jqueryFileTree.js');
        $treecss = plugins_url('/wpdm-extended-shortcodes/css/jqueryFileTree.css');
        $siteurl = site_url();

        @extract($params);
        $category = isset($category) ? get_term_by("slug", $category, 'wpdmcategory') : null;
        $category = $category ? $category->term_id : '/';
        $download_link = isset($download_link) ? (int)$download_link : 0;

        $_params = \WPDM\libs\Crypt::encrypt($params);

        $orderby = isset($orderby) ? $orderby : 'modified';
        $order = isset($order) ? $order : 'desc';
        $newwin = isset($newwin) && (int)$newwin === 1 ? 'newwin=1' : '';

        $tid = uniqid();
        $data = <<<TREE
        
        <div id="wpdmtree"></div>
    <script language="JavaScript" src="{$treejs}"></script>     
    <link rel="stylesheet" href="{$treecss}" />          
    <div id="tree{$tid}"></div>
    <script language="JavaScript">
        jQuery( function($) {            
            $('#tree{$tid}').fileTree({
                script: '{$siteurl}/?task=wpdm_tree&params={$_params}', 
                expandSpeed: 1000,
                collapseSpeed: 1000,
                root: '{$category}',
                multiFolder: false
            }, function(file) {
                location.href = file;  
            });
        });
    </script>    
TREE;

        return str_replace(array("\n","\r"),"", $data);
    }

    function wpdm_slider($params = array())
    {
        $ids = "";
        extract($params);
        $ids = explode(",", $ids);
        ob_start();
        include __DIR__.'/templates'.WPDM()->bsversion.'/slider.php';
        $data = ob_get_clean();
        return $data;
    }

    function wpdm_carousel($params = array())
    {

        if (is_array($params))
            extract($params);
        if (isset($category)) {
            if(intval($category) == 0 && $category != '')
                $cat = get_term_by('slug', $category, 'wpdmcategory');
            else
                $cat = get_term_by('term_id', $category, 'wpdmcategory');
        }
        ob_start();

        ?>

        <div class="w3eden">
        <div class="well">
            <div class="row">
                <div class="col-md-12">
                    <h3 style="line-height: normal;margin: 0px;float:left;">

                    <?php echo isset($category) ? $cat->name : 'New Downloads'; ?>

                    </h3>

                    <div class="pull-right" style="margin-top: -5px;">
                        <a class="btn btn-xs btn-inverse btn-transparent" href="#myCarousel1" data-slide="prev"><i
                                class="fa fa-white fa-chevron-left"></i></a>
                        <a class="btn btn-xs btn-inverse btn-transparent" href="#myCarousel1" data-slide="next"><i
                                class="fa fa-white fa-chevron-right"></i></a>
                        <?php if (isset($category)): ?><a class="btn btn-xs btn-inverse btn-transparent"
                                                          title='View All'  href="<?php echo get_term_link($cat); ?>"
                                                          data-slide="next"><i class="fa fa-white fa-th"></i>
                            </a><?php endif; ?>
                    </div>
                    <hr style="clear: both;margin-top: 25px"/>

                </div>

            </div>
            <div id="myCarousel1" class="carousel slide">
                <!--<ol class="carousel-indicators">
                    <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
                    <li data-target="#myCarousel" data-slide-to="1"></li>
                    <li data-target="#myCarousel" data-slide-to="2"></li>
                </ol>-->
                <!-- Carousel items -->
                <div class="carousel-inner">
                    <div class="active item">
                        <div class="row">
                            <?php
                            $params = array(
                                'post_type' => 'wpdmpro',
                                'posts_per_page' => 4
                            );
                            if (isset($category))
                                $params['tax_query'] = array(array(
                                    'taxonomy' => 'wpdmcategory',
                                    'field' => 'slug',
                                    'terms' => array($cat->slug)
                                ));
                            $packs = get_posts($params);
                            foreach ($packs as $file) {


                                ?>
                                <div class="col-md-3">
                                    <figure class="rift">
                                        <?php wpdm_thumb($file->ID, array(300, 200)); ?>


                                        <figcaption class="caption"><a
                                                href='<?php echo get_permalink($file->ID); ?>'><?php echo $file->post_title; ?></a>
                                        </figcaption>


                                    </figure>
                                </div>
                            <?php
                            }
                            ?>
                        </div>
                    </div>

                    <div class="item">
                        <div class="row">
                            <?php
                            $params = array(
                                'post_type' => 'wpdmpro',
                                'offset' => 4,
                                'posts_per_page' => 4
                            );
                            if (isset($category))
                                $params['tax_query'] = array(array(
                                    'taxonomy' => 'wpdmcategory',
                                    'field' => 'slug',
                                    'terms' => array($category)
                                ));
                            $packs = get_posts($params);
                            foreach ($packs as $file) {

                                ?>
                                <div class="col-md-3">
                                    <figure class="rift">
                                        <?php wpdm_thumb($file->ID, array(300, 200)); ?>


                                        <figcaption class="caption"><a
                                                href='<?php echo get_permalink($file->ID); ?>'><?php echo $file->post_title; ?></a>
                                        </figcaption>


                                    </figure>


                                </div>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        </div>
        <style>
            .carousel-control {
                border-radius: 0 !important;
                border: 0 !important;
                background: rgba(30, 96, 158, 0.8);
                padding: 10px 5px 18px 5px;
            }

            /* Base plugin styles */

            .rift {

                position: relative;
                overflow: hidden;
                backface-visibility: hidden;
                border-radius: 3px !important;
            }

            .rift img {
                width: 100%;
                height: auto;
                opacity: 0;
            }

            .rift .caption {
                position: absolute;
                top: 50%;
                width: 100%;
                height: 60px; /* Define caption height */
                line-height: 60px; /* Define matched line-height */
                margin: -30px 0 0 0; /* Half caption height */
                text-align: center;
                z-index: 0;
            }

            .rift span[class*="span"] {
                display: block;
                width: 100%;
                height: 50%;
                overflow: hidden;
                position: absolute;
                left: 0;
                z-index: 1;
                transform: translate3d(0, 0, 0); /* Acceleration FTW */
                transition: transform .25s; /* Define anim. speed */
            }

            .rift span.top-span {
                top: 0;
            }

            .rift span.btm-span {
                bottom: 0;
            }

            .rift:hover span.top-span {
                transform: translate(0, -30px); /* Half caption height */
            }

            .rift:hover > span.btm-span {
                transform: translate(0, 30px); /* Half caption height */
            }

            /* Non-plugin styles */

            .rift {
                display: inline-block;
                cursor: pointer;
            }

            .rift .caption {
                color: #ffffff;
                background: rgba(0, 0, 0, 0.57);
            }

            .rift .caption a {
                color: #ffffff;
                font-weight: bold;
            }

        </style>
        <script>
            jQuery(function ($) {
                $('#myCarousel1').carousel();
            });

            /**
             * Rift v1.0.0
             * An itsy bitsy image-splitting jQuery plugin
             *
             * Licensed under the MIT license.
             * Copyright 2013 Kyle Foster @hkfoster
             */

            (function ($, window, document, undefined) {

                $.fn.rift = function () {

                    return this.each(function () {

                        // Vurribles
                        var element = $(this),
                            elemImg = element.find('img'),
                            imgSrc = elemImg.attr('src');

                        // We be chainin'
                        element
                            .prepend('<span class="top-span"></span>')
                            .append('<span class="btm-span"></span>')
                            .find('span.top-span')
                            .css('background', 'url(' + imgSrc + ') no-repeat center top')
                            .css('background-size', '100%')
                            .parent()
                            .find('span.btm-span')
                            .css('background', 'url(' + imgSrc + ') no-repeat center bottom')
                            .css('background-size', '100%');
                    });
                };
            })(jQuery, window, document);

            jQuery('.rift').rift();
        </script>
        <?php
        $data = ob_get_clean();

        return $data;
    }


    function wpdm_embed_tree(){

        if (wpdm_query_var('task', 'txt') != 'wpdm_tree') return;
        global $wpdb;

        $sc_params = \WPDM\libs\Crypt::decrypt(wpdm_query_var('params'), true);

        if(!is_array($sc_params)) $sc_params = array();

        $tparams = array( 'hide_empty' => false);
        $tparams['orderby'] =  isset($sc_params['cat_orderby']) && $sc_params['cat_orderby'] !== '' ? $sc_params['cat_orderby'] : 'name';
        $tparams['order'] =  isset($sc_params['cat_order']) && $sc_params['cat_order'] !== '' ? $sc_params['cat_order'] : 'desc';
        $cats = get_terms( 'wpdmcategory', $tparams );


        $newwin = isset($sc_params['newwin']) && (int)$sc_params['newwin'] === 1 ? 'target=_blank' : '';

        echo "<ul class='jqueryFileTree' style='display: none;'>";
        // All Cats
        $scat = wpdm_query_var('dir') == '/' ? '' : wpdm_query_var('dir', 'txt');
        $hide_all = (int)WPDM()->setting->get('_wpdm_hide_all', 0);
        foreach ($cats as $id => $cat) {

            // Only categories with explicit role access will be shown
             //if( ! wpdm_user_has_access( $cat->term_id ,'') ) continue;
            if(!\WPDM\libs\CategoryHandler::userHasAccess($cat->term_id) && $hide_all === 1) continue;

            if ($cat->parent == intval($scat))
                echo "<li class='directory collapsed'><a href='#' rel='" . $cat->term_id . "'>" . $cat->name . "</a></li>";
        }

        // All files
        $params = array(
            'post_type' => 'wpdmpro',
            'posts_per_page' => 9999
        );

        if($scat == ''){
            $params['tax_query'] = array(
                array(
                    'taxonomy' => 'wpdmcategory',
                    'field' => 'term_id',
                    'terms' => get_terms( 'wpdmcategory', array( 'fields' => 'ids'  ) ),
                    'operator' => 'NOT IN',
                )
            );
        } else {
            $params['tax_query'] = array(
                array(
                    'taxonomy' => 'wpdmcategory',
                    'field' => 'term_id',
                    'terms' => $scat,
                    'include_children' => false
                )
            );
        }

        $params['orderby'] = isset($sc_params['orderby']) && $sc_params['orderby'] !== '' ? $sc_params['orderby'] : 'date';
        $params['order'] = isset($sc_params['order']) && $sc_params['order'] !== '' ? $sc_params['order'] : 'desc';
        $download_link = isset($sc_params['download_link']) ? (int)$sc_params['download_link'] : 0;
        $packs = new WP_Query($params);

        while ($packs->have_posts()) {
            $packs->the_post();

            $files = maybe_unserialize(get_post_meta(get_the_ID(), '__wpdm_files', true));

            if ( is_array($files) && count($files) == 1 ) {
                $tfiles = $files;
                $file = array_shift($tfiles);
                $ext = explode(".", $file);
                $ext = end($ext);
            }

            if (!is_array($files) || count($files) == 0) {
                $ext = '_blank';
            }

            if ( is_array($files) && count($files) > 1 ) $ext = 'zip';

            $icon = \WPDM\Package::icon(get_the_ID());

            if(wpdm_user_has_access(get_the_ID())) {
                if ($download_link === 0 || wpdm_is_locked(get_the_ID()))
                    echo "<li  class='file ext_$ext' style='background: url($icon) left center no-repeat;background-size: 16px;'><a {$newwin} href='" . get_permalink(get_the_ID()) . "' rel='" . get_permalink(get_the_ID()) . "'>" . get_the_title() . "</a></li>";
                else
                    echo "<li  class='file ext_$ext' style='background: url($icon) left center no-repeat;background-size: 16px;'><a {$newwin} href='" . wpdm_download_url(get_the_ID()) . "' rel='" . wpdm_download_url(get_the_ID()) . "'>" . get_the_title() . "</a></li>";
            }
        }
        echo "</ul>";

        die();
    }

    function wpdm_popup_link_tag($vars){
        $vars['popup_link'] = "<a class='wpdm-popup-link' data-title='".$vars['title']."'  data-toggle='modal' data-target='#wpdm-popup-link' href='" . get_permalink($vars['ID']) . "'>" . $vars['title'] . "</a>";
        return $vars;
    }

    function wpdm_popup_link(){
        if(is_singular('wpdmpro') || wpdm_query_var('embed')  !== '') return;
        ?>
        <div class="w3eden">
            <div id="wpdm-popup-link" class="modal fade">
                <div class="modal-dialog" style="width: 750px">
                    <div class="modal-content">
                        <div class="modal-header">
                              <h4 class="modal-title"></h4>
                        </div>
                        <div class="modal-body" id='wpdm-modal-body'>
                            <p class="wpdm-placeholder">
                                [ Placeholder content for popup link ]
                                <a href="https://www.wpdownloadmanager.com/">WordPress Download Manager - Best Download Management Plugin</a>
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->


        </div>
        <script language="JavaScript">
            <!--
            jQuery(function () {
                jQuery('.wpdm-popup-link').click(function (e) {
                    e.preventDefault();
                    jQuery('#wpdm-popup-link .modal-title').html(jQuery(this).data('title'));
                    jQuery('#wpdm-modal-body').html('<i class="icon"><img align="left" style="margin-top: -1px" src="<?php echo plugins_url('/download-manager/assets/images/loading-new.gif'); ?>" /></i>&nbsp;Please Wait...');
                    jQuery('#wpdm-popup-link').modal('show');
                    jQuery.post(this.href,{mode:'popup'}, function (res) {
                        jQuery('#wpdm-modal-body').html(res);
                    });
                    return false;
                });
            });
            //-->
        </script>
        <style type="text/css">
            #wpdm-modal-body img {
                max-width: 100% !important;
            }
            .wpdm-placeholder{
                display: none;
            }
        </style>
    <?php
    }

    function wpdm_popup_data(){
        if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'popup'){
            global $post;
            $template = get_post_meta($post->ID,'__wpdm_page_template', true);
            echo FetchTemplate($template, $post->ID, 'page');
            die();
        }
    }

    function wpdm_extsc_generate(){
        ?>
     <div class="panel panel-default">
         <div class="panel-heading">Tree View</div>
         <div class="panel-body">
             <?php //wpdm_dropdown_categories('c',0, 'scc'); ?>
             <?php
                 wp_dropdown_categories(
                     array(
                         'show_option_none' => __('Select category', 'wpdmpro'),
                         'show_count' => 0,
                         'orderby' => 'name',
                         'echo' => 1,
                         'class' => 'form-control selectpicker',
                         'taxonomy' => 'wpdmcategory',
                         'hide_empty' => 0,
                         'name' => 'c',
                         'id' => 'scc' ,
                         'selected' => 0,
                         'value_field' => 'slug',
                     )
                 );
             ?>

             <label><input type="checkbox" id="tvddl"> Direct Download Link</label>

             <button class="btn btn-primary btn-sm" id="tvw">Insert to Post</button>
             <script>
                 jQuery('#tvw').click(function(){

                     var cats = jQuery('#scc').val()!='-1'?' category="' + jQuery('#scc').val() + '" ':'';
                     var tvddl = jQuery('#tvddl').prop('checked')?' download_link=1':'';
                     var win = window.dialogArguments || opener || parent || top;
                     win.send_to_editor('[wpdm_tree' + cats + tvddl + ']');
                     tinyMCEPopup.close();
                     return false;
                 });
             </script>

         </div>
         <div class="panel-heading">Carousel</div>
         <div class="panel-body">
             <?php wpdm_dropdown_categories('c',0, 'scc1'); ?>  <button class="btn btn-primary btn-sm" id="crs">Insert to Post</button>
             <script>
                 jQuery('#crs').click(function(){
                     if(jQuery('#pids').val()=='-1'){
                         alert("Select Category!");
                         return false;
                     }
                     cats = jQuery('#scc1').val()!='-1'?' category="' + jQuery('#scc1').val() + '" ':'';
                     var win = window.dialogArguments || opener || parent || top;
                     win.send_to_editor('[wpdm_carousel' + cats + ']');
                     tinyMCEPopup.close();
                     return false;
                 });
             </script>
         </div>
         <div class="panel-heading">Slider</div>
         <div class="panel-body">
             <input type="text" id="pids" placeholder="Package IDs separated by comma" style="width: 250px;display: inline" class="form-control input-sm" value="" />
             <button class="btn btn-primary btn-sm" id="sld">Insert to Post</button>
             <script>
                 jQuery('#sld').click(function(){
                     if(jQuery('#pids').val()==''){
                         alert("Enter package ids separate by comma!");
                         return false;
                     }
                     var win = window.dialogArguments || opener || parent || top;
                     win.send_to_editor('[wpdm_slider ids="'+jQuery('#pids').val()+'"]');
                     tinyMCEPopup.close();
                     return false;
                 });
             </script>
         </div>
     </div>
    <?php
    }



    add_action('init', 'wpdm_embed_tree');
    add_action('wpdm_ext_shortcode', 'wpdm_extsc_generate');

    add_shortcode('wpdm_tree', 'wpdm_tree');
    add_shortcode('wpdm_slider', 'wpdm_slider');
    add_shortcode('wpdm_carousel', 'wpdm_carousel');
    add_filter('wdm_before_fetch_template', 'wpdm_popup_link_tag');

    add_filter('wp_footer', 'wpdm_popup_link');
    add_action("wp", "wpdm_popup_data");

}
