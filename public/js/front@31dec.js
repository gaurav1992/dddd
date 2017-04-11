var host = window.location.host;
var proto = window.location.protocol;
var ajax_url = proto+"//"+host+"/deziNow/";
var ajax_form_url = proto+"//"+host;

$(function(){
	
	$('#homeUniversityID').combobox();
	$('#hostUniversityID').combobox();
	$('#exchangeTerm').combobox();
	$('#homecountry').combobox();
	$('#hostNewcountry').combobox();
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
	 
	 // upload profile image
	if($('#uploadImage').length>0)
	{
		var token = $("input[name='_token']").val();
		var userID = $('#uploadImage').attr('data-rel');
		console.log(userID);
		myaction = ajax_url+'users/uploadImage';
		var btnUpload=$('#uploadImage');
		var curImgSrc = $('#uploadImage').attr('src');
		var loadingImgSrc = ajax_url+'public/img/loader.gif';
		new AjaxUpload(btnUpload, {
			action: myaction,
			name: 'uploadfile',
			data: {"userId": userID, "_token": token},
			onSubmit: function(file, ext)
			{
				
				$("#uploadImage").attr('src',loadingImgSrc);
				$('#uploadImage').addClass('centerLoader');
				if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext)))
				 { 
					// extension is not allowed 
					alert("Only JPG, PNG, or GIF files are allowed");
					$('#uploadImage').removeClass('centerLoader');
					$("#uploadImage").attr('src',curImgSrc);
					return false;	
				}
			},
			onComplete: function(file, response)
			{
			
				//Add uploaded file to list
				html=response.split(":");
				
				if($.trim(html[0])==="success")
				{
					overlayMessageShow('success','Profile image has been successfully uploaded.');
					//$("#uploadImage").attr('src',ajax_url+'img/Front/memberImages/'+html[1]);
					window.location.reload()
					
				}
				
				else
				{
					overlayMessageShow('error','There was some error in uploading.Try again!');
					$('#uploadImage').removeClass('centerLoader');
					$("#uploadImage").attr('src',curImgSrc);
				}
			}
		});
	}

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
		$('#custom_success').html(msg);
		$('#custom_success').show();
	}
	else if(msgType == 'error')
	{
		$('#custom_error').html(msg);
		$('#custom_error').show();
	}
}




 
