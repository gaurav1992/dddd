//from select  datepiker
       nowFrom = new Date("2016/01/01");
      defaultFrom = new Date(nowFrom.getFullYear(), nowFrom.getMonth(), nowFrom.getDate(), 0, 0, 0, 0);
      FromEndDateFrom = new Date();
      $('#frompicker #from').datepicker({
        format: 'yyyy/mm/dd',
        startDate: defaultFrom,
        endDate : FromEndDateFrom,
        autoclose: true
      });
//to 
      nowTo = new Date("2016/01/01");
      defaultTo = new Date(nowTo.getFullYear(), nowTo.getMonth(), nowTo.getDate(), 0, 0, 0, 0);
      FromEndDateTo = new Date();
      $('#topicker #to').datepicker({
        format: 'yyyy/mm/dd',
        startDate: defaultTo,
        endDate : FromEndDateTo,
        autoclose: true
      });

//for select week datepiker
       nowWeek = new Date("1900/01/01");
      defaultWeek = new Date(nowWeek.getFullYear(), nowWeek.getMonth(), nowWeek.getDate(), 0, 0, 0, 0);
      FromEndDateWeek = new Date();
      $('#selectpicker3 #sweek').datepicker({
        format: 'mm/dd/yyyy',
        startDate: defaultWeek,
        endDate : FromEndDateWeek,
        autoclose: true
      });
  //for select the week based driver tier levels
$( document ).on( "change", "#sweek", function(){
    var sweek = $("#sweek").val();
  var token=$('input[name=_token]').val();
    console.log(sweek);
    $.ajax({
     type: "post",
     dataType: "json",
    url: "tierlevelsweek", //Relative or absolute path to response.php file
   data: {'_token': token,'slectweek':sweek},
    success: function(response) {
  console.log(response['0']);
  if(response['0']){
    var tierLevel ='Silver';
    if(response['0'].tiers_level==1){
    tierLevel='Silver';
    }else if(response['0'].tiers_level==2){
    tierLevel='Gold';
    }else if(response['0'].tiers_level==3){
    tierLevel='Platinum';
    }else if(response['0'].tiers_level==4){
    tierLevel='Diamond';
    }
     
     $('#chweek').html('<tr><td>Tier Levels achived</td><td>' + tierLevel + '</td></tr><tr><td>Active Hours</td><td>'+response['0'].total_active_hours +'</td></tr><tr><td>Active Hours during Rush Hour</td><td>'+ response['0'].scheduled_hours +'</td></tr><tr><td>Acceptance Rate</td><td>'+ response['0'].acceptance_rate +'</td> </tr><tr><td>Cancellattion</td><td>'+ response['0'].cancelation_rate +'</td></tr>');
     $('.acceptance_rate').html(response['0'].acceptance_rate);
     $('.cancelation_rate').html(response['0'].cancelation_rate);
     }else{
      $('#chweek').html('<tr><td>No Tier found</td><td></td></tr>');
       $('.acceptance_rate').html(0);
     $('.cancelation_rate').html(0);
     }
  }
 });
 });
 //for select the week based driver tier levels
