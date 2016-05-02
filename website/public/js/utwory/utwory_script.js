$(document).ready(function() {
	//flag, prevent duplicate ajax requests
	requestSent = false;
	
	edytuj();
	dodaj();
	usun();
	sortuj();
	ocenianie();
	resetInputBackground();

});

function edytuj() {
	//'on' handler działa także z dynamicznymi elementami
	$('.list-song-info-edit').on('click', function(event) {
		event.preventDefault();

		//dostosowanie popup'a do edycji utworu: tytuł,action i submit
		//pobranie zawartosci headera, przefiltrowanie(wybranie tylko 'textu' poprzez nodeType, podmiana - text() nie działa)
			$('.popup h1').contents().filter(function(index) {
				return this.nodeType == 3;
			}).replaceWith('Edytuj utwór');
			$('.popup_form')['0'].setAttribute('action', 'utwory/edytuj');
			$('.popup_form input[type=submit]').val('Edytuj utwór');
		//koniec dostosowywania

		// Ukrywamy starą wiadomość, pokazujemy formularz i przycisk 'X'
		$('.popup-message').hide();
		$('.popup_form, .popup-exit').show();

		var numer = $(this).index('.list-song-info-edit');

		var tytul = $('.list-song-info-title').eq(numer).text();
		var album = $('.list-song-info-album').eq(numer).text();
		var wykonawca = $('.list-song-info-wykonawca').eq(numer).text();

		var inputs = $('.popup-content .popup_form .inputs');

		inputs.children('#tytul').val(tytul);
		inputs.children('#album').val(album);
		inputs.children('#wykonawca').val(wykonawca);

		// check if overlay exists - it was created before
		if(!$('.overlay').length) {
			$('body').append('<div class="overlay"></div>');
		}
		$('.overlay, .popup').fadeIn();


		$('.popup-exit').click(function(event) {
			$('.overlay, .popup').fadeOut();
		});

	

		$('.popup_form').submit(function(event) {
			event.preventDefault();

			empty = false;

			$('.popup_form > .inputs').children('input').not('input[type=file]').each(function(index, el) {
				if(!$(this).val()) {
					$(this).css('background-color', 'red');
					empty = true;

				} else {
					$(this).css('background-color', 'white');
				}
			});

			if(!empty) {
				$('.popup_form, .popup-exit').hide();
				$('#ajax_loader').css('display', 'block');


				if(!requestSent) {
      				requestSent = true;

      				//ajax to send also files
      				var data = new FormData($(this)[0]);
					data.append('old_title', tytul);
					
					$.ajax({
						url: $(this).attr('action'),
						type: 'POST',
						data: data,
						success: function(result) {
							requestSent = false;
							if(result == 'ok') {
								$('#ajax_loader').css('display', 'none');


								message = 'Utwór został edytowany';
							} else {
								message = result;
								
							}

							//reset inputs after submit
							$('.popup_form')[0].reset();

							$('#ajax_loader').css('display', 'none');

							if($('.popup-message').length) {
								$('.popup-message').text(message).show();
							} else {
								$('.popup-content').append('<div class="popup-message">' + message + '</div>');
								$('.popup-message').show();
							}
							$('.popup, .overlay').delay(1500).fadeOut();
						}, 
						error: function() {

						},
						//Options to tell jQuery not to process data or worry about content-type
						cache: false,
						contentType: false,
						processData: false
					});
				}
			}
		});
			
	});
}

function dodaj() {
	$('.add-button').on('click', function(event) {
		event.preventDefault();

		//dostosowanie popup'a do dodania utworu: tytuł,action i submit
		//pobranie zawartosci headera, przefiltrowanie(wybranie tylko 'textu' poprzez nodeType, podmiana - text() nie działa)
			$('.popup h1').contents().filter(function(index) {
				return this.nodeType == 3;
			}).replaceWith('Dodaj utwór');
			$('.popup_form')['0'].setAttribute('action', 'utwory/dodaj');
			$('.popup_form input[type=submit]').val('Dodaj utwór');
		//koniec dostosowywania

		// Ukrywamy starą wiadomość, pokazujemy formularz i przycisk 'X'
		$('.popup-message').hide();
		$('.popup_form, .popup-exit').show();



		// check if overlay exists - it was created before
		if(!$('.overlay').length) {
			$('body').append('<div class="overlay"></div>');
		}
		$('.overlay, .popup').fadeIn();

		$('.popup-exit').click(function(event) {
			$('.overlay, .popup').fadeOut();
		});

		$(document).keydown(function(event) {
			// ESCAPE key pressed
			if(event.keyCode == 27) $('.overlay, .popup').fadeOut();
			return;
		});

		$('.popup_form').submit(function(event) {
			event.preventDefault();

			empty = false;

			$('.popup_form > .inputs').children('input').not('input[type=file]').each(function(index, el) {
				if(!$(this).val()) {
					$(this).css('background-color', 'red');
					empty = true;

				} else {
					$(this).css('background-color', 'white');
				}
			});

			if(!empty) {
				$('.popup_form, .popup-exit').hide();
				$('#ajax_loader').css('display', 'block');


				if(!requestSent) {
      				requestSent = true;

      				//ajax to send also fles
      				var data = new FormData($(this)[0]);
					$.ajax({
						url: $(this).attr('action'),
						type: 'POST',
						data: data,
						success: function(result) {
							requestSent = false;
							if(result == 'ok') {
								$('#ajax_loader').css('display', 'none');


								message = 'Utwór został dodany';
							} else {
								message = result;
							}

							//reset inputs after submit
							$('.popup_form')[0].reset();

							$('#ajax_loader').css('display', 'none');

							if($('.popup-message').length) {
								$('.popup-message').text(message).show();
							} else {
								$('.popup-content').append('<div class="popup-message">' + message + '</div>');
								$('.popup-message').show();
							}
							$('.popup, .overlay').delay(1500).fadeOut();
						}, 
						error: function() {

						},
						//Options to tell jQuery not to process data or worry about content-type
						cache: false,
						contentType: false,
						processData: false
					});
				}
			}
		});
		
	});
}

