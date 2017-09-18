jQuery(document).ready(function ($) {
	$('.firstworks-link-override').off("click").on("click", function (e) {
		e.stopPropagation();
		window.location.href = $(this).attr('href');
		return false;
	});
});