$(document).on( "change", "#to", function(){
   $("#address").hide()
  var fromate = $("#from").val();
  var toate = $("#to").val();
  var token=$('input[name=_token]').val();
  var statusText;
  var status_class;
  //console.log(fromate);
  //console.log(to);
    $.ajax({
      type: "post",
      dataType: "json",
      url: "selectedDate", //Relative or absolute path to response.php file
      data: {'_token': token,'slectfrom':fromate,'selectTo':toate},
      success: function(response) {
        notfound = jQuery("#notfound");
    
        if(response !='' ){
    notfound.html( " ");
          var ridedata=response;
          jQuery("#ridechange").empty();
            for(var i in ridedata){
              ridedatadetail = jQuery("#ridechange");

              if(ridedata[i].status == 1){ 
                statusText = "In progress";
               status_class='glyphicon glyphicon-refresh';
              }else if(ridedata[i].status == 2){
                 statusText = "Completed";
                  status_class='glyphicon glyphicon-ok';
              }
              else if(ridedata[i].status == 3){
                 statusText = "Ride Cancel";
                  status_class='glyphicon glyphicon-remove';
              }
               else if(ridedata[i].status == 4){
                 statusText = "No responsel";
                  status_class= "glyphicon glyphicon-remove";
              }

              if(ridedata[i].amount == null){
                amount = "0.00";
              }else{
                amount =ridedata[i].amount;
              }
      
              ridedatadetail.append('<div class="detail-earn-cls"> \
                        <div class="col-sm-6 left-cls-div"> \
                        <figure><img src="http://www.mobilytedev.com/deziNow/public/images/form-head.jpg"></figure> \
                            <p>'+ridedata[i].id+'</p> \
                            <p>'+ridedata[i].created_at+'</p> \
                            <p></p> \
                            <form method="POST" action="http://www.mobilytedev.com/deziNow/rideAddres" accept-charset="UTF-8"> \
                              <input name="_token" type="hidden" value="6estbUdwyugCoqeGxkUKM7l584zC9VO8CgwkU5d3"> \
                              <input type="hidden" value="'+ridedata[i].id+' " name="id" id="rideid"> \
                              <a href="#address" id="'+ridedata[i].id+'" class="rideaddress">'+ridedata[i].first_name+'</a> \
                              <p></p> \
                            </form> \
                      </div> \
                      <div class="col-sm-6 right-cls-div "> \
                        <div class="col-sm-6 text-center detail-e-cls">$'+amount+'<span>Amount Paid</span></div> \
                          <div class="col-sm-6 text-center detail-e-cls"> \
                            <i class="'+ status_class+'"></i> \
                            <span>'+ statusText+'</span> \
                          </div> \
                      </div> \
                      <div class="clearfix"></div> \
                    </div>');
              //console.log(ridedata[i]);
              }
        }else{
     $(".span2").html('$00');
         $("#ridechange").html('<div class="col-sm-2"></div><div class="col-sm-8" style="margin-left:14%;"><h1>No Data Found</h1></div><div class="col-sm-2"></div>');
     
        }
    }
  });
});
//for find the ride address 
$("#address").hide();
$( document ).on( "click", ".rideaddress", function(){
  $("#address").show()
      var token=$('input[name=_token]').val();
      //var id = $("#rideid" ).val();
    //var id = getUrlVars()["id"];
      var rideId=$(this).prop('id');
     console.log(rideId);
   $.ajax({
    type: "post",
    dataType: "json",
    url: "rideAddres", //Relative or absolute path to response.php file
  beforeSend:function() {
            $('body').append('<div id="divLoading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8;"><p style="position: absolute; color: White; top: 50%; left: 45%;">Loading, please wait...<img src="http://pulse.sindlab.com.pk//images/ajax-loading.gif"></p></div>');
  
          },
    data: {'_token': token,'id':rideId},
    success: function(response) {
    //console.log(response);
  
    var ride_pick_data= response.pickup_formatted_address.split(",");
    var ride_pick_firstline=ride_pick_data.slice(0,3);
   var ride_pick_second_line=ride_pick_data.slice(3);
   var ride_des_data= response.des_formatted_address.split(",");
   var ride_des_firstline=ride_des_data.slice(0,3);
  var ride_des_second_line=ride_des_data.slice(3);
   var img = response.ride_map_image;
           if($.trim(img) == "")
            {
        
        $("#rideimage").show();
              $( "#noMap" ).html( " ");
        $('#rideimage img').attr('src',nomapurl);
             // $("#rideimage").hide();
             // $( "#noMap" ).html( '<div class="col-sm-6" style="display:inline;text-align:center;"> <h1>Map NOT Found</h1></div>');
       //$( "#noMap" ).html( "<h1 >Map NOT Found</h1>");
            }
            else{
              $("#rideimage").show();
              $( "#noMap" ).html( " ");
              
              $('#rideimage img').attr('src',img+'&key=AIzaSyBuc_EFYfMcW3dn_jnpbcsMvV9HjehWiaI');
            }
   //console.log(response.ride_map_image);
   // $('img').attr("src",img );
if(response != ''){
  //console.log(response.id);   
    $('#ride').html('<tr><td class="col-sm-6" >' + ride_pick_firstline +'</td><td class="col-sm-6">' + ride_des_firstline + '</td></tr> \
  <tr><td class="col-sm-6" >' + ride_pick_second_line +'</td><td class="col-sm-6">' + ride_des_second_line + '</td></tr> \
  <tr><td class="col-sm-6">' + response.pick_city + ' , ' + response.pickup_state + '</td><td class="col-sm-6">' + response.des_city + ' , ' + response.des_state + ' </td></tr> \
  <tr><td class="col-sm-6">' +response.pickup_time + '</td> <td class="col-sm-6">' +response.droptime + '</td></tr> \
  <tr> <td colspan="2"><hr/><hr/> </td></tr> ' );
  
  $('#pickupAdd').html(ride_pick_firstline+' , '+ride_pick_second_line+ ' , ' +response.pick_city+' , '+response.pickup_state);
  $('#dropOff').html(ride_des_firstline+' , '+ride_des_second_line+ ' , ' +response.des_city+' , '+response.des_state);
  

  
  $('#receipt-report').html('<div class="btn-cls-down"> \
     <hr/> \
    <div class="col-sm-6 text-center"><a class="btn green-btn-s" href="#" role="button">Report Found Time</a></div>\
    <div class="col-sm-6 text-center"><a class="btn green-btn-s" href= "driverReportAnIssue/' + response.ride_id+ '" role="button">Report An Issue</a></div>\
    <div class="clearfix"></div>\
      </div>');
     }else{
     $("#address").hide()
        $('#ride').html('<tr><td class="col-sm-6"> No pickup Found </td><td class="col-sm-6">No drop Found</td></tr>');
     }
   $('#divLoading').remove();
   }
 });
});


