<?php 
/*
Plugin Name: Hackadelic TOC Boxes
Version: 1.2.1
Plugin URI: http://hackadelic.com/solutions/wordpress/toc-boxes
Description: Easy to use, freely positionable, fancy AJAX-style table of contents for WordPress posts and pages.
Author: Hackadelic
Author URI: http://hackadelic.com
*/
//---------------------------------------------------------------------------------------------

class HackadelicTOC
{
	var $maxLevel = 4;
	var $headers = array();
	var $tocID = 0;
	var $url = ''; // used during preg_replace callback

	var $DEFAULT_HINT = 'table of contents (click to expand/collapse)';

	//-------------------------------------------------------------------------------------

	function initialize() {
		add_shortcode('toc_usage', array(&$this, 'doTOCUsageShortcode'));
		add_action('wp_print_scripts', array(&$this, 'enqueueScripts'));
		add_filter('the_content', array(&$this, 'collectTOC'));
		add_shortcode('toc', array(&$this, 'doTOCShortcode'));
		add_action('wp_footer', array(&$this, 'doEpilogue'));
	}

	//-------------------------------------------------------------------------------------

	function doTOCUsageShortcode($atts, $content=null) {
		return '<p><code>[toc hint="<em>hover hint</em>" class="<em>extra CSS class</em>" style="<em>inline CSS style</em>"]</code></p>';
	}

	//-------------------------------------------------------------------------------------

	function enqueueScripts() {
		wp_enqueue_script('jquery');
	}

	//-------------------------------------------------------------------------------------

	function collectTOC($content) {
		$this->headers = array();
		$this->tocID = 0;

		if ( !is_single() && !is_page() ) return $content;

		$n = $this->maxLevel;
		$regex1 = '@<h([1-'.$n.'])>(.+)</h\1>@i';
		$regex2 = '@<h([1-'.$n.'])\s+.*?>(.+?)</h\1>@i';
		$pattern = array($regex1, $regex2);
		$callback = array(&$this, 'doHeader');

		global $multipage, $numpages, $pages, $page;
		for ($i = 1; $i <= $numpages; $i++) {
			if ($i == $page) { $in = $content; $out =& $content; $this->url = ''; }
			else { $in = $pages[$i-1]; unset($out); $this->url = $this->urlToPageNr($i); }
			$out = preg_replace_callback($pattern, $callback, $in);
		}
		return $content;
	}

	//-------------------------------------------------------------------------------------

	function urlToPageNr($i) {
		$arePermalinksBasic = 
			   '' == get_option('permalink_structure')
			|| in_array($post->post_status, array('draft', 'pending'));
		$url = ($i <= 1) ? get_permalink() : (
			$arePermalinksBasic
			? get_permalink() . '&amp;page=' . $i
			: trailingslashit(get_permalink()) . user_trailingslashit($i, 'single_paged') );
		//BEGIN workaround for conflict with plugin "Nofollow Reciprocity"
		$home = get_option( 'home' ); if (!$home) $home != get_option( 'siteurl' );
		$url = preg_replace( "@^$home@", '', $url);
		//END workaround for conflict with plugin "Nofollow Reciprocity"
		return $url;
	}

	//-------------------------------------------------------------------------------------

	function doHeader($match) {
		global $id;
		$n = count($this->headers) + 1;
		$anchor = "toc-anchor-$id-$n";
		$this->headers[] = array(
			'level' => $match[1],
			'text' => $match[2],
			'href' => "$this->url#$anchor",
			);
		return '<a class="toc-anchor" name="'.$anchor.'"></a>'.$match[0];
	}

	//-------------------------------------------------------------------------------------

	function doTOCShortcode($atts) {
		if (!$this->headers) return '';

		extract(shortcode_atts(array(
			'class' => '',
			'style' => '',
			'hint' => $this->DEFAULT_HINT,
			), $atts ));

		$toc = '';
		foreach ($this->headers as $each) {
			extract($each);
			//-- To handle anchors in headings, either
			// a) separate TOC link from TOC title => will give us titles 1:1, but not clickable
			//$toc .= "<li class=\"toc-level-$level\"><a rel=\"bookmark\" href=\"$href\" title=\"Jump\">&nbsp;&raquo;&nbsp;</a>$text</li>";
			//-- Or b): Filter out anchor HTML => is it enough? any other elements to filter?
			$text = preg_replace(array('@<a>(.+?)</a>@i', '@<a\s+.*?>(.+?)</a>@i'), '\1', $text);
			$toc .= "<li class=\"toc-level-$level\"><a rel=\"bookmark\" href=\"$href\" title=\"$text\">$text</a></li>";
		}

		global $id;
		$tocID = ++$this->tocID;
		$tocID = "toc-$id-$tocID";

		if ($class) $class = ' '.$class;
		if ($style) $style = ' style="'.$style.'"';
		$clickCode = "jQuery('#$tocID').slideToggle('fast')";
		$titleAttr = $hint ? " title=\"$hint\"" : '';
		$titleSpan = '';

		$toc = '<div class="toc'.$class.'"'.$style.'>'
			.'<a class="toc-header" href="javascript:;"'.$titleAttr
			.' onclick="'.$clickCode.'">'.$titleSpan.'&nabla;</a>'
			.'<ul id="'.$tocID.'">'.$toc.'</ul>'
			.'</div>';

		return $toc;
	}

	//-------------------------------------------------------------------------------------

	function doEpilogue() {
?>
<!-- Hackadelic Table Of Contents, http://hackadelic.com -->
<script type="text/javascript">
function toggleDisplayOf(selector, onval) {
	var q = jQuery(selector);
	if (!q) return;
	if (q.css('display') == 'none')
		q.css('display', onval);
	else
		q.css('display', 'none');
}
</script>
<?php
	}
}

//---------------------------------------------------------------------------------------------

if (!is_admin()) {
	$tocBuilder = new HackadelicTOC();
	$tocBuilder->initialize();
}
?>