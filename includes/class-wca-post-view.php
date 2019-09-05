<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('WCA_Post_View')) {

    /**
     * The PostType class
     *
     * @since 1.0.0
     */
    class WCA_Post_View {

        /**
         * Main constructor
         *
         * @since 1.0.0
         *
         */
        private $custom_fields = array(
            "_wca_facebook_url" => 'Facebook Url',
            "_wca_linkedin_url" => 'Linkedin Url',
            "_wca_google_url" => 'Google Url');

        public function __construct() {
            // Hook into actions & filters
            $this->hooks();
        }

        /**
         * Hook in to actions & filters
         *
         * @since 1.0.0
         */
        private function hooks() {
            add_filter('the_content', array($this, 'wca_content_filter'), 10);
        }

        public function wca_content_filter($content) {
            global $post;

            if (WCA_CPT_NAME !== $post->post_type || !is_single())
                return $content;

            // Appending custom fields to content 
            $content .= $this->wca_get_custom_fields($post->ID);
            $content .= $this->wca_get_image_gallery($post->ID);



            return $content;
        }

        private function wca_get_custom_fields($post_id) {
            $html = "<table>";
            foreach ($this->custom_fields as $key => $label) {
                $meta = get_post_meta($post_id, $key, true);
                if ($meta) {
                    $html .= '<tr><td><b>' . $label . '</b>:</td>'
                            . '<td><a href="' . $this->addhttp($meta) . '" target="_blank">' . $meta . '</a></td></tr>';
                }
            }
            $html .= "</table>";

            return $html;
        }

        private function wca_get_image_gallery($post_id) {
            $html = '';
            $subHtml = '';

            $js_img = WCA_PLUGIN_URL . 'assets/frontend/';

            $_wca_image_gallery = get_post_meta($post_id, '_wca_image_gallery', true);
            if (!$_wca_image_gallery)
                return $html;

            $html .= '<div class="wca-image-gallery"> <ul id="wca-lightgallery">';

            $attachmentIds = explode(',', $_wca_image_gallery);

            foreach ($attachmentIds as $attachment_id) {
                $attachment = wp_get_attachment_metadata($attachment_id);
                $thumbnail = wp_get_attachment_image_url($attachment_id, 'thumbnail');
                $large = wp_get_attachment_image_url($attachment_id, 'large');
                if (!$thumbnail)
                    $thumbnail = $large;

                $subHtml = isset($attachment['image_meta']['title']) ? '<h4>' . $attachment['image_meta']['title'] . '</h4>' : '';
                $subHtml .= isset($attachment['image_meta']['caption']) ? '<p>' . $attachment['image_meta']['caption'] . '</p>' : '';

                $html .= '<li  data-src="' . $large . '" data-sub-html="' . $subHtml . '">
                            <a href=""> <img class="img-responsive" src="' . $thumbnail . '"> </a></li>';
            }

            $html .= '</ul> </div>';

            return $html;
        }

        private function addhttp($url) {
            if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
                $url = "http://" . $url;
            }
            return $url;
        }

    }

}

new WCA_Post_View();
