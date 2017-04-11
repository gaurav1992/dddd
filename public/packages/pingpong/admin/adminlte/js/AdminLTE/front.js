var host = window.location.host;
var proto = window.location.protocol;
var ajax_url = proto+"//"+host+"/";
var ajax_form_url = proto+"//"+host;

$(function(){
	

	$('.pro-img').hover(
		/*function(){$(this).next('.upload-icon').addClass('dBlk');},
		function(){$(this).next('.upload-icon').removeClass('dBlk');}*/
		);
	
	/*$("#aboutUsSection").click(function() {
		$('html, body').animate({
			scrollTop: $(".one_section").offset().top
		}, 2000);
	});*/
	
	var focusElement = $(".response-msg");
	$(focusElement).focus();
	ScrollToTop(focusElement);
	
	$('.response-msg').on('click',function(){
			$(this).slideUp(1000);
	});
	setInterval(function() {
	   $('.response-msg').slideUp();}, 6000);
	
	//$('.response-msg').delay(5000);
	$(window).scroll(function()
	{
		if($(window).scrollTop() < 0)
		{
			$('#scc_ovrly_shw').css({'position':'absolute', 'top':'50px'});
			$('#err_ovrly_shw').css({'position':'absolute', 'top':'50px'});
		}
		else
		{
			$('#scc_ovrly_shw').css({'position':'fixed', 'top':'0px'});
			$('#err_ovrly_shw').css({'position':'fixed', 'top':'0px'});
		}
	});	
	
	
	$("body").on('change','#homeUniv',function() {
		filterExchange();
	});
	
	$("body").on('change','#hostUniv',function() {
		filterExchange();
	});
	
	$("body").on('keyup','#exchangeKeyword',function() {
		filterExchange();
	});
	$("body").on('change','#hostCountryC',function() {
		filterExchange();
	});
	
	
	
	$('#countryHome').combobox();
	$('#homeUniv').combobox();
	$('#hostUniv').combobox();
	
	$('#homeUniversityID').combobox();
	$('#hostUniversityID').combobox();
	$('#exchangeTerm').combobox();
	$('#homecountry').combobox();
	$('#hostNewcountry').combobox();
	$('#hostCountryC').combobox();
	$('#type').combobox();
	/*$('.selectType').click(function() {
		$('.selectType').removeClass('active');
		$(this).addClass('active');
		
		var typeUser = $(this).attr('data-usertype');
		
		$('#type').val(typeUser);
		
		if(typeUser=='3' || typeUser=='4') {
			$('.type3-4').hide();
		}else {
			$('.type3-4').show();
			if($("input[name='hostUniversityID']").val()!='1') {
				$('#newHostUniversityDiv').hide();
			}
		}
	});
	*/
	
	$("input[name='type']").on("change", function () {
		var typeVal = $(this).val();
		
		if (typeVal === undefined || typeVal === "") {
           
		} else {
		
		    if(typeVal=='3' || typeVal=='4') {
				$('.type3-4').hide();
			}else {
				$('.type3-4').show();
				if($("input[name='hostUniversityID']").val()!='1') {
					$('#newHostUniversityDiv').hide();
				}
			}
			//$(this).next('.input-group').find('input').focus();
        }
	 });
	
	 $("body").on("click",".glyphicon-remove",function(){
		
		if($(this).parents('.home-univ')) {
			$('#newHomeUniversityDiv').hide();
		}else{
		
			$(this).parents('.form-group').next().find('#hostCountry').val('');
			$(this).parents('.form-group').next().next('#newHostUniversityDiv').hide();
			$(this).parents('.form-group').next('.hostCountryBlk').show();
		}
	 });	
	
	 $("input[name='hostUniversityID']").on("change", function () {
		var hostUnivId = $(this).val();
		if (hostUnivId === undefined || hostUnivId === "") {
            $('#hostCountry').val("");
		} else {
		    if(hostUnivId=='1') {
				$('#newHostUniversityDiv').show();
				$('.hostCountryBlk').hide();
			}else{
				$('#newHostUniversityDiv').hide();
				$('.hostCountryBlk').show();
				var token = $("input[name='_token']").val();
				var id = hostUnivId;
				$.ajax({
				   type:'post',
					url: ajax_url+'university/getUniversityCountryName',
					data: {"id": id, "_token": token},
					dataType: 'json',
					success:function (response) {
						$('#hostCountry').val(response.data);
					}

				});
			}
			
        }
	 });
	 
	 
	  $("input[name='homeUniversityID']").on("change", function () {
		var homeUnivId = $(this).val();
		
		if (homeUnivId === undefined || homeUnivId === "") {
           
		} else {
		    if(homeUnivId=='1') {
				$('#newHomeUniversityDiv').show();
			}else{
				$('#newHomeUniversityDiv').hide();
			}
			
        }
	 });
	 
	

});



