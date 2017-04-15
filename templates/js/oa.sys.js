$(function() {
	openNewWindow("clock", 100, 150, 200, 200, "programs/deny.html");
	openNewWindow("deny yourself", 200, 200, 400, 80, "programs/deny.html");
	openNewWindow("i do not exist", 300, 250, 300, 100, "programs/deny.html");
});

function openNewWindow(title, x, y, width, height, program) {
	d = $("<div></div>").addClass("ui-window ui-draggable ui-resizable");
	titlebar = $("<div></div>").addClass("ui-titlebar");
	caption = $("<h4></h4>").addClass("ui-titlebar-caption").text(title);
	titlebar.append(caption);
	surface = $("<div></div>").addClass("ui-surface").css({'width':width, 'height':height});
	
	prog = $("<iframe src='" + program + "''>corrupt</iframe>").attr('scrolling', 'no');
	surface.append(prog);

	d.append(titlebar);
	d.append(surface);
	d.css({
		'left':x,
		'top':y
	});
	d.draggable({
		handle: titlebar,
		containment:"parent",
		stack: "#ui-desktop .ui-window"
	}).resizable({
      containment: "#ui-desktop"
    });
	d.disableSelection();
	titlebar.disableSelection();

	$('#ui-desktop').append(d);
}