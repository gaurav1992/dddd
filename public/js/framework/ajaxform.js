jQuery(document).ready(function(){
/*--code start for delete car detail--*/
$( document ).on( "click", ".deleteCar", function(){
	var carID = $(this).attr('carID');
	$.ajax({
		type:'get',
		dataType : "json",
		url:deleteCar,
		data : {carID: carID },

		beforeSend: function( xhr ) {
			//$('body').append('<div id="loadering"><img src="'+loadingImage+'"></div>');
			//return false;
		},
		success:function(response)
		{
			//location.reload();
		}
	});
});
/*--//code end for delete car detail--*/
/*--update car detail jquery start here--*/
jQuery(".carDetailUpdate").on('click',function(event){
	event.preventDefault();
	updateform = jQuery(this);
	var make = updateform.closest("form").find(".make").val();
	var model = updateform.closest("form").find(".model").val();
	var year = updateform.closest("form").find(".year option:selected").val();
	var number = updateform.closest("form").find(".number").val();
	var updatedcarId = updateform.closest("form").find(".updatedcarId").val();
	var transmission = updateform.closest("form").find("input[name=transmission]:checked").val();
   var default_check= updateform.closest("form").find("input[name=default_car]:checked").val();
	var _token = updateform.closest("form").find("input[name=_token]").val();
	$.ajax({
		type:'post',
		dataType : "json",
		url:updateCar,
		data : {make : make,model : model,year : year,number : number,transmission : transmission,default_check:default_check,_token : _token,updatedcarId : updatedcarId },

		beforeSend: function( xhr ) {
			//$('body').append('<div id="loadering"><img src="'+loadingImage+'"></div>');
			//return false;
		},

		success:function(response)
		{
			updateform.closest("form").submit();
			location.reload();
		}
	});
});
/*--//update car detail jquery end here--*/

/*--ajax jquery start for save passenger address--*/
	jQuery(".addAdreesPassengerBtn").on('click',function(event){
		event.preventDefault();
		addressForm = jQuery(this);
		var place_name = addressForm.closest("form").find(".place_name option:selected").val();
		var _token = addressForm.closest("form").find("input[name=_token]").val();
		var address = addressForm.closest("form").find(".address").val();
		var latitude = addressForm.closest("form").find(".latitude").val();
		var longitude = addressForm.closest("form").find(".longitude").val();
		jQuery(this).attr('disabled','disabled');
		$.ajax({
				type:'post',
				dataType : "json",
				url:addPassengerAddress,
				data : {place_name : place_name,_token : _token,address : address,latitude : latitude,longitude : longitude },
				beforeSend: function( xhr ) {
					//$('body').append('<div id="loadering"><img src="'+loadingImage+'"></div>');
					//return false;
				},
				success:function(response)
				{
					
					addressForm.closest("form").submit();
					jQuery(this).removeAttr('disabled');
					location.reload();
				},
				error:function(response){
					alert("Kindly Pick a valid Address");
					$('.addAdreesPassengerBtn').removeAttr('disabled');
					
				} 
			});

	});
/*--//ajax jquery end for save passenger address--*/
/*--code start for delete passenger address--*/
	$( document ).on( "click", ".deletePassengerAddress", function(event){
		event.preventDefault();
		var addressID = $(this).attr('addressID');
		$.ajax({
			type:'get',
			dataType : "json",
			url:deletePassengerAddress,
			data : {addressID: addressID },

			beforeSend: function( xhr ) {
				//$('body').append('<div id="loadering"><img src="'+loadingImage+'"></div>');
				//return false;
			},
			success:function(response)
			{
				location.reload();
			}
		});
	});
	$( document ).on( "click", ".UpdateAdreesPassengerBtn", function(event){
		event.preventDefault();
		updateaddressForm = jQuery(this);
		var addId = updateaddressForm.attr('addId');
		var _token = updateaddressForm.closest("form").find("input[name=_token]").val();
		var latitude = updateaddressForm.closest("form").find(".latitude").val();
		var longitude = updateaddressForm.closest("form").find(".longitude").val();
		var address = updateaddressForm.closest("form").find(".address").val();
		var default_check= updateaddressForm.closest("form").find("input[name=default_address]:checked").val();
		$.ajax({
			type:'get',
			dataType : "json",
			url:updatePassengerAddress,
			data : {addId : addId,_token : _token,latitude : latitude,longitude : longitude,address : address,default_check : default_check},

			beforeSend: function( xhr ) {
				//$('body').append('<div id="loadering"><img src="'+loadingImage+'"></div>');
				//return false;
			},
			success:function(response)
			{
				location.reload();
			}
		});
	});
/*--code end for delete passenger address--*/
/*--code start for send referrel code using phone number--*/
	jQuery("#sendreferrelcode .sendreferrelcode").click(function(event){
		event.preventDefault();
		referrelcodeForm = jQuery(this);
		var _token = referrelcodeForm.closest("form").find("input[name=_token]").val();
		var referralCode = referrelcodeForm.closest("form").find("#referralCode").val();
		var phoneCode = referrelcodeForm.closest("form").find("#phonecode1").val();
		var phoneNumber = referrelcodeForm.closest("form").find(".referralContact").val();
			if(jQuery( "#sendreferrelcode" ).valid()){
				jQuery(".lodingDiv").show();
					$.ajax({
						type:'post',
						dataType : "json",
						url:referralcode,
						data : {_token : _token,referralCode : referralCode,phoneCode : phoneCode,phoneNumber : phoneNumber},

						beforeSend: function( xhr ) {
							//$('body').append('<div id="loadering"><img src="'+loadingImage+'"></div>');
							//return false;
						},
						success:function(response)
						{
							referrelcodeForm.closest("form").submit();
							location.reload();
							jQuery(".lodingDiv").hide();
						}
					});
			}
	});
/*--//code end for send referrel code using phone number--*/


});