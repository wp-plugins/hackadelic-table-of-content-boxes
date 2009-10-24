<?php
if ( !defined('ABSPATH') )
	exit("Sorry, you are not allowed to access this page directly.");
if ( !isset($this) || !is_a($this, HackadelicTOC) )
	exit("Invalid operation context.");

$sections = array(
	(object) array(
		'title' =>  'General TOC Settings',
		'help' => 'Basic settings. Contrast to other settings, these are not settable via shortcode parameters.',
		'options' => array(
			(object) array(
				'title' => 'Maximum Heading Level',
				'key' => 'MAX_LEVEL', //'val' => $this->MAX_LEVEL,
				'style' => 'max-width: 5em',
				'help' => 'Maximum heading level in a TOC' ),
			(object) array(
				'title' => 'TOC Anchor Backward Compatibility',
				'key' => 'BCOMPAT_ANCHORS', //'val' => $this->BCOMPAT_ANCHORS,
				'style' => 'max-width: 5em',
				'text' => 'Keep old style anchors?',
				'help' => 'Whether to keep old-style anchors <u>in addition</u> to new style achnors. Required only if your blog contained explicit links to old-style TOC anchors (such as /myblog/some-post<b>#toc-17-2</b>), that would break by switching to new style anchors.' ),
			(object) array(
				'title' => 'TOC Link REL Attribute Value',
				'key' => 'REL_ATTR', //'val' => $this->REL_ATTR,
				'help' => 'Value of the <tt>rel</tt> attribute of links in TOC boxes. Convention is to set it to "bookmark", but other attributes can be added, too. For example, you can add "nofollow" to prevent SEO juice from spreading to secondary pages in multipage posts.' ),
			(object) array(
				'title' => 'Dynamic Effects',
				'text' => 'Suppress dynamic effects (javascript)',
				'key' => 'NOEFFECTS', //'val' => $this->REL_ATTR,
				'style' => 'max-width: 5em', ),
		)),
	(object) array(
		'title' => 'Shortcode Parameter Defaults',
		'help' => 'Useful when you think you would pass the same paramter value <em>in most cases</em> anyway. For example, if most of your TOC boxes are floating to the right, you can make the default CSS class <tt>toc-right</tt>, and just use <tt>[toc]</tt> instead of <tt>[toc class=toc-right]</tt> in the posts.<br /><br /><strong>Beware though that changing the default values after you already have lots of TOC boxes in your blog is problematic, as your old TOC boxes may change appearence and/or positions. It is best to decide for a set of default values and stick to them.</strong>',
		'options' => array(
			(object) array(
				'title' => 'Default TOC Box Title',
				'key' => 'DEF_TITLE', //'val' => htmlentities($this->DEF_TITLE),
				'help' => 'The default TOC box title when none is given via shortcode parameter.' ),
			(object) array(
				'title' => 'Default CSS Class',
				'key' => 'DEF_CLASS', //'val' => $this->DEF_CLASS,
				'help' => 'The default CSS class when none is given via shortcode parameter.' ),
			(object) array(
				'title' => 'Default Inline CSS Style',
				'key' => 'DEF_STYLE', //'val' => htmlentities($this->DEF_STYLE),
				'help' => 'The default inline CSS style when none is given via shortcode parameter.' ),
			(object) array(
				'title' => 'Default Hint',
				'key' => 'DEF_HINT', //'val' => htmlentities($this->DEF_HINT),
				'help' => 'The default hint text when none is given via shortcode parameter.' ),
			(object) array(
				'title' => 'Default TOC Enhancements',
				'key' => 'DEF_ENHANCE', //'val' => $this->DEF_ENHANCE,
				'help' => 'The default TOC box enhancements. Currently only "<tt>comments</tt>" is supported.' ),
		)),
	(object) array(
		'title' => 'Automatic Insertion Settings',
		'help' => 'Settings to control position and appearence of auto-inserted TOC boxes.',
		'options' => array(
			(object) array(
				'title' => 'Automatic Insertion',
				'key' => 'AUTO_INSERT', //'val' => $this->AUTO_INSERT,
				'style' => 'max-width: 10em',
				'help' => 'Controls automatic TOC box insertion into posts and pages:<br />&nbsp;&nbsp;<tt>@start</tt> = insert BEFORE content<br />&nbsp;&nbsp;<tt>@end</tt> = insert AFTER content<br />&nbsp;&nbsp;<tt>@start+end</tt> = insert before AND after content<br />&nbsp;&nbsp;empty or anything else = do not insert automatically' ),
			(object) array(
				'title' => 'CSS Class For Auto-Insertion',
				'key' => 'AUTO_CLASS', //'val' => $this->AUTO_CLASS,
				'help' => 'The CSS class used for automatically inserted TOC boxes.<br />If none specified, the default CSS class above will be used instead.' ),
			(object) array(
				'title' => 'CSS Style For Auto-Insertion',
				'key' => 'AUTO_STYLE', //'val' => htmlentities($this->AUTO_STYLE),
				'help' => 'The inline CSS style used for automatically inserted TOC boxes.<br />If none specified, the default CSS style above will be used instead.' ),
		)),
	);