function usun() {
	$('body').on('click', '.list-song-info-delete', function(event) {
		event.preventDefault();

		var numer = $(this).index('.list-song-info-delete');
		var tytul = $('.list-song-info-title').eq(numer).text();

		// check if overlay exists - it was created before
		if(!$('.overlay').length) {
			$('body').append('<div class="overlay"></div>');
		}

		$('.overlay, .delete_alert').fadeIn();

		$('.alert-exit').click(function(event) {
			$('.overlay, .delete_alert').fadeOut();
		});

		$(document).keydown(function(event) {
			// ESCAPE key pressed
			if(event.keyCode == 27) $('.overlay, .delete_alert').fadeOut();
			return;
		});

		$('.delete-button').unbind().click(function(event) {
			if(!requestSent) {
      			requestSent = true;
      			
				$.ajax({
					url: 'utwory/usun',
					type: 'post',
					data: {tytul: tytul},
					success: function(result) {
						requestSent = false;
						$('.overlay, .delete_alert').fadeOut();
						if(result == 'ok') {
							$('.list-song').eq(numer).remove();
						} else {
							console.log(result);
						}
				}});
			}
		});


	});
}

function sortuj() {
	$('.sort-select').change(function(event) {
		$('.sortuj_form').submit();		
	});

	//set selected option active after refresh
	var sortuj = getURLParameter('sortuj');
	$('.sort-select').children('option').each(function(index, el) {
		if(this.value == sortuj) {
			$(this).attr('selected', 'selected');
		}
	});
}

function ocenianie() {
	//check if cookies ares set(so if user voted before)
	$('.list-song-info-title').each(function(index, el) {
		var title = $(this).text();
		if(Cookies.get(title) != null) {
			$(this).parent().find('.your_rating_label').text('Twoja ocena');
			var ocena = Cookies.get(title);
			var stars = $(this).parent().find('.your_rating');
			stars.children('i').each(function(index, el) {
				if(index <= ocena - 1) {
					$(this).css('color', 'rgb(46, 204, 113)');
				}
				$(this).css('cursor', 'default');
			});
		}
	});

	//color actual rating
	$('.rate_hidden').each(function(index, el) {
		var rate = $(this).text();
			$(this).parent().children('i').each(function(index, el) {
				if(index <= rate - 1) {
					$(this).css('color', '#E67E22');
				}
			});

	});


	//change rating stars color on hover
	$('.your_rating i').on({
		mouseenter: function() {
			var parent = $(this).parent();
			//check if rating was clicked(has 'clicked' color which must be rgb)
			if(parent.children('i').first().css('color') != 'rgb(46, 204, 113)') {
				var hover_index = $(this).index('i');
				//index must be 0-4
				hover_index %= 5;
				
				parent.children('i').each(function(index, el) {
					if(index <= hover_index) {
						$(this).css('color', '#27AE60');
					}
				});
			}
		},
		mouseleave: function() {
			var parent = $(this).parent();
			if(parent.children('i').first().css('color') != 'rgb(46, 204, 113)') {
				parent.children('i').each(function(index, el) {
					$(this).css('color', '#666');
				});
			}
		}
	});

	$('.your_rating i').on('click', function() {
		var parent = $(this).parent();
		var title = parent.parent().parent().find('.list-song-info-title').text();
		//user can vote only once on given song - so cookie mustn't exists
		if(Cookies.get(title) == null) {
			var hover_index = $(this).index('i');
			//index must be 0-4
			var hover_index = hover_index%5;
			
			parent.children('i').each(function(index, el) {
				if(index <= hover_index) {
					$(this).css('color', 'rgb(46, 204, 113)');
				}
				$(this).css('cursor', 'default');
			});
			//set cookie
			//cookie: title and vote
			Cookies.set(title, hover_index + 1, {expires: 30});

			$.ajax({
				url: 'utwory/glosuj',
				type: 'POST',
				data: {tytul: title, ocena: hover_index + 1},
				success: function(result) {
					// if(result != 'ok') {
					// 	console.log(result);
					// }
				}
			});
			
		}
	});
}

function resetInputBackground() {
	$('.inputs input').focus(function(event) {
		$(this).css('background-color', '#fff');
	});
}
