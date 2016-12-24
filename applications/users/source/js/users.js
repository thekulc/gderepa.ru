function valid(elem,min,max)
{
    var msg='';
    var txt=$(elem).val();
    var id = $(elem).prev('label').html();
    var res = false;
    var out=$(elem).next('span');
    if(txt.length<min || txt.length>max)
        {
            msg=id+' в пределах '+min+' и '+max;
            out.css('color','red');
            out.html("(!) "+msg);
            $(elem).css('border','1px solid red');
            res = false;
        }
    else 
        {
            out.html("Ok");
            out.css('color', 'green');
            $(elem).css('border','1px solid green');
            res = true;
        }
    $('#statusAuth').append(msg+' ');
    _resize();
    return res;
}

function validateEmail(email)
{
    
    var splitted = email.match("^(.+)@(.+)$");
    if (splitted == null) return false;
    if (splitted[1] != null)
    {
        var regexp_user = /^\"?[\w-_\.]*\"?$/;
        if (splitted[1].match(regexp_user) == null) return false;
    }
    if (splitted[2] != null)
    {
        var regexp_domain = /^[\w-\.]*\.[A-Za-z]{2,4}$/;
        if (splitted[2].match(regexp_domain) == null)
        {
            var regexp_ip = /^\[\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\]$/;
            if (splitted[2].match(regexp_ip) == null) return false;
        } // if
        return true;
    }
    return false;
}

function mailValid(elem){
    var msg;
    var res = false;
    var out = $(elem).next('span');
    if(!validateEmail($(elem).val()))
        {
            msg='Почта введена неверно';
            out.css('color','red');
            out.html("(!) "+msg);
            $(elem).css('border','1px solid red');
            res = false;
        }
    else 
        {
            out.html("Ok");
            out.css('color', 'green');
            $(elem).css('border','1px solid green');
            res = true;
        }
    return res;
    resize_();
}

function checkLogin(elem){
    var res = false;
    var login = $(elem).val();
    var min = 3;
    var max = 255;
    var txt = $(elem).next('span');
        if (valid(elem,min,max))
        {
            if (validateEmail(login))
            $.ajax({
               type:"POST" ,
               data:'checkLogin='+login,
               async:false,
               url: '/admin/users/',
               success: function(status){
                    if(status!='true') 
                        {
                            txt.html('(!) Этот логин занят');
                            txt.css('color', 'red');
                            res = false;
                        }
                    else {
                        txt.html('Ok');
                        txt.css('color', 'green');
                        res = true;
                    }}
            });
            else 
                {
                    txt.html('(!) Не правильный формат почты');
                    txt.css('color', 'red');
                    res=false;
                }
        }
        else {
                $(elem).next('span').html("(!) В пределах "+min+" и "+max);
                $(elem).next('span').css('color','red');
                res = false;
        }
    return res;
}

function onPassWright(pwd2)
{
    var res = false;
    var pwd1 = $('[id="pwd"]');
    var msgOut = $(pwd2).next('span');
    var prnt = pwd2;
    
    pwd2 = $(pwd2).val();
    if (pwd2!='')
        if ($(pwd1).val() == pwd2)
            {
                res=true;
                msgOut.html('Ok');
                msgOut.css('color', 'green');
                $(prnt).css("border", "solid 1px green");
            }
        else{
                msgOut.html('(!) Пароли не совпадают');
                msgOut.css('color', 'red');
                $(prnt).css("border", "solid 1px red");
                res=false;
            }
    return res;
}



$(document).ready(function(){
    $.datepicker.setDefaults(
        $.extend($.datepicker.regional["ru"])
    );
    
    $(".date").datepicker({
        firstDay: 1,
        defaultDate:"01.01.1970",
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd.mm.yy',
        onSelect: function(){valid(this, 10, 12)}
    });
    
    $(".add_user").click(function(){
            var res = checkLogin($("#logs"));
            res *= valid($("#pwd"),6,255);
            res *= onPassWright($("#pass"));
            res *= valid($("[name=FIO]"),3,255);
            res *= valid($("[name=DOB]"),10,12);
            res *= valid($("[name=telephone]"),6,255);
            if(res) $(this).parent('form').submit();
            return false;
        })
        
    
    
});
    