function overlayShow()
{
	$('#loading_overlay').show();
	$('#loading_overlay').children().show();
}
function overlayHide()
{
	$('#loading_overlay').hide();
	$('#loading_overlay').children().hide();
}
function ScrollToTop(el) {
	 $( "html, body" ).animate({
		scrollTop: 0,
		});
	
}
// custom message
function overlayMessageShow(msgType, msg)
{
	if(msgType == 'success')
	{
		$('#custom_success_client').html(msg);
		$('#custom_success_client').show();
	}
	else if(msgType == 'error')
	{
		$('#custom_error_client').html(msg);
		$('#custom_error_client').show();
	}
}

function filterExchange() {
	
	var homeUniv = $('#homeUniv').val();
	var hostUniv = $('#hostUniv').val();
	var exchangeKeyword = $('#exchangeKeyword').val();
	var hostCountryC = $('#hostCountryC').val();
	

	var counter = 0;
    var tempArr = new Array();
	var tempArr1 = new Array();
	var tempArr2 = new Array();
	var tempArr3 = new Array();
	
	
	$( ".gridView" ).each(function() {
		$(this).hide();
		object = $(this);

		if(homeUniv!='') {
			counter = counter+1;
			var destinationHome = object.attr( "data-homeuniv");
			homeUniv = homeUniv.replace('(','');
			homeUniv = homeUniv.replace(')','');
			var reHome = new RegExp(homeUniv);
			if (destinationHome.match(reHome)) {
				//object.show();
				tempArr.push(object.attr('id'));
			}
		}
		
		
		if(hostUniv!='') {
			counter = counter+1;
			/* Host filter */
			var destinationHost = object.attr( "data-hostuniv");
			hostUniv = hostUniv.replace('(','');
			hostUniv = hostUniv.replace(')','');
			
			
			var reHost = new RegExp(hostUniv);
			if (destinationHost.match(reHost)) {
				tempArr1.push(object.attr('id'));
				//object.show();
			}
		}
		
		if(exchangeKeyword!='') {
			counter = counter+1;
			/* Host filter */
			var destinationEkhomeUniv = object.attr( "data-homeuniv");
			var destinationEkhostUniv = object.attr( "data-hostuniv");
			var destinationEkgoing = object.attr( "data-going");
			var destinationEkStudent = object.attr( "data-student");
			var destinationEkName = object.attr( "data-name");
			
			exchangeKeyword = exchangeKeyword.replace('(','');
			exchangeKeyword = exchangeKeyword.replace(')','');
			
			
			var reEk = new RegExp(exchangeKeyword);
			if (destinationEkhomeUniv.match(reEk) || destinationEkhostUniv.match(reEk) || destinationEkgoing.match(reEk) || destinationEkStudent.match(reEk) || destinationEkName.match(reEk)) {
				//object.show();
				tempArr2.push(object.attr('id'));
			}
		}
		
		
		if(hostCountryC!='') {
			counter = counter+1;
			/* Host filter */
			var destinationHostC = object.attr( "data-going");
			hostCountryC = hostCountryC.replace('(','');
			hostCountryC = hostCountryC.replace(')','');
			var reHostC = new RegExp(hostCountryC);
			if (destinationHostC.match(reHostC)) {
				tempArr3.push(object.attr('id'));
				//object.show();
			}
		}
	});	
	
	console.log(tempArr);
	console.log(tempArr1);
	console.log(tempArr2);
	console.log(tempArr3);
	
	var tempFinalArray = new Array();
	tempFinalArray = $.merge( tempFinalArray, tempArr );
	// tempFinalArray.push(tempArr);
	 /*tempFinalArray.push(tempArr1);
	 tempFinalArray.push(tempArr2);
	 tempFinalArray.push(tempArr3);
	
	*/
	console.log(tempArr1.length);
	
	if(tempArr1.length>0){
		tempFinalArray = $.merge( tempFinalArray, tempArr1 );
	}
	
	if(tempArr2.length>0){
		tempFinalArray = $.merge( tempFinalArray, tempArr2 );
	}
	if(tempArr3.length>0){
		tempFinalArray = $.merge( tempFinalArray, tempArr3 );
	}
	
	if(tempArr1.length>0 || tempArr2.length>0 || tempArr3.length>0 ){
		var sorted_arr = tempFinalArray.sort();
	        //console.log('sdf');
			console.log(tempFinalArray.length);                    
		var results = [];
		if(tempFinalArray.length>1){
			for (var i = 0; i < tempFinalArray.length - 1; i++) {
				if (sorted_arr[i + 1] == sorted_arr[i]) {
					results.push(sorted_arr[i]);
				}
			}
		}else{
			results.push(sorted_arr[0]);
		}
	}else {
		var results = tempFinalArray;
	}
	
	console.log(results);     
	$.each(tempFinalArray,function(e){
		$('#'+tempFinalArray[e]).show();
	});
	

	if(!counter)
	{
		$( ".gridView" ).show();
	}
}


