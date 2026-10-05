=== BlogLogistics Admin Slug Search ===
Tags: admin, search, slug, posts, pages
Requires at least: 7.1
Tested up to: 7.1
Requires PHP: 8.3
Stable tag: 1.0.5
License: GPL-3.0-or-later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Find and edit posts and pages in wp-admin using their URL slug, even when the title is different.

== Description ==

A page or post title can be different from its URL slug, making the content
difficult to find when you only know the URL. BlogLogistics Admin Slug Search
adds slug matching to the existing Posts and Pages listing searches in the
wp-admin backend, so you can quickly find the item and click Edit.

For https://www.manishamelwani.com/book-your-spiritual-journey/, search for
book-your-spiritual-journey under Pages > All Pages.

Search by the full slug or a portion, such as spiritual-journey. Enter the
slug only, not the full URL or a parent page path. Existing title, excerpt
and content search matches remain available.

Features:
* Small search feature with no settings, frontend scripts or styles.
* Includes Plugin Update Checker for updates from BlogLogistics.
* Applies only to the main Posts and Pages admin listing searches.
* Keeps the existing status, author, date and taxonomy query restrictions.
* Leaves searches containing exclusion terms, such as -journey, unchanged.
* No content database changes. The updater stores its update-check state.

For nested pages, search using the final slug segment. Items sharing the
same slug can both appear; use the listing details to select the right item.
For non-ASCII slugs stored as percent-encoded text, use the encoded slug
from the URL. Slug matching follows the database column's collation.

Requires WordPress 7.1 or later and PHP 8.3 or later.
Tested up to WordPress 7.1, based on the working-site testing confirmed
by BlogLogistics.

Website: https://www.bloglogistics.com

== Installation ==

1. In wp-admin, open Plugins > Add New Plugin > Upload Plugin.
2. Select bloglogistics-admin-slug-search.zip and click Install Now.
3. Activate BlogLogistics Admin Slug Search.
4. Open Posts > All Posts or Pages > All Pages and use the existing search box.

Alternatively, upload the bloglogistics-admin-slug-search folder into
wp-content/plugins/ and activate the plugin from the Plugins screen.

== Frequently Asked Questions ==

= Does this change the public website search? =
No. It only extends the Posts and Pages admin listing searches.

= Does it search custom post types or full URLs? =
No. It supports the standard Posts and Pages listings and slug text.

= Are there any settings? =
No. Activate it and use the existing search box.

== Changelog ==

= 1.0.5 =
* Find posts and pages in wp-admin using full or partial URL slug searches.
* Include BlogLogistics branding icons and banners.
* Use the established BlogLogistics manifest updater and release workflow.
* Attach the installable ZIP to the GitHub release and upload only the JSON manifest by FTPS.
* Require WordPress 7.1 and PHP 8.3, with WordPress tested up to 7.1.

== Updates ==

Updates use the JSON manifest at https://updates.bloglogistics.com/plugins/bloglogistics-admin-slug-search.json.
The manifest download URL points to the installable ZIP attached to the GitHub release.
Plugin Update Checker is bundled under its own MIT licence.
Private GitHub release assets require authentication to download. This plugin does not include GitHub credentials.

== Licence ==

Copyright (C) 2026 BlogLogistics.
Licensed under the GNU General Public License, version 3 or, at your option,
any later version (GPL-3.0-or-later). See LICENSE.txt for the full licence.
