$(document).ready(function() {
    $('#loginBtn').click(loginBtnHandler);
    $('.reservationBtn').click(reserveDay);
    $('.cancelRepBtn').click(cancelDay);
    $('#logoutBtn').click(logoutBtnHandler);
	$('.clickable').click(showContacts);
});
var showing = false;
function showContacts(){
	var userid = parseInt($(this).attr('data-userid'));
	
	var tOffset = $(this).position();
	if (!showing){
		$contact = $('#atr_'+userid).clone();
		$contact.css('position', 'absolute');
		$contact.show();
		$contact.offset({top: tOffset.top + $(this).outerHeight(), left: tOffset.left});
		
		addClose($contact);
		$contact.removeClass('invisible');
		$(this).parent().append($contact);
		showing = true;
		$('.clickable').css('border', 'none');
	}
	
	return false;
}

function addClose(obj){
	var btn = $('<i\>', {class: 'closeBtn'});
	$(btn).html('X');
	$(btn).attr('style', 'position: absolute; top: -12px; font-size: 10px; display: inline-block;font-style: normal;font-weight: bold;background-color: lightgray;border: 1px solid;border-radius: 50%;width: 19px;line-height: 18px;text-align: center;');
	$(btn).on('click', function(){ $(obj).remove(); showing = false; $('.clickable').css('border-bottom', '1px dashed');});
	$(btn).hover(function(){$(btn).css('background-color', 'lightyellow');}, function(){$(this).css('background-color', 'lightgray')});
	$(obj).append(btn);
}

function logoutBtnHandler(){
    $.ajax({
        url: $(this).attr('href')
    }).done(function(){
        location.reload();
    });
    return false;
}

function cancelDay(){
    $.ajax({
        url: $(this).attr('href')
    }).done(function(data){
        afterAjax(data)
    });
    return false;
}

function reserveDay(){
    var day = $(this).attr('id').split('_')[1];
    $.ajax({
        url: $(this).attr('href')
    }).done(function(data){
        afterAjax(data)
    });
    return false;
}

function loginBtnHandler(){
    /* $('#loginForm').slideToggle(1000); */
	$('#loginForm').toggle();
	/*var form = $('#loginForm');
	if (!form.hasClass('active')){
		form.effect('drop', {direction: 'up', mode: 'show'}, 500, form.toggleClass('active'));
	}
	else{
		form.effect('drop', {direction: 'up', mode: 'hide'}, 500, form.toggleClass('active'));
	}
	*/
    return false;
}

function showForm(){
	//$('#loginForm').effect('bounce', {direction: 'down', mode: 'show'}, 20, 5], 1000);
}

function hideForm(){
	//$('#loginForm').effect('bounce', ['up', 'hide', 20, 5], 1000);
}

function afterAjax(data){
    if (data != "1"){
        $('body').append('<p>При выполнении запроса произошла ошибка: ' + data + '</p>');
    }
    else{
        location.reload();
    }
}

function pr(txt){
    console.log(txt);
}