<?php 
/*
Plugin Name: Hackadelic TOC Boxes
Version: 1.1.0
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

	var $DEFAULT_HINT = 'table of contents';

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
		$content = preg_replace_callback(
			array($regex1, $regex2),
			array(&$this, 'doHeader'),
			$content);
		
		return $content;
	}

	//-------------------------------------------------------------------------------------

	function doHeader($match) {
		global $id;
		$n = count($this->headers) + 1;
		$anchor = "toc-anchor-$id-$n";
		$this->headers[] = array(
			'level' => $match[1],
			'text' => $match[2],
			'anchor' => $anchor,
			);
		return '<a name="'.$anchor.'"></a>'.$match[0];
	}

	//-------------------------------------------------------------------------------------

	function doTOCShortcode($atts) {
		if (!$this->headers) return '';

		extract(shortcode_atts(array(
			'class' => '',
			'style' => '',
			'hint' =>$this->DEFAULT_HINT,
			), $atts ));

		$toc = '';
		foreach ($this->headers as $each) {
			extract($each);
			//-- To handle anchors in headings, either
			// a) separate TOC link from TOC title => will give us titles 1:1, bit not clickable
			//$toc .= "<li class=\"toc-level-$level\"><a rel=\"bookmark\" href=\"#$anchor\" title=\"Jump\">&nbsp;&raquo;&nbsp;</a>$text</li>";
			//-- Or b): Filter out anchor HTML => is it enough? any other elements to filter?
			$text = preg_replace(array('@<a>(.+?)</a>@i', '@<a\s+.*?>(.+?)</a>@i'), '\1', $text);
			$toc .= "<li class=\"toc-level-$level\"><a rel=\"bookmark\" href=\"#$anchor\" title=\"$text\">$text</a></li>";
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
	q = jQuery(selector);
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