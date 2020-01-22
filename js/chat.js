$(document).ready(function(){
	setInterval(function(){
		updateUserList();	
		updateUnreadMessageCount();	
	}, 600);	//60000
	setInterval(function(){
		showTypingStatus();
		updateUserChat();			
	}, 500); //5000
	$(".messages").animate({ 
		scrollTop: $(document).height() 
	}, "fast");
	$(document).on("click", '#profile-img', function(event) { 	
		$("#status-options").toggleClass("active");
	});
	$(document).on("click", '.expand-button', function(event) { 	
		$("#profile").toggleClass("expanded");
		$("#contacts").toggleClass("expanded");
	});	
	$(document).on("click", '#status-options ul li', function(event) { 
            
            var status;
		$("#profile-img").removeClass();
		$("#status-online").removeClass("active");
		$("#status-away").removeClass("active");
		$("#status-busy").removeClass("active");
		$("#status-offline").removeClass("active");
		$(this).addClass("active");
		if($("#status-online").hasClass("active")) {
			$("#profile-img").addClass("online");
                        status = 1;
		} else if ($("#status-away").hasClass("active")) {
			$("#profile-img").addClass("away");
                        status = 2;
		} else if ($("#status-busy").hasClass("active")) {                        
			$("#profile-img").addClass("busy");
                        status = 3;
		} else if ($("#status-offline").hasClass("active")) {
			$("#profile-img").addClass("offline");
                        status = 0;
		} else {
			$("#profile-img").removeClass();
		};
		$("#status-options").removeClass("active");
                updateStatus(status);
                
	});	
	$(document).on('click', '.contact', function(){		
		$('.contact').removeClass('active');
		$(this).addClass('active');
		var to_user_id = $(this).data('touserid');
		showUserChat(to_user_id);
		$(".chatMessage").attr('id', 'chatMessage'+to_user_id);
		$(".chatButton").attr('id', 'chatButton'+to_user_id);
	});	
	$(document).on("click", '.submit', function(event) { 
		var to_user_id = $(this).attr('id');
		to_user_id = to_user_id.replace(/chatButton/g, "");
		sendMessage(to_user_id);
	});
	$(document).on('focus', '.message-input', function(){
		var is_type = 'yes';
		$.ajax({
			url:"chat_action.php",
			method:"POST",
			data:{is_type:is_type, action:'update_typing_status'},
			success:function(){
			}
		});
	}); 
	$(document).on('blur', '.message-input', function(){
		var is_type = 'no';
		$.ajax({
			url:"chat_action.php",
			method:"POST",
			data:{is_type:is_type, action:'update_typing_status'},
			success:function() {
			}
		});
	}); 	
        $(document).on("click", '#settings', function(event) { 
            alert("Test");        
            $( "#dialog" ).dialog();
                    
            //showSettings();
        });
        
        $("[id^=chatMessage").keyup(function(event) {
            if (event.keyCode === 13) {
		var to_user_id = $(this).attr('id');
		to_user_id = to_user_id.replace(/chatMessage/g, "");
		sendMessage(to_user_id);            }
        });
}); 
function updateUserList() {
    
	$.ajax({
		url:"chat_action.php",
		method:"POST",
		dataType: "json",
		data:{action:'update_user_list'},
		success:function(response){		
			var obj = response.profileHTML;
			Object.keys(obj).forEach(function(key) {
				// update user online/offline status
//                                console.log("#status_"+obj[key].userID).;
				if($("#"+obj[key].userID).length) {
                                    var currentClass = $("#status_"+obj[key].userID).attr('class');
					if(obj[key].status == 1 && !$("#status_"+obj[key].userID).hasClass('online'))
                                        {
                                            $("#status_"+obj[key].userID).removeClass(currentClass);
                                            $("#status_"+obj[key].userID).addClass('contact-status online');
					} else if(obj[key].status == 0 && !$("#status_"+obj[key].userID).hasClass('offline'))
                                        {
                                            $("#status_"+obj[key].userID).removeClass(currentClass);
                                            $("#status_"+obj[key].userID).addClass('contact-status offline');
					} else if(obj[key].status == 2 && !$("#status_"+obj[key].userID).hasClass('away'))
                                        {
                                            $("#status_"+obj[key].userID).removeClass(currentClass);
                                            $("#status_"+obj[key].userID).addClass('contact-status away');
					} else if(obj[key].status == 3 && !$("#status_"+obj[key].userID).hasClass('busy'))
                                        {
                                            $("#status_"+obj[key].userID).removeClass(currentClass);
                                            $("#status_"+obj[key].userID).addClass('contact-status busy');
					}
				}				
			});			
		}
	});
}
function sendMessage(to_user_id) {
	message = $(".message-input input").val();
	$('.message-input input').val('');
	if($.trim(message) == '') {
		return false;
	}
	$.ajax({
		url:"chat_action.php",
		method:"POST",
		data:{to_user_id:to_user_id, chat_message:message, action:'insert_chat'},
		dataType: "json",
		success:function(response) {
			var resp = $.parseJSON(response);			
			$('#conversation').html(resp.conversation);				
                        $(".messages").animate({ scrollTop: $('.messages').height() }, "fast");
                    }
	});	
}
function showUserChat(to_user_id){
	$.ajax({
		url:"chat_action.php",
		method:"POST",
		data:{to_user_id:to_user_id, action:'show_chat'},
		dataType: "json",
		success:function(response){
			$('#userSection').html(response.userSection);
			$('#conversation').html(response.conversation);	
			$('#unread_'+to_user_id).html('');
		}
	});
}
function updateUserChat() {
	$('li.contact.active').each(function(){
		var to_user_id = $(this).attr('data-touserid');
		$.ajax({
			url:"chat_action.php",
			method:"POST",
			data:{to_user_id:to_user_id, action:'update_user_chat'},
			dataType: "json",
			success:function(response){				
				$('#conversation').html(response.conversation);
                                $('#conversation').scroll();
                                $("#conversation").scrollTop($("#conversation")[0].scrollHeight);
			}
		});
	});
}
function updateUnreadMessageCount() {
	$('li.contact').each(function(){
		if(!$(this).hasClass('active')) {
			var to_user_id = $(this).attr('data-touserid');
			$.ajax({
				url:"chat_action.php",
				method:"POST",
				data:{to_user_id:to_user_id, action:'update_unread_message'},
				dataType: "json",
				success:function(response){		
					if(response.count) {
						$('#unread_'+to_user_id).html(response.count);	
					}					
				}
			});
		}
	});
}

