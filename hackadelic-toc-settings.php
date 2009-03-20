<?php
if ( !defined('ABSPATH') )
	exit("Sorry, you are not allowed to access this page directly.");
if ( !isset($this) || !is_a($this, HackadelicTOC) )
	exit("Invalid operation context.");

$options = array(
	array(
		'section' => 'General TOC Settings',
		'optitle' => 'Maximum Heading Level',
		'opstyle' => 'max-width: 5em',
		'opkey' => $this->fullname('MAX_LEVEL'),
		'opval' => $this->MAX_LEVEL,
		'ophlp' => 'Maximum heading level included in TOC boxed' ),
	array(
		'opstyle' => 'max-width: 5em',
		'optitle' => 'TOC Anchor Backward Compatibility',
		'opkey' => $this->fullname('BCOMPAT_ANCHORS'),
		'opval' => $this->BCOMPAT_ANCHORS,
		'ophlp' => 'Whether to keep old-style anchors <u>in addition</u> to new style achnors. Required only if your blog contained explicit links to old-style TOC anchors (such as /myblog/some-post<b>#toc-17-2</b>), that would break by switching to new style anchors. To disable backward compatibility mode, set it to empty or "off".' ),
	array(
		'optitle' => 'TOC Link REL Attribute Value',
		'opkey' => $this->fullname('REL_ATTR'),
		'opval' => $this->REL_ATTR,
		'ophlp' => 'Value of the <tt>rel</tt> attribute of links in TOC boxes. Convention is to set it to "bookmark", but other attributes can be added, too. For example, you can add "nofollow" to prevent SEO juice from spreading to secondary pages in multipage posts.' ),
	array(
		'section' => 'Shortcode Paramter Defaults',
		'sechelp' => 'Useful when you think you would pass the same paramter value <em>in most posts</em> anyway. For example, if most of your TOC boxes are floating to the right, you can make the default CSS class <tt>toc-right</tt>, and just use <tt>[toc]</tt> instead of <tt>[toc class=toc-right]</tt> in the posts.<br /><br /><strong>Beware though that changing the default values after you already have lots of TOC boxes in your blog is problematic, as your old TOC boxes may change appearence and/or positions. It is best to decide for a set of default values and stick to them.</strong>',
		'optitle' => 'Default TOC Box Title',
		'opkey' => $this->fullname('DEF_TITLE'),
		'opval' => htmlentities($this->DEF_TITLE),
		'ophlp' => 'The default TOC box title when none is given via shortcode parameter.' ),
	array(
		'optitle' => 'Default CSS Class',
		'opkey' => $this->fullname('DEF_CLASS'),
		'opval' => $this->DEF_CLASS,
		'ophlp' => 'The default CSS class when none is given via shortcode parameter.' ),
	array(
		'optitle' => 'Default Inline CSS Style',
		'opkey' => $this->fullname('DEF_STYLE'),
		'opval' => htmlentities($this->DEF_STYLE),
		'ophlp' => 'The default inline CSS style when none is given via shortcode parameter.' ),
	array(
		'optitle' => 'Default Hint',
		'opkey' => $this->fullname('DEF_HINT'),
		'opval' => htmlentities($this->DEF_HINT),
		'ophlp' => 'The default hint text when none is given via shortcode parameter.' ),
	array(
		'section' => 'Automatic Insertion Settings',
		'optitle' => 'Automatic Insertion',
		'opstyle' => 'max-width: 10em',
		'opkey' => $this->fullname('AUTO_INSERT'),
		'opval' => $this->AUTO_INSERT,
		'ophlp' => 'Controls automatic TOC box insertion into posts and pages:<br />&nbsp;&nbsp;<tt>@start</tt> = insert BEFORE content<br />&nbsp;&nbsp;<tt>@end</tt> = insert AFTER content<br />&nbsp;&nbsp;<tt>@start+end</tt> = insert before AND after content<br />&nbsp;&nbsp;empty or anything else = do not insert automatically' ),
	array(
		'optitle' => 'CSS Class For Auto-Insertion',
		'opkey' => $this->fullname('AUTO_CLASS'),
		'opval' => $this->AUTO_CLASS,
		'ophlp' => 'The CSS class used for automatically inserted TOC boxes.<br />If none specified, the default CSS class above will be used instead.' ),
	array(
		'optitle' => 'CSS Style For Auto-Insertion',
		'opkey' => $this->fullname('AUTO_STYLE'),
		'opval' => htmlentities($this->AUTO_STYLE),
		'ophlp' => 'The inline CSS style used for automatically inserted TOC boxes.<br />If none specified, the default CSS style above will be used instead.' ),
);

$slugWP = 'table-of-content-boxes';
$slugHome = 'toc-boxes';
$admPageTitle = 'Hackadelic TOC '.$this->t('Settings');
include 'hackadelic-toc-admx.php';
?>

<form method="post" action="options.php">
<?php wp_nonce_field('update-options'); ?>

<table class="form-table" style="clear:none">

<?php $nr = 0;
foreach ($options as $each) :
	++$nr; unset($section); unset($sechelp); unset($opstyle); extract($each); $oplist[] = $opkey
?>
<?php if (isset($section)) : ?>
<tr style="border-bottom: 1px solid #ccc">
<th colspan="2">
<?php if (!isset($sechelp)) : ?>
	<strong><?php _e($section) ?>:</strong>
<?php else: ?>
	<strong style="cursor:help" onclick="jQuery('#sechelp-<?php echo $nr ?>').slideToggle('fast')"><?php _e($section) ?>:</strong>
	<div class="hidden" id="sechelp-<?php echo $nr ?>" style="font-size: .85em; border: 1px solid #ccc; margin: 1em; padding: 1em" >
	<em><?php _e($sechelp) ?></em>
	</div>
<?php endif ?>
</th>
</tr>
<?php endif ?>
<tr>
<td>
	<input <?php echo $opstyle ? "style=\"$opstyle\"" : 'style="width:100%"' ?>
	type="text" name="<?php echo $opkey ?>" value="<?php echo $opval ?>" />
	<div><em><?php $this->e($ophlp) ?></em></div>
</td>
<th scope="row"><?php $this->e($optitle) ?></th>
</tr>
<?php endforeach ?>

</table>

<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="<?php echo join(',', $oplist) ?>" />
<p class="submit">
<input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
</p>
</form>

</div>
