$(window).keydown(function(event) {
	// prevents 'Enter' submit
    if(event.keyCode == 13) {
      	event.preventDefault();
        return false;
	}
});

var sixLenCheck = $("#sixlen-check");
var upperLowerCheck = $("#upperlower-check");
var numPuncCheck = $("#numpunc-check");
var isSixLen = false;
var isUpperLower = false;
var isNumPunc = false;

$("#password-form").submit(function(event) {
	//if any check failed
	if (!isSixLen || !isUpperLower || !isNumPunc) {
		event.preventDefault();
	}
});

$("#password").keyup(function() {
	var password = $(this).val();
	isSixLen = (password.length >= 6);
	isUpperLower = (/[a-z]/.test(password) && /[A-Z]/.test(password));
	isNumPunc = /[0-9\.]/.test(password);

	if (isSixLen) {
		sixLenCheck.removeClass("fa-circle").addClass("fa-check");
	} else {
		sixLenCheck.removeClass("fa-check").addClass("fa-circle");
	}

	if (isUpperLower) {
		upperLowerCheck.removeClass("fa-circle").addClass("fa-check");
	} else {
		upperLowerCheck.removeClass("fa-check").addClass("fa-circle");
	}

	if (isNumPunc) {
		numPuncCheck.removeClass("fa-circle").addClass("fa-check");
	} else {
		numPuncCheck.removeClass("fa-check").addClass("fa-circle");
	}
});

$(".geektext-edit").click(function(){	
	var entryRow = this.parentNode.parentNode.parentNode;
	$(entryRow.children[0]).toggleClass("hidden");
	$(entryRow.children[1]).toggleClass("hidden");
});