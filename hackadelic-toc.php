<?php 
/*
Plugin Name: Hackadelic TOC Boxes
Version: 1.6.0dev1
Plugin URI: http://hackadelic.com/solutions/wordpress/toc-boxes
Description: Easy to use, freely positionable, fancy AJAX-style table of contents for WordPress posts and pages.
Author: Hackadelic
Author URI: http://hackadelic.com
*/
// ===========================================================================
// Foundation
// ===========================================================================

add_action('plugins_loaded', array('HackadelicTOC', 'start'));

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
		$oldvalue = $option;
		$option = ($eval == null) ? $value : call_user_func($eval, $value);
		return $value != $oldvalue;
	}
	function save_option(&$option, $name) {
		$name = $this->fullname($name);
		update_option($name, $option);
	}
	function erase_option($name) {
		$name = $this->fullname($name);
		delete_option($name);
	}
}

// ===========================================================================
// Main
// ===========================================================================

//class HackadelicTOCSettings
//{
//	var $MAX_LEVEL = 4;
//	var $REL_ATTR = 'bookmark nofollow';
//	var $BCOMPAT_ANCHORS = true;
//
//	var $DEF_TITLE = /*'&nabla; '.*/'In this writing:';
//	var $DEF_CLASS = '';
//	var $DEF_STYLE = '';
//	var $DEF_HINT = 'table of contents (click to expand/collapse)';
//	var $DEF_ENHANCE = 'comments';
//
//	var $AUTO_INSERT = '';
//	var $AUTO_CLASS = ''; // used with AUTO_INSERT
//	var $AUTO_STYLE = ''; // used with AUTO_INSERT
//
//	var $INCL_COMMENTS = true;
//
//	function load($ctx) {
//		$ctx->load_option($this->MAX_LEVEL, 'MAX_LEVEL', 'intval');
//		$ctx->load_option($this->REL_ATTR, 'REL_ATTR', 'trim');
//		$ctx->load_option($this->BCOMPAT_ANCHORS, 'BCOMPAT_ANCHORS', create_function('$s', 'return trim($s) == "on"'));
//
//		$ctx->load_option($this->DEF_TITLE, 'DEF_TITLE');
//		$ctx->load_option($this->DEF_CLASS, 'DEF_CLASS', 'trim');
//		$ctx->load_option($this->DEF_STYLE, 'DEF_STYLE', 'trim');
//		$ctx->load_option($this->DEF_HINT, 'DEF_HINT');
//
//		$ctx->load_option($this->AUTO_INSERT, 'AUTO_INSERT', 'trim');
//		$ctx->load_option($this->AUTO_CLASS, 'AUTO_CLASS', 'trim');
//		$ctx->load_option($this->AUTO_STYLE, 'AUTO_STYLE', 'trim');
//	}
//}

class HackadelicTOC extends HackadelicTOCContext
{
	var $VERSION = '1.6.0dev1';

	//-------------------------------------------------------------------------------------
	// Options:

	var $MAX_LEVEL = 4;
	var $REL_ATTR = 'bookmark nofollow';
	var $BCOMPAT_ANCHORS = true;

	var $DEF_TITLE = /*'&nabla; '.*/'In this writing:';
	var $DEF_CLASS = '';
	var $DEF_STYLE = '';
	var $DEF_HINT = 'table of contents (click to expand/collapse)';
	var $DEF_ENHANCE = 'comments';

	var $AUTO_INSERT = '';
	var $AUTO_CLASS = ''; // used with AUTO_INSERT
	var $AUTO_STYLE = ''; // used with AUTO_INSERT

	var $op; // bundled option references

	//-------------------------------------------------------------------------------------
	// State & instance variables:

	var $headers = array();
	var $tocID = 0;
	var $url = ''; // used during preg_replace callback

	//-------------------------------------------------------------------------------------

