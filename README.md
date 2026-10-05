=== BlogLogistics Admin Slug Search ===
Tags: admin, search, slug, posts, pages
Requires at least: 7.1
Tested up to: 7.1
Requires PHP: 8.3
Stable tag: 1.1.0
License: GPL-3.0-or-later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Find and edit posts and pages in wp-admin using their URL slug, even when the title is different.

== Description ==

A page or post title can be different from its URL slug. Search using slug
text or paste a full HTTP(S) URL into the existing admin listing search box.
The plugin extracts the final path segment, ignoring query strings, fragments
and trailing slashes. It does not fetch the pasted URL.

Example: https://example.com/services/bookkeeping/?ref=email searches for
bookkeeping. Nested paths are matched by their final slug, so items sharing
that slug can both appear. Homepage URLs and query-only URLs such as ?p=123
do not supply a slug and retain the normal WordPress search behaviour.

Settings appear under BlogLogistics > Admin Slug Search when the shared menu
exists. Otherwise, Admin Slug Search has its own sidebar menu with an RSS icon.
Administrators can choose enabled content types and slug matching behaviour.
Posts and Pages are enabled by default. Other registered content types with
an admin listing, such as Products, can be enabled; Media is excluded.
Uncheck all content types to disable the additional search behaviour.

Matching options:
* Automatic (default): partial matches for typed text, exact slugs for URLs.
* Partial: find fragments within slugs, including when pasting a URL.
* Exact: require the complete slug for both text and URLs.

Normal WordPress title, excerpt and content matching remains available.
Status, author, date and taxonomy restrictions remain in force. Advanced
text searches with exclusion terms, such as -journey, are left unchanged.
Slug matching follows the database column's collation.

The optional Slug column is hidden by default. Open Screen Options on an
enabled listing and select Slug. Visibility is saved per user by WordPress.
The plugin changes neither content nor public website searches. Settings
and update-check state are stored in WordPress options.

Requires WordPress 7.1 or later and PHP 8.3 or later.
Tested up to WordPress 7.1, based on the working-site testing confirmed
by BlogLogistics.

Website: https://www.bloglogistics.com

== Installation ==

1. In wp-admin, open Plugins > Add New Plugin > Upload Plugin.
2. Select bloglogistics-admin-slug-search.zip and click Install Now.
3. Activate BlogLogistics Admin Slug Search.
4. Use the existing search box in Posts or Pages, or configure other content types under Admin Slug Search.

Alternatively, upload the bloglogistics-admin-slug-search folder into
wp-content/plugins/ and activate the plugin from the Plugins screen.

== Frequently Asked Questions ==

= Does this change the public website search? =
No. It only extends selected admin listing searches.

= Does it search custom post types or full URLs? =
Yes. Pasted HTTP(S) URLs work automatically. Enable additional content types in the settings.

= Are there any settings? =
Yes. Open BlogLogistics > Admin Slug Search, or the standalone Admin Slug Search sidebar menu if BlogLogistics is absent. Choose content types and slug matching. Column visibility is controlled through Screen Options on each listing.

== Changelog ==

= 1.1.0 =
* Search by a pasted HTTP(S) URL using its final slug segment.
* Select which admin content listings support slug and URL searches.
* Choose automatic, partial or exact slug matching.
* Add an optional Slug column controlled through Screen Options.
* Place settings beneath BlogLogistics, or in a standalone sidebar menu with an RSS icon.
* Preserve the established manifest updater, branding assets and release workflow.

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
