String.prototype.trim = function() {
    return this.replace(/^\s+|\s+$/g,"");
}

var Juuz = {
    code: {
        ajaxSuccess: 200,
        ajaxError: 300,
        ajaxNotLogin: 301
    },

    noop: function(){},

    randNum: function(n){
        var rand = "";
        for(var i=0; i<n; i++){
            rand += Math.floor(Math.random() * 10);
        }
        return rand;
    },

    str2json: function($obj){
        return eval('(' + $obj + ')');
    },

    json2str: function($obj){
        return JSON.stringify($obj);
    },

    ajax: function($opt){
        var opt = $.extend({
            url: '',
            type: 'POST',
            data: {},
            dataType: 'json',
            // async: false,
            cache: false,
            timeout: 6000,
            beforeSend: Juuz._ajaxBeforeSend,
            success: Juuz._ajaxSuccess,
            error: Juuz._ajaxError
        }, $opt);
        $.ajax(opt);
    },

    ajaxPost: function(url, data, success, beforeSend){
        var opt = {
            url: url,
            type: 'POST',
            data: data || {},
            beforeSend: beforeSend || Juuz._ajaxBeforeSend
        }
        if($.isFunction(success)){
            opt.success = success;
        }
        Juuz.ajax(opt);
    },

    ajaxGet: function(url, data, success, beforeSend){
        var opt = {
            url: url,
            type: 'GET',
            data: data || {},
            beforeSend: beforeSend || Juuz._ajaxBeforeSend
        }
        if($.isFunction(success)){
            opt.success = success;
        }
        Juuz.ajax(opt);
    },

    ajaxHtml: function(url, data, dataType, success, beforeSend){
        var opt = {
            url: url,
            type: (dataType == 'html') ? 'GET' : 'post',
            data: data || {},
            dataType: dataType || 'html',
            beforeSend: beforeSend || Juuz._ajaxBeforeSend
        }
        if($.isFunction(success)){
            opt.success = success;
        }
        Juuz.ajax(opt);
    },

    _ajaxBeforeSend: function(){
        Juuz.tipsMsg("正在请求，请稍后。", 1000, Juuz.noop);
        return true;
    },

    _ajaxSuccess: function(json){
        Juuz.doByJson(json);
    },

    _ajaxError: function(XMLHttpRequest, status, e){
        var msg = status == "timeout" ? "请求超时" : "请求失败";
        Juuz.errorMsg(msg);
    },

    doByJson: function(json, callback, $this){
        if(json.statusCode == Juuz.code.ajaxNotLogin){
            var message = json.message || '没有权限';
            Juuz.showMsg('error', message, 1500, function(){
                if(json.url && json.url != ''){
                    window.location.href = json.url;
                }
                Juuz.hideMsg();
            });
            return;
        }
        var status = parseInt(json.statusCode) || Juuz.code.ajaxSuccess;
        var message = json.message || '请求失败';
        var $this = $this || null;
        var type = (status == Juuz.code.ajaxSuccess) ? 'success' : 'error';

        Juuz.showMsg(type, message, 1500, function(){
            if(json.url && json.url != ''){
                window.location.href = json.url;
            }
            if(type=='success' && $.isFunction(callback)){
                callback(json, $this);
            }
            Juuz.hideMsg();
        });
    },

    _ajaxFormBefore: function(obj){
        var json = Juuz.checkForm(obj);
        if(!json.status){
            var msg = json.msg || '';
            Juuz.tipsMsg(msg);
            return false;
        }
        return true;
    },

    ajaxForm: function(obj, before, dataType, success){
        var formBefore = before || Juuz._ajaxFormBefore;
        if(!formBefore(obj)){
            return false;
        }
        var $obj = $(obj);
        var $opt = {
            url: $obj.attr('action'),
            type: $obj.attr('method') || 'POST',
            data: $obj.serialize(),
            dataType: dataType || 'json',
            success: $.isFunction(success) ? success : Juuz._ajaxSuccess
        }
        Juuz.ajax($opt);

        return false;
    },

    checkForm:function(obj){
        var $obj = $(obj);
        var dataArray = $obj.serializeArray();

        for(i in dataArray){
            var $this = $("[name='"+dataArray[i]['name']+"']", $obj);
            var _val = dataArray[i]['value'];
            var $opt = $this.data();

            var _type = $opt.type || '';        //自定义正则验证
            var _msg = $opt.msg || '';          //自定义验证提示消息

            if(_type && (_type.indexOf('require') >= 0 || _type.indexOf('require') < 0 && _val != "")){
                var _rule = _type.split(" ");
                var _msgs = _msg.split(" ");
                for(k in _rule){
                    var _msgNow = _msgs[k] ? _msgs[k] : _msgs[0];
                    if(_rule[k] == "repeat"){
                        var _repwd = $("#" + $opt.rel).val();
                        if(_repwd && _repwd != _val){
                            return {status:false, msg:_msgNow, obj:$this};
                        }
                    }else{
                        if(!Juuz.regex(_rule[k], _val)){
                            return {status:false, msg:_msgNow, obj:$this};
                        }
                    }
                }
            }
        }
        return {status:true, msg:'验证通过', obj:$obj};
    },

    //正则验证函数
    regex:function(str, value){
        var arr = {
            'require':/\S/,
            'username':/^[0-9A-Za-z_.]{6,16}$/,
            'nickname':/^[\u4e00-\u9fa5_a-zA-Z0-9\s]+$/,
            'password':/^.{6,16}$/,
            'phone':/^0?1(3|4|5|6|7|8)\d{9}$/,
            'email':/^[0-9A-Za-zd]+([-_.][0-9A-Za-zd]+)*@([0-9A-Za-zd]+[-.])+[0-9A-Za-zd]{2,5}$/,
            'zip':/^[0-9]{6,6}$/,
            'card':/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/,
            'number': /^[0-9]{1,18}$/,
            'price': /\d{1,10}(\.\d{1,2})?$/
        };
        var _regex = arr[str] || str;
        if((typeof _regex=='string')&&_regex.constructor==String){
            return true;
        }
        return _regex.test(value);
    },

    _dialog: function($opt){
        var $obj = $.extend({
            drag: false,
            okValue: '确定',
            cancelValue: '取消',
            backdropOpacity: 0
        }, $opt);

        var d = dialog($obj);
        d.showModal();
        return d;
    },

    showMsg: function(type, msg, time, callback){
        Juuz.hideMsg();

        var time = time || 1500;
        var type = 'ui_' + type || 'ui_tips';
        var $opt = {
            id: 'uiShowMsgID',
            fixed: true,
            backdropOpacity: 0.1,
            content: msg,
            padding: '8px 20px',
            skin: type
        }
        var d = Juuz._dialog($opt);

        setTimeout(function(){
            if($.isFunction(callback)) {
                callback();
            }else{
                Juuz.hideMsg();
            }
        }, time);
    },

    hideMsg: function(){
        if(dialog.get('uiShowMsgID')){
            dialog.get('uiShowMsgID').close().remove();
        }
    },

    successMsg: function(msg, time, callback){
        var time = time || 1500;
        var callback = callback || null;
        Juuz.showMsg('success', msg, time, callback);
    },

    errorMsg: function(msg, time, callback){
        var time = time || 1500;
        var callback = callback || null;
        Juuz.showMsg('error', msg, time, callback);
    },

    tipsMsg: function(msg, time, callback){
        var time = time || 1500;
        var callback = callback || null;
        Juuz.showMsg('tips', msg, time, callback);
    },

    confirm: function($obj, callback){
        var $opt = $.extend({
            fixed: true,
            title: '弹出框',
            content: '消息内容',
            width: 400,
            padding: '30px 20px',
            skin: 'ui_confirm'
        }, $obj);

        var d = Juuz._dialog($opt);

        if($.isFunction(callback)){
            callback(d);
        }
    },

    popBox: function($obj, callback){
        // Juuz.hidePopBox();

        var $opt = $.extend({
            // id: 'popBoxID',
            fixed: true,
            backdropOpacity: 0.2,
            padding: '0',
            content: '',
            skin: 'ui_pop_box',
            quickClose: true
        }, $obj);
        var d = Juuz._dialog($opt);

        if($.isFunction(callback)){
            callback(d);
        }
    },

    hidePopBox: function(){
        if(dialog.get('popBoxID')){
            dialog.get('popBoxID').close().remove();
        }
    },

    ajaxToDo: function($this, scallback){
        var _data = $this.data();
        var url = _data.url || '';
        var title = _data.title || '';
        var msg = _data.msg || title;
        var type = _data.type || '';
        var value = _data.value || '';
        var param = _data.param || null;
        var btnArr = typeof(_data.btnArr) == 'string' ? Juuz.str2json(_data.btnArr) : _data.btnArr;

        if(type == 'switch'){
            var urlArr = url.split("|");

            url = urlArr[value];
        }

        if(title != ''){
            var $opt = {
                title: title,
                content: msg
            }
            if(btnArr && btnArr.length > 0){
                $opt.button = [];
                for(var i = 0; i < btnArr.length; i++){
                    var btn = null;
                    (function(xindex){
                        var btnurl = btnArr[xindex].url;
                        var btncallback = btnArr[xindex].callback;
                        btn = {
                            value: btnArr[xindex].name,
                            callback: function(){
                                Juuz.ajaxGet(btnurl, param, function(json){
                                    if(btncallback && btncallback != ''){
                                        btncallback = window[btncallback];
                                    }
                                    Juuz.doByJson(json, btncallback, $this);
                                });
                            }
                        }
                    })(i);
                    $opt.button.push(btn);
                }
            }else{
                $opt.okValue = "确认";
                $opt.cancelValue = "取消";
                $opt.ok = function(){
                    execAjax();
                }
            }
            Juuz.confirm($opt);
        }else{
            execAjax();
        }

        function execAjax(){
            Juuz.ajaxGet(url, param, function(json){
                Juuz.doByJson(json, scallback, $this);
            })
        }
    },

    updateInputValue: function(args, lookupGroup){
        for(var key in args) {
            var name = lookupGroup+"."+key;
            $("input[name='"+name+"']").val(args[key]);
        }
    },

    uiInit: function(box){
        $('input', box).each(function(){
            $(this).attr('autocomplete', 'off');
        });

        $('a', box).off('focus').on('focus',function(){
            $(this).blur();
        });

        $('*[data-opt]', box).each(function(){
            var $this = $(this);
            var _opt = $this.attr('data-opt') || "{}";
            var $opt = Juuz.str2json(_opt);
            $this.data($opt);
            $this.removeAttr('data-opt');
        });
    }
}