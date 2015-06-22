=== Google Webfont Optimizer ===
Contributors: sigurdurg
Donate link: http://quickfalcon.com/
Tags: plugin, bandwidth, javascript, optimize, performance, cascading style sheet, google, google page speed, google rank, js, css, optimizer, speed, user experience, web performance optimization
Requires at least: 3.0.1
Tested up to: 4.2
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Makes your website faster by combining all Google Fonts in a single request. Your websites gets a higher PageSpeed score which is good for SEO.

== Description ==

Most modern websites and website themes use Google Fonts to make their website typeface nice to look at and read.
Some plugins even include Google Fonts to make their plugin fancy.

Unfortunately, all this adds up to a lot of different Google Fonts being requested for each webpage, making the website slower with a lower Google PageSpeed score.
Ultimately, it lowers your visitor's experience and you could drop down in the search results.

Google already solved this problem on their servers by allowing you to ask for multiple fonts in a single request, but that doesn't fix the problem on YOUR website.

Your website asks for all these different fonts in different requests because there hasn't been a way to automatically find all these font requests and optimize them.

.. until now.

Finally there's a way to do this *automagically*, and that's where Google WebFont Optimizer helps your site.

Google WebFont Optimizer finds every Google Fonts request, bulks them all together so your website only asks Google once for the fonts, instead of multiple times.

This makes:
<ul><li><strong>Your Website faster</strong></li>
<li>Your visitors happier</li>
<li>Your Google PageSpeed score increase</li></ul>

You only have to install and activate the plugin, and the plugin takes care of everything else.

This plugin was created to make our client's websites faster to load with a higher Google PageSpeed score, and now it's yours - free!

If you want to discover more about how you can make your WordPress website faster, go to <a href="http://quickfalcon.com/">the Quick Falcon website</a>.

The plugin works best with W3 Total Cache

You can find the source code on GitHub - https://github.com/sigginet/google-webfont-optimizer


== Installation ==

1. Unzip and upload `google-webfont-optimizer` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. (Optional) Configure the plugin through Settings->GWFO settings

== Frequently Asked Questions ==

= Does this plugin make my website faster? =

If your website uses Google Fonts, yes.

= How can I find out if I'm using Google Fonts? =

Go to http://builtwith.com/ and type in your website URL.

Once builtwith finishes analyzing your website, go all the way down to "Widgets" (around the center of page) and see if Google Fonts API is listed.
If Google Fonts API is listed, your website uses Google Fonts and you should use this plugin.

= What's the difference between "HTML links in the header (no FOUT)" and "Javascript Web Font Loader (higher PageSpeed Score)"? =

"HTML links in the header (no FOUT)" adds a render-blocking stylesheet link in your header. This can lower your Google PageSpeed Score, but removes all Flash-Of-Unstylized-Text (FOUT).

"Javascript Web Font Loader (higher PageSpeed Score)" uses a non render blocking Javascript code to load the Google Fonts. This makes your website display faster, but you may see some FOUT in the first milliseconds when you go between pages on your website. - This is the recommended option

== Screenshots ==

1. Admin menu
2. Before and After Font Optimizing

== Upgrade Notice ==

= 0.2.4 =
Fixed a bug where fonts weren't being removed when &amp; is included.

= 0.2.3 =
Fixed a PHP warning bug.

= 0.2.2 =
Fixed a bug where invalid Google Fonts links with empty font name will be ignored.

= 0.2.1 =
Fixed a bug where "\n" appears on top of your page when using HTML links.

= 0.2.0 =
You can now configure if you want GWFO to be embedded using <link rel="stylesheet" or use the Web font loader javascript (written and recommended by Google, Adobe and Typekit).

== Changelog ==

= 0.2.4 &amp bug =

Fixed a bug where fonts weren't being removed when &amp; is included.
Thanks to Jan Jaap for reporting the bug (and the fix)

= 0.2.3 PHP warning bug =

Fixed a PHP warning bug.

= 0.2.2 Bugfix for empty google font links =

Fixed a bug where invalid Google Fonts links with empty font name will be ignored.

= 0.2.1 Bugfix for link html =

Fixed a bug where "\n" appears on top of your page when using HTML links.

= 0.2.0 First release =

This version added configurable options (HTML links and JS Web Font Loader) and is the first official release in the WordPress repository.

= 0.1.0 Pre-release =

This version was created for our clients, but it didn't have any configurable options.
