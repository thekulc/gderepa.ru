$(document).ready(function(){
    rateInit();
});

function rateInit(){
    
    var ratePanels = $(".panel-control .rating");
    
    $(ratePanels).each(function(idx_panel, panel){
        $('a', panel).each(function(idx_obj, lobj){
            $(lobj).attr('title', 'Оценить на ' + parseInt(idx_obj+1));
        });
    });
    
    $(ratePanels).hover(function(){}, rateHoverOut);
    $('a', ratePanels).bind('click', this, rateClick);
    //$('a', ratePanels).hover(rateHoverIn, 'rateHoverOut');
    $('a', ratePanels).hover(rateHoverIn, function(){});
}

function rateHoverIn(){
    var cObj = this;
    var cObjIdx = 100;
    var container = $(this).parent();
    $(container).addClass('to');
    $('a', container).each(function(idx, obj){
        
        if (obj == cObj)
            cObjIdx = idx;
        
        if (idx <= cObjIdx)
            $(obj).addClass('to');
        else{
            $(obj).removeClass('to');
        }
        
    });
}

function rateHoverOut(){
    var container = $(this).parent();
    $('a.to', container).removeClass('to');
    $('.rating', container).removeClass('to');
}

function rateClick(){
    pr(this);
    return false;
}