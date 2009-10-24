<?php
/*
Sample usage:
	$slugHome = $slugWP = 'myplugin';
	include 'hackadelic-sliders-admx.php';
*/
if ( !defined('ABSPATH') )
	exit('Sorry, you are not allowed to access this page directly.');
if ( !isset($slugHome) )
	exit('Invalid operation context - $slugHome not set.');
if ( !isset($slugWP) )
	exit('Invalid operation context - $slugWP not set.');

$infomercials = array(
	array(
		'text' => 'Visit it',
		'url' => "http://hackadelic.com/solutions/wordpress/$slugHome",
		'icon' => "http://lh5.ggpht.com/_eYaV9fZ6qRg/SYj7lYcnwpI/AAAAAAAAAGU/VEJqpZPeMOc/s800/house.png" ),
	array(
		'text' => 'Comment it',
		'url' => "http://hackadelic.com/solutions/wordpress/$slugHome#comment",
		'icon' => "http://lh6.ggpht.com/_eYaV9fZ6qRg/SYj7etC7muI/AAAAAAAAAF0/K7EPBqbhKvc/s800/comment_edit.png" ),
	array(
		'text' => 'Rate it',
		'url' => "http://wordpress.org/extend/plugins/hackadelic-$slugWP/",
		'icon' => "http://lh4.ggpht.com/_eYaV9fZ6qRg/SYj7eq5ldaI/AAAAAAAAAFs/yeSKn1oBkfc/s800/award_star_gold_2.png" ),
	array(
		'text' => 'Support it',
		//'url' => 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=1805414',
		'url' => 'http://hackadelic.com/donations',
		'icon' => "http://lh4.ggpht.com/_eYaV9fZ6qRg/SYj7e6i0TAI/AAAAAAAAAGM/zDtI8EegmjE/s800/heart.png" ),
	array(
		'text' => 'Contact the author',
		'url' => 'http://hackadelic.com/contact',
		'icon' => "http://lh3.ggpht.com/_eYaV9fZ6qRg/SYj7erc84II/AAAAAAAAAF8/NiwCQIS83Xs/s800/email_edit.png" ),

	array(
		'hr' => true,
		'text' => 'All Hackadelic Stuff',
		//'url' => "http://wordpress.org/extend/plugins/profile/hackadelic",
		'url' => 'http://hackadelic.com/solutions/worpress',
		'icon' => "http://lh5.ggpht.com/_eYaV9fZ6qRg/SZc9jB4lyiI/AAAAAAAAAHk/JTCZCfRxaG4/s800/shield.png" ),
	array(
		'text' => 'Subscribe',
		'url' => 'http://hackadelic.com/feed',
		'icon' => "http://lh4.ggpht.com/_eYaV9fZ6qRg/SYj7e2bE8DI/AAAAAAAAAGE/FoHbGZM2j3A/s800/feed.png" ),
	array(
		'text' => 'Request a plugin',
		'url' => 'http://hackadelic.com/services',
		'icon' => "http://lh5.ggpht.com/_eYaV9fZ6qRg/SYj7lcn3TFI/AAAAAAAAAGc/IIpGTWolB7k/s800/lightbulb.png" ),
);

?>
<style type="text/css">
	.wp-admin form, .wp-admin div.updated {
		margin-right: 180px
	}
	div.hackadelic-adminfobar {
		float: right;
		width: 150px;
		border-left: 1px solid #ccc;
		padding-left: 1em;
		margin-left: 1em;
	}
	.hackadelic-adminfobar a {
		text-decoration: none
	}
	.hackadelic-adminfobar ul {
		list-style: inside;
		padding: 0;
	}
	.hackadelic-adminfobar li {
		margin: .75em 0 .75em 0;
	}
	.hackadelic-adminfobar hr {
		color: #ccc
	}
</style>

<div class="hackadelic-adminfobar">
	<center>You are using a <strong>Hackadelic PlugIn</strong></center>
	<hr size="0" />
	<ul>
<?php foreach ($infomercials as $each) : unset($hr) ; extract($each) ?>
		<?php if ($hr) : ?><hr size="0" /><?php endif ?>
		<li style="list-style-image:url(<?php echo $icon ?>)">
		<a href="<?php echo $url ?>" ><?php echo $text ?></a>
		</li>
<?php endforeach ?>
	</ul>
	<hr size="0" />
	<center><small>
		<!-- License --><?php if (@!$license) $license = 'AGPL'; ?>
		<?php include "license.$license.php" ?>
		<!-- /License -->
	</small></center>
</div>
