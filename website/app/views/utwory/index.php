<div class="title row">
	<div class="col-xs-12">
		<a href="">Internetowy Ranking Utworów Muzycznych</a>
		<?php if($this->czyZalogowany) echo '<a class="wyloguj_button" href="logowanie/wyloguj">Wyloguj</a>'; ?>
	</div>
</div>
<div class="content row">
	<div class="search row">
		<form action="utwory/szukaj" method="get">
			<div class="col-xs-9 col-xs-offset-1">
				<input class="search-input" name="szukaj" type="text" placeholder="Szukaj" value="<?php if(isset($_GET['szukaj'])) echo $_GET['szukaj']; ?>">
			</div>
			<?php if(isset($_GET['sortuj'])): ?>
				<input type="hidden" name="sortuj" value="<?=$_GET['sortuj']?>">
			<?php endif; ?>
			<div class="search-submit col-xs-2"><input type="submit" value="Szukaj"></div>
		</form>
	</div>
	<div class="list row">
		<?php if(isset($this->utwory)): ?>
		<div class="sort col-xs-4 col-xs-offset-4">
			<form class="sortuj_form" action="utwory/szukaj" method="Get">
				<?php if(isset($_GET['szukaj'])): ?>
					<input type="hidden" name="szukaj" value="<?=$_GET['szukaj']?>">
				<?php endif; ?>
				<select class="sort-select" name="sortuj">
					<option style="display:none;">--Sortuj--</option>
					<option value="ocenaD">Ocena: od najwyższej</option>
					<option value="ocenaA">Ocena: od najniższej</option>
					<option value="tytulA">Tytuł: od A do Z</option>
					<option value="tytulD">Tytuł: od Z do A</option>
					<option value="albumA">Album: od A do Z</option>
					<option value="albumD">Album: od Z do A</option>
					<option value="wykonawcaA">Wykonawca: od A do Z</option>
					<option value="wykonawcaD">Wykonawca: od Z do A</option>
				</select>
			</form>
		</div>

		<?php foreach ($this->utwory as $utwor): ?>
		<div class="list-song col-xs-12">
			<div class="row">
				<div class="col-sm-2">
					<?php if($utwor['zdjecie']!=NULL): ?>
					<!-- dodany rand() aby data-lightbox dla zdjec nie byl taki sam(by nie bylo galerii) -->
					<a data-lightbox="image<?=rand()?>" href="<?='public/okladki/' . $utwor['zdjecie']; ?>"><img class="list-song-img" src="<?='public/okladki/' . $utwor['zdjecie']; ?>"></a>
					<?php else: ?>
					<img class="list-song-img default_image" src="img/music_disc.png">
					<?php endif; ?>
				</div>
				<div class="list-song-info col-sm-10">
					<div class="list-song-info-title col-sm-11 col-sm-offset-1"><?=$utwor['tytul']?></div>
					<div class="list-song-info-album_wykonawca col-sm-2 col-sm-offset-2"><div class="list-song-info-album"><?=$utwor['album']?></div><br><div class="list-song-info-wykonawca"><?=$utwor['wykonawca']?></div></div>
					<div class="static_height col-sm-8">
						<?php if(isset($_SESSION['logged'])): ?>
						<div class="your_rating_label col-sm-4"><strong id="zaglosuj_text">Zagłosuj!</strong></div>
						<div class="actual_rating_label col-sm-4">Aktualna ocena</div>
						<div class="list-song-info-edit col-sm-4"><a href="">Edytuj</a></div>

						<div class="list-song-info-rating your_rating col-sm-4">
							<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
						</div>
						<div class="list-song-info-rating actual_rating col-sm-4">
							<?php if(isset($utwor['ocena'])): ?>
							<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
							<span class="rate_hidden" style="display: none;"><?=$utwor['ocena']?></span>
							<?php else: ?>
							<span>Brak oceny</span>
							<?php endif; ?>
						</div>
						<div class="list-song-info-delete col-sm-4"><a href="">Usuń</a></div>
						<?php else: ?>
						<div class="your_rating_label col-sm-6"><strong id="zaglosuj_text">Zagłosuj!</strong></div>
						<div class="actual_rating_label col-sm-6">Aktualna ocena</div>

						<div class="list-song-info-rating your_rating col-sm-6">
							<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
						</div>
						<div class="list-song-info-rating actual_rating col-sm-6">
							<?php if(isset($utwor['ocena'])): ?>
							<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
							<span class="rate_hidden" style="display: none;"><?=$utwor['ocena']?></span>
							<?php else: ?>
							<span>Brak oceny</span>
							<?php endif; ?>
						</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<hr>
		</div>
		<?php endforeach; ?>

		<!-- Gdy brak utworow lub blad w bazie -->
		<?php else: ?>
			<p class="wiadomosc"><?php if(isset($this->wiadomosc)) echo $this->wiadomosc; ?></p>
		<?php endif; ?>
		
	</div>
	<?php if(isset($_SESSION['logged'])): ?>
	<div class="add row">
		<div class="col-xs-2 col-xs-offset-5"><i class="add-button fa fa-plus"></i></div>
	</div>
	<?php endif; ?>
</div>


<!-- ogólny popup do edycji/dodawania utworu, dostosowywany jest w jquery(tytuł,action,submit) -->
<div class="popup">
	<h1>Tytuł<i class="popup-exit fa fa-times"></i></h1>
	<div class="popup-content">
		<form class="popup_form" action="" method="Post" enctype="multipart/form-data">
			<div class="inputs">
				<label for="tytul">Tytuł: </label> <input type="text" name="tytul" id="tytul" maxlength="50"><br>
				<label for="album">Album: </label> <input type="text" name="album" id="album" maxlength="50"><br>
				<label for="wykonawca">Wykonawca: </label> <input type="text" name="wykonawca" id="wykonawca" maxlength="50"><br>
				<label for="zdjecie">Zdjęcie(opcjonalne): </label> <input type="file" name="zdjecie" id="zdjecie"><br>
			</div>

			<input type="submit" value="">
		</form>
		<img id="ajax_loader" src="img/ajax_loader.gif">
	</div>
</div>

<!-- alert do usuwania utworu -->
<div class="delete_alert">
	<div class="alert alert-danger">
		<h4>Czy na pewno chcesz usunąć utwór?</h4>
		<button class="delete-button btn btn-danger">Usuń</button>
		<button class="alert-exit btn btn-default">Anuluj</button>
	</div>
</div>
