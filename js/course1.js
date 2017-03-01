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
		    if(data==='Invalid Email.......')
			   {
				$('input[type="text"]').css({"border":"2px solid red","box-shadow":"0 0 3px red"});
				$('input[type="password"]').css({"border":"2px solid #00F5FF","box-shadow":"0 0 5px #00F5FF"});
				alert(data);
			   }
		    else if(data==='Username or Password is wrong...!!!!')
			   {
				$('input[type="text"],input[type="password"]').css({"border":"2px solid red","box-shadow":"0 0 3px red"});
				alert(data);
			   } 
			else if(data==='Successfully Logged in...')
		   {
			   alert(data);
			   window.location="course.html";
			//$("form")[0].reset();
			//$('input[type="text"],input[type="password"]').css({"border":"2px solid #00F5FF","box-shadow":"0 0 5px #00F5FF"});
		   }   
			else
				{
				 alert(data);
				}   
		   
		});
	   }
	
	});

});
