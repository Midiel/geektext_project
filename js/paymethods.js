const MIN_CC_LEN = 13;

$(window).keydown(function(event) {
	// prevents 'Enter' submit
    if(event.keyCode == 13) {
      	event.preventDefault();
        return false;
	}
});

var addCard = $("#add-card");
var newCard = $("#new-card");

var toggleAddNew = () => {
	addCard.toggleClass("hidden");
	newCard.toggleClass("hidden");
}

addCard.click(toggleAddNew);
$("#new-cancel").click(toggleAddNew);

var cardIcon = $("#card-icon");
var cardType = document.getElementById("card-type");
var hasType = false;
var lastIcon = 0;
var newIcon = 0;
var iconArr = ["", "fa-exclamation-circle", "fa-cc-amex", "fa-cc-visa", 
				"fa-cc-mastercard", "fa-cc-discover", "fa-cc-diners-club",
				"fa-cc-jcb", "fa-credit-card"];
$("#card-number").keyup(function () {
	var cardNumber = $(this).val();
	//if cc number is greater than min and is only numbers and passes Luhn check
	if (cardNumber.length >= MIN_CC_LEN && !(/[^0-9]/.test(cardNumber))
		&& luhnChk(cardNumber)) {
		// find appropriate icon
		var fTwo = parseInt(cardNumber.substring(0,2), 10);
		var fThree = parseInt(cardNumber.substring(0,3), 10);
		var fFour = parseInt(cardNumber.substring(0,4), 10);
		if (fTwo === 34 || fTwo === 37) {
			newIcon = 2;
			cardType.value = "Amex";
		} else if (parseInt(cardNumber[0]) === 4) {
			newIcon = 3;
			cardType.value = "Visa";
		} else if (fTwo >= 51 && fTwo <= 55) {
			newIcon = 4;
			cardType.value = "MasterCard";
		} else if (fFour === 6011 || parseInt(cardNumber[0]) === 5) {
			newIcon = 5;
			cardType.value = "Discover";
		} else if ((fThree >= 300 && fThree <= 305) || fTwo === 36 || fTwo === 38) {
			newIcon = 6;
			cardType.value = "Diner's Club";
		} else if (fFour === 2131 || fFour === 1800 || fTwo === 35) {
			newIcon = 7;
			cardType.value = "JCB";
		} else {
			newIcon = 8;
			cardType.value = "Card";
		}
		
		hasType = true;
	} else {
		if (cardNumber.length > 0) { // input not valid
			newIcon = 1;
		} else { // input empty
			newIcon = 0;
		}
		cardType.value = "";
		
		hasType = false;
	}

	if (lastIcon !== newIcon) {
		cardIcon.removeClass(iconArr[lastIcon]);
		cardIcon.addClass(iconArr[newIcon]);
		lastIcon = newIcon;
	}
});

newCard.submit((event) => {
	// if no card type registered, prevent submission
	if (!hasType) {
		event.preventDefault();
	}
});

/**
 * Luhn algorithm in JavaScript: validate credit card number supplied as string of numbers
 * @author ShirtlessKirk. Copyright (c) 2012.
 * @license WTFPL (http://www.wtfpl.net/txt/copying)
 */
var luhnChk = (function (arr) {
    return function (ccNum) {
        var 
            len = ccNum.length,
            bit = 1,
            sum = 0,
            val;

        while (len) {
            val = parseInt(ccNum.charAt(--len), 10);
            sum += (bit ^= 1) ? arr[val] : val;
        }

        return sum && sum % 10 === 0;
    };
}([0, 2, 4, 6, 8, 1, 3, 5, 7, 9]));