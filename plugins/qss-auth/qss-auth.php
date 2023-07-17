<?php
/*
Plugin Name: QSS Auth
Plugin URI: https://efortis.net
Description:
Version: 1.0.0
Author: Tomislav Šuk
Author URI: https://efortis.net
Text Domain: qss-client
*/

session_start();

class QSS_Client {
    private $api_url;
    private $email;
    private $password;
    private $access_token;
    private $token_expiration;

    public function __construct($email, $password) {
        $this->api_url = 'https://symfony-skeleton.q-tests.com/api/v2/token';
        $this->email = $email;
        $this->password = $password;
    }

    public function login() {
        $data = array(
            'email' => $this->email,
            'password' => $this->password
        );
        $headers = array(
            'Content-Type' => 'application/json',
        );

        $args = array(
            'body' => json_encode($data),
            'headers' => $headers,
            'method' => 'POST',
            'timeout' => 45,
        );

        $response = wp_remote_post($this->api_url, $args);

        if (!is_wp_error($response)) {
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);

            if (isset($data['token_key'])) {
                $this->access_token = $data['token_key'];
                $this->saveToken();
                echo 'Authentication success.';
            } else {
                echo 'Authentication failed.';
            }
        } else {
            $error_message = $response->get_error_message();
            echo 'HTTP API Error: ' . $error_message;
        }
    }

    public function saveToken() {
        $this->token_expiration = time() + (90 * 24 * 60 * 60);
        $_SESSION['access_token'] = $this->access_token;
        $_SESSION['token_expiration'] = $this->token_expiration;
    }

    public static function isTokenValid() {
        if (isset($_SESSION['access_token']) && !empty($_SESSION['access_token']) &&
            isset($_SESSION['token_expiration']) && !empty($_SESSION['token_expiration'])) {
            if ($_SESSION['token_expiration'] > time()) {
                return true;
            }
        }
        return false;
    }
}

class LoginTemplate {
    public function render() {
        ob_start();
        print_r($_SESSION);
        if (!QSS_Client::isTokenValid()) {
            ?>
            <form method="post" style="padding: 25px;">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>
                <br><br>
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
                <br><br>
                <input type="submit" name="login" value="Log In">
                <br><br>
                <a href="#" id="forgot-password">Forgot your password?</a>
                <br><br>
                <label for="username">Username:</label>
                <input type="text" name="username" id="username">
            </form>
            <?php
        } else {
            echo 'Token: ' . $_SESSION['access_token'];
        }

        return ob_get_clean();
    }

    public function handleFormSubmission() {
        if (isset($_POST['login'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $qss_client = new QSS_Client($email, $password);
            $qss_client->login();
        }

        if (isset($_POST['forgot_password'])) {
            $username = $_POST['username'];
        }
    }
}

class Custom_Page_Template_Plugin {
    public function __construct() {
        add_filter('theme_page_templates', array($this, 'register_page_template'));
        add_filter('page_template', array($this, 'load_login_template'));
    }

    public function register_page_template($templates) {
        $templates['login-template.php'] = 'Login Template';
        return $templates;
    }

    public function load_login_template($template) {
        if (is_page_template('login-template.php')) {
            $template = plugin_dir_path(__FILE__) . 'templates/login-template.php';
        }
        return $template;
    }
}

$custom_page_template_plugin = new Custom_Page_Template_Plugin();