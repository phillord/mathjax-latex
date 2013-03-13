=== MathJax-LaTeX ===

Contributors: philliplord, sjcockell, knowledgeblog, d_swan
Tags: mathematics, math, latex, mathml, mathjax, science, res-comms, scholar, academic
Requires at least: 3.0
Tested up to: 3.5.1
Stable tag: 1.2

This plugin enables mathjax (http://www.mathjax.org) functionality for
WordPress (http://www.wordpress.org). 

== Description ==

Mathjax enables enables rendering of embedded latex or mathml in HTML pages.
This plugin adds this functionality to wordpress. The mathjax javascript is
inject on-demand only to those pages which require it. This ensures that
mathjax is not loaded for all pages, which will otherwise slow loading down.

The MathJax javascript can be delivered from your own server, or you can
utilise the [MathJax Content Distribution Network (CDN)]
(http://www.mathjax.org/docs/latest/start.html#mathjax-cdn), which is the preferred
mechanism as it offers increased speed and stability over hosting the Javascript
and configuring the library yourself. Use of the CDN is governed by these
[Terms of Service](http://www.mathjax.org/download/mathjax-cdn-terms-of-service/).

You may embed latex using a variety of different syntaxes. The shortcode
(http://codex.wordpress.org/Shortcode_API) syntax is preferred. So
[latex]E=mc^2[/latex] will work out of the box. This also forces loading of
mathjax.  

Additionally, you can use native mathjax syntax -- $$E=mc^2$$ or \(E=mc^2\).
However, if this is the only syntax used, the plugin must be explicitly told
to load mathjax for the current page. This can be achieved by adding a 
[mathjax] shortcode anywhere in the post. For posts with both [latex]x[/latex]
and $$x$$ syntaxes this is unnecessary. 

You can use wp-latex syntax, $latex E=mc^2$. Parameters can be
specified as with wp-latex but will be ignored. This means that mathjax-latex
should be a drop in replacement for wp-latex. Because this conflicts with
wp-latex, this behaviour is blocked when wp-latex is present, and must be
explicitly enabled in the settings.

You can also specify [nomathjax] -- this will block mathjax on the
current page, regardless of other tags.

== Installation ==

1. Unzip the downloaded .zip archive to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. This uses the mathjax
   CDN(<http://www.mathjax.org/docs/1.1/start.html#mathjax-cdn>). Alternatively:
1. Download the MathJax Javascript library (http://www.mathjax.org /download/)
1. Place the Javascript library in the mathjax-latex directory (`/wp-content/plugins/mathjax-latex/MathJax`)
1. You can configure the plugin to load MathJax from a different URL to the default. See the options page.


== Changelog ==

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

= 1.2 =

Security update. All users advised to update. Options will require resetting.

= 1.1 = 
Documentation updates only. Upgrade for existing users is optional. 

= 1.0 =

The 1.0 release offers compatibility with MathJax 1.1, and enables use of the
CDN for javascript delivery. Upgrading is strongly recommended.

== Copyright ==

This plugin is copyright Phillip Lord, Newcastle University and is licensed
under GPLv2. 
