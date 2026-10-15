<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Security extends CI_Security {

    /**
     * Override CSRF failure
     */
    public function csrf_show_error()
    {
        // Try to get CI instance safely
        $CI = null;
        if (class_exists('CI_Controller')) {
            $CI =& get_instance();
        }

        // Set flash message only if session library is loaded
        if ($CI && isset($CI->session)) {
            $CI->session->set_flashdata('error', 'Your session expired or the form is invalid. Please try again.');
        }

        // Redirect back safely
        if (!empty($_SERVER['HTTP_REFERER'])) {
            $redirect_url = $_SERVER['HTTP_REFERER'] . 'sris';
            $redirect_url .= (strpos($redirect_url, '?') !== false ? '&' : '?') . 'csrf_redirect=1';
            header('Location: ' . $redirect_url);
        } else {
            header('Location: /');
        }

        exit;
    }
}