<?php 
/*
Plugin Name: Hackadelic TOC Boxes
Version: 1.3.0a
Plugin URI: http://hackadelic.com/solutions/wordpress/toc-boxes
Description: Easy to use, freely positionable, fancy AJAX-style table of contents for WordPress posts and pages.
Author: Hackadelic
Author URI: http://hackadelic.com
*/
// ===========================================================================
// Foundation
// ===========================================================================

class HackadelicTOCContext
{
	function CTXID() { return get_class($this); }

	// I18N -------------------------------------------------------------------------------

	function t($s) { return __($s, $this->CTXID());	}
	function e($s) { _e($s, $this->CTXID());	}

	// Option Access ----------------------------------------------------------------------

	function fullname($name) {
		return $this->CTXID() . '__' . $name;
	}
	function load_option(&$option, $name, $eval=null) {
		$name = $this->fullname($name);
		$value = get_option($name);
		if ($value == null) return false;
		$option = ($eval == null) ? $value : call_user_func($eval, $value);
		return true;
	}
	function save_option(&$option, $name) {
		$name = $this->fullname($name);
		update_option($name, $option);
	}
	function erase_option(&$option, $name) {
		$name = $this->fullname($name);
		delete_option($name);
		$option = null;
	}
}

// ===========================================================================
// Main
// ===========================================================================

class HackadelicTOC extends HackadelicTOCContext
{
	var $MAX_LEVEL = 4;
	var $DEFAULT_CLASS = '';
	var $DEFAULT_STYLE = '';
	var $DEFAULT_HINT = 'table of contents (click to expand/collapse)';
	var $AUTO_INSERT = '';

	//-------------------------------------------------------------------------------------

	var $maxLevel = 4;
	var $headers = array();
	var $tocID = 0;
	var $url = ''; // used during preg_replace callback

	//-------------------------------------------------------------------------------------

	function HackadelicTOC() {
	//function initialize() {
		$this->load_option($this->MAX_LEVEL, 'MAX_LEVEL', intval);
		$this->load_option($this->DEFAULT_CLASS, 'DEFAULT_CLASS');
		$this->load_option($this->DEFAULT_STYLE, 'DEFAULT_STYLE');
		$this->load_option($this->DEFAULT_HINT, 'DEFAULT_HINT');
		$this->load_option($this->AUTO_INSERT, 'AUTO_INSERT', trim);

		if (is_admin()) {
			add_action('admin_menu', array(&$this, 'addAdminMenu'));
		}
		else {
			add_shortcode('toc_usage', array(&$this, 'doTOCUsageShortcode'));
			add_action('wp_print_scripts', array(&$this, 'enqueueScripts'));
			add_filter('the_content', array(&$this, 'collectTOC'));
			add_shortcode('toc', array(&$this, 'doTOCShortcode'));
			if ($this->AUTO_INSERT) add_filter('the_content', array(&$this, 'autoInsertTOC'), 12);
			add_action('wp_footer', array(&$this, 'doEpilogue'));
		}
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

	function autoInsertTOC($content) {
		if ($this->shortcodeWasHere) return $content;
		$toc = $this->insertTOC($this->DEFAULT_CLASS, $this->DEFAULT_STYLE, $this->DEFAULT_HINT);
		if ($this->AUTO_INSERT == 'before') return $toc . $content;
		if ($this->AUTO_INSERT == 'after') return $content . $toc;
		return $content;
	}

	//-------------------------------------------------------------------------------------

	function collectTOC($content) {
		$this->headers = array();
		$this->tocID = 0;
		$this->shortcodeWasHere = false;

		if ( !is_single() && !is_page() ) return $content;

		$n = $this->MAX_LEVEL;
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
		$this->shortcodeWasHere = true;

		extract(shortcode_atts(array(
			'class' => $this->DEFAULT_CLASS,
			'style' => $this->DEFAULT_STYLE,
			'hint' => $this->DEFAULT_HINT,
			'auto' => '',
			), $atts ));

		//$auto = strtolower($auto);
		return $auto == 'off' ? '' : $this->insertTOC($class, $style, $hint);
	}

	function insertTOC($class, $style, $hint) {
		if (!$this->headers) return '';
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

	//=====================================================================================
	// ADMIN
	//=====================================================================================

	function addAdminMenu() {
		$title = 'Hackadelic TOC';
		add_options_page($title, $title, 10, __FILE__, array(&$this, 'displayOptionsPage'));
	}

	//-------------------------------------------------------------------------------------

	function displayOptionsPage() {
		include 'hackadelic-toc-settings.php';
	}
}

//---------------------------------------------------------------------------------------------

new HackadelicTOC();

?>