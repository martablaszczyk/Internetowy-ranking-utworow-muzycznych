<div class="logowanie_row row">
	<div class="logowanie_col col-sm-4 col-sm-offset-4 col-xs-10 col-xs-offset-1">
		<h1>Logowanie</h1>

		<form class="logowanie_form" action="logowanie/zaloguj" method="Post">
		<label for="login">Login:&nbsp;</label>
		<input type="text" name="login" id="login"><br><br>

		<label for="password">Hasło:</label>
		<input type="password" name="password" id="password"><br><br>

		<input type="submit" value="Zaloguj się">
		</form>
		<div class="blad"><?php echo (isset($this->blad)) ? $this->blad : ''?></div></div>
	</div>
</div>