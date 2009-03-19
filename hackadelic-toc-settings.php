<?php
if ( !defined('ABSPATH') )
	exit("Sorry, you are not allowed to access this page directly.");
if ( !isset($this) || !is_a($this, HackadelicTOC) )
	exit("Invalid operation context.");

$options = array(
	array(
		'optitle' => 'Maximum Heading Level', 
		'opkey' => $this->fullname('MAX_LEVEL'),
		'opval' => $this->MAX_LEVEL,
		'ophlp' => 'Maximum heading level included in TOC boxed' ),
	array(
		'optitle' => 'Default CSS Class', 
		'opkey' => $this->fullname('DEFAULT_CLASS'),
		'opval' => $this->DEFAULT_CLASS,
		'ophlp' => 'The default CSS class when none is given via shortcode parameter' ),
	array(
		'optitle' => 'Extra Link Attributes', 
		'opkey' => $this->fullname('DEFAULT_STYLE'),
		'opval' => htmlentities($this->DEFAULT_STYLE),
		'ophlp' => 'The default inline CSS style when none is given via shortcode parameter' ),
	array(
		'optitle' => 'Extra Link Attributes', 
		'opkey' => $this->fullname('DEFAULT_HINT'),
		'opval' => htmlentities($this->DEFAULT_HINT),
		'ophlp' => 'The default hint text style when none is given via shortcode parameter' ),
	array(
		'optitle' => 'Automatic Insertion', 
		'opkey' => $this->fullname('AUTO_INSERT'),
		'opval' => $this->AUTO_INSERT,
		'ophlp' => 'Controls automatic TOC box insertion into posts and pages:<br />&nbsp;&nbsp;<tt>before</tt> = insert BEFORE content<br />&nbsp;&nbsp;<tt>after</tt> = insert AFTER content<br />&nbsp;&nbsp;empty or anything else = do not insert automatically' ),
);

$slugHome = 'table-of-content-boxes';
$slugWP = 'toc-boxes';
$admPageTitle = 'Hackadelic TOC Boxes '.$this->t('Settings');
include 'hackadelic-toc-admx.php';
?>

<form method="post" action="options.php">
<?php wp_nonce_field('update-options'); ?>

<table class="form-table" style="clear:none">

<?php foreach ($options as $each) :	extract($each) ; $oplist[] = $opkey ?>
<tr>
<td>
	<input type="text" name="<?php echo $opkey ?>" value="<?php echo $opval ?>" style="width:100%" />
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
