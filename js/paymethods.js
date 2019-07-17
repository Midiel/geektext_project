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