	function HackadelicTOC() {
		$this->initOptionsMap();
		$this->loadOptions();

		//die(print_r(compact($this->MAX_LEVEL, $this->REL_ATTR), true));

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

	function start() {
		$me = new HackadelicTOC();
		//NOTE: Interestingly, the following call does not work inside an instance method.
		//      However, it works here, as this method is invoked statically.
		register_deactivation_hook(__FILE__, array(&$me, 'uninstall'));
	}

	function uninstall() {
		delete_option($this->CTXID());
	}

	//-------------------------------------------------------------------------------------
	// Core:

	function doTOCUsageShortcode($atts, $content=null) {
		return '
<h5>Normal usage (all parameters are optional):</h5>
<pre class="syntax-highlight:html">
[toc title="TOC box title" hint="hover hint"
     class="extra CSS class" style="inline CSS style"]
</pre>
<h5>Suppressing auto-insertion:</h5>
<pre class="syntax-highlight:html">[toc auto=off]</pre>';
	}

	//-------------------------------------------------------------------------------------

	function enqueueScripts() {
		wp_enqueue_script('jquery');
	}

	//-------------------------------------------------------------------------------------

	function autoInsertTOC($content) {
		if ($this->shortcodeWasHere) return $content;
		$this->setVar($class, $this->AUTO_CLASS, $this->DEF_CLASS);
		$this->setVar($style, $this->AUTO_STYLE, $this->DEF_STYLE);
		$toc = $this->renderAutoTOC($class, $style);
		if ($this->AUTO_INSERT == '@start') return $toc . $content;
		if ($this->AUTO_INSERT == '@end') return $content . $toc;
		if ($this->AUTO_INSERT == '@start+end') return $toc . $content . $this->renderAutoTOC($class, $style);
		return $content;
	}

	//-------------------------------------------------------------------------------------

	function renderAutoTOC($class, $style) {
		return $this->renderTOC($class, $style, $this->DEF_HINT, $this->DEF_TITLE, $this->DEF_ENHANCE);
	}

	//-------------------------------------------------------------------------------------

	function setVar(&$var, $mainVal, $auxVal) {
		$var = $mainVal ? $mainVal : $auxVal;
	}

	//-------------------------------------------------------------------------------------

	function collectTOC($content) {
		$this->headers = array();
		$this->tocID = 0;
		$this->shortcodeWasHere = false;

		if ( !is_single() && !is_page() ) return $content;

		$n = $this->MAX_LEVEL;
		//--
		//-- Derived from Artem's patch, see http://wordpress.org/support/topic/268259 :
		//--
		// Replacing of the following 3 lines,
		/*
		$regex1 = '@<h([1-'.$n.'])\s+.*?>(.+?)</h\1>@i';
		$regex2 = '@<h([1-'.$n.'])>(.+)</h\1>@i';
		$pattern = array($regex1, $regex2);
		*/
		// With this one:
		$pattern = '@<h([1-'.$n.'])(?:\s+.*?)?>(.+?)</h\1>@i';
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
		global $post;
		$arePermalinksBasic = 
			   '' == get_option('permalink_structure')
			|| in_array($post->post_status, array('draft', 'pending'));
		$posturl = get_permalink();
		$url = ($i <= 1) ? $posturl : (
			$arePermalinksBasic
			? $posturl . '&amp;page=' . $i
			: trailingslashit($posturl) . user_trailingslashit($i, 'single_paged') );
		//BEGIN workaround for conflict with plugin "Nofollow Reciprocity"
		$url = preg_replace( "@.*://[^/]*@i", '', $url);
		//END workaround for conflict with plugin "Nofollow Reciprocity"
		return $url;
	}

	//-------------------------------------------------------------------------------------

	function doHeader($match) {
		global $id;
		$n = count($this->headers) + 1;
		$anchor0 = "toc-anchor-$id-$n";
		//--
		// strip_tags hint contributed by Artem, see http://wordpress.org/support/topic/268568
		//--
		$text = strip_tags($match[2]);
		$anchor = sanitize_title( $text, $anchor0 );
		$this->headers[] = array(
			'level' => $match[1],
			'text' => $text, 
			'href0' => "$this->url#$anchor0",
			'href' => "$this->url#$anchor",
			);
		$result = '<a class="toc-anchor" name="'.$anchor.'"></a>';
		if ($this->BCOMPAT_ANCHORS) $result .= '<a class="toc-anchor" name="'.$anchor0.'"></a>';
		return $result . $match[0];
	}

	//-------------------------------------------------------------------------------------

	function doTOCShortcode($atts) {
		$this->shortcodeWasHere = true;
		extract(shortcode_atts(array(
			'title' => $this->DEF_TITLE,
			'class' => $this->DEF_CLASS,
			'style' => $this->DEF_STYLE,
			'hint' => $this->DEF_HINT,
			'enhance' => $this->DEF_ENHANCE,
			'auto' => '',
			), $atts ));
		return $auto == 'off' ? '' : $this->renderTOC($class, $style, $hint, $title, $enhance);
	}

	function renderTOC($class, $style, $hint, $title, $enhance) {
		if (!$this->headers) return '';
		$toc = '';
		$rel = $this->REL_ATTR;
		foreach ($this->headers as $each) {
			extract($each);
/*			This whole block is superfluous, now that the text is strip_tags()'ed in collectTOC
			//-- To handle anchors in headings, either
			// a) separate TOC link from TOC title => will give us titles 1:1, but not clickable
			//$toc .= "<li class=\"toc-level-$level\"><a rel=\"bookmark\" href=\"$href\" title=\"Jump\">&nbsp;&raquo;&nbsp;</a>$text</li>";
			//-- Or b): Filter out anchor HTML => is it enough? any other elements to filter?
			$text = preg_replace(array('@<a>(.+?)</a>@i', '@<a\s+.*?>(.+?)</a>@i'), '\1', $text);
*/
			$toc .= "<li class=\"toc-level-$level\"><a rel=\"$rel\" href=\"$href\" title=\"$text\">$text</a></li>";
		}

		//--
		//-- derived from johnbillion's patch to include link to comments in the toc
		//-- (see http://hackadelic.com/toc-boxes-151-release#comment-1025)
		//--
		//$enhancements = explode('+', $enhance); // <= possible future extensions
		//foreach ($enhancements as $enhance):
			if ($enhance == 'comments'):
				global $post; $n = $post->comment_count;
				if ( $n ): #have_comments() won't work here
					$sComments = __( 'Comments' );
					$toc .= '<li class="toc-level-2"><a rel="' . $rel
						. '" href="#comments" title="' . $sComments . '">' . 
						"$sComments ($n)</a></li>";
				endif;
			endif;
		//endforeach;

		global $id;
		$tocID = ++$this->tocID;
		$tocID = "toc-$id-$tocID";
		$boxID = "$tocID-box";

		if ($class) $class = ' '.$class;
		if ($style) $style = ' style="'.$style.'"';

		//$clickCode = "jQuery('#$tocID').slideToggle('fast')";
		$clickCode = "tocToggle('#$tocID', '#$boxID')";
		$titleAttr = $hint ? " title=\"$hint\"" : '';

		$tochdr = '<a class="toc-header" href="javascript:;"'.$titleAttr
		        . ' onclick="'.$clickCode.'">'.$title.'</a>';
		$toc = '<div id="'.$boxID.'" class="toc'.$class.'"'.$style.'>'.$tochdr
		     . '<ul id="'.$tocID.'">'.$toc.'</ul>'
			 . '</div>';
		return $toc;
	}

	//-------------------------------------------------------------------------------------

	function doEpilogue() {
?>
<!-- Hackadelic TOC Boxes <?php echo $this->VERSION ?>, http://hackadelic.com -->
<span style="display:none">This site uses <a href="http://hackadelic.com/solutions/wordpress/toc-boxes">Hackadelic TOC Boxes <?php echo $this->VERSION ?></a>, a <a href="http://hackadelic.com">Hackadelic</a> PlugIn.</span>
<script type="text/javascript">
function tocToggle(toc, box) {
	var q = jQuery(toc);
	if (!q) return;
	q.slideToggle('fast', function() {
		jQuery(box).toggleClass('toc-collapsed', q.css('display') == 'none');
	});
}
</script>
<?php
	}

	//=====================================================================================
	// AUX
	//=====================================================================================

	function assignTo(&$var, $value) {
		settype($value, gettype($var));
		$var = $value;
	}

	//=====================================================================================
	// ADMIN
	//=====================================================================================

	function addAdminMenu() {
		$title = 'Hackadelic TOC';
		add_options_page($title, $title, 10, __FILE__, array(&$this, 'handleOptions'));
	}

	//-------------------------------------------------------------------------------------

	function handleOptions() {
		$actionURL = $_SERVER['REQUEST_URI'];
		$context = $this->CTXID();
		$options = $this->op;
		$updated = false;
		$status = '';
		if ( $_POST['action'] == 'update' ) {
			check_admin_referer($context);
			//die(sprintf('<PRE>%s</PRE>', print_r($_POST, true)));
			if (isset($_POST['submit'])):
				foreach ($options as $key => $val):
					$bistate = $key == 'BCOMPAT_ANCHORS';
					if ($bistate):
						$newval = isset($_POST[$key]);
					else:
						if ( !isset($_POST[$key]) ) continue;
						$newval = trim( $_POST[$key] );
					endif;
					if ( $newval == $val ) continue;
					$this->assignTo($options->$key, $newval);
					$updated = true; $status = 'updated';
				endforeach;
				//die(sprintf('<PRE>%s</PRE>', print_r($options, true)));
				if ($updated): update_option($context, $options); endif;
			elseif (isset($_POST['reset'])):
				//echo sprintf('<PRE>%s</PRE>', print_r($_POST, true));
				delete_option($context);
				$updated = true; $status = 'reset';
			endif;
		}
		include 'hackadelic-toc-settings.php';
		//echo(sprintf('<PRE>%s</PRE>', print_r($options, true)));
	}

	//-------------------------------------------------------------------------------------

	function initOptionsMap() {
		$opnames = array(
			'MAX_LEVEL', 'REL_ATTR', 'BCOMPAT_ANCHORS',
			'DEF_TITLE', 'DEF_CLASS', 'DEF_STYLE', 'DEF_HINT', 'DEF_ENHANCE',
			'AUTO_INSERT', 'AUTO_CLASS', 'AUTO_STYLE',
		);
		$this->op = (object) array();
		foreach ($opnames as $name)
			$this->op->$name = &$this->$name;
	}

	function loadOptions() {
		$context = $this->CTXID();
		$options = $this->op;
		$saved = get_option($context);
		if ($saved) foreach ( (array) $options as $key => $val ) {
			if (!isset($saved->$key)) continue;
			$this->assignTo($options->$key, $saved->$key);
		}
		// Backward compatibility hack, to be removed in 
		$this->migrateOptions($options, $context);
	}

	//-------------------------------------------------------------------------------------

	function migrateOptions(&$options, $context) {
		// Backward compatibility hack
		$anychange = false;
		foreach ( (array) $options as $key => $val ):
			// 1) load options from prior version
			$bistate = $key == 'BCOMPAT_ANCHORS';
			$v = $bistate ? ($val ? 'on' : 'off') : (string) $val;
			$modified = $this->load_option($v, $key, 'trim');
			$val = $bistate ? ($v == 'on') : $v;
			if ($modified):
				$this->assignTo($options->$key, $val);
				$anychange = true;
			endif;
			// 2) erase options from prior version
			$this->erase_option($key);
			//print("<p class=\"updated fade\">Option migrated $key: $v -&gt; $val; (Modified: $modified; Any change: $anychange)</p>");
		endforeach;
		// 3) save options in new format
		if ($anychange) update_option($context, $options);
	}
}

?>