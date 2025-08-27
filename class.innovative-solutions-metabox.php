<?php

 if(!class_exists('Innovative_Solutions_Post_Metabox')){
    class Innovative_Solutions_Post_Metabox{
        function __construct(){
            add_action('add_meta_boxes',array($this,'add_meta_boxes'));
            add_action('save_post',array($this,'save_post'),10,2);
        }
    
            public function add_meta_boxes() {
                    add_meta_box(
                        'innovative-solutions_meta_box',
                        esc_html__('Innovative Solutions Content Fields','innovative-solutions'),
                        array($this,'add_inner_meta_boxes'),
                        'post',
                        'normal',
                        'high'
                    );
            }
            
            public function add_inner_meta_boxes($post) {
                require_once(INNOVATIVE_SOLUTIONS_PATH.'views/innovative-solutions_metabox.php');
            }
            public function save_post($post_id) {
                
                if(!isset($_POST['innovative_solutions_nonce']) || !wp_verify_nonce($_POST['innovative_solutions_nonce'],'innovative_solutions_nonce')){
                    return;
                }
                if(defined ('DOING_AUTOSAVE') && DOING_AUTOSAVE){
                    return;
                }
                if(!current_user_can('edit_post',$post_id)){
                    return;
                }
                
                if (isset($_POST['action']) && $_POST['action'] == 'editpost') {
                    
                    $cta_text = isset($_POST['innovative_solutions_cta_text']) ? sanitize_text_field($_POST['innovative_solutions_cta_text']) : '';
                    $video_url = isset($_POST['innovative_solutions_video_url']) ? esc_url_raw($_POST['innovative_solutions_video_url']) : '';

                    update_post_meta($post_id, 'innovative_solutions_cta_text', $cta_text);
                    update_post_meta($post_id, 'innovative_solutions_video_url', $video_url);
                }
            }
        }
    }
