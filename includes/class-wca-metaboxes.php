<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('WCA_Metaboxes')) {

    /**
     * The Metaboxes class
     *
     * @since 1.0.0
     */
    class WCA_Metaboxes {

        /**
         * Main constructor
         *
         * @since 1.0.0
         *
         */
        private $main_fields = array(
            "_wca_fname" => ['label' => "First Name", 'type' => 'text'],
            "_wca_lname" => ['label' => "Last Name", 'type' => 'text'],
        );
        private $other_fields = array(
            "_wca_facebook_url" => ['label' => "Facebook URL", 'type' => 'text'],
            "_wca_linkedin_url" => ['label' => "Linkedin URL", 'type' => 'text'],
            "_wca_google_url" => ['label' => "Google+ URL", 'type' => 'text'],
            "_wca_user_id" => ['label' => "Wordpress User", 'type' => 'user-select']
        );

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
            add_action('edit_form_after_title', array($this, 'fname_lname_after_title'));
            add_action('add_meta_boxes', array($this, 'add_extra_fields_meta_box'));
            add_action('save_post', array($this, 'save_metabox'), 10, 2);
            add_filter('wp_insert_post_data', array($this, 'modify_post_title'), '99', 1);
        }

        /**
         * Adds the meta box.
         */
        public function add_extra_fields_meta_box() {
            add_meta_box('extra_fields_meta_box', __('Extra Fields', 'wp-custom-authors'), array($this, 'extra_fields_meta_box'), WCA_CPT_NAME, 'normal', 'default');
            add_meta_box('image_gallery_meta_box', __('Image Gallery', 'wp-custom-authors'), array($this, 'image_gallery_meta_box'), WCA_CPT_NAME, 'side', 'default');
        }

        /**
         * Adds the meta box.
         */
        public function modify_post_title($data) {
            if ($data['post_type'] == WCA_CPT_NAME) {
                $title = $_POST['_wca_fname'] . " " . $_POST['_wca_lname'];
                $data['post_title'] = $title; //Updates the post title
                $data['post_name'] = sanitize_title($title); //Updates the post slug
            }
            return $data; // Returns the modified data.
        }

        /**
         * Renders the meta box.
         */
        public function fname_lname_after_title($post) {
            global $typenow;
            if ($typenow == WCA_CPT_NAME) {
                echo $this->render_fields($this->main_fields, $post->ID);
                //Append Biography Title for Content are
                echo '<b>Biography</b>';
            }
        }

        /**
         * Renders the meta box.
         */
        public function extra_fields_meta_box($post) {
            wp_nonce_field('_wca_nonce_action', '_wca_custom_nonce');
            echo $this->render_fields($this->other_fields, $post->ID);
        }

        /**
         * Renders the Image Gallery meta box.
         */
        public function image_gallery_meta_box($post) {
            wp_nonce_field('_wca_nonce_action', '_wca_custom_nonce');
            $meta_key = '_wca_image_gallery';
            echo $this->image_gallery_uploader_field($meta_key, $post->ID);
        }

        /**
         * Handles saving the meta box.
         *
         * @param int $post_id Post ID.
         * @param WP_Post $post Post object.
         *
         * @return null
         */
        public function save_metabox($post_id, $post) {
            // Add nonce for security and authentication.
            $nonce_name = isset($_POST['_wca_custom_nonce']) ? $_POST['_wca_custom_nonce'] : '';
            $nonce_action = '_wca_nonce_action';

            // Check if nonce is valid.
            if (!wp_verify_nonce($nonce_name, $nonce_action)) {
                return;
            }

            // Check if user has permissions to save data.
            if (!current_user_can('edit_post', $post_id)) {
                return;
            }

            // Check if not an autosave.
            if (wp_is_post_autosave($post_id)) {
                return;
            }

            // Check if not a revision.
            if (wp_is_post_revision($post_id)) {
                return;
            }
            $all_fields = array_keys(array_merge($this->other_fields, $this->main_fields));

            foreach ($all_fields as $keyName) {
                update_post_meta($post_id, $keyName, $_POST[$keyName]);
            }
            if (isset($_POST['_wca_image_gallery'])) {
                update_post_meta($post_id, '_wca_image_gallery', esc_attr($_POST['_wca_image_gallery']));
            } else {
                update_post_meta($post_id, '_wca_image_gallery', '');
            }
        }

        /**
         * Render Fields.
         *
         * @return string
         */
        private function render_fields($arr, $post_id) {
            $html = '<div class="wca-col-12">';
            foreach ($arr as $key => $value) {
                $val = get_post_meta($post_id, $key, true);
                $html .= '<div class="wca-col-6"><div class="wca-inner">'
                        . '<label for="' . $key . '"><b>' . $value['label'] . '</b></label>';
                if ($value['type'] == 'text') {
                    $html .= '<input type="text" class="regular-text" name="' . $key . '" id="' . $key . '" placeholder="' . $value['label'] . '" value="' . $val . '">';
                } elseif ($value['type'] == 'user-select') {
                    $blogusers = get_users(array('fields' => array('display_name')));
                    $html .= '<select class="width100p" name="' . $key . '" id="' . $key . '">
                                <option value="">Select ' . $value['label'] . '</option>';
                    foreach ($blogusers as $user) {
                        $html .= '<option value="' . $user->ID . '">' . $user->display_name . '</option>';
                    }
                    $html .= '</select>';
                }
                $html .= '</div></div>';
            }

            $html .= '</div>';

            return $html;
        }

        /**
         * Render Image Gallery.
         *
         * @return string
         */
        private function image_gallery_uploader_field($meta_key, $post_id) {
            $_wca_image_gallery = get_post_meta($post_id, $meta_key, true);
            $image_ids = explode(',', $_wca_image_gallery);

            $html = '<p><i>Set Images for Image Gallery</i></p>'
                    . '<div class="wca-image-gallery clearfix">';

            foreach ($image_ids as $attachment_id) {
                $img = wp_get_attachment_image_src($attachment_id, 'thumbnail');
                $html .= '<div class="wca-image-thumb"><img src="' . esc_url($img[0]) . '" /></div>';
            }
            $html .= '</div>';

            $html .= '<input id="wca-edit-gallery" class="button wca-upload-gallery" type="button" value="Add/Edit Gallery"/>';
            $html .= '<input id="wca-clear-gallery" class="button wca-upload-gallery" type="button" value="Clear"/>';
            $html .= '<input type="hidden" name="' . $meta_key . '" id="' . $meta_key . '" class="wca-gallery-values" value="' . $_wca_image_gallery . '">';
            return $html;
        }

    }

}
new WCA_Metaboxes();
