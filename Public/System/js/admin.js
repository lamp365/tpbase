//admin.js
function shopShortTip(ele,msg,tit){
    if(tit == null){
        tit = "恭喜：";
    }else{
        tit = tit+"：";
    }
    var html = '<div style="padding:10px 18px 0px 18px;display:none" id="shortTip">'+
                    '<div class="alert alert-green">'+
                        '<span class="close rotate-hover"></span><strong>'+ tit +'</strong>'+ msg +
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

/**
 * 显示加载层或者关闭
 * @param type
 */
function alertLoder(type){
    if(type == null){
        type = 1;
    }
    if(type == 1){
        $(".alert-loader").fadeIn();
        $(".alert-loader-box").fadeIn();
    } else if(type == 0){
        $(".alert-loader-box").fadeOut();
        $(".alert-loader").fadeOut();
    }
}