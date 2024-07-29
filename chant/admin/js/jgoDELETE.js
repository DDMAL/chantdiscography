
// JavaScript Document



$(document).ready(function(){
		
//Insert into Database
   $("button.sendName").click(function(){
        //add a Record
       if ($("button.sendName").val() == "update"){
            var record_id = $("#record_id").val(); 
            var fc_update = $("#format_code").val(); 
            var serial_num = $("#serial_num").val();
            var cc = $("#country_code").val();
            var label = $("#label_name").val(); 
            var prefix = $("#prefix_to_num").val();
            var suffix = $("#suffix").val();
            var issue = $("#issue_num").val();
            var title = $("#record_title").val();
            var alt_num = $("#alt_num").val();
            var performers = $("#performers").val();
            var director = $("#director").val();
            var solo = $("#solo").val();
            var date = $("#date").val();
            var comments = $("#comments").val();
            $.post("implement/i.record.php", { fc_update: fc_update, serial_num: serial_num, cc: cc, label: label, prefix: prefix, suffix: suffix, issue: issue, title: title, alt_num: alt_num, performers: performers, director: director, solo: solo, date: date, comments: comments, record_id: record_id }, //this is JSON that gets posted
            function(data){
                $("#loadName").html(data).slideDown("slow"); // send this data from the PHP echo into the #screen DIV
               // $("#update").fadeIn("slow");
                $("button.sendName").val('track');
                $("button.sendName").html('Submit Chant');
            }); 

        }

		if ($("button.sendName").val() == "record"){
            var fc = $("#format_code").val(); 
            var serial_num = $("#serial_num").val();
            var cc = $("#country_code").val();
            var label = $("#label_name").val(); 
            var prefix = $("#prefix_to_num").val();
            var suffix = $("#suffix").val();
            var issue = $("#issue_num").val();
            var title = $("#record_title").val();
            var alt_num = $("#alt_num").val();
            var performers = $("#performers").val();
            var director = $("#director").val();
            var solo = $("#solo").val();
            var date = $("#date").val();
            var comments = $("#comments").val();
		
            $.post("implement/i.record.php", { fc: fc, serial_num: serial_num, cc: cc, label: label, prefix: prefix, suffix: suffix, issue: issue, title: title, alt_num: alt_num, performers: performers, director: director, solo: solo, date: date, comments: comments }, //this is JSON that gets posted
            function(data){
                $("#loadName").html(data).slideDown("slow"); // send this data from the PHP echo into the #screen DIV
              //  $("#update").fadeIn("slow");
                $("button.sendName").val('track');
                $("button.sendName").html('Submit Chant');
            }); 
        }
        if ($("button.sendName").val() == "track"){
            //add a Track
            var record_id = $("#record_id").val(); 
            var item_num = $("#item_num").val();
            var track_num = $("#track_num").val(); 
            var title_of_chant = $("#title_of_chant").val();
            var page = $("#page").val();
            var time = $("#time").val();
            var comments = $("#comments").val();
		
            $.post("implement/i.track.php", { record_id: record_id, item_num: item_num, track_num: track_num, title_of_chant: title_of_chant, page: page, time: time, comments: comments }, 
            function(data){
                $("#results").html(data).slideDown("slow"); // send this data from the PHP echo into the #screen DIV
                $("#item_num").val('');
                $("#track_num").val(''); 
                $("#title_of_chant").val('');
                $("#page").val('');
                $("#time").val('');
                $("#comments").val('');            
            });

        }
    });
}); //end DOC