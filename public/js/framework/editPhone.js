jQuery("#editPhone").on("click",function(){
	
	 
	jQuery("#verificationMessage").html('');
	jQuery("#verificationMessage").removeClass("alert-info");
	phone_code_resend = jQuery("#country_phone_code").attr("value");
    phone_num_resend = jQuery("#contact_number").attr("value");
    if(phone_code_resend !=''){
       jQuery("#phonecode").attr("value",phone_code_resend);
       jQuery(".country-list li .dial-code:contains("+phone_code_resend+")").addClass("findCountryCode");
       var countryName = jQuery(".findCountryCode").closest("li").find(".country-name").text();
       var countryShortName = jQuery(".findCountryCode").closest("li").attr("data-country-code");

       jQuery(".selected-flag").attr("title",countryName +":"+phone_code_resend);
       jQuery(".selected-flag .iti-flag").attr("class","iti-flag "+countryShortName+"");
       jQuery(".findCountryCode").closest("li").addClass("active");
     }
});


jQuery(".updatePhone").on('click',function(event){
		event.preventDefault();
		editContactForm = jQuery(this);
		var _token = editContactForm.closest("form").find("input[name=_token]").val();
		var phone_number = editContactForm.closest("form").find("input[name=phone_number]").val();
		var UerID = editContactForm.closest("form").find("input[name=user_id]").val();
		var phonecode = editContactForm.closest("form").find("input[name=phonecode]").val();
		if(jQuery( "#editPhoneNumber" ).valid()){
			jQuery("#verificationMessage").html('');
			jQuery("#verificationMessage").removeClass("alert-info");
			jQuery(".lodingDiv").show();
			editContactForm.attr('disabled','disabled');
			$.ajax({
					type:'post',
					dataType : "json",
					url:editPhone,
					data : {phone_number : phone_number,_token : _token,UerID : UerID,phonecode : phonecode },
					beforeSend: function( xhr ) {
						//$('body').append('<div id="loadering"><img src="'+loadingImage+'"></div>');
						//return false;
					},
					success:function(response)
					{
						if(response == '11'){
							jQuery("#verificationMessage").addClass("alert-info");
							jQuery("#verificationMessage").html("Contact Number Already Exist");
							jQuery(".lodingDiv").hide();
							editContactForm.removeAttr("disabled");
						}else{
							jQuery("#verificationMessage").addClass("alert-info");
							jQuery("#verificationMessage").html("Verification code sent");
							jQuery(".lodingDiv").hide();
							jQuery("#phone").attr('readonly','true');
							jQuery(".updatePhone").html("Re-Send verification code");
							editContactForm.removeAttr("disabled");
						}
					}
				});
		}	

	});

jQuery(".confirmOTP").on('click',function(event){
		event.preventDefault();
		confirmOtp = jQuery(this);
		var _token = confirmOtp.closest("form").find("input[name=_token]").val();
		var otp = confirmOtp.closest("form").find("input[name=opt]").val();
		if(jQuery( "#confirmOTP" ).valid()){
			jQuery(".lodingDivconfirm").show();
			jQuery("#otpConfirmMessage").html('');
			jQuery("#otpConfirmMessage").removeClass("alert-info");
			$.ajax({
					type:'post',
					dataType : "json",
					url:confirmOTP,
					data : {otp : otp,_token : _token},
					beforeSend: function( xhr ) {
						//$('body').append('<div id="loadering"><img src="'+loadingImage+'"></div>');
						//return false;
					},
					success:function(response)
					{
						if(response == '11'){
							jQuery("#otpConfirmMessage").addClass("alert-info");
							jQuery("#otpConfirmMessage").html("OTP Doesn't Match Please Try Again");
							jQuery(".lodingDivconfirm").hide();
						}else{
							confirmOtp.closest("form").submit();
							location.reload();
							jQuery(".lodingDivconfirm").hide();
						}
					}
				});
		}

	});