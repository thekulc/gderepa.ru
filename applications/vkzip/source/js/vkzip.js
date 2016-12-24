var ajax_url = "/vkzip/ajax";

$(document).ready(function() {
	$(':checkbox').checkboxpicker();
	showAudiosByAlbumInit();
	getAudioFromHostInit();
	
	$('#btnLogin').on('click', function(){
		var params = "width=800,height=600";
		var oauthHref = $(this).attr('href');
		var hrefParams = "?setUserSectionPermissions&sections=" + getSections();
		var authWindow = window.open("/vkzip/auth" + hrefParams, "Войти с помощью ВК", params);
		
		return false;
	});
});

function getAudioFromHostInit(){
	$("#user-audios-list .getAudioBtn").on('click', function(e){
		var album_id = $(this).attr('data-album_id');
		var req = {
			request: "startQueue",
			album_id: album_id
		};
		$.get({
			url: ajax_url,
			data: req,
			success: function(response){
				pr(response);
				if (response.status == "success") {
					$(this).text("Ваши аудио поставлены в очередь на скачивание");
				}
				else{
					pr(response.error);
				}
			},
			dataType: "json"
		});
	});
}

function showAudiosByAlbumInit(){
	$("#user-audios-nav a").on('show.bs.tab', function(e){
		var allAudioList = $("#user-audios-list");
		var album_id = $(this).attr('data-album_id');
		var req = {
				request: "getAlbum",
				album_id: album_id
		};
		
		if ( $(this).attr("data-album_id") != "all" && $("li", "#user-audios-list-" + album_id).length <= 0){
			var tabContainer = $("#tabAlbum_" + album_id);
			$.get({
				url: ajax_url,
				data: req,
				success: function(response){
					var albumList = $("#user-audios-list-" + album_id);
					if (response){
						for (var i = 0; i < response.length; i++){
							var dur = new Date (response[i].duration * 1000);
							response[i].duration = dur.toLocaleString("ru", {minute: 'numeric',second: 'numeric'});
							
							var list = $("<li />").addClass("list-group-item").append($("<div />").addClass("media-left").append($("<a />").attr({"data-url":response.url}).append($("<span />").addClass("glyphicon glyphicon-play").attr({"aria-hidden":"true"}))));
							list.append( $("<div />").addClass("media-body").append( $("<p />").addClass("media-heading").html(response[i]['artist'] + " - " + response[i].title).append( $("<span />").addClass("pull-right").html(response[i].duration) ) ) );
							$(albumList).append(list);
						}
					}
					else{
						albumList.append("<li />").html("Не удалось получить данные");
					}
							
					
					tabContainer.append(albumList);
					allAudioList.parent().after(tabContainer);
				},
				dataType: "json"
			});
		}
	});
}

function getSections(){
	var res = "";
	var subj = $('#form_sections input[type="checkbox"]:checked');
	subj.each(function(idx, el){
		res += $(el).attr('data-mname');
		if (idx < subj.length-1)
			res += ',';
	});
	return res;
}

function parseGetParams(params) { 
   var $_GET = {}; 
   var __GET = params.substring(1).split("&"); 
   for(var i=0; i<__GET.length; i++) { 
      var getVar = __GET[i].split("="); 
      $_GET[getVar[0]] = typeof(getVar[1])=="undefined" ? "" : getVar[1]; 
   } 
   return $_GET; 
}

function pr(txt){
	console.log(txt);
}
