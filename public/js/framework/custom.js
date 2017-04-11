/*--js start for all form validation--*/
jQuery("#ContactForm").validate({
    errorClass: "my-error-class",
    validClass: "my-valid-class",
    rules: {
        email: { required: true},
        message: { required: true}
    }
});
jQuery("#EmailVerification").validate({
    errorClass: "my-error-class",
    validClass: "my-valid-class",
    rules: {
        email: { required: true}
    }
});
 jQuery("#addCarDetail").validate({
     errorClass: "my-error-class",
     validClass: "my-valid-class",
     rules: {
         make: { required: true},
       password: { required: true,minlength: 6}
     }
});
jQuery("#LoginForm").validate({
    errorClass: "my-error-class",
    validClass: "my-valid-class",
    rules: {
        email: { required: true},
        password: { required: true,minlength: 6}
    }
});
jQuery("#EmailVerificationCode").validate({
  errorElement: "div",
    errorClass: "my-error-class",
    validClass: "my-valid-class",
    errorPlacement: function(error, element) {
        error.appendTo("div#errorscode");
    },
    rules: {
        password_token: { required: true,minlength: 5,maxlength: 5}
    }
});
jQuery("#resetpasswordform").validate({
    errorClass: "my-error-class",
    validClass: "my-valid-class",
    rules: {
        password: {
          minlength: 6,
          required: true
        },
      password_confirmation: {
          minlength: 6,
          required: true,
          equalTo: "#password"
        }
    },
});
jQuery("#SignUpForm").validate({
    errorClass: "my-error-class",
    validClass: "my-valid-class",
    rules: {
        email: { required: true},
        password: {
            minlength: 6,
            required: true
          },
        password_confirmation: {
            minlength: 6,
            required: true,
            equalTo: "#password"
          }
    },
});
jQuery.validator.addMethod("lettersonly", function(value, element) {
          return this.optional(element) || /^[a-z]+$/i.test(value);
}, "Please enter only letters");
jQuery("#EditPassanger").validate({
    errorClass: "my-error-class",
    validClass: "my-valid-class",
    ignore: "",
    rules: {
      first_name: { required: true, lettersonly: true},
      last_name: { required: true, lettersonly: true},
      profile_pic: {
                    required: false,
                    accept:"jpg,png,jpeg,gif"
                  },
      email: { required: true},
      dob: { required: true}
    },
    submitHandler:function(form){
      jQuery(".lodingDiv").show();
      jQuery(".updatepassengerprofilebtn").attr('disabled','disabled');
      form.submit();
    }
});
jQuery("#BecomeDriver").validate({
    errorClass: "my-error-class",
    validClass: "my-valid-class",
   
    rules: {
      first_name: { required: true },
      last_name: { required: true},
      email: { required: true},
      profile_pic: {
                    required: true,
                    accept:"jpg,png,jpeg,gif"
                  },
      licence_exp: { required: true},
      anniversary: { required: false},
      driver_records_9: { required: true},
      licVer: { required: true},
      proofins: { required: true},
      address_1: { required: true},
      city: { required: true},
      state: { required: true},
      zip_code: { required: true,minlength: 4,maxlength: 6,number: true},
      //license_number: { required: true },
      SSN:{ required: true,minlength: 9,maxlength: 9},
      
        termsConditions: { required: true}
    },
    submitHandler:function(form){
      var r = confirm("Confirm your driver information.The information provided will not be changed once you confirm. Click confirm to continue or cancel to edit.");
        if (r == true) {
          jQuery(".lodingDiv").show();
          jQuery(".becomedriveredit").attr('disabled','disabled');
            form.submit();
        }
    }
});
jQuery("#bank_info").validate({
    errorClass: "my-error-class", 
    validClass: "my-valid-class",
    rules: {
        bank_name: { required: true},
        acc_number: { required: true,number:true,digit: true},
        routing_number: { required: true},
        branch: { required: true}
    }
});
jQuery("#editdriver").validate({
  errorClass: "my-error-class",
    validClass: "my-valid-class",
    rules: {
	  
      first_name: { required: true },
      last_name: { required: true},
      email: { required: true},
      licence_exp: { required: true},
      anniversary: { required: false},
      address_1: { required: true},
      city: { required: true},
	  licVer: { required: true},
      proofins: { required: true},
      state: { required: true},
      ssn:{ required: true,minlength: 9,maxlength: 9},
      zip_code: { required: true,minlength: 4,maxlength: 6,number: true},
      license_number: { required: true},
      termsConditions: { required: true},
        //license_verification:{required: true},
        //proof_of_insurance:{required: true}
    },
    submitHandler:function(form){
      jQuery(".lodingDiv").show();
      jQuery(".UpdateDriverProfile").attr('disabled','disabled');
            form.submit();
    }
  });
