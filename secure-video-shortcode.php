<?php
/**
 * Plugin Name: Secure Video Shortcode
 * Plugin URI:  https://contactcustody.kcdev.site
 * Description: Provides a [secure_video] shortcode to embed self-hosted videos with expiring URLs for security.
 * Version:     1.0
 * Author:      Your Name
 * Author URI:  https://contactcustody.kcdev.site
 * License:     GPLv2 or later
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Main function for the [secure_video] shortcode.
 *
 * Usage example in a WP page/post:
 *   [secure_video file="myvideo.mp4" width="800" height="450"]
 */
function svs_secure_video_shortcode( $atts ) {

    // 1. Parse shortcode attributes
    $atts = shortcode_atts( array(
        'file'   => '',         // e.g. 'myvideo.mp4'
        'width'  => '640',
        'height' => '360',
    ), $atts, 'secure_video' );

    // 2. Ensure a file is provided
    if ( empty( $atts['file'] ) ) {
        return '<p style="color:red;">No file specified for secure video.</p>';
    }

    /**
     * 3. Your SECRET KEY
     *    - Must match your server's validation code.
     *    - Change "mySuperSecretKey123" to a more secure string.
     */
    $secret_key = 'mySuperSecretKey123';

    /**
     * 4. The PROTECTED BASE URL
     *    - Where your videos are physically hosted & protected.
     *    - e.g. "https://contactcustody.kcdev.site/ppv/"
     */
    $protected_base_url = 'https://contactcustody.kcdev.site/ppv/';

    /**
     * 5. Set an expiry time for the URL (example: 1 hour = 3600 seconds)
     */
    $expires = time() + 3600;

    // 6. Build the file name & compute the token (hash)
    $file = trim( $atts['file'] );
    $token = md5( $secret_key . $file . $expires );

    // 7. Construct the final expiring URL
    //    e.g. "https://contactcustody.kcdev.site/ppv/myvideo.mp4?st=<hash>&e=<timestamp>"
    $video_url = $protected_base_url . $file . '?st=' . $token . '&e=' . $expires;

    // 8. Create the HTML5 <video> tag
    $html  = '<video width="' . esc_attr( $atts['width'] ) . '" height="' . esc_attr( $atts['height'] ) . '" controls>';
    $html .= '  <source src="' . esc_url( $video_url ) . '" type="video/mp4">';
    $html .= '  Your browser does not support the video tag.';
    $html .= '</video>';

    // Return the HTML so it appears in the post/page
    return $html;
}

// Register the [secure_video] shortcode
add_shortcode( 'secure_video', 'svs_secure_video_shortcode' );