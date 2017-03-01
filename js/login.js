$(document).ready(function(){
"use strict";
$("#login").click(function(){ 
	
	var username = $("#username").val();
	var password = $("#password").val();
	
	//checking for blank fields
	if( username ==='' || password ==='')
		{
		  $('input[type="text"],input[type="password"]').css("border","2px solid red");
		  $('input[type="text"],input[type="password"]').css("box-shadow","0 0 3px red");
		  alert("Please fill all fields...!!!!!!");
		}	
	
	else 
	   {
	     $.post("login.php",{ username1: username, password1:password},
		  function(data) {
		    if(data==='Username or Password is wrong...!!!!')
			   {
				$('input[type="text"],input[type="password"]').css({"border":"2px solid red","box-shadow":"0 0 3px red"});
				alert(data);
			   } 
			else
		   {
			   alert("Welcome!");
			   window.location="course.php?id=" + data;
			//$("form")[0].reset();
			//$('input[type="text"],input[type="password"]').css({"border":"2px solid #00F5FF","box-shadow":"0 0 5px #00F5FF"});
		   } 
		});
	   }
	
	});

});