jQuery("#PhoneVerification").validate({
    errorClass: "my-error-class",
    validClass: "my-valid-class",
    rules: {
        phone_number: { required: true, minlength: 8,maxlength: 15,number: true}
    },
});
jQuery("#editPhoneNumber").validate({
    errorClass: "my-error-class",
    validClass: "my-valid-class",
    rules: {
        phone_number: { required: true, minlength: 8,maxlength: 15,number: true}
    },
});
jQuery("#sendreferrelcode").validate({
    errorClass: "my-error-class",
    validClass: "my-valid-class",
    rules: {
        phone_number: { required: true, minlength: 8,maxlength: 15,number: true}
    },
});
jQuery("#PhoneCodeConfirm").validate({
    errorClass: "my-error-class",
    validClass: "my-valid-class",
    rules: {
        opt: { required: true, minlength: 4,maxlength: 4}
    },
});
jQuery("#confirmOTP").validate({
    errorClass: "my-error-class",
    validClass: "my-valid-class",
    rules: {
        opt: { required: true, minlength: 4,maxlength: 4}
    },
});
/*--payment card validation start--*/
/*--//payment card validation end--*/
/*--//js end for all form validation--*/
/*--jquery for becomedriver verification files start--*/
/*jQuery(".becomedriveredit").on('click',function(event){
    var driverReqyest = jQuery(".driverRequest").text();
    if(driverReqyest === '1'){
    }else{
        if(jQuery(".license_verification").val() && jQuery(".proof_of_insurance").val()){
            var r = confirm("Confirm your driver information.The information provided will not be changed once you confirm. Click confirm to continue or cancel to edit.");
                if (r == true) {
                    
                } else {
                    event.preventDefault();
                }
        }else{
            alert("Please Upload Verification Documents First");
            event.preventDefault();
        }
    }    
});*/
/*--//jquery for becomedriver verification files end--*/
/*--jquery start for datepicker--*/
 nowDob = new Date("1900/01/01");
      defaultDob = new Date(nowDob.getFullYear(), nowDob.getMonth(), nowDob.getDate(), 0, 0, 0, 0);
      FromEndDate = new Date();

      jQuery('#datetimepicker1 #dob').datepicker({
        format: 'mm/dd/yyyy',
        startDate: defaultDob,
        endDate : FromEndDate,
        autoclose: true
      });
      /*jQuery('#datetimepicker2 #anniversary').on("click",function(event){
        dob = jQuery('#datetimepicker1 #dob').val();
        var d=new Date(dob.split("/").reverse().join("-"));
        var dd=d.getDate();
        var mm=d.getMonth()+1;
        var yy=d.getFullYear();
        var newdate=yy+"/"+mm+"/"+dd;
        nowDate = new Date(newdate);
        today = new Date(nowDate.getFullYear(), nowDate.getMonth(), nowDate.getDate(), 0, 0, 0, 0);
         jQuery('#datetimepicker2 #anniversary').datepicker({
            format: 'mm/dd/yyyy',
            startDate: today,
            autoclose: true
         });
      });*/
	
	// A $( document ).ready() block.
	$( document ).ready(function() {
	/**
	 *VALIDATING DATE RANGE
	 *
	 */
	$('#anniversary').on("change", function(){
			var dob=$("#dob").val();
			var anniversary=$("#anniversary").val();
			var dateA = new Date(dob);
			var dateB = new Date(anniversary);
			if (dateA >= dateB){
				alert("DOB should be less than Anniversary Date!");
				$("#anniversary").val("");
				return false;
			}
			// set end date to max one year period:
			});
		});
	
    jQuery('#datetimepicker2 #anniversary').datepicker({
        format: 'mm/dd/yyyy',
        autoclose: true
      });
      
      jQuery('#datetimepicker3 #dob').datepicker({
      
        format: 'mm/dd/yyyy',
        startDate: defaultDob,
        endDate : FromEndDate,
        autoclose: true
      });

      jQuery('#datetimepicker4 #anniversary').datepicker({
        format: 'mm/dd/yyyy',
        startDate: defaultDob,
        autoclose: true
      });

      /*jQuery('#datetimepicker4 #anniversary').on("click",function(event){
        dob = jQuery('#datetimepicker3 #dob').val();
        var d=new Date(dob.split("/").reverse().join("-"));
        var dd=d.getDate();
        var mm=d.getMonth()+1;
        var yy=d.getFullYear();
        var newdate=yy+"/"+mm+"/"+dd;
        nowDate = new Date(newdate);
        today = new Date(nowDate.getFullYear(), nowDate.getMonth(), nowDate.getDate(), 0, 0, 0, 0);
         jQuery('#datetimepicker4 #anniversary').datepicker({
            format: 'mm/dd/yyyy',
            startDate: today,
            autoclose: true
         });
      });*/
    
    var dp_now = new Date();
   
    var dp_yesterday = new Date(dp_now.getFullYear(), dp_now.getMonth(), dp_now.getDate()-1);
    var dp_tomorrow = new Date(dp_now.getFullYear(), dp_now.getMonth(), dp_now.getDate()+1);
     jQuery('#datetimepicker5 #licence_exp').datepicker({
        format: 'mm/dd/yyyy',
        startDate: dp_tomorrow,
        autoclose: true
      });
     jQuery('#datetimepicker6 #insurance_exp').datepicker({
        format: 'mm/dd/yyyy',
        startDate: dp_tomorrow,
        autoclose: true
      });
