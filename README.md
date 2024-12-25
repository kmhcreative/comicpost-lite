# ComicPost
<img src="images/comicpost_logo.png" width="400"/>
A simple webcomic plugin with content restriction options to prevent AI scraping.

**Version:** 0.1

**Requires WordPress Version:** 3.5 or higher, PHP 5+

**Compatible up to:** 6.7.1

**Beta Version Disclaimer**

This plugin is still being tested.  It seems pretty solid, but use it at your own risk.

## Description

ComicPost is a relatively lightweight and simple webcomics plugin that *should* work with pretty much any theme that displays Featured Images with posts. This also may be the first WordPress webcomics plugin to specifically address the issue of Artificial Intelligence image scrapers stealing and training on comic artwork without permission or compensation. To combat that there are a number of **Content Restriction** options and a built-in automatic **Watermarking** feature.

This plugin is intended as an easy (but not drop-in) replacement for ComicPress/Comic Easel because it uses the same default custom post-type and chapter taxonomy, so any existing comics will automatically show up in it and work with it without having to migrate/import anything. But a prior installation of either ComicPress or Comic Easel are not necessary.

## Installation

### Using Admin Upload

1. Download the GitHub archive as a ZIP file.
2. Go to your _Dashboard > Plugins > Add New_ and press the "Upload Plugin" at the top of the page.
3. Browse to where you downloaded the ZIP file and select it.
4. Press the "Install Now" button.
5. On your _Dashboard > Plugins_ page activate "ComicPost"
(There are no settings or options)

### Using FTP
  
2. Unzip it so you have a "comicpost-master" folder
3. FTP upload it into your WordPress blog’s _~/wp-content/plugins/_ folder.
4. Go to your _Dashboard > Plugins_ and activate “ComicPost”
(There are no settings or options)

### Shortcodes

**Insert Comic**
Allows you to insert a comic anywhere with a link to the comic post. By design it can show comics regardless of required login or hiding old comics. It can add additional security features above the global settings but not reduce them.
*Parameters:*
* _comicnav_ = "true|false" : whether or not to include comic navigation below the comic image.
* _size_ = "thumbnail|medium|large|full" : the size of comic image to display
* _protect_ = "encode,glass,noprint" : single or comma-separated list of protections to apply.
* _orderby_ = "ASC|DESC : start at beginning or start at end, ignored if single.
* _number_ = "1" : offset from start/end (depending on orderby)
* _chapter_ = "slug_for_chapter" : which chapter to grab, ignored if single.
* _single_ = "true|false" + id="12" : shows comic post by ID number.

*Example Usage:*
`[insertcomic size="full" chapter="chapter-one" orderby="DESC" comicnave="true"]`
`[insertcomic size="large" single="true" id="8045" protect="glass"]`
`[insertcomic size="medium" chapter="chapter-three" orderby="ASC" number="5" comicnav="true"]`

**Archive Comic List**
Adds a unordered list of Comic Chapters anywhere. When a user selects a chapter from the list they are immediately taken the Archives for that chapter.
*Parameters:*
* _include_ = "1,slug,Name" : single or comma-separated list of chapter IDs, slugs, or names to include. Will not automatically include tree of sub-chapters.
* _exclude_ = "1,slug,Name" : single or comma-separated list of chapter IDs, slugs, or names to exclude. WILL automaticlaly exclude the tree of sub-chapters.
* _emptychaps_ = "show|hide" : whether or not to show chapters with no comic posts in them
* _thumbnails_ = "true|false" : whether or not to show chapter thumbnail or not. It uses the image from the first post in the chapter, assuming there is one.
* _order_ = "ASC|DESC" : whether to display the list in ascending or descending order.
* _orderby_ = "name|slug|ID" : what to order the list by, remember that name and slug are sorted alphabetically.
* _postdate_ = "first|last" : whether the chapter date shown should be by the first or last comic posted in it.
* _dateformat_ = "site|Y-m-d" : whether to use the date format for the site defined in Settings > General or some other date format.
* _description_ = "true|false" : whether to include the Chapter Description or not. This is instended for short descriptions like "#1" or "Ep.1" If it is longer you will need to custom style the list to display it.
* _comments_ = "true|false" : whether to include a count of the total number of comments on all posts in the chapter.
* _showratings_ = "true|false" : whether to include the cumulative ratings for the chapter (only works if you have enabled either Post Likes or Five-Star Ratings).
* _liststyle_ = "flat|indent|custom" : the unordered list style. The "indent" option visually indicates the chapter hierarchy by shifting sub-chapters to the right. You can also declare a class name (list-style-custom) for custom styling, where "custom" is whatever you want.
* _title_ = "Chapters|custom" : This is the title of the Chapter List, if any. You could change this to "Episodes" or "" for no heading.

*Example Usage:*
`[comicpost-chapter-list]` (would show all chapters and subchapters with default layout)
`[comicpost-chapter-list exclude="124,chapter-one,Title Three"]`
`[comicpost-chapter-list thumbnails="false" comments="false" postdate="false" liststyle="indented"]` (barebones hierarchical list of just chapter titles)
`[comicpost-chapter-list dateformat="Y/m/d" description="true" showratings="true"]` (would show all elements plus using a custom date format)

**Archive Drop-Down**
Adds a drop-down list of Comic Chapters anywhere. When a user selects a chapter they are immediately taken to the Archives for that chapter.
*Parameters:*
* _include_ = "1,slug,name" : single or comma-seperated list of chapter IDs, slugs, or names to include. Will NOT automatically include tree of sub-chapters.
* _exclude_ = "1,2,3,4" : single or comma-separted list of chapter IDs, slugs, or names to exclude. WILL automatically excluse tree of sub-chapters.
* _emptychaps_ = "show|hide" : whether or not to include chapters that have no comic posts in them.
* _title_ = "Select Chapter|custom" : first item in the drop-down says what it selects. If set to "" it uses the default title.

*Example Usage:*
`[comicpost-archive-dropdown]` (would show ALL chapters and sub-chapters)
`[comicpost-archive-dropdown exclude="124,142,143,168"]` (excludes 4 chapters and all their sub-chapters).

## Changelog

Version 0.1

* Initial public release.

## Developers

K.M. Hansen (@kmhcreative) - Lead Developer
http://www.kmhcreative.com

## License

GPLv3 or later
http://www.gnu.org/licenses/gpl-3.0.html

