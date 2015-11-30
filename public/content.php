<!doctype html>

<html class="no-js" lang="en">

<?php require("php/content-head.php"); ?>

<body>

<div class="off-canvas-wrapper">
	<div class="off-canvas-wrapper-inner" data-off-canvas-wrapper>
		<div class="off-canvas position-left reveal-for-large" id="my-info" data-off-canvas data-position="left">
			<div class="row column">
				<?php require("php/content-sidebar.php"); ?>
			</div>
		</div>
		<div class="off-canvas-content" data-off-canvas-content>
			<div class="title-bar hide-for-large">
				<div class="title-bar-left">
					<button class="menu-icon" type="button" data-open="my-info"></button>
					<span class="title-bar-title"><?= $_SESSION['title']; ?></span>
				</div>
			</div>
			<div class="callout primary">
				<div class="row column">
					<?php require("php/content-messagepanel.php"); ?>
				</div>
			</div>
			<div class="row small-up-2 medium-up-3 large-up-4">
				<?php require("php/content-tileboard.php"); ?>
			</div>
			<hr>
			<?php //require("php/content-footer.php"); ?>
		</div>
	</div>
</div>

<?php include("php/content-footer.php"); ?>

<?= load_resource('jquery.min.js'); ?>
<?= load_resource('foundation.min.js'); ?>

<script>
	$(document).foundation();
</script>
</body>
</html>
