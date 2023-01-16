=== MathJax-LaTeX ===

Contributors: philliplord, sjcockell, knowledgeblog, d_swan, paulschreiber, jwenerd
Tags: mathematics, math, latex, mathml, mathjax, science, res-comms, scholar, academic
Requires at least: 3.0
Tested up to: 6.1.1
Stable tag: 1.3.12
Requires PHP: 7.0.0
License: GPLv2

This plugin enables MathJax (http://www.mathjax.org) functionality for
WordPress (http://www.wordpress.org).

== Description ==

MathJax enables enables rendering of embedded LaTeX or MathML in HTML pages. This plugin adds this functionality to WordPress. The MathJax JavaScript is inject on-demand only to those pages which require it. This ensures that MathJax is not loaded for all pages, which will otherwise slow loading down.

The MathJax JavaScript can be delivered from your own server, or you can use the Cloudflare Content Distribution Network (CDN), which is the preferred mechanism as it offers increased speed and stability over hosting the JavaScript and configuring the library yourself.

You may embed latex using a variety of different syntaxes. The shortcode (http://codex.wordpress.org/Shortcode_API) syntax is preferred. So `[latex]E=mc^2[/latex]` will work out of the box. This also forces loading of MathJax.

Additionally, you can use native MathJax syntax -- `$$E=mc^2$$` or `\(E=mc^2\)`. However, if this is the only syntax used, the plugin must be explicitly told to load MathJax for the current page. This can be achieved by adding a `[mathjax]` shortcode anywhere in the post. For posts with both `[latex]`x`[/latex]` and `$$x$$` syntaxes this is unnecessary.

You can use wp-latex syntax, `$latex E=mc^2$`. Parameters can be specified as with wp-latex but will be ignored. This means that MathJax-LaTeX should be a drop-in replacement for wp-latex. Because this conflicts with wp-latex, this behaviour is blocked when wp-latex is present, and must be explicitly enabled in the settings.

You can also specify `[nomathjax]` -- this will block mathjax on the current page, regardless of other tags.

MathJax-LaTeX is developed on [GitHub](https://github.com/phillord/mathjax-latex).

== Installation ==

1. Unzip the downloaded .zip archive to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Changelog ==

= 1.3.12 =

1. Use version 2.7.9 of MathJax JS
1. Add code comments to all variables, functions and parameters.

= 1.3.11 =

1. Use version 2.7.5 of MathJax JS

= 1.3.10 =

1. Rename class files, per PHPCS
2. Gracefully handle null values in filter_br_tags_on_math. Thanks to Yang Liu.

= 1.3.9 =

1. Code style changes, per PHPCS 3.3.0 and WPCS 0.14.1
1. Use PHP 7 short array syntax

= 1.3.8 =

1. Code style changes, per PHPCS 3.1.1 and WPCS 0.14

= 1.3.7 =

1. Update MathJax to 2.7.2

= 1.3.6 =

1. Update location of MathJax CDN

= 1.3.5 =

1. Add support for MathJax config via filter

= 1.3.4 =

1. PHP code cleanup
1. Always use https URL for MathJax library
1. Updated "tested up to" to 4.3

= 1.3.3 =

1. Fixed inconsistent version numbers between readme and php file

= 1.3.2 =

1. Further code clean ups.

= 1.3.1 =

1. Accessibility Improvements for Admin page
1. VIP Coding Standards
1. MathML tags enabled in TinyMCE

All code for this release was submitted by users of
this plugin! Thanks to Jared Wenerd and Paul Schreiber.

= 1.3.0 =

1. Whitelist MathML tags and attributes.
1. Sanitization of input and escaping of output.

= 1.2.1 =

1. Bug fix: custom location was not correctly applied.
1. Bug fix: force load was not correctly applied.

= 1.2 =

1. Admin page was open to attack from third party sites which user was logged
   in as admin.
1. The admin page has been isolated and rewritten.
1. All the options have been renamed, which will, unfortunately mean
   reconfiguring the plugin. In particular, wp-latex syntax is switched off by
   default.

= 1.1 =

1. Documentation update
1. Update test-with documentation for WordPress 3.5.1
1. Tested against MathJax2.1

= 1.0 =
1. Compatibility with MathJax 1.1. Load a default configuration from the MathJax distribution.
1. Use the MathJax Content Distribution Network to deliver the javascript library. Offers improved performance and stability.

= 0.2 =
1. MathJax.js can be loaded form a configurable URL. Defaults to $PLUGIN/MathJax/MathJax.js

== Upgrade Notice ==

= 1.3.1 =

Accessibility improvements.

= 1.2.1 =

Bug fix: Custom location was not correctly applied.
Bug fix: Force load was not correctly applied.

= 1.2 =

Security update. All users advised to update. Options will require resetting.

= 1.1 =
Documentation updates only. Upgrade for existing users is optional.

= 1.0 =

The 1.0 release offers compatibility with MathJax 1.1, and enables use of the
CDN for javascript delivery. Upgrading is strongly recommended.

== Copyright ==

This plugin is copyright Phillip Lord, Newcastle University and is licensed under GPLv2.
