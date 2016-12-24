var bag = {
    user: '',
    mo: new Array(),
    timer: '',
    container: '#bag-panel .objects-list',
    init: function(usr){
        if (usr.length > 0){
            bag.user = usr;
        }
        strbag = getCookie('user_bag[' + bag.user + ']');
        if (typeof strbag === "string"){
            lBag = JSON.parse(strbag);
            bag.mo = lBag.mo;
        }
        else{
            bag.mo = new Array();
        }
        bag.recalcCount();
        $('.head .clearBag', $(bag.container).parent()).bind('click', bag.clear);
        $('.panel-control a.add').bind("click", bag.push);
    },
    fillBagContainer: function(){
        if (bag.mo.length > 0){
            for (i=0; i < bag.mo.length; i++){
                $(bag.container).append(bag.getHTMLObj(bag.mo[i]));
            }
            bag.bagTouched();
        }
    },
    getMObj: function (id){
        res = {};
        i = 0;
        for (i = 0; i < bag.mo.length; i++){
            if (bag.mo[i].id === id){
                res.obj = bag.mo[i];
                res.idx = i;
            }
        }
        return res;
    },
    push: function (obj){
        obj = $(this).parent().parent().parent();
        mObj = bag.genMObj(obj);
        if (typeof bag.getMObj(mObj.id).obj === "object"){
            alert('Этот объект уже есть в портфеле');
            return false;
        }
        else{
            bag.mo.push(bag.genMObj(obj));
            bag.sendData();

            $(bag.container).append(bag.getHTMLObj(obj));

            bag.bagTouched();
            return false;
        }
        
    },
    remove: function(obj){
        id = explode("_", $(obj).attr('id'))[1];
        mo = bag.getMObj(id);
        if (parseInt(mo.idx) >= 0)
            bag.mo.splice(mo.idx, 1);
        bag.recalcCount();
        bag.sendData();
    },
    sendData: function(){
        setCookie('user_bag['+bag.user+']', JSON.stringify(bag));
    },
    genMObj: function(obj){
        if(parseInt(obj.id) > 0){
            return obj;
        }
        else if($(obj).length){
            pushObj = {};
            pushObj.id = explode("_", $(obj).attr('id'))[1];
            pushObj.type = explode(' ', $(obj).attr('class'))[1];
            pushObj.href = $('a', obj).attr('href');
            return pushObj;
        }
    },
    getHTMLObj: function(obj){
        mObj = bag.genMObj(obj);
        insObj = $('<span \>');

        switch (mObj.type){
            case 'image': {
                    insObj.addClass('image');
                    insObj.attr('id', 'mo_' + mObj.id);
                    img = $('<img \>', {alt: '', src: mObj.href});
                    a = $('<a \>', {href: mObj.href, class: 'showFullImage'});
                    $(a).append(img).appendTo(insObj);
                }break;
        }

        insObj.append(this.getObjControls(obj));

        return insObj;
    },
    getObjControls: function(){
        lContainer = $('<span \>', {class: 'object-controls'});
        buttons = [];
        buttons['remove'] = $('<a \>', {
            href: '#', 
            class: 'remove',
            title: 'Убрать из портфеля',
            click: function(){
                bag.remove($(this).parent().parent());
                mObj = $(this).parent().parent().remove();
                return false;
            }
        });

        $(lContainer).append(buttons['remove']);

        return lContainer;
    },
    clear: function(){
        deleteCookie('user_bag[' + bag.user + ']');
        $('span', $(bag.container)).each(function(){
            if (!$(this).hasClass('empty'))
                $(this).remove();
        });
        bag.bagTouched();
        bag.mo = new Array();
        return false;
    },
    bagTouched: function(){
        count = 4;
        delay = 100;
        i=0;
        clearInterval(bag.timer);
        bag.timer = setInterval(function (){
            $(bag.container).parent().toggleClass('blind');
            i++;
            if (i >= count){
                $(bag.container).parent().removeClass('blind');
                bag.recalcCount();
                clearInterval(bag.timer);
                bag.timer = '';
            }
        }, delay);
        return;
    },
    recalcCount: function(){
        $('.counter', $(bag.container).parent()).html(bag.mo.length);
    }
};