//for findthe  Receipt address
//var me = getUrlVars()["id"];
$( document ).on( "click", ".rideaddress", function(){
  
 $("#address").show()
  var rideId=$(this).prop('id');
  //console.log(rideId);
      var token=$('input[name=_token]').val();
     // var id = $("#rideid" ).val();
     $.ajax({
    type: "post",
    dataType: "json",
    url: "receipt", //Relative or absolute path to response.php file
  beforeSend:function() {
            $('body').append('<div id="divLoading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8;"><p style="position: absolute; color: White; top: 50%; left: 45%;">Loading, please wait...<img src="http://pulse.sindlab.com.pk//images/ajax-loading.gif"></p></div>');
  
          },
    data: {'_token': token,'id':rideId},
     success: function(response) { 
   $('#divLoading').remove();
    if(response.id){
    $('#receipt').html('<tr><td class="col-sm-6">' + response.miles  + '  Miles</td><td class="col-sm-6">' + response.miles_charges + ' $</td></tr> \
    <tr><td class="col-sm-6">'+ response.duration  +'Minutes</td> <td class="col-sm-6">' + response.duration_charges  + ' $</td></tr> \
    <tr> <td></td><td></td></tr><tr><td class="col-sm-6">Subtotal</td> <td class="col-sm-6">' + response.subtotal + ' $</td> </tr> \
    <tr><td class="col-sm-6">Dezi Fee</td> <td class="col-sm-6">' + response.service_fee  + ' $</td> </tr> \
    <tr><td class="col-sm-6">Pick up Free</td> <td class="col-sm-6">' +response.pickup_fee  + ' $</td></tr> \
    <tr><td></td><td></td> </tr><tr><td class="col-sm-6">Total Bill </td><td class="col-sm-6">' + response.total_charges + ' $</td></tr>');
  
  $('#distance').html( response.miles + " Miles");
  $('#distanCharges').html( response.miles_charges );
  $('#time').html(response.duration+ " Minutes ");
  $('#timeCharges').html(response.duration_charges);
  $('#subtotal').html(response.subtotal);
  $('#deziFee').html(response.service_fee);
  $('#pickupFee').html(response.pickup_fee);
  $('#TotalEarning').html(response.total_charges);

    $('#receipt-report').html('<div class="btn-cls-down"> \
       <hr/> \
      <div class="col-sm-6 text-center"><a class="btn green-btn-s" href="#" role="button">Report Found Time</a></div>\
      <div class="col-sm-6 text-center"><a class="btn green-btn-s" href= "driverReportAnIssue/' + response.ride_id+ '" role="button">Report Found Issue</a></div>\
      <div class="clearfix"></div>\
        </div>');
     }else{
   
     $('#receipt').html('<tr><td class="col-sm-6">0 Miles</td><td class="col-sm-6">$00.00</td></tr>\
       <tr><td class="col-sm-6">0 Minutes</td><td class="col-sm-6">$00.00</td> </tr> \
       <tr><td></td><td></td> </tr><tr> <td class="col-sm-6">Subtotal</td>  <td class="col-sm-6">$00.00 </td> </tr>\
       <tr>  <td class="col-sm-6">Dezi Fee</td><td class="col-sm-6">$00.00</td>    </tr>\
      <tr>  <td class="col-sm-6">Pick up Free</td>   <td class="col-sm-6">$00.00</td> </tr> \
        <tr> <td></td> <td></td> </tr>\
       <tr><td class="col-sm-6">Total Bill </td><td class="col-sm-6">$00.00</td> </tr>'); 
         
    }
   }
 });
});
//for save the bank detail
$( document ).on( "click", "#bankdetail", function(){
      var bankname=$("#bank_name" ).val();
    var account_number=$("#acc_number" ).val();
    var routing_number=$('#routing_number').val();
     var branch=$('#branch').val();
     var token=$('input[name=_token]').val();
     if(jQuery( "#bank_info" ).valid()){
         $.ajax({
          type: "put",
          dataType: "json",
          url: "payments", //Relative or absolute path to response.php file
          data: {'_token': token,'bankname':bankname,'account_number':account_number,'routing_number':routing_number,'branch':branch},
         success: function(response) {
          location.reload();
          console.log(response);
         }
       });
    }
});
// pdf download weekly report


