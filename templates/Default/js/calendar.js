var ajax_url = "/studios/ajax";
var $modal;
var user_id;
var user_login;
$(document).ready(function(){
	//$(".dropdown-button").dropdown();
	//$('.modal-trigger').leanModal();
    //$('#logoutBtn').click(logoutBtnHandler);
	
	//user_id = $("#user_button").attr("data-user_id");
	//user_login = $("#user_button").attr("data-user_login");
				
	/*
	$(".calendar-container > div").on("click", function(){
		now = new Date();
		$date = $(this).attr("data-date");
		$modal = getModal("modal2");
		$timeGrid = $("ul", this).clone();
		$timeGrid.addClass("collapsible");
		$timeGrid.attr("data-collapsible","accordion");
		
		$('li', $timeGrid).each(function(idx, obj){
			var owner_id = $(this).attr("data-owner_id");
			var event_id = $(this).attr("data-event_id");
			var start_time = $(this).attr("data-time");
			var sDate = parseDate(start_time);
			var allowByDate = sDate >= now;
			var vkBtn = getVKButton(event_id);
			var contact;
			
			var header = $("<div />", {
				class: "collapsible-header",
				html: $(this).html()
			});
			$(this).html("");
			
			if (user_id){
				if ($('#atr_'+owner_id).html()){
					contact = "<p>" + $('#atr_'+owner_id).html() + '<a class="right" href="'+getEventUrl(event_id)+'">Ссылка на репу</a></p>';
					var body = $("<div />",{
						class: "collapsible-body",
						html: contact
					});
				}
				if (owner_id == user_id && allowByDate){
					$(header).append('<div class="secondary-content">'+vkBtn+' <a class="cancelRepBtn btn lime lighten-3 grey-text text-darken-3 waves-effect waves-green" href="/calendar/delete_event/'+ event_id +'">Отменить</a></div>');
				}
				else if (owner_id) {
					$(header).append('<div class="secondary-content">'+vkBtn+' <a href="#" title="Контакты"><i class="material-icons right">contacts</i></a></div>');
				}
				else if (allowByDate){
					$(header).append('<div class="secondary-content"><a title="на ' + $date + '" class="reservationBtn btn teal lighten-4 grey-text text-darken-4 waves-effect waves-purple" href="/calendar/add_event/?date=' + start_time + '">Забить время</a></div>');
				}
			}
			
			$(this).append(header);
			$(this).append(body);
		});
		
		$closeBtn = "<p class='center'>" + $closeBtn.get(0).outerHTML + "</p>";
		$content = $(".modal-content", $modal);
		$content.append("<h4>Расписание на " + $date + "</h4>");
		$content.append($timeGrid);
		$content.append($closeBtn); 
		$modal.openModal({
			dismissible: true, // Modal can be dismissed by clicking outside of the modal
			opacity: .7, // Opacity of modal background
			in_duration: 100, // Transition in duration
			out_duration: 200 // Transition out duration
		});
		
		$('.collapsible').collapsible({accordion : true});
		$('.reservationBtn').on('click', reservTime);
		$('.cancelRepBtn').on('click', cancelTime)
	});*/
	/*
	$('#my-modal').on({

		'show.uk.modal': function(){
			console.log("Modal is visible.");
		},

		'hide.uk.modal': function(){
			console.log("Element is not visible.");
		}
	});
	*/
	$modals = [];
	$modal = UIkit.modal(".uk-modal");
    eventButtonInit();

    $callerBtn = $("#btn-more-dates");
	$callerBtn.on("click", function (e){
        var callerBtn = $(this);

        callerBtn.attr("data-update_count", parseInt($(this).attr("data-update_count")) + 1);

        getWeekByOffset( callerBtn );

	    e.preventDefault();
	    return false;
    });
    initScrollUpdate($("#btn-more-dates"));
});

function initScrollUpdate($obj) {
	$obj.on('inview.uk.scrollspy', function(){
        $obj.trigger("click");
    });
}

function eventButtonInit() {
    $(".day-container .timeGrid .uk-button").on("click", function(){
        getEventByTime($(this).attr("data-start_time"));
    });
}

