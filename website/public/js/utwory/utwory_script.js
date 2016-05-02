$(document).ready(function() {
	//flag, prevent duplicate ajax requests
	requestSent = false;

	ocenianie();

});


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