$('#customers').hide();
function weeklyReport() {
  $('body').append('<div id="divLoading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8;"><p style="position: absolute; color: White; top: 50%; left: 45%;">Loading, please wait...<img src="http://pulse.sindlab.com.pk//images/ajax-loading.gif"></p></div>');
  
  $('#customers').show();
  var doc = new jsPDF();
  var specialElementHandlers = {
    '#editor': function (element, renderer) {
        return true;
    }
  };
  
  var pik_up=$('.pik_up').text();
  doc.text(15, 25, "Ride Details"); 
  var imageUrl = $('#mapFrame').attr('src');
  
    var convertType = 'Canvas';
  convertImgToDataURLviaCanvas(imageUrl, function(base64Img) {
    $('.output').find('.textbox').val(base64Img)
      .end()
      .find('.link')
      .attr('href', base64Img)
      .text(base64Img)
      .end()
      .find('.img')
      .attr('src', base64Img)
      .end()
      .find('.size')
      .text(base64Img.length)
      .end()
      .find('.convertType')
      .text(convertType)
      .end()
      .hide()
  });
  
  
  //var a = $('[active_contact][serial=1] '+img)[0];
  //console.log(getBase64Image(a));
  setTimeout(function(){ 
  
  var data_img_url = $.trim($(".data_img_url").val());
  console.log(data_img_url);
  doc.addImage(data_img_url, 'PNG', 15, 40, 155, 80); 
  
  doc.fromHTML($('#customers').get(0), 15, 120, {
        'width': 180,
            'elementHandlers': specialElementHandlers
    });
  
  
  $('#customers').hide();
  $('#divLoading').remove();
  doc.save('invoice');
  }, 2000);
}

 
