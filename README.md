# Instant Breaking News - WordPress Plugin

This plugin will allow you to pin posts and feature them as "Breaking News" at the header of your website.

## Table of Contents:
1) ibn.php - **Main plugin file**
2) index.php - **Directory protector**
3) pages (folder):
- dashboard.php - **Plugin Options Page**
4) metaboxes (folder):
- breaking-news.php - **Metabox container and it's relevant markup used in single Post Edit Screen**
5) assets (folder):
* scripts (folder):
    * admin.js - **Front-End logic in the WP Dashboard**  
    * public.js - **Front-End logic for the public website**
* styles (folder):
    * admins.scss (compiled .CSS file and .CSS.MAP are in the package as well) - **Front-End styles for the Plugin Options Page and metabox used by the plugin**
    * public.scss (compiled .CSS file and .CSS.MAP are in the package as well) - **Front-End styles for the public website**

## Instalation:
1) Clone the repository to your local / website server **wp-content/plugins** folder.
2) Active the plugin through the WP Dashboard.
3) That's it!

## Working with it:
**Instant Breaking News** plugin will appear in your WP Dashboard menu once it's activated.
You'll find the plugin Options Page under the name **Breaking News**.

From the options page you'll be able to change the "Breaking News" banner title, background and text colors. You'll also be able to preview the current pinned post and go to it's edit page directly from here. However if you haven't pinned anything yet, you'll be able to go to your Posts archive too!

### Pinning your first post:
Once you choose which will be your first **Breaking News** post in its Edit screen you'll find a newly created metabox under the name of **Breaking News Options** somewhere at the bottom of the Edit screen.

There you'll find the following three options:
1) **Make this post breaking news**: Once checked it'll pin your post and overwrite previously pinned posts. Sadly at version 1.0 you can have only one pinned post at a time.
2) **Custom breaking news title**: This setting allows you to choose a specific title for your post, which will be presented only in the Breaking News banner.
3) **Set an expiration Date & Time**: This option allows you to choose when the pinned post to disappear from your website automatically.

There is something that you should remember about the **Expiring Pins functionality**.
**Instant Breaking News** plugin automatically takes your server time and converts it to your WordPress Timezone.
That is **extremely important to remember** when setting the expiration date & time of your post, because if you pick a time which has already passed at your WordPress Timezone that post will be automatically unpinned in order to protect you from pinning expired posts. However if that happens **don't worry**! Once you set the new date and time you'll be able to re-pin it again, just by checking the **Make this post breaking news** option again.

### Note:
This plugin will work with almost every standart WordPress theme which has **<header>** in it's structure. If your template has unique structure, the plugin will need a bit of tweeking in order to run properly. The change that'll be required is at the **/assets/scripts/public.js** file.
You'll have to specify where on your website you would like to attach the **Breaking News** banner through those two lines:
```javascript
let $firstHeader = jQuery( "header" ).first();
jQuery( container ).insertAfter( $firstHeader );
```

##### That's it, let's start pinning!
