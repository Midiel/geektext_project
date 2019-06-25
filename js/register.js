var sixLenCheck = $("#sixlen-check");
var upperLowerCheck = $("#upperlower-check");
var numPuncCheck = $("#numpunc-check");
var passMatchCheck = $("#passmatch-check");
var isSixLen = false;
var isUpperLower = false;
var isNumPunc = false;
var isMatch = false;
var password1 = "";

$("#register-form").submit((event) => {
	//if any check failed
	if (!isSixLen || !isUpperLower || !isNumPunc || !isMatch) {
		event.preventDefault();
	}
});

$("#password1").keyup(function() {
	password1 = $(this).val();
	isSixLen = (password1.length >= 6);
	isUpperLower = (/[a-z]/.test(password1) && /[A-Z]/.test(password1));
	isNumPunc = /[0-9\.]/.test(password1);

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

$("#password2").keyup(function() {
	isMatch =  (password1 !== "") && (password1 === $(this).val());
	if (isMatch) {
		passMatchCheck.removeClass("fa-circle").addClass("fa-check");
	} else {
		passMatchCheck.removeClass("fa-check").addClass("fa-circle");
	}
<<<<<<< HEAD
});
=======
});
>>>>>>> origin/master
