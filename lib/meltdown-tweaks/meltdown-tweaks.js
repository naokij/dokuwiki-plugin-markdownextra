jQuery(document).ready(function($) {
	// Disabling default DukuWiki toolbar (but allow users to renable it if needed):
	$("#tool__bar").addClass("markdownextra_disabledtools").append($("<div\>", {
			id: "markdownextra_tool__bar_overlay",
			"class": "markdownextra_disabledtools_overlay",
			text: "[ Activate DukuWiki toolbar ]",
			click: function() {
				$(this).fadeOut(200);
		}}));
	
	// Activate Meltdown on the textarea:
	$("#wiki__text").meltdown();
	
	// Put preview after the editor:
	$(".meltdown_wrap").append($(".meltdown_preview-wrap"));
	// Open the preview (There is no init option for this...):
	$(".meltdown_control-preview").click();
});