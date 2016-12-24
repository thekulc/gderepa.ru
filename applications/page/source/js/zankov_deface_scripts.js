$(document).ready(function(){
    externalLinks();
    shareInit('blockShare');
    
    $('.menu a').click(menuItemClick);
    $('.spoiler-button').click(spoilerToggle);
    signedTimerInit(3000);
    
    var backUrl = getBackUrl(location.hash.substring(1));
    
    if (backUrl.length > 0)
        $('.goToCurrentPage').attr('href', backUrl + '#read');
});

function getBackUrl(backUrl){
    
    var url = "";
    if (backUrl.length > 0 && backUrl != 'undefined/'){
        var pieces = backUrl.split('/');
        var i;
        url += "http://";
        for (i = 1; i < pieces.length; i++){
            if (pieces[i] !== ''){
                url += pieces[i] + "/";
            }
        }
    }
    else
        url = "http://zankov.ru";
    
    return url;
}

function shareInit(containerId){
    var link = location.origin + location.pathname;
    var lDescr = $('#shareContent > p').text().substring(0, 308);
    var imageUrl = "http://" + location.host + $('.header > .closeDeface > img').attr('src');
    
    if (containerId.length > 0){
        new Ya.share({
            element: containerId,
            theme: 'counter',
            elementStyle: {
                'border': false,
                'quickServices': ['facebook', 'odnoklassniki', 'vkontakte', 'yaru', 'twitter']
            },
            description: lDescr,
            image: imageUrl,
            link: link
        });
    }
}

var signedTimer;
var lastCurrent;
function getNextBlock(list){
    currentObj = $('.view', list);
    items = $('li', list);
    if (currentObj.length > 0){
        
        if (typeof items[lastCurrent+3] != 'undefined'){
            lastCurrent += 3;
        }
        else{
            lastCurrent = 0;
        }
    }
    $(currentObj).removeClass('view');
    $(items[lastCurrent]).addClass('view');
    return lastCurrent;
}

function signedTimerInit(duration){
    var list = $('.carousel-container');
    lastCurrent = 3;
    
    $(list).mouseover(function (){
        clearInterval(signedTimer);
    });
    $(list).mouseleave(function (){
        signedTimer = getSignedTimer(list, duration);
    });
    
    signedTimer = getSignedTimer(list, duration);
}

function getSignedTimer(list, duration){
    return setInterval(function(){
        getNextBlock(list);
        $(list).scrollTo('.view', 500);
    }, duration);
}

function spoilerToggle(){
    var duration = 1000;
    var specClass = 'viewed';
    var button = $(this);
    var targets = [];

    var targetSelector = $(this).attr('href').substring(1);
    var targets = $('.'+targetSelector);
    $(targets).slideToggle(duration, 'swing');
    if (!button.hasClass(specClass)){
        button.html('Скрыть подробности');
        button.addClass(specClass);
    }
    else{
        button.html('Читать полностью');
        button.removeClass(specClass);
    }
    
    return false;
}

function menuItemClick(){
    id = $(this).attr('href');
    $.scrollTo($(this).attr('href'), 600, {
        offset: {top: -64, left: 0},
        easing: 'swing'
    });
    return false;
}

function externalLinks() {
    if (!document.getElementsByTagName) return;
    var anchors = document.getElementsByTagName("a");
    for (var i=0; i < anchors.length; i++) {
        if (anchors[i].getAttribute("href") && anchors[i].getAttribute("rel") == "external") {
            anchors[i].target = "_blank";
        }
    }
}

function pr (txt){
    console.log(txt);
}

function getCookie(name) {
  var matches = document.cookie.match(new RegExp(
    "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
  ));
  return matches ? decodeURIComponent(matches[1]) : undefined;
}

// устанавливает cookie c именем name и значением value
// options - объект с свойствами cookie (expires, path, domain, secure)
function setCookie(name, value, options) {
  options = options || {};

  var expires = options.expires;

  if (typeof expires == "number" && expires) {
    var d = new Date();
    d.setTime(d.getTime() + expires*1000);
    expires = options.expires = d;
  }
  if (expires && expires.toUTCString) { 
  	options.expires = expires.toUTCString();
  }

  value = encodeURIComponent(value);

  var updatedCookie = name + "=" + value;

  for(var propName in options) {
    updatedCookie += "; " + propName;
    var propValue = options[propName];    
    if (propValue !== true) { 
      updatedCookie += "=" + propValue;
     }
  }

  document.cookie = updatedCookie;
}

// удаляет cookie с именем name
function deleteCookie(name) {
  setCookie(name, "", { expires: -1 })
}
