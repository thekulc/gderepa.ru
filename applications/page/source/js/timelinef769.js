function ReCalculate()
{
    
}

function CollapseArticle(AElement)
{
//var lJSPAPI = jQuery("#timeline").data("jsp");
    /*jQuery(".tlVisibleRight", AElement).fadeOut("fast", function () {
        			AElement.animate({marginTop:"50px", width:"125px", height:"175px"},
            		{
            		complete: function ()
            			{
            				AElement.removeClass("animating");
            				AElement.removeClass("extended");
            				lJSPAPI.reinitialise();
            			}
            		});
        		}
        		);*/
        		AElement.hide();
                jQuery("#timelineContainer").css("width", parseInt(jQuery("#timelineContainer").css("width")) - 370 + "px");
        		AElement.prev().css('background','');
				AElement.removeClass("animating");
				AElement.removeClass("extended");
				//lJSPAPI.reinitialise();
}

function ExpandArticle(AElement)
{
	//var lJSPAPI = jQuery("#timeline").data("jsp");
		/*AElement.animate({marginTop:"0px", width:"400px", height:"275px"},
    		{
    		complete: function ()
    			{
    				AElement.removeClass("animating");
    				AElement.addClass("extended");
    				jQuery(".tlVisibleRight", AElement).fadeIn();
    				lJSPAPI.reinitialise();
    			}
    		});*/
    		AElement.show();
    		jQuery("#timelineContainer").css("width", parseInt(jQuery("#timelineContainer").css("width")) + 370 + "px");
    		AElement.prev().css('background','#40403d');
			AElement.removeClass("animating");
			AElement.addClass("extended");
			if(typeof AElement.children().eq(1).data('jsp') == 'undefined')
			{
                var lAllText = jQuery('.tlDescription',AElement).innerHeight();
                var lVisibleText = jQuery('.tlDescriptionContainer',AElement).height();
                if(lAllText <= lVisibleText)
                {
                    jQuery('.tlBtnUp',AElement).hide();
                    jQuery('.tlBtnDown',AElement).hide();
                    jQuery('.tlBtnConnector',AElement).hide();
                }
                else
                {
                    jQuery('.tlBtnUp',AElement).show();
                    jQuery('.tlBtnDown',AElement).show();
                    jQuery('.tlBtnConnector',AElement).show();
                }
    			AElement.children().eq(1).jScrollPane(
            		{
            			showArrows: false,
            			animateScroll: true,
            			arrowScrollOnHover: true,
            			arrowButtonSpeed : 300
            		}
            	);
            	var lC = AElement.children().eq(1).data('jsp');
            	jQuery('.tlBtnUp',AElement).click({jsp:lC},function(AEvent){AEvent.data.jsp.scrollByY(-50);AEvent.stopPropagation();});
            	jQuery('.tlBtnDown',AElement).click({jsp:lC},function(AEvent){AEvent.data.jsp.scrollByY(50);AEvent.stopPropagation();});
        	}
			//lJSPAPI.reinitialise();
}

function StopMouseTracking()
{
	jQuery('body').unbind("mousemove.scrolltracker");
}

function CheckScrollButtonsHovered(AEvent)
{
	var lMouseX = AEvent.pageX;
	var lMouseY = AEvent.pageY;

	var lLeftButton = jQuery("#leftArrow");
	var lRightButton = jQuery("#rightArrow");

	var lLeftButtonHit = lMouseX >= lLeftButton.offset().left && lMouseX <= lLeftButton.offset().left + lLeftButton.width() &&
						 lMouseY >= lLeftButton.offset().top && lMouseY <= lLeftButton.offset().top + lLeftButton.height();

	var lRightButtonHit = lMouseX >= lRightButton.offset().left && lMouseX <= lRightButton.offset().left + lRightButton.width() &&
						 lMouseY >= lRightButton.offset().top && lMouseY <= lRightButton.offset().top + lRightButton.height();

	if (!lLeftButtonHit && !lRightButtonHit)
	{
		StopMouseTracking();
		clearInterval(window["SrclTimerID"]);
		window["SrclTimerID"] = 0;
	}
}

var scrlb = 10;

