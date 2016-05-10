		<div class="footer row">
			<div class="col-xs-12">
				
			</div>
		</div>		
		</div>

		<script type="text/javascript" src="js/jquery.js"></script>
		<script type="text/javascript" src="js/lightbox.min.js"></script>
		<script type="text/javascript" src="js/getURLParameter.js"></script>
		<script type="text/javascript" src="js/js.cookie.js"></script>
		<script type="text/javascript" src="http://ciasteczka.eu/cookiesEU-latest.min.js"></script>
		<script type="text/javascript" src="public/js/cookieInfo.js"></script>
		<?php
			if(isset($this->js)) {
				foreach ($this->js as $js) {
					echo "<script src='public/" . $js . "'></script>";
				}
			}	
		?>
	</body>
</html>