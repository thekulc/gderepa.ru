$('#btnLogin').on('click', function(){
	var params = "width=800,height=600";
	var hrefParams = "?setUserSectionPermissions";
	var authWindow = window.open("/calendar/auth" + hrefParams, "Войти с помощью ВК", params);
	return false;
});

$("#registrationForm").on('submit', function(){
	$("button>i ", this).removeClass('uk-hidden');
	$(".uk-alert", this).html('').addClass('uk-hidden');
	var message = [];
	var user = {};
	user.email = $('input[name="email"]', this).val().trim();
	user.password = $('input[name="password"]', this).val();
	dblPass = $('input[type="password"]', this)[0].value;
	
	if (!isValidEmail(user.email)){
		message.push("Введеннный email некорректен");
	}
	if ((user.password != dblPass) || user.password.length <= 0 || dblPass.length <= 0){
		message.push("Пароли не совпадают");
	}
	if (message.length <= 0){
		registration(user, '#registrationForm');
	}
	$("button>i ", this).addClass('uk-hidden');
	showAlert(implode('<br>', message), '#registrationForm','');
	return false;
});

function registration(user, parent){
	$.ajax({
		url: '/users/registration',
		type: "POST",
		data: user,
		complete: function(response){
			messages = implode('<br>', JSON.parse(response.responseText));
			if (messages.message){
				showAlert(messages.message, parent,'');
				$("#registrationForm button>i").addClass('uk-hidden');
			}
			else if (messages.success){
				showAlert(messages.success, parent, "uk-alert-success");
				$("#registrationForm button>i").addClass('uk-hidden');
			}/*
			else
				location.reload();*/
		}	
	});
}

$("#loginForm").on('submit', function(){
	$("button>i ", this).removeClass('uk-hidden');
	$(".uk-alert", this).html('').addClass('uk-hidden');
	var user = {};
	user.email = $('input[name="email"]', this).val().trim();
	user.password = $('input[name="password"]', this).val();
	
	//Включить на продакшене для логина только через Email
	if (isValidEmail(user.email) && user.password.length > 0)
		if (user.password.length > 0){
			login(user, '#loginForm');
		}
		else {
			$("button>i ", this).addClass('uk-hidden');
			showAlert("Введеннный email некорректен", "#loginForm",'');
		}
	
	return false;
});

function login(user, parent){
	$.ajax({
		url: '/users/login',
		type: "POST",
		data: user,
		complete: function(response){
			errors = implode('<br>', JSON.parse(response.responseText)['errors']);
			if (errors){
				showAlert(errors, parent, '');
				$("#loginForm button>i").addClass('uk-hidden');
			}
			else
				location.reload();
		}	
	});
}

function showAlert(message, parent, status){
	if (status == "")
		status = 'uk-alert-danger';

	if (message.length > 0 && parent.length > 0)
		$(parent + ' .uk-alert').html(message).addClass(status).removeClass('uk-hidden');
}