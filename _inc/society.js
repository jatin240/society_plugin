jQuery('.register').click(function(){  
        jQuery('#myregister').removeClass("hidden");
        jQuery('#mylogin').addClass("hidden");
        //jQuery('h1').hide();
});
 
    jQuery('.login').click(function(){  
        jQuery('#mylogin').removeClass("hidden");
        jQuery('#myregister').addClass("hidden");
});                 

$(".area").hide();
    $(".update_value").hide();
    function editCost(id){
    //console.log(id);     
        $(".area"+id).removeClass("hidden");
        $(".update_value").show();
        $(".edit").hide();
    }


function updateuser(id){
   var time = jQuery("#time"+id).val();
    var id = id;
    $.ajax({
      type: "post",
      url: ajaxurl,
      data: {action:"update_userdata",time:time,id:id},
      success: function(response) {
        //console.log("hello");
        location.reload();
        }
    });
    
}

   
$(document).ready(function () {
    $('#table_user').DataTable();
});

