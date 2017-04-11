/*--functionality start for resend otp--*/
      jQuery(document).ready(function(){
        phone_code_resend = jQuery("#phone_code_resend").attr("value");
        phone_num_resend = jQuery("#phone_num_resend").attr("value");
        
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
/*--//functionality end for resend otp--*/