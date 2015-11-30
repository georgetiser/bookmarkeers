<?php

$blankinsert = <<<'HTMLBLOCK'
<div class="column">
	<div class="button secondary linkpanel">blank</div>
</div>
HTMLBLOCK;

if(!empty($_SESSION['links'])) {
	foreach ($_SESSION['links'] as $linkdata) { ?>
	<div class="column">
		<a target = "_blank" href="<?= $linkdata['url'] ?>"><div class="button linkpanel"><?= $linkdata['linkname'] ?></div></a>
	</div>
<?php
	}
} else {
	print($blankinsert);print($blankinsert);print($blankinsert);
	?>
	<div class="column">
		<div class="alert-panel">
			<div class="alert-panel-title large-8-columns">
				Pro Tip!
			</div>
			<div class="alert-panel-secondary">
				There are several users, named user1, user2, etc.
				<p>Their passwords are aaaaaaaa, bbbbbbbb, etc.</p>
				<p>Each user has their own set of links. Try logging in!</p>
			</div>
		</div>
	</div>
	<div class="column">
	</div>
	<div class="column">
	</div>
<?php
/*
	print($blankinsert);print($blankinsert);print($blankinsert);
	print($blankinsert);print($blankinsert);print($blankinsert);
	print($blankinsert);print($blankinsert);print($blankinsert);
	print($blankinsert);print($blankinsert);print($blankinsert);
	print($blankinsert);print($blankinsert);print($blankinsert);
*/
}




?>