?>
<?php // ------------------------------------------------------------------------------------ ?>
<style type="text/css">
<?php
	$R = '3px';
	$sideWidth = '13em';
?>
a.button { display: inline-block; margin: 5px 0 }

dl { padding: 0; margin: 10px 1em 20px 0; background-color: white; border: 1px solid #ddd; }
dt { font-size: 10pt; font-weight: bold; margin: 0; padding: 4px 10px 4px 10px;
	background: #dfdfdf url(<?php echo admin_url('images/gray-grad.png') ?>) repeat-x left top;
<?php	/*
	background: #dfdfdf url(http://lh4.ggpht.com/_eYaV9fZ6qRg/SkFP0KzGcXI/AAAAAAAAALA/aQJlXvTd-IE/s800/bg-pane-header-gray.png) repeat-x left top;
	border-bottom: 1px solid #ddd;
*/ ?>
}
dd { margin: 0; padding: 10px 20px 10px 20px }
dl {<?php foreach (array('-moz-', '-khtml-', '-webkit-', '') as $pfx) echo " {$pfx}border-radius: $R;" ?> }

dd .caveat { font-weight: bold; color: #C00; text-align: center }

.box { border: 1px solid #ccc; padding: 5px; margin: 5px }
.help { background-color: whitesmoke }

</style>
<?php // ------------------------------------------------------------------------------------ ?>
<div class="wrap">
<div id="icon-options-general" class="icon32"><br /></div>
<h2>Hackadelic SEO Table Of Contents</h2>

<?php
$slugWP = 'table-of-content-boxes';
$slugHome = 'toc-boxes';
//include 'hackadelic-toc-admx.php';
include 'common/xadm.php';

$helpicon = 'http://lh3.ggpht.com/_eYaV9fZ6qRg/SkFS5WKMVRI/AAAAAAAAALE/BH-09LuNRg8/s800/help.png';
?>

<?php // ------------------------------------------------------------------------------------ ?>
<?php if ($updated) : ?>
<div class="updated fade"><p>Plugin settings <?php echo ($status == 'reset') ? 'reset to default values and deleted from database. If you want to, you can safely remove the plugin now' : 'saved' ?>.</p></div>
<?php endif ?>

<?php // ------------------------------------------------------------------------------------ ?>
<?php if ( $updated && $status == 'reset') : ?>

<p class="submit" align="center">
	<a class="button" href="<?php echo $actionURL ?>">Back To Settings ...</a>
</p>

<?php // ------------------------------------------------------------------------------------ ?>
<?php else: ?>

<form method="post">
	<input type="hidden" name="action" value="update" />
	<?php wp_nonce_field($context); ?>

<?php foreach ($sections as $s) : $snr += 1; $shlpid = "shlp-$snr" ?>
<dl>
	<dt><?php echo $s->title ?><?php 
	if ($s->help) :
		?> <a href="javascript:;" onclick="jQuery('#<?php echo $shlpid ?>').slideToggle('fast')"><img src="<?php
			echo $helpicon ?>" /></a><?php
	endif ?></dt>
	<dd>
<?php if ($s->help) : ?>
	<div id="<?php echo $shlpid ?>" class="hidden help box"><?php echo $s->help ?></div>
<?php endif ?>

		<table class="form-table" style="clear:none">
<?php foreach ($s->options as $o) :
	$key = $o->key;
	$v = $options->$key; $t = gettype($v);

	$type = ' type="' . (is_bool($v) ? 'checkbox' : 'text') . '"';
	$style = $o->style ? " style=\"$o->style\"" : 'style="width:100%"';
	$value = is_bool($v) ? ($v ? ' checked="checked"' : '') : ' value="'.$v.'"';
	$name = ' name="'.$key.'"';
	$attr = $type . $style . $name . $value;
	unset($type, $style, $name, $value);
	$text = $o->text ? " <span>$o->text</span>" : '';
?>
		<tr>
			<th scope="row"><?php echo $o->title ?></th>
			<td>
				<div style="vertical-align:bottom"><input<?php echo $attr ?> /><?php echo $text ?></div>
				<div><em><?php echo $o->help ?></em></div>
			</td>
		</tr>
<?php endforeach ?>
		</table>
	</dd>
</dl>
<?php endforeach ?>

	<p class="submit" align="center">
		<input type="submit" name="submit" value="<?php _e('Save Settings') ?>"  title="This will store the settings to the database." />
		<input type="submit" name="reset" value="<?php _e('Reset Settings') ?>" title="This will remove the settings from the database, giving you the factory defaults"/>
	</p>
</form>

<?php endif // if ($status) ... ?>
</div>