function updateStatus(status){
    $.ajax({
        url:"chat_action.php",
	method:"POST",
	data:{action:'update_status', status:status},
        	dataType: "json",
		success:function(response){				
//			$('#isTyping_'+to_user_id).html(response.message);			
		}
	});
}

function showTypingStatus() {
	$('li.contact.active').each(function(){
		var to_user_id = $(this).attr('data-touserid');
		$.ajax({
			url:"chat_action.php",
			method:"POST",
			data:{to_user_id:to_user_id, action:'show_typing_status'},
			dataType: "json",
			success:function(response){				
				$('#isTyping_'+to_user_id).html(response.message);			
			}
		});
	});
}

function showSettings(){
    $.ajax({
		url:"chat_action.php",
		method:"GET",
		data:{action:'get_settings'},
		dataType: "json",
		success:function(response) {
			$('#content').html(response.settings);
                        console.log("test");	
		},

                
	});
}

function searchUser(){
    // Declare variables
  var input, filter, ul, li, a, i, txtValue;
  input = document.getElementById('mySearch');
  filter = input.value.toUpperCase();
  ul = document.getElementById("myUL");
  li = ul.getElementsByTagName('li');

  // Loop through all list items, and hide those who don't match the search query
  for (i = 0; i < li.length; i++) {
    a = li[i].getElementsByTagName("p")[0];
    txtValue = a.textContent || a.innerText;
    if (txtValue.toUpperCase().indexOf(filter) > -1) {
      li[i].style.display = "";
    } else {
      li[i].style.display = "none";
    }
  }
}
