
// JavaScript Document



$(document).ready(function(){
		
//Insert into Database
   $("button.sendLogin").click(function(){
        //add a Record
       if ($("#user").val() == ""){
          $("#user_error").html('Enter a user ID').fadeIn("slow");
        }else {
            $("#user_error").html('').fadeIn("slow");
        }
        if ($("#password").val() == ""){
          $("#password_error").html('Enter a password').fadeIn("slow");
        }else {
             $("#password_error").html('').fadeIn("slow");
        }
        if ($("#password").val() != "" && $("#user").val() != ""){
             $.post("implement/i.login.php", { userid: $("#user").val(), password: $("#password").val() }, //this is JSON that gets posted
            function(data){
                if(data==true){
                    window.location="insert.php";
                }else{
                    $("#password_error").html(data).slideDown("slow"); 
                }
            }); 

        }
    });
}); //end DOC

