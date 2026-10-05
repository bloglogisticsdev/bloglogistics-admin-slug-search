<?php
/**
 * Plugin Name: BlogLogistics Admin Slug Search
 * Plugin URI: https://www.bloglogistics.com
 * Description: Find content in wp-admin by slug or pasted URL, with configurable content types, slug matching and an optional Slug column.
 * Version: 1.1.0
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

define( 'BLOGLOGISTICS_ASS_VERSION', '1.1.0' );
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


require_once BLOGLOGISTICS_ASS_DIR . 'includes/class-bloglogistics-admin-slug-search-admin.php';
BlogLogistics_Admin_Slug_Search_Admin::init();

/**
 * Extract the final slug from an HTTP(S) URL without fetching it.
 *
 * @return string|null Null for ordinary text; empty string for a URL without a slug.
 */
function bloglogistics_ass_url_slug( $term ) {
    $term = trim( $term );
    if ( ! preg_match( '~^https?://~i', $term ) ) {
        return null;
    }
    $parts = wp_parse_url( $term );
    if ( ! is_array( $parts ) || empty( $parts['host'] ) ) {
        return '';
    }
    $path = rtrim( $parts['path'] ?? '', '/' );
    if ( '' === $path ) {
        return '';
    }
    return sanitize_title( rawurldecode( basename( $path ) ) );
}

/**
 * Add slug matching only to the main enabled content-type admin listing search.
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
	$settings = BlogLogistics_Admin_Slug_Search_Admin::settings();
	if ( ! $screen || 'edit' !== $screen->base || ! in_array( $screen->post_type, $settings['post_types'], true ) ) {
		return $search;
	}

	if ( ! in_array( $query->get( 'post_type' ) ?: 'post', $settings['post_types'], true ) ) {
		return $search;
	}

	$term = $query->get( 's' );
	if ( ! is_string( $term ) || '' === trim( $term ) ) {
		return $search;
	}

	$url_slug = bloglogistics_ass_url_slug( $term );
	if ( '' === $url_slug ) {
		return $search;
	}

	// Leave advanced exclusion searches to WordPress, preserving their meaning.
	$exclusion_prefix = apply_filters( 'wp_query_search_exclusion_prefix', '-' );
	foreach ( null === $url_slug ? (array) $query->get( 'search_terms' ) : array() as $search_term ) {
		if ( $exclusion_prefix && str_starts_with( $search_term, $exclusion_prefix ) ) {
			return $search;
		}
	}

	global $wpdb;
	$slug = $wpdb->esc_like( null === $url_slug ? trim( $term ) : $url_slug );
	$exact = $query->get( 'exact' ) || 'exact' === $settings['matching'] || ( 'smart' === $settings['matching'] && null !== $url_slug );
	if ( ! $exact ) {
		$slug = '%' . $slug . '%';
	}
	$slug_search = $wpdb->prepare( "{$wpdb->posts}.post_name LIKE %s", $slug );

	// Keep the existing search intact. Other WHERE restrictions stay outside
	// this group, so slug matches still obey status, author and taxonomy filters.
	return " AND ( ( 1=1 {$search} ) OR ( {$slug_search} ) ) ";
}

add_filter( 'posts_search', 'bloglogistics_admin_slug_search', 10, 2 );
