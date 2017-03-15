var Manage = {
    uiInit: function(box){
        var $p = $(box || document);
        Juuz.uiInit($p);
        $(document).click(function(e){
            var target = $(e.target);
            if(target.closest('.js_list_tools').length == 0){
                $('.list_tools_menu').hide();
            }
        });
        $('input,textarea', $p).focusClass().addRequireClass();
        $('input[readonly=readonly],textarea[readonly=readonly]', $p).addClass('readonly');
        $('input[disabled=disabled],textarea[disabled=disabled]', $p).addClass('disabled');
        $("input[type=radio]", $p).radio();
        $("input[type=checkbox]", $p).checkbox();
        $('input.js_color_input', $p).colorInput();
        $("textarea.editor", $p).each(function(){
            var $this = $(this);
            var R = '' + new Date().getTime() + "_" + Juuz.randNum(4);
            $this.attr("id", "editor" + R);
            if(UM){
                UM.getEditor($this.attr("id"));
            }
        });

        if($('select.combox', $p).size() >0 ){
            $('select.combox').combox();
        };
        if($('input.js_hidden_file', $p).size() > 0 || $('.js_hidden_group', $p).size() > 0){
            SWFInit();
        };
        if($('.js_map_warp', $p).size() > 0){
            $('.js_map_warp').mapInit();
        };

        $('.js_color_chang').each(function() {
            $(this).minicolors({
                control: $(this).attr('data-control') || 'hue',
                opacity: $(this).attr('data-opacity'),
                position: $(this).attr('data-position') || 'bottom left',
                change: function(hex, opacity) {
                    var log;
                    try {
                        log = hex ? hex : 'transparent';
                        if( opacity ) log += ', ' + opacity;
                    } catch(e) {}
                },
                theme: 'default'
            });
        });

        $('a[target=ajaxDel]').each(function(){
            var $this = $(this);
            $this.off('click').on('click', function(){
                var $this = $(this);
                if($this.hasClass('disable')){
                    return false;
                }
                var callback = $this.data().callback || '';
                Juuz.ajaxToDo($this,function(json, obj){
                    obj.parents('.js_remove').remove();
                    if(callback && callback != ''){
                        window[callback](obj);
                    }
                });
                return false;
            })
        });

        $('a[target=ajaxTodo]').each(function(){
            var $this = $(this);
            $this.off('click').on('click', function(){
                var $this = $(this);
                if($this.hasClass('disable')){
                    return false;
                }
                var callback = $this.data().callback || '';
                if(callback && callback != ''){
                    callback = window[callback];
                    Juuz.ajaxToDo($this, callback);
                }else{
                    Juuz.ajaxToDo($this);
                }

                return false;
            })
        });

        $('a[target=popDialog]').each(function(){
            var $this = $(this);
            $this.off('click').on('click', function(){
                var $this = $(this),
                    type = $this.data().type,
                    url = $this.data().url,
                    dataType = $this.data().dataType || 'html',
                    callback = $this.data().callback || '',
                    before = $this.data().before || '';

                if(before != '' && !window[before]()){
                    return;
                }

                var $obj = {
                    title: $this.data().title,
                    width: $this.data().width || 'auto',
                    height: $this.data().height || 'auto',
                    quickClose: false
                }

                if(type == 'findBack'){
                    var data = {
                        id : $this.prev('input').val()
                    }
                    $obj.okValue = '保存';
                    $obj.ok = function(){
                        if(callback && callback != ''){
                            window[callback]();
                        }
                    }
                }else{
                    var data = null;
                }

                Juuz.ajaxHtml(url, data, dataType, function(json){
                    try{
                        json = Juuz.str2json(json);
                    }catch(e){

                    }
                    if(json.statusCode == Juuz.code.ajaxNotLogin){
                        var message = json.message || "没有权限访问";
                        Juuz.errorMsg(message, 1500, function(){
                            window.location.href = json.url;
                        });
                        return false;
                    }


                    var html = '';
                    if(dataType == 'html'){
                        html = json;
                    }else if(dataType == 'jsonp' && parseInt(json.statusCode) == Juuz.code.ajaxSuccess){
                        html = json.html;
                    }else {
                        var msg = json.message || "请求失败，请稍后重试。";
                        Juuz.errorMsg(msg);
                        return;
                    }
                    $obj.content = html;
                    Juuz.popBox($obj, function(d){
                        Manage.uiInit('.ui-popup');
                        d.reset();
                    });
                }, Juuz.noop);

                return false;
            })
        });

        $('.js_list_tools').each(function(){
            var $this = $(this);
            $this.off('click').on('click', function(){
                var $this = $(this);
                $('.list_tools_menu').hide();
                $this.find('ul').show();
            })
        });

        $('.js_list_paging a').each(function(){
            var $this = $(this);
            $this.off('click').on('click', function(){
                var currentPageInput = $('input[name=currentPage]');
                var currentPage = currentPageInput.val();
                var $this = $(this);
                if($this.hasClass('disable') || $this.hasClass('current')){
                    return;
                }
                var type = $this.attr('data-type');
                if(type == 'first'){
                    currentPage = 1;
                }else if(type == 'pre'){
                    if(currentPage > 1){
                       currentPage--;
                    }
                }else if(type == 'next'){
                    if(currentPage < listJson.totalPage){
                        currentPage++;
                    }
                }else if(type == 'last'){
                    currentPage = listJson.totalPage;
                }else{
                    currentPage = $this.attr('data-value');
                }

                currentPageInput.val(currentPage);
                $('#searchForm').submit();

                return false;
            });
        });

        $('.js_imgitem_del').each(function(){
            var $this = $(this);
            $this.off('click').on('click', function(){
                var $this = $(this);
                var liCount = $('.ui_upload_imgitem').find('li').size();
                if(liCount > 1){
                    $this.parents('li').remove();
                }else{
                    Juuz.tipsMsg("只剩一条数据，无法删除！");
                }

                return false;
            })
        });

        $('.js_imgitem_sort').each(function(){
            var $this = $(this);
            $this.off('click').on('click', function(){
                var $this = $(this);
                var type = $this.attr('data-type');
                var prevObj = $this.parents('li').prev();
                var nextObj = $this.parents('li').next();
                var currentObj = $this.parents('li');
                if(type == 'up'){
                    if(prevObj.size() >= 1){
                        currentObj.insertBefore(prevObj);
                    }else{
                        Juuz.tipsMsg("当前已经是第一个！");
                    }
                }else if(type == 'dowm'){
                    if(nextObj.size() >= 1){
                        currentObj.insertAfter(nextObj);
                    }else{
                        Juuz.tipsMsg("当前已经是最后一个！");
                    }
                }
            })
        });

        $('.js_label_del').each(function(i, item){
            var $this = $(this);
            $this.off('click').on('click', function(){
                var $this = $(this);
                var id = $this.next('input').val();
                var idsArr = [];
                var parentObj = $this.parents('.select_val');
                if(parentObj.size() <= 0){
                    parentObj = $this.parents('.cagegory_label');
                }
                var ids = parentObj.attr('data-ids')+"";

                if(ids.indexOf(',') >= 0){
                    idsArr = ids.split(',');
                }else{
                    idsArr.push(ids);
                }
                for(i in idsArr){
                    if(idsArr[i] == id){
                        idsArr.splice(i, 1);
                    }
                }
                ids = idsArr.join(',');

                parentObj.attr('data-ids', ids);
                $this.parent().remove();
                $('#label'+id).removeClass('active');

                return false;
            });
        });
    },

    listTemp: function(json){
        var thead = json.thead;
        var tbody = json.tbody;
        var toolsOption = json.toolsOption;
        var showPageNum = json.showPageNum || 10;
        var currentPageNum = json.currentPage || 1;
        var totalPage = json.totalPage || 1;

        var listStr = "";
        listStr += '<table>';
        listStr += '    <thead>';
        listStr += '        <tr>';
                    for(var i = 0,len = thead.length;i < len; i++){
                        if(thead[i].width){
        listStr += '        <th style="width:'+thead[i].width+'px;">'+thead[i].name+'</th>';
                        }else{
        listStr += '        <th>'+thead[i].name+'</th>';
                        }
                    }
        listStr += '        </tr>';
        listStr += '    </thead>';
        listStr += '    <tbody>';
                if(tbody.length > 0){
                    for(var j = 0,lenj = tbody.length;j < lenj; j++){
                        var toolsHtml = Manage.getToolsHtml(toolsOption, tbody[j]);
        listStr += '        <tr class="js_remove">';
                        for(var k = 0,lenk = thead.length;k < lenk; k++){
                            var align = thead[k].align || 'center';
                            if(align == 'left'){
                                var clsVal = 'left';
                            }else if(align == 'right'){
                                var clsVal = 'right';
                            }else{
                                var clsVal = 'center';
                            }
                            var fieldVal = tbody[j][thead[k].field] || '';
                            if(thead[k].type == "IMG"){
        listStr += '            <td class="img" style="text-align:'+clsVal+';"><img src="'+fieldVal+'" /></td>';
                            }else if(thead[k].type == "TOOLS"){
        listStr += '            <td style="text-align:'+clsVal+';">';
        listStr += '                <i class="btn_caozuo js_list_tools">';
        listStr += '                操作' + toolsHtml;
        listStr += '                </i>';
        listStr += '            </td>';
                            }else{
        listStr += '            <td style="text-align:'+clsVal+';"><span>'+fieldVal+'</span></td>';
                            }
                        }
        listStr += '        </tr>';
                    }
                }else{
                    totalPage = 1;
        listStr += '        <tr><td colspan="'+thead.length+'">暂无数据</td></tr>';
                }
        listStr += '    </tbody>';
        listStr += '    <tfoot>';
        listStr += '        <tr>';
        listStr += '            <td colspan="'+thead.length+'">';
        listStr +=                  Manage.getPagingHtml(currentPageNum, totalPage, showPageNum);
        listStr += '            </td>';
        listStr += '        </tr>';
        listStr += '    </tfoot>';
        listStr += '</table>';

        return listStr;
    },

    getToolsHtml: function(opt, tbody){
        if(!opt){
            return '';
        }
        var html = '';
        html += '<ul class="list_tools_menu">';
            for(i in opt){
                var _op     = opt[i];
                var _opt    = _makeOptValue(_op.opt);
                var _target = '',
                    _url    = 'javascript:void(0);',
                    _sopt   = '';
                if(_op.target){
                    _target = 'target='+_op.target;
                    _sopt   = 'data-opt=\''+Juuz.json2str(_opt)+'\'';
                }else if(_opt.url){
                    _url = _opt.url;
                }
                if(_opt.value && _opt.value != ''){
                    var nameArr = _op.name.split('|');
                    var name = nameArr[_opt.value];
                }else{
                    var name = _op.name;
                }
                if(name != ''){
                    html += '<li><a href="'+_url+'" '+_target+' '+ _sopt +'>'+name+'</a></li>';
                }
            }
        html += '</ul>';
        return html;

        function _makeOptValue(opts){
            var reg =  /___(\w+)___/ig;
            var arrs = {};
            for(i in opts){
                var strs = opts[i];
                strs.replace(reg, function(str, val){
                    strs = strs.replace(str, tbody[val]);
                });
                arrs[i] = strs;
            }
            return arrs;
        }
    },

    getPagingHtml: function(cpage, tpage, spage){
        if(tpage <= spage){
            spage = tpage;
        }
        var clsnameFirst = '',
            clsnameLast = '',
            clsnameCurrent = '';
        if(tpage <= 1){
            clsnameFirst = clsnameLast = 'disable';
        }else if(cpage <= 1){
            clsnameFirst = 'disable';
        }else if(cpage >= tpage){
            clsnameLast = 'disable';
        }
        var leftPage = parseInt(spage/2);
        var rightPage = spage - Math.ceil(spage/2);
        if(cpage <= leftPage){
            start = 1;
            end   = spage;
        }else if(cpage < tpage-spage+leftPage){
            start = cpage - leftPage;
            end   = cpage + rightPage;
        }else{
            start = tpage - spage + 1;
            end   = tpage;
        }

        var html = '';
        html += '<div class="paging js_list_paging">';
        html += '   <a href="javascript:void(0);" class="'+clsnameFirst+'" data-type="first">首页</a>';
        html += '   <a href="javascript:void(0);" class="'+clsnameFirst+'" data-type="pre">上一页</a>';
                for(var h = start; h <= end; h++){
                    if(h > tpage){
                        break;
                    }
                    if(cpage > tpage){
                        clsnameCurrent = h == end ? 'current' : '';
                    }else if(cpage < 1){
                        clsnameCurrent = h == 1 ? 'current' : '';
                    }else{
                        clsnameCurrent = h == cpage ? 'current' : '';
                    }
        html += '   <a href="javascript:void(0);" class="'+clsnameCurrent+'" data-value="'+h+'">'+h+'</a>';
                }
        html += '   <a href="javascript:void(0);" class="'+clsnameLast+'" data-type="next">下一页</a>';
        html += '   <a href="javascript:void(0);" class="'+clsnameLast+'" data-type="last">末页</a>';
        html += '</div>';

        return html;
    },

    //更新list数据
    updateListData: function(obj){
        var $obj = $(obj);
        var url = $obj.attr('action') || window.location.href;
        var data = $(obj).serialize();

        Juuz.ajaxPost(url, data, function(json){
            if(json.statusCode == Juuz.code.ajaxSuccess){
                Juuz.hideMsg();

                var listTable = Manage.listTemp(json);
                var listWrap = $('.ui_list_wrap');
                listWrap.html(listTable);
                Manage.uiInit(listWrap);
            }else{
                Juuz.errorMsg(json.message);
            }
        });

        return false;
    },

    //清除分页数据
    clearPage: function(){
        $('input[name=currentPage]').val(1);
        return true;
    },

    //柱形图
    chartBar: function($opt, chartId){
        var data = $.extend(true, {
            chart: {
                type: 'column',
                backgroundColor: '#fff',
                borderColor: '#4572A7',
                borderWidth: 0,
                borderRadius: 0,
                width: 960,
                height: 400
            },
            credits: {
                enabled: true,
                text: '来源：sealyyg.com',
                href: 'http://www.sealyyg.com',
                style: {
                    cursor: 'pointer',
                    fontSize: '12px',
                    color: '#999'
                }
            },
            title: {
                text: '柱形图',
                align: 'center',
                style: {
                    fontSize: '16px',
                    color: '#333'
                }
            },
            plotOptions: {
                column: {
                    pointPadding: 0.1,
                    borderWidth: 0
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            xAxis: {
                title: {
                    text: 'X轴标题',
                    fontSize: '12px',
                    color: '#888'
                },
                gridLineWidth: 1,
                gridLineColor: '#eee',
                gridLineDashStyle: 'longdash',
                lineColor: '#999',
                lineWidth: 1,
                categories: [
                    '1', '2', '3', '4', '5','6', '7', '8', '9', '10','11', '12'
                ],
                crosshair: true
            },
            yAxis: {
                title: {
                    text: 'Y轴标题',
                    fontSize: '12px',
                    color: '#888'
                },
                gridLineWidth: 1,
                gridLineColor: '#eee',
                gridLineDashStyle: 'longdash',
                lineColor: '#999',
                lineWidth: 1
            }
        }, $opt);

        $('#'+chartId).highcharts(data);
    },

    //曲线图
    chartLine: function($opt, chartId){
        var data = $.extend(true, {
            chart: {
                type: 'line',
                backgroundColor: '#fff',
                borderColor: '#4572A7',
                borderWidth: 0,
                borderRadius: 0,
                width: 960,
                height: 400
            },
            credits: {
                enabled: true,
                text: '来源：sealyyg.com',
                href: 'http://www.sealyyg.com',
                style: {
                    cursor: 'pointer',
                    fontSize: '12px',
                    color: '#999'
                }
            },
            title: {
                text: '曲线图',
                align: 'center',
                style: {
                    fontSize: '16px',
                    color: '#333'
                }
            },
            plotOptions: {
                column: {
                    pointPadding: 0.1,
                    borderWidth: 0
                }
            },
            xAxis: {
                title: {
                    text: 'X轴标题',
                    fontSize: '12px',
                    color: '#888'
                },
                gridLineWidth: 1,
                gridLineColor: '#eee',
                gridLineDashStyle: 'longdash',
                lineColor: '#999',
                lineWidth: 1,
                categories: [
                    '1', '2', '3', '4', '5','6', '7', '8', '9', '10','11', '12'
                ],
                crosshair: false
            },
            yAxis: {
                title: {
                    text: 'Y轴标题',
                    fontSize: '12px',
                    color: '#888'
                },
                gridLineWidth: 1,
                gridLineColor: '#eee',
                gridLineDashStyle: 'longdash',
                lineColor: '#999',
                lineWidth: 1
            }
        }, $opt);

        $('#'+chartId).highcharts(data);
    },

    //饼状图
    chartPie: function($opt, chartId){
        var data = $.extend(true, {
            chart: {
                type: 'pie',
                backgroundColor: '#fff',
                borderColor: '#4572A7',
                borderWidth: 0,
                borderRadius: 0,
                width: 960,
                height: 400
            },
            credits: {
                enabled: true,
                text: '来源：sealyyg.com',
                href: 'http://www.sealyyg.com',
                style: {
                    cursor: 'pointer',
                    fontSize: '12px',
                    color: '#999'
                }
            },
            title: {
                text: '饼状图',
                align: 'center',
                style: {
                    fontSize: '16px',
                    color: '#333'
                }
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer'
                }
            }
        }, $opt);

        $('#'+chartId).highcharts(data);
    },

    resizeIniti:function(){
        var docHeight = document.documentElement.clientHeight || $(document).height();
        var minHeight = docHeight - 86;
        var lHeight = $(".col_side").height();
        var rHeight = $(".col_main").height();

        if(rHeight > minHeight){
            $(".col_side").css('min-height', rHeight);
        }else{
            $(".col_side").css('min-height', minHeight);
        }
    }
}

$(function(){
    Manage.uiInit();

    Manage.resizeIniti();
    $('.main_bd').resize(function(){
        Manage.resizeIniti();
    });

    $('.js_imgitem_add').off('click').on('click', function(){
        var $this = $(this);
        var w = $this.attr('data-upload-w') || 0;
        var h = $this.attr('data-upload-h') || 0;
        var uploadData = {
            imgW: w,
            imgH: h
        }

        var prevImgVal = $this.parent().prev().find('li:last-child').find('.js_hidden_file').val();
        if(prevImgVal && prevImgVal != ''){
            var imgitem = TPL.imageUpload(uploadData);
            $('.ui_upload_imgitem').find('ul').append(imgitem);
            Manage.uiInit($('.ui_upload_imgitem'));
        }else{
            Juuz.tipsMsg("请上传完图片后再进行添加！");
        }
    });
})