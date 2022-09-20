jQuery('.customer_value').hide();
jQuery('.check').click(function(){
  jQuery('.customer_value').show();
        //alert("tyfuy");
});

function updatecustomer(id){
   var status = jQuery("#status"+id).val();
    var id = id;
    //console.log(id);
    jQuery.ajax({
       type: "post",
       url: ajaxurl,
       data: {action:"update_statususer",status:status,id:id},
      success: function(response) {
        //console.log("hello");
        location.reload();
      }
    });
}