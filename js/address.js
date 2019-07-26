$(window).keydown(function(event) {
	// prevents 'Enter' submit
    if(event.keyCode == 13) {
      	event.preventDefault();
        return false;
	}
});

$(".geektext-edit").click(function(){	
	var entryRow = this.parentNode.parentNode.parentNode;
	$(entryRow.children[0]).toggleClass("hidden");
	$(entryRow.children[1]).toggleClass("hidden");
});

var addAddress = $("#add-address");
var newAddress = $("#new-address");

var toggleAddNew = () => {
	addAddress.toggleClass("hidden");
	newAddress.toggleClass("hidden");
}

addAddress.click(toggleAddNew);
$("#new-cancel").click(toggleAddNew);