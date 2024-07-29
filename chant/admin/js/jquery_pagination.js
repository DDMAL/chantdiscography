$(document).ready(function(){
		
	//Display Loading Image
	function Display_Load()
	{
	    $("#loading").fadeIn(900,0);
		$("#loading").html("<img src='bigLoader.gif' />");
	}
	//Hide Loading Image
	function Hide_Load()
	{
		$("#loading").fadeOut('slow');
	};
    

   //Default Starting Page Results
   
	$("#pagination li:first").css({'color' : '#FF0084'}).css({'border' : 'none'});
	
	Display_Load();
	if(document.getElementById('searchtxt').value != ''){
        stxt = document.getElementById('searchtxt').value;
        stxt = stxt.replace(" ","%20");
       // alert(stxt);
        $("#content").load("pagination_data.php?page=1&strtxt="+stxt, Hide_Load());
    }else{
        $("#content").load("pagination_data.php?page=1", Hide_Load());
    }



	//Pagination Click
	$("#pagination li").click(function(){
			
		Display_Load();
		
		//CSS Styles
		$("#pagination li")
		.css({'border' : 'solid #dddddd 1px'})
		.css({'color' : '#0063DC'});
		
		$(this)
		.css({'color' : '#FF0084'})
		.css({'border' : 'none'});

		//Loading Data
		var pageNum = this.id;
		//alert(document.getElementById('searchtxt').value);
		$("#content").load("pagination_data.php?page=" + pageNum, Hide_Load());
	});
	

});