function getWeekByOffset(callerBtn) {
    offset = parseInt(callerBtn.attr("data-offset")) + 1;
    var req = {
        request: "getWeekByOffset",
        "offset": offset,
        studio_id: $(".calendar-container").attr('data-studio_id')
    };
    $.ajax({
        url: ajax_url,
        data: req,
        dataType: "json",
        beforeSend: function(){ $("i", callerBtn).toggleClass("uk-icon-spin") },
        complete: function(){ $("i", callerBtn).toggleClass("uk-icon-spin") },
        success: function(response){
            if (typeof response.week == "object") {
                callerBtn.attr("data-update_count", parseInt(callerBtn.attr("data-update_count")) + 1 );

                var cWeek = $("<div />").attr("class", $(".calendar-container .week").attr("class"));
                var week = response.week[offset];
                pr(offset);
                pr(response);
                for (var lday in week){
                    cWeek.append(getDayContainer(week[lday]));
                };
                $(".calendar-container").append(cWeek);
                callerBtn.attr("data-offset", offset);
                eventButtonInit();
            }
        },
        error: function (e) {
            pr(e);
        }
    });
    //pr(offset);
}

function getDayContainer(day){
    //day = Array(day);
    var $dayCntnr = $("<div />").attr("class", "day-container");
    var lDate = parseDate(day.date.date);
    $dayCntnr
        .attr("data-date", lDate.getFullYear() +"-"+ strPadLeft(lDate.getMonth() + 1) +"-"+ lDate.getDate())
        .append($("<div />").attr("class", "uk-panel")
        .append($("<p />").attr("class", "uk-text-center").html(lDate.toLocaleString("ru",{day: 'numeric', month: 'long'})))
        .append($("<ul />").attr("class", "uk-list timeGrid") ))
    ;
    for (prop in day.events){
        var event = day.events[prop];
        var start = parseDate(event.start_time.date);
        var end = parseDate(event.end_time.date);
        var fromTo = strPadLeft(start.getHours()) + ":" + strPadLeft(start.getMinutes()) + "-" + end.getHours() + ":" + strPadLeft(end.getMinutes());

        var $btn = $("<a />")
            .attr("class", "uk-button")
            .attr("data-start_time", event.start_time.date)
            .append($("<i />"));
        ;
        if (app.session.user && event.owner_id == app.session.user.id) {
            $btn.addClass("uk-text-primary").attr("data-owner_id", event.owner_id);
            $("i", $btn).addClass("uk-icon-user");
        }
        else if (event.owner_id){
            $btn.addClass("uk-text-warning").attr("data-owner_id", event.owner_id);
            $("i", $btn).addClass("uk-icon-user-times");
        }
        else {
            $btn.addClass("uk-text-success").attr("data-owner_id", event.owner_id);
            $("i", $btn).addClass("uk-icon-clock-o");
        }
        $("i", $btn).after(" " + fromTo);
        $("ul", $dayCntnr).append($("<li />").attr("class","free").append($btn));
    };
    return $dayCntnr;
}


function getEventByTime(strTime){
	var req = {
		request: "getEventByTime",
		str_time: strTime,
        studio_id: $(".calendar-container").attr('data-studio_id')
	};
	$.ajax({
		url: ajax_url,
		data: req,
        dataType: "json",
		success: function(response){
            var event = response.event;
            var tId = md5(strTime);
            var modal;

            if ($modals[tId]==1){
                modal = UIkit.modal("#" + tId );
            }
            else{
                $modals[tId] = 1;
                modal = $($modal.element[0]).clone();
                $(modal).attr("id", tId);
                $("body").append(modal);
            }

            var localeFormat = {
                day: 'numeric',
                month: 'long',
                year: 'numeric',
                weekday: 'long'
            };
            var start = parseDate(event.start_time.date);
            var end = parseDate(event.end_time.date);
            var fromTo = strPadLeft(start.getHours()) + ":" + strPadLeft(start.getMinutes()) + " - " + end.getHours() + ":" + strPadLeft(end.getMinutes());

            if(event.owner_id){
                $(".event-owner-info .owner-fio", modal).attr("href", "/users/id" + event.owner_id).html (event.owner);
                if (event.avatar)
                    $(".event-owner-info img", modal).attr ("src", event.avatar);
                $(".event-owner-info").show();
            }
            else {
                $(".event-owner-info").hide();
			}

            $(".event-date", modal).html(start.toLocaleDateString("ru", localeFormat));
            $(".event-start-end", modal).html (fromTo);
            $(".event-duration", modal).html (event.duration_time.h + "ч. " + event.duration_time.m + "мин.");
            $(".event-member-count", modal).html ();
            $(".event-cost", modal).html ();

            modal = UIkit.modal("#" + tId );
            modal.show();
		},
		error: function (data) {
			pr(data);
        }
	});
	
}

