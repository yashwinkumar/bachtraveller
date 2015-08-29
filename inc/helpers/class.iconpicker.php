<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 07/05/2015
 * Time: 1:58 CH
 */

if(!class_exists('ST_Icon_Picker'))
{
    class ST_Icon_Picker
    {
        static function _init()
        {
            add_action('admin_enqueue_scripts',array(__CLASS__,'_add_script'));
        }

        static function _add_script()
        {
            $packages=array(
                'fontawesome' => array(
                    'icon_list' => array(
                        'fa-glass','fa-music','fa-search','fa-envelope-o','fa-heart','fa-star','fa-star-o','fa-user','fa-film','fa-th-large','fa-th','fa-th-list','fa-check','fa-times','fa-search-plus','fa-search-minus','fa-power-off','fa-signal','fa-gear','fa-cog','fa-trash-o','fa-home','fa-file-o','fa-clock-o','fa-road','fa-download','fa-arrow-circle-o-down','fa-arrow-circle-o-up','fa-inbox','fa-play-circle-o','fa-rotate-right','fa-refresh','fa-list-alt','fa-lock','fa-flag','fa-headphones','fa-volume-off','fa-volume-down','fa-volume-up','fa-qrcode','fa-barcode','fa-tag','fa-tags','fa-book','fa-bookmark','fa-print','fa-camera','fa-font','fa-bold','fa-italic','fa-text-height','fa-text-width','fa-align-left','fa-align-center','fa-align-right','fa-align-justify','fa-list','fa-dedent','fa-indent','fa-video-camera','fa-picture-o','fa-pencil','fa-map-marker','fa-adjust','fa-tint','fa-edit','fa-share-square-o','fa-check-square-o','fa-move','fa-step-backward','fa-fast-backward','fa-backward','fa-play','fa-pause','fa-stop','fa-forward','fa-fast-forward','fa-step-forward','fa-eject','fa-chevron-left','fa-chevron-right','fa-plus-circle','fa-minus-circle','fa-times-circle','fa-check-circle','fa-question-circle','fa-info-circle','fa-crosshairs','fa-times-circle-o','fa-check-circle-o','fa-ban','fa-arrow-left','fa-arrow-right','fa-arrow-up','fa-arrow-down','fa-share','fa-plus','fa-minus','fa-asterisk','fa-exclamation-circle','fa-gift','fa-leaf','fa-fire','fa-eye','fa-eye-slash','fa-warning','fa-plane','fa-calendar','fa-random','fa-comment','fa-magnet','fa-chevron-up','fa-chevron-down','fa-retweet','fa-shopping-cart','fa-folder','fa-folder-open','fa-bar-chart-o','fa-twitter-square','fa-facebook-square','fa-camera-retro','fa-key','fa-cogs','fa-comments','fa-thumbs-o-up','fa-thumbs-o-down','fa-star-half','fa-heart-o','fa-sign-out','fa-linkedin-square','fa-thumb-tack','fa-external-link','fa-sign-in','fa-trophy','fa-github-square','fa-upload','fa-lemon-o','fa-phone','fa-square-o','fa-bookmark-o','fa-phone-square','fa-twitter','fa-facebook','fa-github','fa-unlock','fa-credit-card','fa-rss','fa-hdd-o','fa-bullhorn','fa-bell','fa-certificate','fa-hand-o-right','fa-hand-o-left','fa-hand-o-up','fa-hand-o-down','fa-arrow-circle-left','fa-arrow-circle-right','fa-arrow-circle-up','fa-arrow-circle-down','fa-globe','fa-wrench','fa-tasks','fa-filter','fa-briefcase','fa-group','fa-chain','fa-link','fa-cloud','fa-flask','fa-cut','fa-copy','fa-paperclip','fa-save','fa-floppy-o','fa-square','fa-reorder','fa-list-ul','fa-list-ol','fa-strikethrough','fa-underline','fa-table','fa-magic','fa-truck','fa-pinterest','fa-pinterest-square','fa-google-plus-square','fa-google-plus','fa-money','fa-caret-down','fa-caret-up','fa-caret-left','fa-caret-right','fa-columns','fa-sort','fa-envelope','fa-linkedin','fa-rotate-left','fa-legal','fa-dashboard','fa-comment-o','fa-comments-o','fa-flash','fa-sitemap','fa-umbrella','fa-paste','fa-lightbulb-o','fa-exchange','fa-cloud-download','fa-cloud-upload','fa-user-md','fa-stethoscope','fa-suitcase','fa-bell-o','fa-coffee','fa-cutlery','fa-file-text-o','fa-building','fa-ambulance','fa-medkit','fa-fighter-jet','fa-beer','fa-h-square','fa-plus-square','fa-angle-double-left','fa-angle-double-right','fa-angle-double-up','fa-angle-double-down','fa-angle-left','fa-angle-right','fa-angle-up','fa-angle-down','fa-desktop','fa-laptop','fa-tablet','fa-mobile','fa-circle-o','fa-quote-left','fa-quote-right','fa-spinner','fa-circle','fa-reply','fa-github-alt','fa-folder-o','fa-folder-open-o','fa-smile-o','fa-frown-o','fa-meh-o','fa-gamepad','fa-keyboard-o','fa-flag-o','fa-flag-checkered','fa-terminal','fa-code','fa-reply-all','fa-star-half-empty','fa-location-arrow','fa-crop','fa-code-fork','fa-unlink','fa-question','fa-info','fa-exclamation','fa-superscript','fa-subscript','fa-eraser','fa-puzzle-piece','fa-microphone','fa-microphone-slash','fa-shield','fa-calendar-o','fa-fire-extinguisher','fa-rocket','fa-maxcdn','fa-chevron-circle-left','fa-chevron-circle-right','fa-chevron-circle-up','fa-chevron-circle-down','fa-html5','fa-css3','fa-anchor','fa-bullseye','fa-rss-square','fa-play-circle','fa-ticket','fa-minus-square','fa-minus-square-o','fa-level-up','fa-level-down','fa-check-square','fa-pencil-square','fa-external-link-square','fa-share-square','fa-compass','fa-caret-square-o-down','fa-toggle-up','fa-toggle-right','fa-eur','fa-gbp','fa-usd','fa-rupee','fa-yen','fa-ruble','fa-won','fa-bitcoin','fa-file','fa-file-text','fa-sort-alpha-asc','fa-sort-alpha-desc','fa-sort-amount-asc','fa-sort-amount-desc','fa-sort-numeric-asc','fa-sort-numeric-desc','fa-thumbs-up','fa-thumbs-down','fa-youtube-square','fa-youtube','fa-xing','fa-xing-square','fa-youtube-play','fa-dropbox','fa-stack-overflow','fa-instagram','fa-flickr','fa-adn','fa-bitbucket','fa-bitbucket-square','fa-tumblr','fa-tumblr-square','fa-long-arrow-down','fa-long-arrow-up','fa-long-arrow-left','fa-long-arrow-right','fa-apple','fa-windows','fa-android','fa-linux','fa-dribbble','fa-skype','fa-foursquare','fa-trello','fa-female','fa-male','fa-gittip','fa-sun-o','fa-moon-o','fa-archive','fa-bug','fa-vk','fa-weibo','fa-renren','fa-pagelines','fa-stack-exchange','fa-arrow-circle-o-right','fa-arrow-circle-o-left','fa-toggle-left','fa-caret-square-o-left','fa-dot-circle-o','fa-wheelchair','fa-vimeo-square','fa-turkish-lira','fa-try'
                    ),
                    'path_folder' => '',
                    'link_file_css' => get_template_directory_uri().'/css/font-awesome.css',
                ),
                'icomoon' => array(
                    'icon_list' => array(
                        'im-climate-control',
                        'im-dog',
                        'im-elder',
                        'im-smoking',
                        'im-shift-auto',
                        'im-lock',
                        'im-wheel-chair',
                        'im-casino',
                        'im-diesel',
                        'im-car-doors',
                        'im-patio',
                        'im-satellite',
                        'im-parking',
                        'im-air',
                        'im-bathtub',
                        'im-soundproof',
                        'im-meet',
                        'im-width',
                        'im-shift',
                        'im-bed',
                        'im-car-window',
                        'im-pool',
                        'im-terrace',
                        'im-plane',
                        'im-spa',
                        'im-fm',
                        'im-children',
                        'im-wi-fi',
                        'im-tv',
                        'im-washing-machine',
                        'im-bar',
                        'im-stereo',
                        'im-electric',
                        'im-car-wheel',
                        'im-business-person',
                        'im-driver',
                        'im-icon_1041',
                        'im-fitness',
                        'im-shower',
                        'im-bus',
                        'im-restaurant',
                        'im-sunrise',
                        'im-sun',
                        'im-moon',
                        'im-sun3',
                        'im-windy',
                        'im-wind',
                        'im-snowflake',
                        'im-cloudy',
                        'im-cloudy-moon',
                        'im-sun-lines',
                        'im-moon-lines',
                        'im-cloud-lines',
                        'im-lines',
                        'im-cloud',
                        'im-cloud-lightning',
                        'im-lightning',
                        'im-rainy',
                        'im-rain',
                        'im-windy-cloud',
                        'im-windy-cloud-rain',
                        'im-snowy',
                        'im-snow-cloud',
                        'im-snow-cloud-2',
                        'im-cloud-2',
                        'im-cloud-lightning-2',
                        'im-lightning-2',
                        'im-sun-fill',
                        'im-moon-fill',
                        'im-cloudy-fill',
                        'im-cloudy-moon-fill',
                        'im-cloud-fill',
                        'im-cloud-lightning-fill',
                        'im-rainy-fill',
                        'im-rain-fill',
                        'im-windy-cloud-fill',
                        'im-windy-cloud-rain-fill',
                        'im-snowy-cloud-fill',
                        'im-snow-cloud-fill-2',
                        'im-cloud-fill-2',
                        'im-cloud-lightning-fill-2',
                        'im-thermometer',
                        'im-compass',
                        'im-none',
                        'im-celsius',
                        'im-fahrenheit',
                    ),
                    'path_folder' => '',
                    'link_file_css' => get_template_directory_uri().'/css/icomoon.css',
                )
            );
            
            // Get font packages from database
            $list_fonts = get_option('st_list_fonticon_', array());

            if(is_array($list_fonts) && count($list_fonts))

                $packages = array_merge($packages, $list_fonts);

            $packages = apply_filters('st_list_icon_packages',$packages);

            $list_icons=array();

            if(!empty($packages))
            {
                foreach($packages as $value)
                {
                    $list_icons=array_merge($list_icons,$value['icon_list']);
                }
            }


            wp_localize_script('jquery','st_icon_picker',array(
                'icon_list'=> $list_icons
            ));


            wp_enqueue_script('iconpicker', get_template_directory_uri().'/js/iconpicker/js/fontawesome-iconpicker.js', array('jquery'), '1.0', true );
            wp_enqueue_script('custom-iconpicker', get_template_directory_uri().'/js/iconpicker/js/custom-iconpicker.js', array('jquery'), null, true );
            wp_enqueue_style('iconpicker-css',get_template_directory_uri().'/js/iconpicker/css/fontawesome-iconpicker.css');

            /* Enqueue all font icon style*/
            foreach($packages as $item => $val){
                wp_enqueue_style($item, $val['link_file_css']);
            }
            
        }
    }

    ST_Icon_Picker::_init();
}