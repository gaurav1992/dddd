var telInput = $("#phone"),
  errorMsg = $("#error-msg"),
  validMsg = $("#valid-msg");

// initialise plugin
telInput.intlTelInput({
  utilsScript: "../utils.js"
});

var reset = function() {
  telInput.removeClass("error");
  errorMsg.addClass("hide");
  validMsg.addClass("hide");
};

// on blur: validate
telInput.blur(function() {
  reset();
  if ($.trim(telInput.val())) {
    if (telInput.intlTelInput("isValidNumber")) {
      //alert("valid");
      //validMsg.removeClass("hide");
    } else {
      //alert("notvalid");
      //telInput.addClass("error");
      //errorMsg.removeClass("hide");
    }
  }
});
// on keyup / change flag: reset
telInput.on("keyup change", reset);