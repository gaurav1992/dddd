jQuery(document).ready(function(){
  /*--script for card validation start here--*/
  $(function() {
      var creditly = Creditly.initialize(
          '.creditly-wrapper .expiration-month-and-year',
          '.creditly-wrapper .credit-card-number',
          '.creditly-wrapper .security-code',
          '.creditly-wrapper .card-type');

      $(".saveCrdInfo").click(function(event) {
        event.preventDefault();
        addCardDetail = jQuery(this);
        //console.log(creditly);
        var output = creditly.validate();
        if (output) {
          //console.log(output);
           
          var number = output['number'];
          var last_4_no = output['security_code'];

          var expiration_month = output['expiration_month'];
          var expiration_year = output['expiration_year'];

          var expiration_date = expiration_month+'/'+expiration_year;
          var cardType = addCardDetail.closest("form").find(".card-type").val();
          var _token = addCardDetail.closest("form").find("input[name=_token]").val();

          var account_type = 'card';
        $.ajax({
        type:'post',
        dataType : "json",
        url:savecarddetail,
        data : {number : number,last_4_no : last_4_no,expiration_date : expiration_date,cardType : cardType,account_type : account_type,_token : _token },

        beforeSend: function( xhr ) {
          //$('body').append('<div id="loadering"><img src="'+loadingImage+'"></div>');
          //return false;
        },

        success:function(response)
        {
          addCardDetail.closest("form").submit();
          location.reload();
        }
      });
          
          // Your validated credit card output
          //console.log(output);
        }
      });
    });
/*--//script for card validation end here--*/
});


  