function TimeLineScrollLeft()
{
	var lJSPAPI = jQuery("#timeline").data("jsp");
	lJSPAPI.scrollByX(-scrlb);
}

function TimeLineScrollRight()
{
	var lJSPAPI = jQuery("#timeline").data("jsp");
	lJSPAPI.scrollByX(scrlb);
}

jQuery(document).ready( function () {


jQuery("#leftArrow").mouseover(function (e)
{
	if (typeof window["SrclTimerID"] != 'undefined')
	{
		clearInterval(window["SrclTimerID"]);
	}
	window["SrclTimerID"] = setInterval(TimeLineScrollLeft,45);
	jQuery('body').bind("mousemove.scrolltracker", CheckScrollButtonsHovered);
});

jQuery("#leftArrow,#rightArrow").mouseout( function ()
	{
		StopMouseTracking();
		clearInterval(window["SrclTimerID"]);
		window["SrclTimerID"] = 0;
	}
).click( function ()
	{		
		if (!window["SrclTimerID"])
		{
			window["SrclTimerID"] = setInterval(jQuery(this).attr("id") == "leftArrow" ? TimeLineScrollLeft : TimeLineScrollRight,45);			
		}
		setTimeout(function ()
		{
			StopMouseTracking();
			clearInterval(window["SrclTimerID"]);
			window["SrclTimerID"] = 0;
		}, 400
		);
	});	

jQuery("#rightArrow").mouseover(function (e)
{
	if (typeof window["SrclTimerID"] != 'undefined')
	{
		clearInterval(window["SrclTimerID"]);
	}
	window["SrclTimerID"] = setInterval(TimeLineScrollRight, 45);
	jQuery('body').bind("mousemove.scrolltracker", CheckScrollButtonsHovered);
});


var lCtlWidth = jQuery("#timeline").width();
var lScrollerWidth = 0;
var lScrollerWidthUp = 0;
var lScrollerWidthDown = 0;
jQuery(".timelineItem.tlItemUp").each(
	function (i,item)
	{
		lScrollerWidthUp += 1.4*jQuery(item).outerWidth();
	}
);
jQuery(".timelineItem.tlItemDown").each(
	function (i,item)
	{
		lScrollerWidthDown += 1.4*jQuery(item).outerWidth();
	}
);

if(lScrollerWidthDown >= lScrollerWidthUp)
lScrollerWidth = lScrollerWidthDown;
else
lScrollerWidth = lScrollerWidthUp;

lScrollerWidth = 2700;

jQuery("#timelineContainer .timelineItem .tlVisibleLeft").css("cursor","pointer");
jQuery("#timelineContainer .timelineItem .tlVisibleLeft").click(
    function () {
    	var lJSPAPI = jQuery("#timeline").data("jsp");
    	var lX, lNewX, lTLItemLeft;
    	var lContainer = jQuery(this).next();
        var lContainer2 = jQuery(this);
    	lX = jQuery(lContainer2).offset().left;
    	lTLItemLeft = jQuery(".timelineItem").offsetParent().offset().left;
        if(lContainer.hasClass("extended"))
        {
        	jQuery(".timelineItem > .tlVisibleRight.extended").each(function(i,item)
        	{
                CollapseArticle(jQuery(item));
                lJSPAPI.reinitialise();
        	})
        }
        else
        {
        	jQuery(".timelineItem > .tlVisibleRight.extended").each(function(i,item)
        	{
                CollapseArticle(jQuery(item));
                lJSPAPI.reinitialise();
        	})
        	if (!lContainer.hasClass("animating"))
        	{
        		lContainer.addClass("animating");
        		if (!lContainer.hasClass("extended"))
        		{
                	ExpandArticle(lContainer);
                	lJSPAPI.reinitialise();
                	lX = jQuery(lContainer2).offset().left;
                	lTLItemLeft = jQuery(".timelineItem").offsetParent().offset().left;
                    lJSPAPI.scrollTo(lX-lTLItemLeft - 20);
        		}
        	}
        }
});

jQuery("#timelineContainer").css("width", lScrollerWidth + "px");

jQuery("#timeline").jScrollPane(
		{
			showArrows: true,
			animateScroll: false,
			arrowScrollOnHover: true,
			arrowButtonSpeed : 100
		}
	);
});