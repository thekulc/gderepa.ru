$(document).ready(function() {
    getObjects();
});

var allObjects = [];
var terms = [];

function chooseObjects(){
    var id = $(this).parent().siblings()[0];
    id = parseInt($(id).html());
    if (typeof terms[id] !== 'object'){
        terms[id] = new term(id).init();
    }
    
    showObjects(id, $(this).parent());
    
    return false;
}

function showObjects(id, parent){
    var lTerm = terms[id];
    var container = $('<div \>', {
        class: 'chooseObjectsList'
    });
    
    var action = parent.children('.' + container.attr('class')).length > 0 ? true : false;
    
    if (!action){
        $(container).append(lTerm.getListView());
        parent.append(container);
    }
    
    parent.children('.' + container.attr('class')).slideToggle();
}

function compareObjects(obj1, obj2){
    return obj1 === obj2 ? true : false;
}

function getObjects(){
    var res = [];
    $.ajax({
        async: true,
        dataType: 'json',
        url: '/admin/taxonomy/ajaxHandler/?action=getObjects',
        success: function(data){
            res = getObjectsArray(data);
        }
    }).then(function () {
        allObjects = res;
        $('.chooseObjects').click(chooseObjects).addClass('active');
    });
    return res;
}

function getObjectsArray(data){
    var res = [];
    for (lObject in data){
        res[lObject] = new mObject(data[lObject].id, data[lObject].title);
    }
    return res;
}

function term(AId){
    return {
        id: AId,
        mObjects: [],
        init: function(){
            this.mObjects = this.getObjects();
            return this;
        },
        linkObject: function(){
            return bool;
        },
        unlinkObject: function(){
            return bool;
        },
        getObjects: function(){
            var res = {};
            $.ajax({
                async: false,
                dataType: 'json',
                url: '/admin/taxonomy/ajaxHandler/?action=getObjectsByTerm&id=' + this.id,
                success: function(data){
                    res = getObjectsArray(data);
                }
            }).then(this.mObjects = res);
            return res;
        },
        getListView: function(){
            var list = $('<ul \>', {
                class: 'object-list',
                id: 'term_' + this.id
            });
            var lObjects = this.mObjects;
            for (lObject in allObjects){
                var listItem = $('<li \>');
                var checked = false;
                for (obj in lObjects){
                    if (obj === lObject){
                        checked = true;
                        break;
                    }
                }
                allObjects[lObject].getCheckboxView(checked);
                listItem.append(allObjects[lObject].checkbox);
                list.append(listItem);
            }
            return list;
        }
    };
}

function mObject(AId, ATitle){
    return {
        id: AId,
        title: ATitle,
        getCheckboxView: function(AChecked){
            var chbx = $('<input \>', {
                type: 'checkbox',
                name: 'objects[]',
                value: this.id
            });
            chbx.prop('checked', AChecked);
            var link = $('<a \>', {
                href: '/admin/mObject/id'+this.id,
                text: this.title !== '' ? this.title : 'Без имени',
                title: 'Открыть в новой вкладке',
                target: '_blank'
            });
            
            var linkA = $('<a \>', {
                href: '/admin/mObject/id'+this.id+'/attached',
                text: " (A) ",
                title: 'Перейти к привязанным объектам',
                target: '_blank'
            });
            
            this.checkbox = $('<label \>').append(chbx).append(link).append(linkA);
            this.checkbox.change(this.onSelect);
            return this.checkbox;
        },
        checkbox: '',
        onSelect: function(){
            var parentTermId = $(this).parent().parent().attr('id');
            var sendData = {
                term_id: parentTermId.split('_')[1],
                object_id: $('input', this).val()
            };
            
            var action = 'unlink';
            if ($('input', this).prop('checked'))
                action = 'link';
            pr(sendData);
            pr(action);
            
            $.ajax({
                async: true,
                data: sendData,
                url: '/admin/taxonomy/ajaxHandler/?action='+action,
                success: function(data){
                    pr(data);
                }
            });
            
        }
    };
    
}