function strPadLeft(str, pad, count){
    str = String(str);
    if (str.length > 0){
        if (!pad){
            pad = "0";
        }
        if (!count)
            count = 2;
        while(str.length < count){
            str=pad+str;
        }
    }
    return str;
}

function getVKButton (event_id){
	res = "";
	if (event_id){
		url_params = {url: getEventUrl(event_id)};		
		//btn_params = {type: 'custom', text: 'Поделиться '}
		//btn_params = {type: 'custom', text: '<img src="//vk.com/images/vk32.png" title="Поделиться в VK" />'};
		btn_params = {type: 'custom', text: '<img src="/source/images/vk-logo_32x32.png" style="width:32px;height:32px;" title="Поделиться в VK" />'};
		
		$btn = $(VK.Share.button(url_params, btn_params));
		//0 5px 11px 0 rgba(0,0,0,0.18),0 4px 15px 0 rgba(0,0,0,0.15);
		$('img', $btn).addClass('btn');
		res = $btn.addClass("btn-flat vkBtn").get(0).outerHTML;
	}
	return res;
}

function getEventUrl(event_id){
	var res = "";
	if (event_id){
		res = location.origin + "/calendar/event/"+event_id;
	}
	return res;
}

function parseDate(ADate){
	var t = explode(" ", ADate);
	var lDate = explode(" ", ADate)[0];
	lDate = explode("-", lDate);
	var lTime = explode(" ", ADate)[1];
	lTime = explode(":", lTime);
	date = new Date(parseInt(lDate[0]), parseInt(lDate[1])-1, parseInt(lDate[2]), parseInt(lTime[0]), parseInt(lTime[1]), parseInt(lTime[2]));
	return date;
}

function logoutBtnHandler(){
    $.ajax({
        url: "/users/logout"
    }).done(function(){
		delete_cookie("usr");
		delete_cookie("key");
        location.reload();
    });
    return false;
}

function cancelTime(){
	$(this).parent().remove();
	$.post($(this).attr("href"),{},function (){location.reload()});
	return false;
}

function reservTime (){
	var href = $(this).attr("href");
	$.post(href,{}, onReservationAnswer);
	return false;
}

function onReservationAnswer($data){
	if ($data == 1){
		$container = $("a[href='"+$(this)[0].url+"']").parent();
		text = $("span", $container).html();
		text += " " + user_login;
		$("span", $container).html(text);
		$("a[href='"+$(this)[0].url+"']", ".modal").remove();
		location.reload();
	}
}
function delete_cookie ( cookie_name )
{
  var cookie_date = new Date ( );  // Текущая дата и время
  cookie_date.setTime ( cookie_date.getTime() - cookie_date.getTime() - 1 );
  document.cookie = cookie_name += "=; expires=" + cookie_date.toGMTString();
}

function getModal($id){
	if ($("#" + $id).length <= 0){
		$modal = $("#modal1").clone();
		$modal.attr("id",$id);
	}
	else
		$modal = $("#" + $id);
	$closeBtn = $(".modal-content .modal-close", $modal).clone();
	$closeBtn.removeClass("right");
	$content = $(".modal-content", $modal).html("");
	$("#modal1").after($modal);
	return $modal;
}