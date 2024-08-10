jQuery(document).ready(function($) {

	$('.table-bordered').css('background', '#FFF');

	$('.currency-table').on('click', '.add-btn', function(event) {
		event.preventDefault();
		$(this).closest('tr').clone(true).find('input').val('').end().appendTo('.currency-table');
	});

	$('.currency-table').on('click', '.delete-btn', function(event) {
		event.preventDefault();
		if($(this).closest('.currency-table').find('tr').length > 2){
			$(this).closest('tr').remove();
		} else {
			alert('Sorry, you can not delete first entry');
		}
	});

	$('.ich-settings-main-wrap').on('click', '.save-btn', function(event) {
		event.preventDefault();
		swal('Please Wait', 'Fetching Live Rates...', 'info');
		var currencyData = {};
		$('.currency-table').find('.currency-options').each(function(index, el) {
			var thisRow = $(this);
            var singleOp = {
                code: thisRow.find('.currency-code').val(),
                position: thisRow.find('.currency-position').val(),
                tsep: thisRow.find('.currency-sep-t').val(),
                dsep: thisRow.find('.currency-sep-d').val(),
                decimals: thisRow.find('.currency-decimals').val(),
            };

            currencyData[thisRow.find('.currency-code').val()] = singleOp;
		});

		var settings = {
			api: $('.api-key').val(),
			provider: $('.api-provider').val(),
			schedule: $('.schedule').val(),
			menu: $('.menu-switcher').val(),
		}

		$.post(ajaxurl, {action: 'rem_currency_options_save', data: currencyData, settings: settings }, function(resp) {
			console.log(resp);
			swal(resp.message, '', resp.status);
			window.location.reload();
		}, 'json');
	});
});