/*--//jquery end for datepicker--*/
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                jQuery('.passengerprofile.profilePic').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    /*jQuery(".uploadProfilePic input").change(function(){
        readURL(this);
    });*/

/*--jquery start for check file extension--*/
      jQuery(".uploadProfilePic input").change(function(){
        var filename = jQuery(this).val();
        var extension = filename.replace(/^.*\./, '');
        /*--size limit for file upload code start--*/
          var fileSize = jQuery(this).get(0).files[0].size; // in bytes
          var sizeInMB = (fileSize / (1024*1024)).toFixed(2);
          if(fileSize > '15000000'){
            alert("file size is more then 15 MB (Please Select File Size Less Than 15 MB)");
            return false();
          } else if (extension == 'jpg' || extension == 'jpeg' || extension == 'png' || extension == 'gif'){
            readURL(this);
          } else{
            jQuery("#profile_pic").val('');
            alert("Use Only jpg, PNG, JPEG, gif formet");
            return false();
          }
      });
      jQuery(".license_verification").change(function(){
        jQuery(".DriverLicense").attr("value",'');
        var filename = jQuery(this).val();
        var filenameonly = filename.replace(/^.*[\\\/]/, '');

        var lengthfile = $.trim(filenameonly).length;
        if(lengthfile > '15' ){
          var fileUploadName = filenameonly.slice(-15);
        }else{
          var fileUploadName = filenameonly;
        }

        var extension = filename.replace(/^.*\./, '');
        /*--size limit for file upload code start--*/
          var fileSize = jQuery(this).get(0).files[0].size; // in bytes
          var sizeInMB = (fileSize / (1024*1024)).toFixed(2);
          if(fileSize > '15000000'){
            alert("file size is more then 15 MB (Please Select File Size Less Than 15 MB)");
            jQuery(".DriverLicense").attr("value",'');
            return false;
          }else{
            jQuery(".DriverLicense").attr("value",fileUploadName);
          }
        /*--//size limit for file upload code end--*/  
        if(extension == 'jpg' || extension == 'jpeg' || extension == 'png' || extension == 'gif' || extension == 'pdf' || extension == 'doc' || extension == 'rtf' || extension == 'docx'){

        }else{
          jQuery(".DriverLicense").attr("value",'');
          alert("Use Only PDF, doc, rtf, jpg, PNG, JPEG, gif, docx");
          return false;
        }
      });
      jQuery(".proof_of_insurance").change(function(){
        jQuery(".ProofOfInsurance").attr("value",'');
        var filename = jQuery(this).val();
        var filenameonly = filename.replace(/^.*[\\\/]/, '');
        
        var lengthfile = $.trim(filenameonly).length;
          if(lengthfile > '15' ){
            var fileUploadName = filenameonly.slice(-15);
          }else{
            var fileUploadName = filenameonly;
          }

        var extension = filename.replace(/^.*\./, '');
        /*--size limit for file upload code start--*/
          var fileSize = jQuery(this).get(0).files[0].size; // in bytes
          var sizeInMB = (fileSize / (1024*1024)).toFixed(2);
          if(fileSize > '15000000'){
            alert("file size is more then 15 MB (Please Select File Size Less Than 15 MB)");
            jQuery(".ProofOfInsurance").attr("value",'');
            return false;
          }else{
            //readURL(this);
            jQuery(".ProofOfInsurance").attr("value",fileUploadName);
          }
        /*--//size limit for file upload code end--*/  
        if(extension == 'jpg' || extension == 'jpeg' || extension == 'png' || extension == 'gif' || extension == 'pdf' || extension == 'doc' || extension == 'rtf' || extension == 'docx'){

        }else{
          jQuery(".ProofOfInsurance").attr("value",'');
          alert("Use Only PDF, doc, rtf, jpg, PNG, JPEG, gif, docx");
          return false;
        }
      });
