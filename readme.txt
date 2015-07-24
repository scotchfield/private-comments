=== Private User Comments ===
Contributors: sgrant
Donate link: http://scootah.com/
Tags: comments, private, private comments, user comments, private user comments, privacy
Requires at least: 3.5
Tested up to: 4.2.3
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allow WordPress users to make their comments private (visible by themselves and admins).

== Description ==

Adds a checkbox to the user settings page to allow a user to set all of their comments to private. Private comments are only visible by the comment author and site administrators.

== Installation ==

Place all the files in a directory inside of wp-content/plugins (for example, private-user-comments), and activate the plugin.

== Frequently Asked Questions ==

= Where is the private flag stored? =

The private tag is stored as a piece of comment metadata. Private Comments won't hit the database for every comment (especially important on larger sites). One query is used to check the array of comment IDs against the commentmeta table for privacy.

== Screenshots ==

1. The checkbox added to the user settings page.
2. A sample comment that has been prefixed with "Private".

== Changelog ==

= 1.0 =
* First release!

== Upgrade Notice ==

= 1.0 =
First public release.
