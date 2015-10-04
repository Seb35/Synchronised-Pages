The WordPress plugin __Synchronised Pages__ empower you in creating a lot of pages according to a template in which some parts are remplaced by informations from a database.

Description
-----------

An easy way to describe this plugin is to give an example: imagine you want to create a small website for a festival in which each concert has specific hours, title, band/musicians, description… Without this plugin, you have to create by hand all the pages. With this plugin you can put all informations in a spreadsheet, create a template for the pages, and then automatically generate all the pages.

The three steps of the workflow:

1. Create the database of all your events/pages you want to create
2. Create the template for your futures pages
3. Launch the tool

In the posts list, you can view all the generated pages tagged with the word ”Synchronised” and the template is tagged with the word ”Template”. Obviously, the generated posts appear to the public.

Installation
------------

This section describes how to install the plugin and get it working.

1. Download the code from GitHub: https://github.com/Seb35/Synchronised-Pages/archive/master.zip
2. Upload the directory `synchronised-pages` in the `/wp-content/plugins/` directory
3. Activate the plugin through the “Plugins” menu in WordPress

With default settings, you can only generate pages, not posts; you can change this setting in the menu Settings > Writing > Synchronised Pages.

Frequently Asked Questions
--------------------------

### Are the templates public?

No. They are similar to Private posts.

### If some pages already exist, are they remplaced or re-created?

Currently, pages whose the title already exist are remplaced, so the old content is overwritten by the generated new content.

Possibly in the future, an option will give the choice.

## Is it the same thing as “child pages”?

Not exactly. Child pages is a feature specific for the post type “Page” (not standard “Posts”) where you can say a given page is a child of some other page, it is useful to show a hierarchical list of pages. Here the synchronised pages are only similar in their layout. But if you want make that all synchronised pages have a parent page, it’s up to you to make it – you can automatically get the list of all synchronised pages from a given import.

Screenshots
-----------

None.

Changelog
---------

### 0.2

Beta version. First public version.

### 0.1

Alpha version. Working and stable plugin, could be 1.0 but some parts of code should be rewritten.

Upgrade Notice
--------------

None.

Development
-----------

Development is done on GitHub, in the repository [Seb35/Synchronised-Pages](https://github.com/Seb35/Synchronised-Pages). You can report bugs or ask features in the [bug tracker](https://github.com/Seb35/Synchronised-Pages/issues), and if you are a developer you are welcome to submit [pull request](https://github.com/Seb35/Synchronised-Pages/pulls).

Versions on GitHub will be send back to the officiel WordPress repository.

### WordPress plugin metadata

- Contributors: sseb35
- Donate link: 
- Tags: content, template, template engine, pages
- Requires at least: 4.0
- Tested up to: 4.3
- Stable tag: trunk
- License: GPLv2 or later
- License URI: http://www.gnu.org/licenses/gpl-2.0.html

