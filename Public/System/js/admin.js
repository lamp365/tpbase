//admin.js
function shopShortTip(ele,msg,tit){
    if(!tit){
        tit = "恭喜";
    }
    var html = '<div style="padding:10px 18px 0px 18px;display:none" id="shortTip">'+
                    '<div class="alert alert-green">'+
                        '<span class="close rotate-hover"></span><strong>'+ tit +'：</strong>'+ msg +
                    '</div>'+
                '</div>';

    $(ele).after(html);
    var obj = $("#shortTip");
    if(obj.length > 0){
        $(obj).fadeIn();
        setTimeout(function(){
            $(obj).fadeOut();
        },2000)
    }
}