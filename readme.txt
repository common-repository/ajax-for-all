=== Ajax For All ===
Contributors: nkuttler
Author URI: http://www.nkuttler.de/
Plugin URI: http://www.nkuttler.de/wordpress-plugin/automatic-ajax-for-wordpress-plugin/
Donate link: http://www.nkuttler.de/wordpress/donations/
Tags: theme, themes, template, templates, theme development, ajax, admin, plugin, jQuery, twentyten, i18n, l10n, interntationalized, localized, magic
Requires at least: 2.9
Tested up to: 3.1
Stable tag: 0.5.2

This plugin will enable a fancy ajax functionality on simple sites and most themes.

== Description ==

The best way to see what this plugin does is to look at the [live demo](http://ajax.nkuttler.de). The theme on that site wasn't tweaked at all to make the Ajax functionality work (except some irrelevant CSS changes). It works out of the box on most WordPress themes that follow the [theme coding recommendations](http://codex.wordpress.org/Theme_Development#Anatomy_of_a_Theme).

This plugin depends on the php curl library.

= What won't work =

1. This plugin won't work properly with plugins that load the JavaScript they need only on the correct page. I can not provide a list of such plugins, you will need to check if the effect works correctly with your plugins.
2. This plugin does not support adsense ads inside your content area. Other ad services might break as well.

= Other plugins I wrote =

 * [Better Related Posts](http://www.nkuttler.de/wordpress-plugin/wordpress-related-posts-plugin/)
 * [Custom Avatars For Comments](http://www.nkuttler.de/wordpress-plugin/custom-avatars-for-comments/)
 * [Better Tag Cloud](http://www.nkuttler.de/wordpress-plugin/a-better-tag-cloud-widget/)
 * [Snow and more](http://www.nkuttler.de/wordpress-plugin/snow-balloons-and-more/)
 * [Zero Conf Mail](http://www.nkuttler.de/wordpress-plugin/zero-conf-mail/)
 * [Theme Switch](http://www.nkuttler.de/wordpress-plugin/theme-switch-and-preview-plugin/)
 * [Better Lorem Ipsum Generator](http://www.nkuttler.de/wordpress-plugin/wordpress-lorem-ipsum-generator-plugin/)
 * [MU fast backend switch](http://www.nkuttler.de/wordpress-plugin/wpmu-switch-backend/)
 * [Visitor Movies for WordPress](http://www.nkuttler.de/wordpress-plugin/record-movies-of-visitors/)
 * [Move WordPress Comments](http://www.nkuttler.de/wordpress-plugin/move-wordpress-comments/)
 * [Delete Pending Comments](http://www.nkuttler.de/wordpress-plugin/delete-pending-comments/)

== Installation ==
Unzip, upload to your plugin directory, enable the plugin and configure it as needed, see the FAQ as well.

== Frequently Asked Questions ==
Q: It doesn't work on my theme, what can I do?<br />
A: First, you need to make sure that the base layout doesn't change. Your posts, pages, archive views etc. shouldn't be fundamentally different. Then you should find the div that you want to refresh on clicks. In most WordPress themes this is the div with the ID 'content', which is the plugin default as well. You might have to add a div wrapper with an ID of 'content' if your theme wasn't coded properly. Make sure that this wrapping div also wraps the comments and post meta info, post navigation (next page) etc. Basically, everything that you want updated.

Q: Your plugin doesn't work at all!<br />
A: I know for a fact that it doesn't when you have adsense on your site. Other ad services might have the same issue.

Q: My unicode strings/accents look all wrong after an Ajax request, what can I do?<br />
A: This has been fixed in 0.3.2 but the fix depends on the mbstring PHP library. Ask your hoster/admin to install it.

Q: Won't this hurt my SEO efforts?<br />
A: The plugin is written so that it won't be used by search engine crawlers and should not affect SEO at all. Since version 0.5 the plugin also supports browser history, deep linking etc.

Q: How does this work?<br />
A: The idea is rather simple: On any click fire an ajax request to this plugin's ajax handler. The handler checks if the link is good, fetches the content, and returns the requested div. JavaScript then inserts that response into the specified element of the existing page.

Q: I use cookies to present different output to users.<br />
A: Ouch. The fetching of the new content happens on the server side. An earlier version used to do this differently. You're out of luck for the moment. My own theme switching plugin won't work with this plugin because of this. I plan to move the code to the client-side again in the future, but that will take time.

Q: Why doesn't the plugin do X?<br />
A: You should read through the comments on the plugin page and add your feature request if it isn't there yet.

Q: I can't figure this out on my own, can you help me?<br />
A: I offer [professional WordPress services](http://www.nkuttler.de/services/), do not hesitate to contact me.

== Changelog ==
= 0.5.2 ( 2010-12-15 ) =
 * Make jumping to deep links optional, thanks [Wroth](http://www.wrothstudio.com/)
= 0.5.1 ( 2010-12-14 ) =
 * Respect target attribute on links, thanks to [Ted](http://aryanalaw.btwimages.com/)
= 0.5 ( 2010-12-14 ) =
 * Fix back/forward buttons and deep links
= 0.4.1 ( 2010-07-27 ) =
 * Require PHP 5.2 at activation time
= 0.4 ( 2010-07-26 ) =
 * Add placeholder to fix layout jumping bug
 * Add transitions
 * Add more config options
 * Complete i18n
= 0.3.2 ( 2010-07-25 ) =
 * Handle unicode in content properly. Requires mbstring.
= 0.3.1 ( 2010-07-23 ) =
 * Correct name dangit.
= 0.3 ( 2010-07-23 ) =
 * Public release
 * JS class based.
= 0.2 =
 * Class based
 * Curl authentication works
= 0.1 =
 * First working version

== Upgrade Notice ==
= 0.5 ( 2010-12-14 ) =
 * Fix back/forward buttons, at last
= 0.3 ( 2010-07-23 ) =
 * Hello, world!
