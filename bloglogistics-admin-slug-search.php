<?php
/**
 * Plugin Name: BlogLogistics Admin Slug Search
 * Plugin URI: https://www.bloglogistics.com
 * Description: When a page or post title differs from its URL slug, this plugin makes it easy to find and then edit it in the wp-admin backend by adding URL slug matching to the Posts and Pages listing searches.
 * Version: 1.0.5
 * Requires at least: 7.1
 * Requires PHP: 8.3
 * Author: BlogLogistics
 * Author URI: https://www.bloglogistics.com
 * License: GPL-3.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Update URI: https://github.com/bloglogisticsdev/bloglogistics-admin-slug-search
 * Text Domain: bloglogistics-admin-slug-search
 *
 * Copyright (C) 2026 BlogLogistics
 * SPDX-License-Identifier: GPL-3.0-or-later
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See LICENSE.txt.
 */

defined( 'ABSPATH' ) || exit;

define( 'BLOGLOGISTICS_ASS_VERSION', '1.0.5' );
define( 'BLOGLOGISTICS_ASS_SLUG', 'bloglogistics-admin-slug-search' );
define( 'BLOGLOGISTICS_ASS_FILE', __FILE__ );
define( 'BLOGLOGISTICS_ASS_DIR', plugin_dir_path( __FILE__ ) );
define( 'BLOGLOGISTICS_ASS_UPDATE_MANIFEST_URL', 'https://updates.bloglogistics.com/plugins/bloglogistics-admin-slug-search.json' );

$bloglogistics_ass_puc = BLOGLOGISTICS_ASS_DIR . 'vendor/plugin-update-checker/plugin-update-checker.php';
if ( file_exists( $bloglogistics_ass_puc ) ) {
    if ( ! class_exists( \YahnisElsts\PluginUpdateChecker\v5\PucFactory::class, false ) ) {
        require_once $bloglogistics_ass_puc;
    }
    require_once BLOGLOGISTICS_ASS_DIR . 'includes/class-bloglogistics-admin-slug-search-updater.php';
    if ( class_exists( \YahnisElsts\PluginUpdateChecker\v5\PucFactory::class, false ) && class_exists( 'BlogLogistics_Admin_Slug_Search_Updater', false ) ) {
        BlogLogistics_Admin_Slug_Search_Updater::init( array(
            'repo_url' => BLOGLOGISTICS_ASS_UPDATE_MANIFEST_URL,
            'plugin_file' => BLOGLOGISTICS_ASS_FILE,
            'slug' => BLOGLOGISTICS_ASS_SLUG,
        ) );
    }
}


/**
 * Add slug matching only to the main Posts and Pages admin listing search.
 *
 * @param string   $search Existing search WHERE fragment.
 * @param WP_Query $query  Current query.
 * @return string
 */
function bloglogistics_admin_slug_search( $search, $query ) {

	if ( ! is_admin() || ! is_user_logged_in() || ! $query->is_main_query() || '' === trim( $search ) ) {
		return $search;
	}

	$screen = get_current_screen();
	if ( ! $screen || 'edit' !== $screen->base || ! in_array( $screen->post_type, array( 'post', 'page' ), true ) ) {
		return $search;
	}

	if ( ! in_array( $query->get( 'post_type' ) ?: 'post', array( 'post', 'page' ), true ) ) {
		return $search;
	}

	$term = $query->get( 's' );
	if ( ! is_string( $term ) || '' === trim( $term ) ) {
		return $search;
	}

	// Leave advanced exclusion searches to WordPress, preserving their meaning.
	$exclusion_prefix = apply_filters( 'wp_query_search_exclusion_prefix', '-' );
	foreach ( (array) $query->get( 'search_terms' ) as $search_term ) {
		if ( $exclusion_prefix && str_starts_with( $search_term, $exclusion_prefix ) ) {
			return $search;
		}
	}

	global $wpdb;
	$slug = $wpdb->esc_like( trim( $term ) );
	if ( ! $query->get( 'exact' ) ) {
		$slug = '%' . $slug . '%';
	}
	$slug_search = $wpdb->prepare( "{$wpdb->posts}.post_name LIKE %s", $slug );

	// Keep the existing search intact. Other WHERE restrictions stay outside
	// this group, so slug matches still obey status, author and taxonomy filters.
	return " AND ( ( 1=1 {$search} ) OR ( {$slug_search} ) ) ";
}

add_filter( 'posts_search', 'bloglogistics_admin_slug_search', 10, 2 );
