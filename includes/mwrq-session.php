<?php
if ( !defined( 'ABSPATH' )) {
    exit; // Exit if accessed directly
}

class Motif_Request_Session extends WC_Session {

    private $_cookie;

    private $_session_expiring;

    private $_session_expiration;

    private $_has_cookie = false;

    public function __construct() {
        $this->_cookie = 'motif_mwrq_session_' . COOKIEHASH;

        if ( $cookie = $this->get_session_cookie() ) {
            $this->_customer_id        = $cookie[0];
            $this->_session_expiration = $cookie[1];
            $this->_session_expiring   = $cookie[2];
            $this->_has_cookie         = true;

            if ( time() > $this->_session_expiring ) {

                $this->set_session_expiration();
                $session_expiry_option = '_motif_mwrq_session_expires_' . $this->_customer_id;

                if ( false === get_option( $session_expiry_option ) ) {
                    add_option( $session_expiry_option, $this->_session_expiration, '', 'no' );
                } else {
                    update_option( $session_expiry_option, $this->_session_expiration );
                }
            }

        } else {
            $this->set_session_expiration();
            $this->_customer_id = $this->generate_customer_id();
        }

        $this->_data = $this->get_session_data();

        add_action( 'woocommerce_cleanup_sessions', array( $this, 'cleanup_sessions' ), 10 );
        add_action( 'shutdown', array( $this, 'save_data' ), 20 );
        add_action( 'clear_auth_cookie', array( $this, 'destroy_session' ) );
        if ( ! is_user_logged_in() ) {
            add_action( 'woocommerce_thankyou', array( $this, 'destroy_session' ) );
        }
    }

    public function set_customer_session_cookie( $set ) {
        if ( $set ) {

            $to_hash           = $this->_customer_id . $this->_session_expiration;
            $cookie_hash       = hash_hmac( 'md5', $to_hash, wp_hash( $to_hash ) );
            $cookie_value      = $this->_customer_id . '||' . $this->_session_expiration . '||' . $this->_session_expiring . '||' . $cookie_hash;
            $this->_has_cookie = true;

            wc_setcookie( $this->_cookie, $cookie_value, $this->_session_expiration, apply_filters( 'motif_mwrq_session_use_secure_cookie', false ) );
        }
    }

    public function has_session() {
        return isset( $_COOKIE[ $this->_cookie ] ) || $this->_has_cookie || is_user_logged_in();
    }

    public function set_session_expiration() {
        $this->_session_expiring    = time() + intval( apply_filters( 'motif_mwrq_session_expiring', 60 * 60 * 47 ) );
        $this->_session_expiration  = time() + intval( apply_filters( 'motif_mwrq_session_expiration', 60 * 60 * 48 ) ); 
    }

    public function generate_customer_id() {
        if ( is_user_logged_in() ) {
            return get_current_user_id();
        } else {
            require_once( ABSPATH . 'wp-includes/class-phpass.php');
            $hasher = new PasswordHash( 8, false );
            return md5( $hasher->get_random_bytes( 32 ) );
        }
    }

    public function get_session_cookie() {
        if ( empty( $_COOKIE[ $this->_cookie ] ) ) {
            return false;
        }
        list( $customer_id, $session_expiration, $session_expiring, $cookie_hash ) = explode( '||', $_COOKIE[ $this->_cookie ] );

        $to_hash = $customer_id . $session_expiration;
        $hash    = hash_hmac( 'md5', $to_hash, wp_hash( $to_hash ) );

        if ( $hash != $cookie_hash ) {
            return false;
        }
        return array( $customer_id, $session_expiration, $session_expiring, $cookie_hash );
    }

    public function get_session_data() {
        return $this->has_session() ? (array) get_option( '_motif_mwrq_session_' . $this->_customer_id, array() ) : array();
    }

    public function save_data() {

        if ( $this->_dirty && $this->has_session() ) {

            $session_option        = '_motif_mwrq_session_' . $this->_customer_id;
            $session_expiry_option = '_motif_mwrq_session_expires_' . $this->_customer_id;
            if ( false === get_option( $session_option ) ) {
                add_option( $session_option, $this->_data, '', 'no' );
                add_option( $session_expiry_option, $this->_session_expiration, '', 'no' );
            } else {
                update_option( $session_option, $this->_data );
            }
        }
    }

    public function destroy_session() {

        wc_setcookie( $this->_cookie, '', time() - YEAR_IN_SECONDS, apply_filters( 'motif_mwrq_session_use_secure_cookie', false ) );

        $session_option        = '_motif_mwrq_session_' . $this->_customer_id;
        $session_expiry_option = '_motif_mwrq_session_expires_' . $this->_customer_id;

        delete_option( $session_option );
        delete_option( $session_expiry_option );

        $this->_data        = array();
        $this->_dirty       = false;
        $this->_customer_id = $this->generate_customer_id();
    }

    public function cleanup_sessions() {
        global $wpdb;

        if ( ! defined( 'WP_SETUP_CONFIG' ) && ! defined( 'WP_INSTALLING' ) ) {
            $now                = time();
            $expired_sessions   = array();
            $wc_session_expires = $wpdb->get_col( "SELECT option_name FROM $wpdb->options WHERE option_name LIKE '\_motif_mwrq\_session\_expires\_%' AND option_value < '$now'" );

            foreach ( $wc_session_expires as $option_name ) {
                $session_id         = substr( $option_name, 20 );
                $expired_sessions[] = $option_name;  // Expires key
                $expired_sessions[] = "_motif_mwrq_session_$session_id"; // Session key
            }

            if ( ! empty( $expired_sessions ) ) {
                $expired_sessions_chunked = array_chunk( $expired_sessions, 100 );
                foreach ( $expired_sessions_chunked as $chunk ) {
                    if ( wp_using_ext_object_cache() ) {

                        foreach ( $chunk as $option ) {
                            wp_cache_delete( $option, 'options' );
                        }
                    }

                    $option_names = implode( "','", $chunk );
                    $wpdb->query( "DELETE FROM $wpdb->options WHERE option_name IN ('$option_names')" );
                }
            }
        }
    }
}