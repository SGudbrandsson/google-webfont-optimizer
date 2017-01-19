<?php
/**
 * Plugin Name: Google Webfont Optimizer
 * Plugin URI: http://quickfalcon.com/
 * Description: This plugin optimizes the way Google Fonts loads on your webpage, increasing your website's performance.
 * Version: 0.2.4
 * Author: Sigurdur Gudbrandsson
 * Author URI: http://quickfalcon.com/
 * License: GPL2
 */

/*  Copyright 2015 Quick Falcon  (email : siggy@quickfalcon.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

// Require the SCB framework
require dirname( __FILE__ ) . '/scb/load.php';

class GWFO {
    const version = '0.2.4';
    protected static $_instance;

    // Class Construct
    function __construct() {
        $options = self::_get_options();

        if ($options->get( 'gwfo_enabled' ) == 'enabled') {
            // Hook the observer
            add_action('template_redirect', array( $this, 'googlefonts_start_buffering' ),11);
            add_action('shutdown', array( $this, 'googlefonts_ob_end_flush' ),11);
        }
    }

    static function singleton() {
        if ( ! isset( self::$_instance ) ) {
            $className = __CLASS__;
            self::$_instance = new $className;
        }
        return self::$_instance;
    }

    // Find the included Google fonts
    function googlefonts_find_google_fonts($content) {
        # Initialize the fonts array
        $returnFonts = array();
        $fontLinks = array();

        # Make sure to use DOMDocument to process the HTML, regex can break easily
        $dom = new DOMDocument();
        @$dom->loadHTML($content);

        # Find all <link> elements
        $linkNodes = $dom->getElementsByTagName('link');
        foreach ($linkNodes as $linkNode) {
            # Find all the Stylesheets
            if ($linkNode->attributes->getNamedItem("rel")->nodeValue == "stylesheet"
                && $linkNode->attributes->getNamedItem("type")->nodeValue == "text/css"
                && substr_count($linkNode->attributes->getNamedItem("href")->nodeValue, "fonts.googleapis.com/css") > 0) {
                    $fontLinks[] = $linkNode->attributes->getNamedItem("href")->nodeValue;
                }
        }

        # Process the font links
        if (count($fontLinks) > 0) {
            foreach ($fontLinks as $fontLink) {
                $returnFonts['links'][] = $fontLink;

                parse_str(parse_url($fontLink, PHP_URL_QUERY), $urlParameters);

                if (isset($urlParameters['text'])) {
                    # Fonts with character limitations will be seperated into "other"
                    $fontFamily = explode(':', $urlParameters['family']);
                    $returnFonts['other']['name'][] = $fontFamily[0];
                    $returnFonts['other']['url'][] = $fontLink;
                } else {
                    foreach (explode('|', $urlParameters['family']) as $fontFamilies) {
                        $fontFamily = explode(':', $fontFamilies);

                        if (isset($urlParameters['subset'])) {
                            # Use the subset parameter for a subset
                            $subset = $urlParameters['subset'];
                        } else {
                            if (isset($fontFamily[2])) {
                                # Use the subset in the family string
                                $subset = $fontFamily[2];
                            } else {
                                # Use a default subset
                                $subset = "latin";
                            }
                        }

                        if (strlen($fontFamily[0]) > 0 && strlen($fontFamily[1]) > 0)
                            $returnFonts['google'][] = $fontFamily[0] . ":" . $fontFamily[1] . ":" . $subset;
                    }
                }
/*
                # Check if the "other" Google fonts are already included - no need to download the same font twice
                $subsets = array('Cyrillic',
                    'Cyrillic Extended',
                    'Devanagari',
                    'Greek',
                    'Greek Extended',
                    'Khmer',
                    'Latin',
                    'Latin Extended',
                    'Viatnamese');
                $fontSizes = array('Thin',
                    'Extra-Light',
                    'Light',
                    'Normal',
                    'Medium',
                    'Semi-Bold',
                    'Bold',
                    'Extra-Bold',
                    'Ultra-Bold',
                    'Italic',
                    'Regular',
                    'BoldItalic',
                    'i',
                    'b',
                    'bi',
                    '100',
                    '200',
                    '300',
                    '400',
                    '500',
                    '600',
                    '700',
                    '800',
                    '900',
                    '100italic',
                    '200italic',
                    '300italic',
                    '400italic',
                    '500italic',
                    '600italic',
                    '700italic',
                    '800italic',
                    '900italic',
                    '100i',
                    '200i',
                    '300i',
                    '400i',
                    '500i',
                    '600i',
                    '700i',
                    '800i',
                    '900i');
                $otherCount = count($returnFonts['other']['url']);
                foreach (explode(':', $returnFonts['google']) as $fontFamily) {
                    for ($i = 0; $i < $otherCount; $i++) {
                        parse_str(parse_url($returnFonts['other']['url'][$i], PHP_URL_QUERY), $urlParameters);
                        $otherFontFamily = explode(':', $urlParameters['family']);
                        switch (count($otherFontFamily)) {
                        case 1:


                            if ($otherFontFamily[0] == $fontFamily[0] &&
                                ($otherFontFamily[1])
                        }
                    }
                }*/
            }
            return $returnFonts;
        } else {
            return false;
        }
    }

    // Create the Web Font script
    function googlefonts_create_web_font_script($fontList) {
        $options = self::_get_options();

        # Get custom fonts
        if ( $options->get( 'custom_font_names' ) && $options->get( 'custom_font_urls' ) ) {
            if ( array_key_exists( $fontList['other'] ) && array_key_exists( $fontList['other']['name'] ) && is_array( $fontList['other']['name'] ) ) {
                $fontList['other']['name'] = array_merge( $fontList['other']['name'], explode( ',', $options->get( 'custom_font_names' ) ) );
            } else {
                $fontList['other']['name'] = explode( ',', $options->get( 'custom_font_names' ) );
            }

            if ( array_key_exists( $fontList['other'] ) && array_key_exists( $fontList['other']['url'] ) && is_array( $fontList['other']['url'] ) ) {
                $fontList['other']['url'] = array_merge( $fontList['other']['url'], explode( ',', $options->get( 'custom_font_urls' ) ) );
            } else {
                $fontList['other']['url'] = explode( ',', $options->get( 'custom_font_urls' ) );
            }
        }


        if ( $options->get( 'import_type' ) == 'html_link' ) {
            # Create the Google family fonts
            $googleFamilies = implode("|", $fontList['google']);

            $script = '<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=' . urlencode($googleFamilies) . '">';

            if (is_array($fontList['other']['url'])) {
                foreach ($fontList['other']['url'] as $otherfontlink) {
                    $script .= '<link rel="stylesheet" href="' . $otherfontlink . '">';
                }
            }
        } else {

            $googleFamilies = "'" . implode("', '", $fontList['google']) . "'";

            # Check the "other"
            if (isset($fontList['other'])) {
                $other = ",
                    custom: { families: [ '" . implode("', '", $fontList['other']['name']) . "' ],
                    urls: [ '" . implode("', '", $fontList['other']['url']) . "' ] }
                    ";
            } else {
                $other = "";
            }

            $script = "<script type=\"text/javascript\">
                WebFontConfig = {
                    google: { families: [ " . $googleFamilies . " ] }" . $other . "
        };
        (function() {
            var wf = document.createElement('script');
            wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
                '://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
            wf.type = 'text/javascript';
            wf.async = 'true';
            var s = document.getElementsByTagName('script')[0];
            s.parentNode.insertBefore(wf, s);
        })(); </script>";
        }

        return $script;
    }

    // Modify the website contents
    function googlefonts_modify_contents($content, $fontScript, $fontLinks) {
        # Remove the stylesheet links
        $modifiedContent = $content;
        foreach ($fontLinks as $fontLink) {
            $fontLink = preg_quote($fontLink, "/");

            # Minor fix for DOMDocument "fixes"
            $fontLink = str_ireplace("&#038;", "&", $fontLink);
            $fontLink = str_ireplace("&", "(&|&#038;|&amp;)", $fontLink);

            $pattern = "/<link[^>]*" . $fontLink . "[^>]*>/";
            $modifiedContent = preg_replace($pattern, "", $modifiedContent);
        }

        # Add the font script to the bottom of <head>
        # Expect this to break when someone uses </head> in HTML comments ...
        $modifiedContent = str_ireplace("<head>", "<head>" . $fontScript, $modifiedContent);

        # Return the modified HTML
        return $modifiedContent;
    }

    // Process the buffer and return the HTML
    function googlefonts_end_buffering($content) {
        # Make sure to only process html or xhtml
        if ( stripos($content,"<html") === false || stripos($content,"<xsl:stylesheet") !== false ) { return $content; }

    # Find the Google Fonts $fontList = 
    $fontList = self::googlefonts_find_google_fonts($content);

    # If there are no Google Fonts, return the original content
    if ($fontList === false) {
        return $content;
    }

    # Create the Web Font script
    $fontScript = self::googlefonts_create_web_font_script($fontList);

    # Modify the content
    $modifiedContent = self::googlefonts_modify_contents($content, $fontScript, $fontList['links']);

    return $modifiedContent;
    }

    // Default options
    protected static function _get_options() {
        return new scbOptions( 'google_webfont_optimizer_options', __FILE__, array(
            'gwfo_enabled'      => 'enabled',
            'import_type'       => 'webfont_script',
            'custom_font_names' => NULL,
            'custom_font_urls'  => NULL,
        ) );
    }

    static function options_init() {

        $options = self::_get_options();

        // Creating settings page objects
        if ( is_admin() ) {
            require_once( dirname( __FILE__ ) . '/adminpage.php' );
            new GWFO_Admin_Page( __FILE__, $options );
        }
    }


    // Check if we should use the observer
    function gwfo_can_ob() {
        /**
         * Skip if admin
         */
        if (defined('WP_ADMIN')) {
            return false;
        }

        /**
         * Skip if doing AJAX
         */
        if (defined('DOING_AJAX')) {
            return false;
        }

        /**
         * Skip if doing cron
         */
        if (defined('DOING_CRON')) {
            return false;
        }

        /**
         * Skip if APP request
         */
        if (defined('APP_REQUEST')) {
            return false;
        }

        /**
         * Skip if XMLRPC request
         */
        if (defined('XMLRPC_REQUEST')) {
            return false;
        }

        /**
         * Check for WPMU's and WP's 3.0 short init
         */
        if (defined('SHORTINIT') && SHORTINIT) {
            return false;
        }

        /**
         * Check User Agent
         */
        if (isset($_SERVER['HTTP_USER_AGENT']) && stristr($_SERVER['HTTP_USER_AGENT'], W3TC_POWERED_BY) !== false) {
            return false;
        }

        /**
         * Check for Disqus actions
         */
        if (isset($_GET['cf_action']) && !empty($_GET['cf_action'])) {
            return false;
        }

        /**
         * Check if we're displaying feed
         */
        if (is_feed()) {
            return false;
        }

        return true;
    }

    // The observer function
    function googlefonts_start_buffering() {
        if (self::gwfo_can_ob())
            ob_start(array( $this, 'googlefonts_end_buffering' ));
    }

    // The observer flush
    function googlefonts_ob_end_flush() {
        if (self::gwfo_can_ob())
            ob_end_flush();
    }

}

add_action( 'wp', array( 'GWFO', 'singleton'), 10, 0 );

scb_init( array( 'GWFO', 'options_init' ) );
