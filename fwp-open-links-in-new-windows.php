<?php
/*
Plugin Name: FWP+: Open links in new windows
Plugin URI: http://projects.radgeek.com/fwp-open-links-in-new-windows
Description: enable FeedWordPress to rewrite syndicated posts so that links in them open in new windows
Version: 2009.1013
Author: Charles Johnson
Author URI: http://radgeek.com/
License: GPL
*/

// Setting: Only rewrite syndicated posts. If TRUE, then we only force links
// from syndicated posts to open in a new window. If FALSE, then we force links
// from ALL posts, syndicated or local, to open in a new window. Change at your
// discretion; you can't hurt much by it.
define('FWP_OLNW_ONLY_REWRITE_SYNDICATED_POSTS', true);

add_action(
	/*hook=*/ 'the_content',
	/*function=*/ array('FWPPlusOpenLinksInNewWindows', 'the_content'),
	/*priority=*/ 10002,
	/*arguments=*/ 1
);

class FWPPlusOpenLinksInNewWindows {
	function the_content ($content) {
		if (!FWP_OLNW_ONLY_REWRITE_SYNDICATED_POSTS or is_syndicated()) :
			$content = preg_replace_callback(
				'/<a \s+ ([^>]* href=[^>]*)>/ix',
				array('FWPPlusOpenLinksInNewWindows', 'addTargetAttribute'),
				$content
			);
		endif;
		return $content;
	}

	function addTargetAttribute ($matches) {
		preg_match_all(
			'/\s* ( [^="\']+ ) \s* = \s* (
				("|\')
				(((?!\\3).)*)
				\\3
			|
				([^\s]+)
			)/ix',
			$matches[1],
			$attr_matches,
			PREG_SET_ORDER
		);
		$attr = array();
		foreach ($attr_matches as $ref) :
			$key = $ref[1];
			$value = $ref[2];

			$attr[$key] = $value;
		endforeach;
		
		$attr['target'] = '"_blank"';

		$tag = '<a';
		foreach ($attr as $key => $value) :
			$tag .= ' '. $key . '=' . $value;
		endforeach;
		$tag .= '>';

		return $tag;
	}
}

