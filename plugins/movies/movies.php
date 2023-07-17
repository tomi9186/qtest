<?php

/*
Plugin Name: Movies
Plugin URI: https://efortis.net
Description: 
Version: 1.0.0
Author: Tomislav Šuk
Author URI: https://efortis.net
Text Domain: q-movies
*/

class MoviesPlugin {

    public function __construct() {
        add_action('init', array($this, 'register_post_type'));
        add_action('add_meta_boxes', array($this, 'add_meta_box'));
        add_action('save_post_movies', array($this, 'save_meta_box_data'));
        add_action('enqueue_block_editor_assets', array($this, 'register_block'));
    }

    public function register_post_type() {
        $labels = array(
            'name' => 'Movies',
            'singular_name' => 'Movie',
            'menu_name' => 'Movies',
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'has_archive' => true,
            'menu_icon' => 'dashicons-video-alt2',
            'show_in_rest' => true,
            'supports' => array('title', 'editor', 'thumbnail', 'editor'),
        );

        register_post_type('movies', $args);
    }

    public function add_meta_box() {
        add_meta_box(
            'movie_title',
            'Movie Title',
            array($this, 'render_meta_box'),
            'movies',
            'normal',
            'default'
        );
    }

    public function render_meta_box($post) {
        $movie_title = get_post_meta($post->ID, 'movie_title', true);
        ?>
        <label for="movie_title_field">Movie Title:</label>
        <input type="text" id="movie_title_field" name="movie_title_field" value="<?php echo esc_attr($movie_title); ?>" style="width: 100%;">
        <?php
    }

    public function save_meta_box_data($post_id) {
        if (isset($_POST['movie_title_field'])) {
            $movie_title = sanitize_text_field($_POST['movie_title_field']);
            update_post_meta($post_id, 'movie_title', $movie_title);
        }
    }

    public function register_block() {
        wp_register_script(
            'movies-block-script',
            plugins_url('blocks/favorite-movie-quote.js', __FILE__),
            array('wp-blocks', 'wp-element', 'wp-editor')
        );

        register_block_type('movies/favorite-quote', array(
            'editor_script' => 'movies-block-script',
            'render_callback' => array($this, 'render_block'),
        ));
    }

    public function render_block($attributes) {
        $quote = isset($attributes['quote']) ? $attributes['quote'] : '';

        ob_start();
        ?>
        <p class="movie-quote"><?php echo esc_html($quote); ?></p>
        <?php
        return ob_get_clean();
    }
}

new MoviesPlugin();