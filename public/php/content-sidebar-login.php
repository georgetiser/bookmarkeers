<?php ?>

<form action="index.php" method="post">
	<label for="raw_username">Login</label>
	<input type="text" id="raw_username" name="raw_username" value="" placeholder="">
	<label for="raw_password">Password</label>
	<input type="password" id="raw_password" name="raw_password" value="">
	<input type="hidden"  id="perform_action" name="perform_action" value="login">
	<button class="button expanded">Log In</button>
</form>