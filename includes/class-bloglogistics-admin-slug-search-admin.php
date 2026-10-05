<?php
/** Admin settings, menu placement and optional listing column. */
defined( 'ABSPATH' ) || exit;

final class BlogLogistics_Admin_Slug_Search_Admin {
    const OPTION = 'bloglogistics_ass_settings';
    const PAGE = 'bloglogistics-admin-slug-search';

    public static function init() {
        add_action( 'admin_menu', array( __CLASS__, 'menu' ), PHP_INT_MAX );
        add_action( 'admin_init', array( __CLASS__, 'register' ) );
        add_action( 'current_screen', array( __CLASS__, 'columns' ) );
        add_filter( 'default_hidden_columns', array( __CLASS__, 'hidden' ), 10, 2 );
    }

    public static function types() {
        $types = get_post_types( array( 'show_ui' => true ), 'objects' );
        unset( $types['attachment'] );
        return $types;
    }

    public static function settings() {
        $value = get_option( self::OPTION, array() );
        if ( ! is_array( $value ) ) {
            $value = array();
        }
        return array(
            'post_types' => isset( $value['post_types'] ) && is_array( $value['post_types'] ) ? $value['post_types'] : array( 'post', 'page' ),
            'matching' => isset( $value['matching'] ) && in_array( $value['matching'], array( 'smart', 'partial', 'exact' ), true ) ? $value['matching'] : 'smart',
        );
    }

    public static function sanitise( $value ) {
        $value = is_array( $value ) ? $value : array();
        $selected = isset( $value['post_types'] ) && is_array( $value['post_types'] ) ? array_filter( $value['post_types'], 'is_string' ) : array();
        return array(
            'post_types' => array_values( array_intersect( array_keys( self::types() ), $selected ) ),
            'matching' => isset( $value['matching'] ) && in_array( $value['matching'], array( 'smart', 'partial', 'exact' ), true ) ? $value['matching'] : 'smart',
        );
    }

    public static function register() {
        register_setting( 'bloglogistics_ass', self::OPTION, array( 'type' => 'array', 'sanitize_callback' => array( __CLASS__, 'sanitise' ) ) );
    }

    public static function menu() {
        $title = __( 'Admin Slug Search', 'bloglogistics-admin-slug-search' );
        if ( ! empty( $GLOBALS['admin_page_hooks']['bloglogistics'] ) ) {
            add_submenu_page( 'bloglogistics', $title, $title, 'manage_options', self::PAGE, array( __CLASS__, 'render' ) );
        } else {
            add_menu_page( $title, $title, 'manage_options', self::PAGE, array( __CLASS__, 'render' ), 'dashicons-rss' );
        }
    }

    public static function render() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $settings = self::settings();
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'BlogLogistics Admin Slug Search', 'bloglogistics-admin-slug-search' ); ?></h1>
            <p><?php esc_html_e( 'Find content using a slug or a pasted URL in the existing admin search box. These options affect admin listings only.', 'bloglogistics-admin-slug-search' ); ?></p>
            <?php settings_errors(); ?>
            <form action="options.php" method="post">
                <?php settings_fields( 'bloglogistics_ass' ); ?>
                <table class="form-table" role="presentation">
                    <tr><th scope="row"><?php esc_html_e( 'Search these content types', 'bloglogistics-admin-slug-search' ); ?></th><td>
                        <fieldset><legend class="screen-reader-text"><?php esc_html_e( 'Search these content types', 'bloglogistics-admin-slug-search' ); ?></legend>
                        <input type="hidden" name="bloglogistics_ass_settings[post_types][]" value="">
                        <?php foreach ( self::types() as $name => $type ) : ?>
                            <label><input type="checkbox" name="bloglogistics_ass_settings[post_types][]" value="<?php echo esc_attr( $name ); ?>" <?php checked( in_array( $name, $settings['post_types'], true ) ); ?>> <?php echo esc_html( $type->labels->name ); ?></label><br>
                        <?php endforeach; ?>
                        </fieldset>
                        <p class="description"><?php esc_html_e( 'Enable slug and URL searching for these listings. Unchecked types retain their normal WordPress search. Uncheck all to disable the extra search behaviour.', 'bloglogistics-admin-slug-search' ); ?></p>
                    </td></tr>
                    <tr><th scope="row"><label for="bloglogistics-ass-matching"><?php esc_html_e( 'Slug matching', 'bloglogistics-admin-slug-search' ); ?></label></th><td>
                        <select id="bloglogistics-ass-matching" name="bloglogistics_ass_settings[matching]">
                            <?php foreach ( array( 'smart' => __( 'Automatic: partial text, exact pasted URL', 'bloglogistics-admin-slug-search' ), 'partial' => __( 'Partial slug matches', 'bloglogistics-admin-slug-search' ), 'exact' => __( 'Exact slug matches', 'bloglogistics-admin-slug-search' ) ) as $value => $label ) : ?>
                                <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $settings['matching'], $value ); ?>><?php echo esc_html( $label ); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description"><?php esc_html_e( 'Partial matches find fragments such as bookkeeping within monthly-bookkeeping. Exact matches require the whole slug. Normal title and content matching remains available for typed text.', 'bloglogistics-admin-slug-search' ); ?></p>
                    </td></tr>
                </table>
                <?php submit_button(); ?>
            </form>
            <h2><?php esc_html_e( 'Using pasted URLs', 'bloglogistics-admin-slug-search' ); ?></h2>
            <p><?php esc_html_e( 'Paste a full http:// or https:// URL into a selected content listing’s search box. The final path segment becomes the slug; query strings, fragments and trailing slashes are ignored. A URL ending in /services/bookkeeping/?ref=email searches for bookkeeping. Nothing is fetched from the pasted address.', 'bloglogistics-admin-slug-search' ); ?></p>
            <p><?php esc_html_e( 'This is a slug search, not a full address lookup. Items with the same final slug can both appear. Homepage URLs and URLs that identify content only through a query string, such as ?p=123, do not provide a slug.', 'bloglogistics-admin-slug-search' ); ?></p>
            <h2><?php esc_html_e( 'Optional Slug column', 'bloglogistics-admin-slug-search' ); ?></h2>
            <p><?php esc_html_e( 'Open an enabled content listing, open Screen Options at the top and select Slug. Each user controls their own column visibility.', 'bloglogistics-admin-slug-search' ); ?></p>
        </div>
        <?php
    }

    public static function columns( $screen ) {
        $settings = self::settings();
        if ( 'edit' !== $screen->base || ! in_array( $screen->post_type, $settings['post_types'], true ) ) {
            return;
        }
        add_filter( 'manage_' . $screen->post_type . '_posts_columns', array( __CLASS__, 'heading' ) );
        add_action( 'manage_' . $screen->post_type . '_posts_custom_column', array( __CLASS__, 'cell' ), 10, 2 );
    }

    public static function heading( $columns ) {
        $columns['bloglogistics_ass_slug'] = __( 'Slug', 'bloglogistics-admin-slug-search' );
        return $columns;
    }

    public static function cell( $column, $id ) {
        if ( 'bloglogistics_ass_slug' === $column ) {
            echo '<code>' . esc_html( get_post_field( 'post_name', $id ) ) . '</code>';
        }
    }

    public static function hidden( $hidden, $screen ) {
        $settings = self::settings();
        if ( 'edit' === $screen->base && in_array( $screen->post_type, $settings['post_types'], true ) && ! in_array( 'bloglogistics_ass_slug', $hidden, true ) ) {
            $hidden[] = 'bloglogistics_ass_slug';
        }
        return $hidden;
    }
}
