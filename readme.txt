=== Instant Breaking News ===
Contributors: GeroNikolov
Donate link: https://geronikolov.com/
Tags: Open Source, Breaking News, Universal
Requires at least: 3.0.1
Tested up to: 5.4.1
Stable tag: 1.0
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin will allow you to pin posts and feature them as "Breaking News" at the header of your website.

== Description ==

Instant Breaking News or IBN,<br>
will allow you to pin posts and feature them as "Breaking News" at the header of your website.

<strong>How it works ?</strong>
<br>
Instant Breaking News plugin will appear in your WP Dashboard menu once it's activated.<br>
You'll find the plugin Options Page under the name Breaking News.<br>
<br>
From the options page you'll be able to change the "Breaking News" banner title, background and text colors.<br>
You'll also be able to preview the current pinned post and go to it's edit page directly from here.<br>
However if you haven't pinned anything yet, you'll be able to go to your Posts archive too!

<strong>Pinning your first post:</strong>
<br>
Once you choose which will be your first Breaking News post in its Edit screen you'll find a newly created metabox under the name of Breaking News Options somewhere at the bottom of the Edit screen.<br>
<br>
There you'll find the following three options:<br>
1. <strong>Make this post breaking news</strong>: Once checked it'll pin your post and overwrite previously pinned posts. Sadly at version 1.0 you can have only one pinned post at a time.<br>
2. <strong>Custom breaking news title</strong>: This setting allows you to choose a specific title for your post, which will be presented only in the Breaking News banner.<br>
3. <strong>Set an expiration Date & Time</strong>: This option allows you to choose when the pinned post to disappear from your website automatically.<br>
<br>
There is something that you should remember about the Expiring Pins functionality.<br>
Instant Breaking News plugin automatically takes your server time and converts it to your WordPress Timezone.<br>
That is extremely important to remember when setting the expiration date & time of your post, because if you pick a time which has already passed at your WordPress Timezone that post will be automatically unpinned in order to protect you from pinning expired posts. However if that happens don't worry! Once you set the new date and time you'll be able to re-pin it again, just by checking the Make this post breaking news option again.

<strong>Note:</strong>
<br>
This plugin will work with almost every standart WordPress theme which has in it's structure.<br>
If your template has unique structure, the plugin will need a bit of tweeking in order to run properly.<br>
The change that'll be required is at the /assets/scripts/public.js file.<br>
You'll have to specify where on your website you would like to attach the Breaking News banner through those two lines:<br>
<br>
<pre>
let $firstHeader = jQuery( "header" ).first();
jQuery( container ).insertAfter( $firstHeader );
</pre>
<br>
<strong>That's it, let's start pinning!</strong>

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Settings->Plugin Name screen to configure the plugin

Alternative:
Download and activate the plugin from WordPress.org or
clone the repository from GitHub - https://github.com/Gero0Nikolov/IBN-WordPress-Plugin -
to your wp-content/wp-plugins/ folder and activate it from the WordPress Dashboard after that.

== Screenshots ==
