<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


if ( ! class_exists( 'AWS_Helpers' ) ) :

    /**
     * Class for plugin help methods
     */
    class AWS_Helpers {

        /*
         * Removes scripts, styles, html tags
         */
        static public function html2txt( $str ) {
            $search = array(
                '@<script[^>]*?>.*?</script>@si',
                '@<[\/\!]*?[^<>]*?>@si',
                '@<style[^>]*?>.*?</style>@siU',
                '@<![\s\S]*?--[ \t\n\r]*>@'
            );
            $str = preg_replace( $search, '', $str );

            $str = esc_attr( $str );
            $str = stripslashes( $str );
            $str = str_replace( array( "\r", "\n" ), ' ', $str );

            $str = str_replace( array(
                "Â·",
                "â€¦",
                "â‚¬",
                "&shy;"
            ), "", $str );

            return $str;
        }

        /*
         * Check if index table exist
         */
        static public function is_table_not_exist() {

            global $wpdb;

            $table_name = $wpdb->prefix . AWS_INDEX_TABLE_NAME;

            return ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) != $table_name );

        }

        /*
         * Get amount of indexed products
         */
        static public function get_indexed_products_count() {

            global $wpdb;

            $table_name = $wpdb->prefix . AWS_INDEX_TABLE_NAME;

            $indexed_products = 0;

            if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) === $table_name ) {

                $sql = "SELECT COUNT(*) FROM {$table_name} GROUP BY ID;";

                $indexed_products = $wpdb->query( $sql );

            }

            return $indexed_products;

        }

        /*
         * Check if index table has new terms columns
         */
        static public function is_index_table_has_terms() {

            global $wpdb;

            $table_name =  $wpdb->prefix . AWS_INDEX_TABLE_NAME;

            $return = false;

            if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) === $table_name ) {

                $columns = $wpdb->get_row("
                    SELECT * FROM {$table_name} LIMIT 0, 1
                ", ARRAY_A );

                if ( $columns && ! isset( $columns['term_id'] ) ) {
                    $return = 'no_terms';
                } else {
                    $return = 'has_terms';
                }

            }

            return $return;

        }
        
        /*
         * Get special characters that must be striped
         */
        static public function get_special_chars() {
            
            $chars = array(
                '-',
                '_',
                '|',
                '+',
                '`',
                '~',
                '!',
                '@',
                '#',
                '$',
                '%',
                '^',
                '&',
                '*',
                '(',
                ')',
                '\\',
                '?',
                ';',
                ':',
                "'",
                '"',
                ".",
                ",",
                "<",
                ">",
                "{",
                "}",
                "/",
                "[",
                "]",
                "’",
                "“",
                "”"
            );
            
            return apply_filters( 'aws_special_chars', $chars );
            
        }

        /*
         * Replace stopwords
         */
        static public function filter_stopwords( $str_array ) {

            $stopwords = AWS()->get_settings( 'stopwords' );

            if ( $stopwords && $str_array && ! empty( $str_array ) ) {
                $stopwords_array = explode( ',', $stopwords );
                if ( $stopwords_array && ! empty( $stopwords_array ) ) {

                    $stopwords_array = array_map( 'trim', $stopwords_array );

                    foreach ( $str_array as $str_word => $str_count ) {
                        if ( in_array( $str_word, $stopwords_array ) ) {
                            unset( $str_array[$str_word] );
                        }
                    }

                }
            }

            return $str_array;

        }

        /*
         * Strip shortcodes
         */
        static public function strip_shortcodes( $str ) {
            $str = preg_replace( '#\[[^\]]+\]#', '', $str );
            return $str;
        }

        /*
         * Get index table specific source name from taxonomy name
         *
         * @return string Source name
         */
        static public function get_source_name( $taxonomy ) {

            switch ( $taxonomy ) {

                case 'product_cat':
                    $source_name = 'category';
                    break;

                case 'product_tag':
                    $source_name = 'tag';
                    break;

                default:
                    $source_name = '';

            }

            return $source_name;

        }

        /*
         * Registers the WPML translations
         *
         */
        static public function register_wpml_translations( $params = false ) {

            // No WPML
            if ( ! function_exists( 'icl_register_string' ) ) {
                return;
            }

            // These options are registered
            $options_to_reg = array(
                "search_field_text" => "Search",
                "not_found_text"    => "Nothing found",
                "show_more_text"    => "View all results",
            );

            if ( ! $params ) {
                $params = $options_to_reg;
            }

            foreach ( $options_to_reg as $key => $option ) {
                icl_register_string( 'aws', $key, $params[$key] );
            }

        }

        /*
         * Wrapper for WPML print
         *
         * @return string Source name
         */
        static public function translate( $name, $value ) {

            if ( function_exists( 'icl_t' ) ) {
                return icl_t( 'aws', $name, $value );
            }

            return $value;

        }

    }

endif;