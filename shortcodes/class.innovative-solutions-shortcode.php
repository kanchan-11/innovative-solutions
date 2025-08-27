<?php
if (!class_exists('Innovative_Solutions_Shortcode')) {
    class Innovative_Solutions_Shortcode
    {
        public function __construct()
        {
            add_shortcode('innovative_solutions', array($this, 'add_shortcode'));
        }
        public function add_shortcode($atts)
        {
            unset($atts['service_name'], $atts['subservice']);

            $data = [];

            foreach ($atts as $post_type => $ids) {
                $post_ids = array_map('intval', explode(',', $ids));
                $featured_image =  '';
                
                $posts = get_posts([
                    'post__in' => $post_ids,
                    'orderby' => 'post__in',
                    'posts_per_page' => -1
                ]);
                $data[$post_type] = [];
                foreach ($posts as $post) {
                    if ($post_type === 'webinar') {
                        $featured_image = get_field('acf_webinar_banner_image', $post->ID);
                    } else {
                        $featured_image = get_the_post_thumbnail_url($post->ID, 'full');
                    }
                    $video_url = get_post_meta($post->ID, 'innovative_solutions_video_url', true);
                    $cta_text   = get_post_meta($post->ID, 'innovative_solutions_cta_text', true);
                    $data[$post_type][] = [
                        'id' => $post->ID,
                        'title' => get_the_title($post),
                        'url' => get_permalink($post),
                        'video_url' => esc_url($video_url),
                        'cta_text'   => esc_html($cta_text),
                        'featured_image' => $featured_image
                    ];
                }
            }
            wp_enqueue_script('innovative-solutions-main-js');
            wp_localize_script('innovative-solutions-main-js', 'wpData', $data);
            ob_start();
            require(INNOVATIVE_SOLUTIONS_PATH . 'views/innovative-solutions_shortcode.php');
            wp_enqueue_style('innovative-solutions-main-css');
            return ob_get_clean();
        }
    }
}
