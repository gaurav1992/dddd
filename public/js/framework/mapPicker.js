/*--code start for pic address from map--*/
$('#us2').locationpicker({
    location: {
        latitude: 36.778261,
        longitude: -119.41793239999998
    },
    radius: 10,
    inputBinding: {
        latitudeInput: $('#us2-lat'),
        longitudeInput: $('#us2-lon'),
        radiusInput: $('#us2-radius'),
        locationNameInput: $('#us2-address')
    },
    enableAutocomplete: true
});

jQuery(".updateAddress").on('click',function(){
  //jQuery(".lodingDiv").show();

  ADDRESSID = jQuery(this).attr("data-addId");
  modelId = jQuery(this).attr("data-id");
  mapId = jQuery(this).attr("data-mapID");
  mapIdHash = jQuery(this).attr("data-map");
  maplatNo = jQuery(this).attr("data-latNo");
  maplongNo = jQuery(this).attr("data-longNo");
  maplatidHash = jQuery(this).attr("data-lat");
  maplongidHash = jQuery(this).attr("data-long");
  maplatID = jQuery(this).attr("data-latID");
  maplongID = jQuery(this).attr("data-longID");
  mapAddress = jQuery(this).attr("data-Address");
  mapAddressIDHash = jQuery(this).attr("data-location");
  mapAddressID = jQuery(this).attr("data-locationID");
  DefaultAddress = jQuery(this).attr("data-defaultValue");
    if(DefaultAddress == '1'){
      jQuery(".updateaddressmodel #inlineRadio3").prop("checked", true);
    }else{
      jQuery(".updateaddressmodel #inlineRadio3").removeAttr("checked");
    }
    if(DefaultAddress == '0'){
      jQuery(".updateaddressmodel #inlineRadio4").prop("checked", true);
    }else{
      jQuery(".updateaddressmodel #inlineRadio4").removeAttr("checked");
    }

  jQuery(".updateaddressmodel").attr("id",modelId);
  jQuery(".updateaddressmodel .mapDiv").attr("id",mapId);
  jQuery(".updateaddressmodel .latitude").attr("id",maplatID);
  jQuery(".updateaddressmodel .longitude").attr("id",maplongID);
  jQuery(".updateaddressmodel .AddressForupdate").attr("id",mapAddressID);
  jQuery(".updateaddressmodel .AddressForupdate").attr("value",mapAddress);
  jQuery(".updateaddressmodel .UpdateAdreesPassengerBtn").attr("addId",ADDRESSID);
  setTimeout(function(){
    //jQuery(".lodingDiv").hide();
    $(mapIdHash).locationpicker({
        location: {
            latitude: maplatNo,
            longitude: maplongNo
        },
        radius: 10,
        inputBinding: {
            latitudeInput: $(maplatidHash),
            longitudeInput: $(maplongidHash),
            locationNameInput: $(mapAddressIDHash)
        },
        enableAutocomplete: true
    });
  }, 3000);

});
jQuery(".MapCloseBtn").on('click',function(){
  jQuery(".updateaddressmodel").attr("id",'');
  jQuery(".updateaddressmodel .mapDiv").attr("id",'');
  jQuery(".updateaddressmodel .latitude").attr("id",'');
  jQuery(".updateaddressmodel .longitude").attr("id",'');
  jQuery(".updateaddressmodel .AddressForupdate").attr("id",'');
  jQuery(".updateaddressmodel .AddressForupdate").attr("value",'');
  jQuery(".updateaddressmodel .UpdateAdreesPassengerBtn").attr("addId",'');
});
/*--//code end for pic address from map--*/