/*--//jquery end for check file extension--*/
/*--jquery start for change image on upload--*/
    jQuery(".submitContactForm").click(function(){
      if (grecaptcha.getResponse() == ""){
          alert("Please Select reCAPTCHA");
          return false;
      } else {
          
      }
    });
/*--//jquery code end for file upload--*/
/*--check user login code start--*/
  jQuery(".becomedriver").click(function(){
    localStorage.setItem("checkuser", "1");
    var usertype = localStorage.getItem("checkuser"); 
  });
  jQuery(".loginuser").click(function(){
    localStorage.setItem("checkuser", "0");
  });
  jQuery(".LoginHere").click(function(){
    var usertype = localStorage.getItem("checkuser");
    jQuery("#checkuser").attr("value",usertype);
  });
/*--//check user login code end--*/
/*--code start for edit or update passenger--*/
jQuery(".EDITBtnPassenger1").click(function(event){
  event.preventDefault();
  editPassengerForm = jQuery(this);
  editPassengerForm.hide();
  jQuery(".EDITBtnPassenger2").show();
  editPassengerForm.closest("form").find("input[name=first_name]").prop("disabled", false).focus();
  editPassengerForm.closest("form").find("input[name=last_name]").prop("disabled", false);
  editPassengerForm.closest("form").find("input[name=email]").prop("disabled", false);
  if(editPassengerForm.closest("form").find("input[name=dob]").val()==''){
    editPassengerForm.closest("form").find("input[name=dob]").prop("disabled", false);
  }
  editPassengerForm.closest("form").find("input[name=dob]").prop("disabled", false);
  editPassengerForm.closest("form").find("input[name=anniversary]").prop("disabled", false);
  jQuery(".profilePicIcon").show();
});
/*--code end for edit or update passenger--*/
/*--code start for edit or update drivere profile--*/
jQuery(".editDriverProfile").click(function(event){
  event.preventDefault();
  editDriverForm = jQuery(this);
  editDriverForm.hide();
  jQuery(".UpdateDriverProfile").show();
  editDriverForm.closest("form").find("input[name=license_number]").prop("disabled", false).focus();
  editDriverForm.closest("form").find("input[name=licence_exp]").prop("disabled", false);
  editDriverForm.closest("form").find("input[name=profile_pic]").prop("disabled", false);
  editDriverForm.closest("form").find("input[name=email]").prop("disabled", false);
  editDriverForm.closest("form").find("input[name=insurance_exp]").prop("disabled", false);
  editDriverForm.closest("form").find("input[name=anniversary]").prop("disabled", false);
  editDriverForm.closest("form").find("input[name=address_1]").prop("disabled", false);
  editDriverForm.closest("form").find("input[name=address_2]").prop("disabled", false);
  editDriverForm.closest("form").find("input[name=city]").prop("disabled", false);
  editDriverForm.closest("form").find("input[name=ssn]").prop("disabled", false);
  editDriverForm.closest("form").find("input[name=state]").prop("disabled", false);
  editDriverForm.closest("form").find("input[name=licVer]").prop("disabled", false);
  editDriverForm.closest("form").find("input[name=license_verification]").prop("disabled", false);
  editDriverForm.closest("form").find("input[name=zip_code]").prop("disabled", false);
  editDriverForm.closest("form").find("input[name=license_verification]").removeAttr("disabled");
  editDriverForm.closest("form").find("input[name=proof_of_insurance]").removeAttr("disabled");
  editDriverForm.closest("form").find("input[name=car_transmission]").prop("disabled", false);
});
/*--//code end for edit or update drivere profile--*/
/*jQuery(document).ready(function () {
     jQuery("input[type='submit']").one('click', function (event) {  
           event.preventDefault();
     });
});*/
jQuery(document).ready(function(e) {
      var $input = jQuery('#refresh');
      $input.val() == 'yes' ? location.reload(true) : $input.val('yes');
});

