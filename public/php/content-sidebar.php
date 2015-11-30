<?php ?>

<?= insert_image("app_icon.png", ['class' => 'thumbnail', 'width' => '50%']); ?>

<?php

if ($_SESSION['status'] == 'NOBODY') {
	include('content-sidebar-login.php');
}
