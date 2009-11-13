=== Hackadelic SEO Table Of Contents ===
Contributors: Hackadelic
Donate link: http://hackadelic.com/donations
Tags: content, page, Post, AJAX, shortcode, toc
Requires at least: 2.6
Tested up to: 2.8.5
Stable tag: 1.7.1

Easy to use, freely positionable, fancy AJAX-style table of contents for WordPress posts and pages.

== Description ==

Check out the [plug-in homepage](http://hackadelic.com/solutions/wordpress/toc-boxes "Hackadelic SEO Table Of Contents") for in-depth information about the plug-in.

== Changelog ==

#### 1.7.1

* Fixing a packaging glitch

#### 1.7.0

* TOC box dynamic effects are now optional (and jQuery is not loaded when turned off).
* TOC entries on the active post page get the extra CSS class 'active'. Useful for TOC's on mutlipage posts.
* Verified compatibility with WP 2.8.5
* Added small signature to toc box
* Several minor fixes and improvements

#### [1.6.0 - Resurrection](http://hackadelic.com/toc-boxes-is-seo-table-of-contents)

* TOC boxes now optionally include a link to the comments section on the page. (Idea and initial code [contributed by JohnBillion](http://hackadelic.com/toc-boxes-151-release#comment-1025))
* Overhauled settings handling for **WPMU compatibility**. Old-style settings are automatically migrated.
* Settings can be reset to "factory values", removing them from the database. Useful when uninstalling the plugin.
* Increased security.
* Overhauled back-end UI.
* Various code improvements.
* License change from GPL 2.0 to GNU Affero GPL (AGPL) 3.0.

#### [1.5.2 - Artem's Rib](http://hackadelic.com/toc-boxes-152-release-artems-rib)

* Fixes formatting issues with headers containing tags.
* Fixes a TOC box entry ordering issue due to ambiguities in the PHP regular expression handling.

Many thanks to Artem for contributing [the](http://wordpress.org/support/topic/268568) [patches](http://wordpress.org/support/topic/268259) on this.

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

**Please check if your CSS styling needs an update, [according to the updated CSS template on the plug-in homepage](http://hackadelic.com/solutions/wordpress/toc-boxes#installation"Hackadelic SEO Table Of Contents - Installation").**

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
1. Copy the CSS template fragment from the [plug-in homepage](http://hackadelic.com/solutions/wordpress/toc-boxes#css-setup "Hackadelic SEO Table Of Contents") to your CSS file (either your themes style.css file, or, which I would recommend, through the [MyCSS plug-in](http://wordpress.org/extend/plugins/ "MyCSS plug-in"), and adjust it to your liking.

== Screenshots ==

Screenshots are boring. See the plug-in live in action at the [plug-in homepage](http://hackadelic.com/solutions/wordpress/toc-boxes "Hackadelic SEO Table Of Contents"). :-) 

== License ==

This file is part of the *Hackadelic SEO Table Of Contents* WordPress plugin.

*Hackadelic SEO Table Of Contents* is free software: you can redistribute it and/or modify it under the terms of the [GNU General Public License](http://creativecommons.org/licenses/GPL/2.0/) as published by the Free Software Foundation, either version 2 of the License, or (at your option) any later version.

*Hackadelic SEO Table Of Contents* is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with *Hackadelic SEO Table Of Contents*. If not, see <http://www.gnu.org/licenses/>.

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

[Yes I am](http://hackadelic.com/services). [Contact me](http://hackadelic.com/contact) to "talk turkey" :)