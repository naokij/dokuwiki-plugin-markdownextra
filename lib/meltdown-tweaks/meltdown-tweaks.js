jQuery(document).ready(function($) {
	// Disabling default DukuWiki toolbar
	$("#tool__bar").hide();
	
	// Activate Meltdown on the textarea:
	$("#wiki__text").meltdown(
		openPreview = true;
	);

	// Put preview after the editor:
	$(".meltdown_wrap").append($(".meltdown_preview-wrap"));
	// Open the preview (There is no init option for this...):
	$(".meltdown_control-preview").click();
});
