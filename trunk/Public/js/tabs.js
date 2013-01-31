$.tabs = function(selector, start) {
	$(selector).each(function(i, element) {
		$($(element).attr('tab')).css('display', 'none');
		
		$(element).click(function() {
			$(selector).each(function(i, element) {
				$(element).removeClass('selected').addClass('nobg');
				$($(element).attr('tab')).css('display', 'none');
				//$($(element).attr('tab')).removeClass('bg').addClass('nobg');
			});
			
			$(this).addClass('selected').removeClass('nots');
			
			$($(this).attr('tab')).css('display', 'block');
			$($(this).attr('tab')).addClass("bg");
		});
	});
	
	if (!start) {
		start = $(selector + ':first').attr('tab');
	}
	$(selector + '[tab=\'' + start + '\']').trigger('click');
};
$.tabs2 = function(selector, start) {
	$(selector).each(function(i, element) {
		$($(element).attr('tab')).css('display', 'none');
		
		$(element).mousemove(function() {
			$(selector).each(function(i, element) {
				$(element).removeClass('selected').addClass('nots');
				
				$($(element).attr('tab')).css('display', 'none');
			});
			
			$(this).addClass('selected').removeClass('nots');
			
			$($(this).attr('tab')).css('display', 'block');
		});
	});
	
	if (!start) {
		start = $(selector + ':first').attr('tab');
	}

	$(selector + '[tab=\'' + start + '\']').trigger('mousemove');
};

