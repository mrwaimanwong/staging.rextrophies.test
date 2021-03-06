=== WP Max Submit Protect ===
Contributors: judgej
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=B4STZL8F5WHK6
Tags: woocommerce, forms, data-integrity
Requires at least: 3.6
Tested up to: 4.2.3
Stable tag: 1.1.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Prevent large forms being submitted that may blow the server's field count limit and lose data.

== Description ==

Some appications, such as ecommerce sites, can have administration forms that submit well over a thousand
parameters. PHP, by default, is set to accept only one thousand parameters and so some of the submitted data can get lost.

Most applications don't check whether they received everything, and so data can get broken easily and silently.
A WooCommerce product with 40 variations can have over 1300 submitted form items, and when saving the product you have no idea that much of that data is being discarded.

Luckily [the maximum number of accepted parameters can be changed in php.ini](http://docs.woothemes.com/document/problems-with-large-amounts-of-data-not-saving-variations-rates-etc/)
The problem is, many site owners have no idea this needs to be done until it is too late and their application,
for example their WooCommerce store, has lost half its product variations.

If using php.ini on your site, the file needs to be in the /wp-admin directory as that is where the admin forms are submitted to.

To protect yourself and make sure the server limit does not catch
you unawares, install this plugin and let it run in the background. Each time you try to submit a form in the
admin pages (e.g. updating a WooCommerce product with lots of variatrions) this plugin will check that the
number of form parameters you are about to submit does not exceed the server limit. If it does, then it
will inform you and give you the opportunity to postpone the submit while you increase the server settings.
The link above describes how to set the limits on the server. More details in the FQAs.

This plugin has been tested against PHP5.4 but is written to be compatible with PHP5.3. The project repository is here:

[https://github.com/academe/wp-max-submit-protect](https://github.com/academe/wp-max-submit-protect)

Note: turning on the Gravity Forms' ""No-Conflict Mode" will disable this plugin. You may have reasons for using that setting, but it will disable most other plugins, some of which may be security-related.

Please let me know how this plugin works for you, whether you like it, and how it can be improved.

== Installation ==

Upload wp-max-submit-protect/ to the `/wp-content/plugins/` directory or wp-max-submit-protect-x.y.z.zip through the "Add Plugins" administration page,
or install from wordpress.org by searching for "WP Max Submit Protect".

== Frequently asked questions ==

= Who is this plugin for? =

Any WordPress site that uses forms which have lots of fields. For example, a WooCommerce site containing products
with many variations.

= Is this just for WooCommerce sites? =

No! Any WordPress application that submits big forms can benefit from the protection this plugin offers.
Sites with multi-page forms implemented through GravityForms could use this.

= Are there any configuration options? =

Through the plugin no; you just install and go. There may be some server settings to update,
and the purpose of this plugin is to warn you about those.

= What are the PHP.ini settings that may need to be changed? =

The PHP ini settings that could affect the data in large forms are:

* max_input_vars
* suhosin.post.max_vars
* suhosin.request.max_vars

You may have all, some or none of these settings configured. If none are set, then max_input_vars
will default to 1000. This is certainly too low for some e-commerce plugins.

== Screenshots ==

1. An example of the message that appears if you try to submit more POST parameters than the server is able to accept.

== Changelog ==

= Version 1.1.2 =
* Issue #10: Disable the inline JS in the header if the enqueued JS library link has not completed. This can happen if another plugin (e.g. Gravity Forms) disables the JS for all other plugins to avoid having to play nicely with them. This worries me.

= Version 1.1.1 =
* Remove check of suhosin.get.max_vars as we are only interested in POST forms.
* Change the form selector to limit only to POST forms.

= Version 1.1.0 =
* Upgraded jquery.maxsubmit.js to 1.2.1 containing some substantial rewrites

= Version 1.0.7 =
* Upgraded jquery.maxsubmit.js to 1.1.3 to fix HTML5 input field matching

= Version 1.0.6 =
* Fixed JS syntax error that Chrome was throwing up.

= Version 1.0.5 =
* Ticket #7 Suppress warning message if no options set at all on the server.

= Version 1.0.4 =
* Update the jQuery maxsubmit plugin to 1.1.0

= Version 1.0.3 =
* Issue #1 (this readme.txt)

= Version 1.0.2 =
* Bugfix to 1.0.1

= Version 1.0.1 =
* Issue #2 and make all text translatable.

= Version 1.0.0 =
* Initial release.

== Upgrade notice ==
