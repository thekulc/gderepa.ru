$(document).ready(function() {
    showFullImageInit();
    if (typeof bag !== 'undefined'){
        var lBag = bag;
        lBag.init(getCookie('PHPSESSID'));
        lBag.fillBagContainer();
    }
});

function showFullImageInit(){
    hs.graphicsDir = '/source/css/graphics/';
    hs.align = 'center';
    hs.transitions = ['expand'];
    //hs.wrapperClassName = 'dark borderless floating-caption';
    hs.fadeInOut = true;
    hs.dimmingOpacity = .75;
    hs.restoreCursor = '';
    
    
    $(".object.image > center > a").click(function(){
        return hs.expand(this);
    });
    
    /*
    if($.lightbox){
        
    }
    $(".object.image > center > a").lightBox({
            imageLoading: '/source/images/lightbox/lightbox-ico-loading.gif',
            imageBtnClose: '/source/images/lightbox/lightbox-btn-close.gif',
            imageBtnPrev: '/source/images/lightbox/lightbox-btn-prev.gif',
            imageBtnNext: '/source/images/lightbox/lightbox-btn-next.gif',
            imageBlank: '/source/images/lightbox/lightbox-blank.gif',
            txtImage: 'Изображение',
            txtOf: 'из',
            containerResizeSpeed: 0
        });
    */
}