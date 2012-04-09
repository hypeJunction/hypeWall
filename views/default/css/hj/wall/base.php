<?php

?>

#hj-wall {
	margin:20px 0;
	padding:10px;
	border:1px dashed #e8e8e8;
	background:#f4f4f4;
}

#hj-wall form {
	background:#fff;
}

#hj-wall .elgg-tabs {
	border-bottom:0;
}

#hj-wall .elgg-tabs > li {
	border:0;

	-moz-border-radius:0;
	-webkit-border-radius:0;
	border-radius:0;

	padding:0;
	margin:0 10px 0 0;

	background:none;
}

#hj-wall .elgg-tabs > li > a,
#hj-wall .elgg-tabs > li > a:hover {
	background:none;
	line-height:20px;
}


#hj-wall .elgg-tabs > li > a > span {
	width:20px;
	height:20px;
	vertical-align:middle;
	text-align:center;
}


#hj-wall form {
	border:1px solid #e8e8e8;
	padding:0;
	margin:0;
}

#hj-wall textarea
{
	-moz-border-radius:0;
	-webkit-border-radius:0;
	border-radius:0;
	border:0;
	height:50px;
	border-bottom:1px solid #e8e8e8;
}

#hj-wall input[type="text"]
{
	-moz-border-radius:0;
	-webkit-border-radius:0;
	border-radius:0;
	border:0;

	color:#666;
	padding:4px;
	margin:0;
	border-top:1px dashed #e8e8e8;

}

#hj-wall select.elgg-input-dropdown
{
	margin:0;
}

.hj-wall-form-attachment {
	border-top:1px dashed #e8e8e8;
}

.hj-wall-form-bar {
	padding:0 5px;
	border-top:1px solid #e8e8e;
	border-bottom:1px solid #e8e8e;
	background:#f8f8f8;
}

.hj-wall-bar-controls
{
	float:right;
	padding:5px;
}

ul.hj-wall-bar-controls > li
{
	float:left;
	margin:2px 4px;
}

.hj-wall-tags-list {
	border:0;
	margin:0;
	padding:0;
}

.hj-wall-tags-list > li {
	border:1px solid #e8e8e8;
	background:#f4f4f4;
	float:left;
	margin:5px;
	padding:3px 6px;
	font-size:11px;

	-moz-border-radius:5px;
	-webkit-border-radius:5px;
	border-radius:5px;
}

.hj-wall-tags-list > li:hover {
	background:#f8f8f8;
}

.hj-wall-tags-list > li a.hj-wall-tag-remove {
	margin:0 0 0 5px;
	width:16px;
	height:16px;
	background: transparent url(<?php echo elgg_get_site_url() ?>mod/hypeWall/graphics/close.png) no-repeat;
}

.hj-wall-river-extras {
	text-decoration:italic;
	color:#666;
}

.elgg-icon.elgg-icon-wall-status,
.elgg-icon.elgg-icon-wall-post {
	background: transparent url(<?php echo elgg_get_site_url() ?>mod/hypeWall/graphics/note.png) no-repeat 50% 50%;
	height:20px;
	width:20px;
}

.elgg-icon.elgg-icon-wall-photo {
	background: transparent url(<?php echo elgg_get_site_url() ?>mod/hypeWall/graphics/photo.png) no-repeat 50% 50%;
	height:20px;
	width:20px;
}

.elgg-icon.elgg-icon-wall-file {
	background: transparent url(<?php echo elgg_get_site_url() ?>mod/hypeWall/graphics/file.png) no-repeat 50% 50%;
	height:20px;
	width:20px;
}

.elgg-module-widget .hj-list-wall img,
.elgg-module-widget #hj-wall img {
	max-width:250px;
}


/* jQuery UI Tabs 1.8.18
 *
 * Copyright 2011, AUTHORS.txt (http://jqueryui.com/about)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * http://docs.jquery.com/UI/Tabs#theming
 */
.ui-tabs { position: relative; padding: .2em; zoom: 1; } /* position: relative prevents IE scroll bug (element with position: relative inside container with overflow: auto appear as "fixed") */
.ui-tabs .ui-tabs-nav { margin: 0; padding: .2em .2em 0; }
.ui-tabs .ui-tabs-nav li { list-style: none; float: left; position: relative; top: 1px; margin: 0 .2em 1px 0; padding: 0; white-space: nowrap; }
.ui-tabs .ui-tabs-nav li a { float: left; padding: .5em 1em; text-decoration: none; }
.ui-tabs .ui-tabs-nav li.ui-tabs-selected { margin-bottom: 0; padding-bottom: 1px; font-weight:bold; }
.ui-tabs .ui-tabs-nav li.ui-tabs-selected a, .ui-tabs .ui-tabs-nav li.ui-state-disabled a, .ui-tabs .ui-tabs-nav li.ui-state-processing a { cursor: text; }
.ui-tabs .ui-tabs-nav li a, .ui-tabs.ui-tabs-collapsible .ui-tabs-nav li.ui-tabs-selected a { cursor: pointer; } /* first selector in group seems obsolete, but required to overcome bug in Opera applying cursor: text overall if defined elsewhere... */
.ui-tabs .ui-tabs-panel { display: block; background: none; }
.ui-tabs .ui-tabs-hide { display: none !important; }