function validateSubscriber() {
	var form = $("#about_sub_form");
	
	form.validate();
	
	if(form.valid()) {
		var formAction = form.attr('action');
		var email = $("#email");
		var token = $("input[name='_token']");
		var emailValue = email.val();
		var tokenValue = token.val();
		var subscriberMessageBoxDivID = "#subscriberMessageBox";
		$.ajax({
			type: "post",
			url: formAction,
			data: {"email": emailValue, "_token": tokenValue},
			dataType: 'json',
			success: function (response, textStatus, jqXHR) {
				//remove existing error classes and error messages from form groups
				if (response.success) {
					overlayMessageShow('success',response.data);
				} else {
					overlayMessageShow('error',response.data);
				}

			},
			error: function (response) {
				var errors = JSON.parse(response.responseText);
				if (response.status === 422) {
					associate_errors(errors, form);
				}
			}
		});
	}
	return false;
}
function associate_errors(errors, $form) {
	//remove existing error classes and error messages from form groups
	$form.find('.form-group').removeClass('has-feedback').removeClass('has-error').find('.help-block').text('');
	//$form.find('.form-group').find('.glyphicon').removeClass('glyphicon-remove');
	$form.find("#subscribeBtn").removeClass('btn-danger').addClass('btn-default');
	$form.find("#subscribeBtn").css('border-color', "#ccc");
	for (var key in errors) {
		//find each form group, which is given a unique id based on the form field's name
//                var $group = $form.find('#' + key + '-group');
		var $group = $form.find('#' + key);
		//$group.parent().parent().append('<span style="margin-right:6em;" class="glyphicon form-control-feedback glyphicon-remove"></span>');
		var $msg = $form.find('.' + key + '-form-group').append('<span class="help-block"> </span>');
		//add the error class and set the error text
		$msg.addClass('has-feedback').addClass('has-error').find('.help-block').text(errors[key]);
		//$form.find("#subscribeBtn").removeClass('btn-default').addClass('btn-danger');
		$form.find("#subscribeBtn").css('border-color', "#a94442");
	}
}

function sendRequest(obj) {
	
	var UID = $('#uId').val();
	var TOID = $('#toId').val();
	
	var MESSAGE = $('#message').val();
	
	$('#requestForm').validate();
	if($('#requestForm').valid()) {
			$(obj).text('Processing...');
			$(obj).attr('onclick','');
			if(TOID!='' && UID!='') {
			var frm = $('#requestForm');
			
			var formAction = frm.attr('action');
		
			var token = frm.find("input[name='_token']");
		
			var tokenValue = token.val();
			var subscriberMessageBoxDivID = "#subscriberMessageBox";
			$.ajax({
				type: "post",
				url: formAction,
				data: {"toId": TOID,"uId": UID,"message": MESSAGE, "_token": tokenValue},
				dataType: 'json',
				success: function (response, textStatus, jqXHR) {
					//remove existing error classes and error messages from form groups
					if (response.success) {
						overlayMessageShow('success',response.data);
					} else {
						overlayMessageShow('error',response.data);
					}
					$(obj).text('Submit');
					$(obj).attr('onclick','return sendRequest(this);');
					$('.closeModal').trigger('click');
				},
				error: function (response) {
					var errors = JSON.parse(response.responseText);
					if (response.status === 422) {
						associate_errors(errors, form);
					}
				}
			});
		}else {
			alert('Some problem occur. Please try again!');return false;
		}
	}
	
	return false;
}

function openModalPop(obj) {
	
	$('#requestPopUp').find('label.error').hide();
	var uId = $(obj).attr('data-userid');
	var toId = $(obj).attr('data-toid');
	
	$('#uId').val(uId);
	$('#toId').val(toId);
	
	$('#message').val($(obj).attr('data-personalizedMsg'));
	$('#requestPopUp').modal(); 

}

function bulktransfer(){
	var userstatus = $("#userstatus").val();
	console.log(userstatus);
	data[Member][check][]
}

