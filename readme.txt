=== Hackadelic Table Of Contents Boxes ===
Contributors: Hackadelic
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=1805414
Tags: content, page, Post, AJAX, shortcode
Requires at least: 2.6
Tested up to: 2.7
Stable tag: 1.5.1

Easy to use, freely positionable, fancy AJAX-style table of contents for WordPress posts and pages.

== Description ==

Check out the [plug-in homepage](http://hackadelic.com/solutions/wordpress/toc-boxes "Hackadelic Table Of Contents Boxes") for in-depth information about the plug-in.

### Change Log

#### [1.5.1](http://hackadelic.com/toc-boxes-151-release)

* Fixes a bug with auto-insertion, [reported by DavyB](http://hackadelic.com/solutions/wordpress/toc-boxes#comment-893)
* Fixes a bug with TOC's on multi-page entries when the blog does not reside at top-level URL's. [Reported by Aleisha](http://hackadelic.com/toc-boxes-1-5-with-auto-insertion/comment-page-1#comment-841).

#### [1.5.0 "Warp 3" Release](http://hackadelic.com/toc-boxes-1-5-major-release "TOC Boxes 1.5 release announcement")

* Added the option to auto-insert TOC at a default position, if not explicitly added by shortcode.
* A new shortcode form, *[toc auto=off]*, was added to provide for suppressing the automatic shortcode insertion for individual posts and pages when auto-insertion is enabled globally. Other shortcode parameters are ignored in this form.
* A new shortcode paramter, *title*, is available to specify the title of the TOC box.
* Default shortcode parameter values, can now be configured view plugin settings. They are used implicitly when a shortcode parameter is omitted.
* New (pretty) naming scheme for heading anchors. Heading anchor names are derived from a sanitized form of the corresponding heading now, resulting into better readability and better SEO. Thanks to [an idea of johnbillion](http://hackadelic.com/toc-boxes-1-2-1-resolves-conflict-with-nofollow-reciprocity#comment-656).
* Several bug fixes and improvements.

**Please check if your CSS styling needs an update, [according to the updated CSS template on the plug-in homepage](http://hackadelic.com/solutions/wordpress/toc-boxes#installation"Hackadelic Table Of Contents Boxes - Installation").**

#### [1.2.1](http://hackadelic.com/toc-boxes-1-2-1-resolves-conflict-with-nofollow-reciprocity)

Resolves a conflict with the plugin [Nofollow Reciprocity](http://wordpress.org/extend/plugins/nofollow-reciprocity/) which totally messes up multipage TOCs.

#### [1.2.0](http://hackadelic.com/toc-boxes-1-2-with-multipage-support-released)

Adds support for multipage posts.

#### [1.1.1](http://hackadelic.com/toc-boxes-1-1-1-released)

* Added the class `toc-anchor` to the named anchors generated at the headings (i.e. the link targets for the TOC links) to support their individual styling via CSS.
* Minor bug-fixes

#### [1.1.0](http://hackadelic.com/toc-boxes-1-1-support-hyperlinks-in-headings)

Added ability to handle headings containing hyperlinks.

#### [1.0.0](http://hackadelic.com/a-new-plugin-is-born-toc-boxes)

Initial public release

== Installation ==

1. Upload the whole plugin folder to your /wp-content/plugins/ folder.
1. Go to the Plugins page and activate the plugin.
1. Activate the plug-in as usual.
1. Copy the CSS template fragment from the [plug-in homepage](http://hackadelic.com/solutions/wordpress/toc-boxes#css-setup "Hackadelic Table Of Contents Boxes") to your CSS file (either your themes style.css file, or, which I would recommend, through the [MyCSS plug-in](http://wordpress.org/extend/plugins/ "MyCSS plug-in"), and adjust it to your liking.

== Screenshots ==

Screenshots are boring. See the plug-in live in action at the [plug-in homepage](http://hackadelic.com/solutions/wordpress/toc-boxes "Hackadelic Table Of Contents Boxes"). :-) 

== License ==

This file is part of the *Hackadelic Table Of Contents Boxes* WordPress plugin.

*Hackadelic Table Of Contents Boxes* is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

*Hackadelic Table Of Contents Boxes* is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with *Hackadelic Table Of Contents Boxes*. If not, see <http://www.gnu.org/licenses/>.

== Frequently Asked Questions ==

= How do I use the plug-in? =

Easy: Add the shortcode `[toc]` to your post or page, where you want the TOC box to appear. To align the TOC box to the left or right, use `[toc class="toc-left"]` or `[toc class="toc-right"]`.

= How do I change the width of a TOC box? =

The TOC box is displayed with default widths which depend on it's alignment. `[toc]` will consume the full post width, while `[toc class="toc-left"]` and `[toc class="toc-right"]` will adjust to the length of your headings, up to a maximum width specified with the CSS. (Look for `div.toc.toc-right` and `div.toc.toc-left`, respectively.)

You may change the maximum width individually, using the style argument. Say, to have a right-aligned TOC box with a maximum width of 200 pixels, you would write `[toc class="toc-right" style="max-width:200px"]`.

= I inserted the [toc] thingy, but nothing happens. What's going on? =

The TOC shortcode is ignored if there are no headings on the page. To format a heading, go to the "Kitchen Sink" toolbar of the visual editor, and use the formatting drop-down box.

= Can I use it on multipage posts? =

Yes, since version 1.2.

= I love your work, are you available for hire? =

Yes I am. [Contact me](http://hackadelic.com/contact) to "talk turkey" :)