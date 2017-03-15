
function loadHTML(value) {
    var _html = '', _url = $("#webdomain").val();
    switch (value) {
        case 1 : // 发送验证码
            _url += '/Api/User/send.html';
            _html += '<div class="form-control radius">\
							<div class="form-cols">\
								<label>手机号码</label>\
								<div class="form-txt">\
									<input type="text" name="phone" value=""placeholder="手机号码"/>\
								</div>\
							</div>\
						</div>';
            _html += '<div class="form-control radius">\
							<div class="form-cols">\
								<label>类 型</label>\
								<div class="form-txt">\
									<input type="text" name="type"  value="" placeholder="备注:1注册2找回"/>\
								</div>\
							</div>\
						</div>';

            break;
        case 2 : // 效验验证码
            _url += '/Api/User/verify.html';

            _html += '<div class="form-control radius">\
							<div class="form-cols">\
								<label>tel</label>\
								<div class="form-txt">\
									<input type="text" name="tel"  value="" placeholder="电话"/>\
								</div>\
							</div>\
						</div>';
            _html += '<div class="form-control radius">\
							<div class="form-cols">\
								<label>code</label>\
								<div class="form-txt">\
									<input type="text" name="code"  value="" placeholder="验证码"/>\
								</div>\
							</div>\
						</div>';

            break;
        case 3 : // 注册
            _url += '/Api/User/reg.html';

            _html += '<div class="form-control radius">\
							<div class="form-cols">\
								<label>tel</label>\
								<div class="form-txt">\
									<input type="text" name="tel"  value="" placeholder="电话"/>\
								</div>\
							</div>\
						</div>';
            _html += '<div class="form-control radius">\
							<div class="form-cols">\
								<label>pwd</label>\
								<div class="form-txt">\
									<input type="password" name="pwd" value="" placeholder="密码"/>\
								</div>\
							</div>\
						</div>';
            _html += '<div class="form-control radius">\
							<div class="form-cols">\
								<label>确认密码</label>\
								<div class="form-txt">\
									<input type="password" name="repwd" value="" placeholder="确认密码"/>\
								</div>\
							</div>\
						</div>';
            _html += '<div class="form-control radius">\
							<div class="form-cols">\
								<label>code</label>\
								<div class="form-txt">\
									<input type="text" name="code" value="" placeholder="验证码"/>\
								</div>\
							</div>\
						</div>';

            break;
        case 4 : // 登录
            _url += '/Api/User/login.html';
            _html += '<div class="form-control radius">\
							<div class="form-cols">\
								<label>tel</label>\
								<div class="form-txt">\
									<input type="text" name="tel" value="" placeholder="电话"/>\
								</div>\
							</div>\
						</div>';
            _html += '<div class="form-control radius">\
							<div class="form-cols">\
								<label>pwd</label>\
								<div class="form-txt">\
									<input type="password" name="pwd" value="" placeholder="密码"/>\
								</div>\
							</div>\
						</div>';
            _html += '<div class="form-control radius">\
							<div class="form-cols">\
								<label>os</label>\
								<div class="form-txt">\
									<input type="text" name="os" value="" placeholder="操作系统"/>\
								</div>\
							</div>\
						</div>';
            _html += '<div class="form-control radius">\
							<div class="form-cols">\
								<label>osversion</label>\
								<div class="form-txt">\
									<input type="text" name="osversion" value="" placeholder="操作系统版本"/>\
								</div>\
							</div>\
						</div>';
            _html += '<div class="form-control radius">\
							<div class="form-cols">\
								<label>machinecode</label>\
								<div class="form-txt">\
									<input type="text" name="machinecode" value="" placeholder="机器码"/>\
								</div>\
							</div>\
						</div>';
            break;
        case 5 : // 选购指南分类
            _url += '/Api/Guide/class.html';
            break;
        case 6 : // 样板间分类
            _url += '/Api/Example/class.html';
            break;
        case 7 : // 选购指南样板间搜索列表
            _url += '/Api/Search/xgznybjsearch.html';

            _html += '<div class="form-control radius">\
							<div class="form-cols">\
								<label>uid</label>\
								<div class="form-txt">\
									<input type="text" name="uid" value="1" />\
								</div>\
							</div>\
						</div>';
            _html += '<div class="form-control radius">\
							<div class="form-cols">\
								<label>uuid</label>\
								<div class="form-txt">\
									<input type="text" name="uuid" value="8f0bc90db67ae1170e1649f2d4f44e8d" />\
								</div>\
							</div>\
						</div>';
            _html += '<div class="form-control radius">\
							<div class="form-cols">\
								<label>keyword</label>\
								<div class="form-txt">\
									<input type="text" name="keyword" value="" placeholder="地中海"/>\
								</div>\
							</div>\
						</div>';
            _html += '<div class="form-control radius">\
							<div class="form-cols">\
								<label>type</label>\
								<div class="form-txt">\
									<input type="text" name="type" value="" placeholder="类型（1选购指南，2样板间）"/>\
								</div>\
							</div>\
						</div>';
            _html += '<div class="form-control radius">\
							<div class="form-cols">\
								<label>areaid</label>\
								<div class="form-txt">\
									<input type="text" name="areaid" value="" placeholder="区域id"/>\
								</div>\
							</div>\
						</div>';
            _html += '<div class="form-control radius">\
							<div class="form-cols">\
								<label>page</label>\
								<div class="form-txt">\
									<input type="text" name="page" value="" placeholder="起始页"/>\
								</div>\
							</div>\
						</div>';
            _html += '<div class="form-control radius">\
							<div class="form-cols">\
								<label>pagenum</label>\
								<div class="form-txt">\
									<input type="text" name="pagenum" value="" placeholder="每页显示的记录数"/>\
								</div>\
							</div>\
						</div>';
            break;
        case 8 : // 设计师/工长列表（找设计、找工长）
            _url += '/Api/Designer/list.html';

            _html += '<div class="form-control radius">\
							<div class="form-cols">\
								<label>type</label>\
								<div class="form-txt">\
									<input type="text" name="type" value="" placeholder="设计师类型（1设计师，2工长）"/>\
								</div>\
							</div>\
						</div>';
            _html += '<div class="form-control radius">\
							<div class="form-cols">\
								<label>page</label>\
								<div class="form-txt">\
									<input type="text" name="page" value="" placeholder="起始页"/>\
								</div>\
							</div>\
						</div>';
            _html += '<div class="form-control radius">\
							<div class="form-cols">\
								<label>pagenum</label>\
								<div class="form-txt">\
									<input type="text" name="pagenum" value="" placeholder="每页显示数"/>\
								</div>\
							</div>\
						</div>';
            _html += '<div class="form-control radius">\
							<div class="form-cols">\
								<label>areaid</label>\
								<div class="form-txt">\
									<input type="text" name="areaid" value="" placeholder="区域id"/>\
								</div>\
							</div>\
						</div>';

            break;
        case 9 : // 设计师/工长详情
            _url += '/Api/Designer/userinfo.html';

            _html += '<div class="form-control radius">\
							<div class="form-cols">\
								<label>uid</label>\
								<div class="form-txt">\
									<input type="text" name="uid" value="1"/>\
								</div>\
							</div>\
						</div>';
            _html += '<div class="form-control radius">\
							<div class="form-cols">\
								<label>uuid</label>\
								<div class="form-txt">\
									<input type="text" name="uuid" value="8f0bc90db67ae1170e1649f2d4f44e8d"/>\
								</div>\
							</div>\
						</div>';
            _html += '<div class="form-control radius">\
							<div class="form-cols">\
								<label>id</label>\
								<div class="form-txt">\
									<input type="text" name="id" value="" placeholder="设计师/工长id"/>\
								</div>\
							</div>\
						</div>';

            break;
        case 10 : // 案例展示
            _url += '/Api/Designer/caselist.html';

            _html += '<div class="form-control radius">\
							<div class="form-cols">\
								<label>uid</label>\
								<div class="form-txt">\
									<input type="text" name="uid" value="" placeholder="设计师/工长id"/>\
								</div>\
							</div>\
						</div>';
            _html += '<div class="form-control radius">\
							<div class="form-cols">\
								<label>page</label>\
								<div class="form-txt">\
									<input type="text" name="page" value="" placeholder="起始页"/>\
								</div>\
							</div>\
						</div>';
            _html += '<div class="form-control radius">\
							<div class="form-cols">\
								<label>pagenum</label>\
								<div class="form-txt">\
									<input type="text" name="pagenum" value="" placeholder="每页显示数"/>\
								</div>\
							</div>\
						</div>';

            break;

    }

    $('[data-ele=getLinkAddress]').val(_url);
    $('[data-ele=formHtml]').html(_html);
    $('.ajax_from').attr('action',_url);
}


/*
 function ajaxPost(obj){
 var $obj = $(obj);
 var _url = $('[data-ele=getLinkAddress]').val();
 var _data= $obj.serialize();


 $.ajax({
 url     : _url,
 type    : 'post',
 data    : _data,
 dataType: 'json',
 success : function(json){
     $(".json-result").html(JSON.stringify(json));
 }
 });

 return false;
 }
 */


function ajaxPost() {
    var _data = $('.ajax_from').serialize(),
       _url    = $('.ajax_from').attr('action');
        ajax = $.ajax({
            url: _url,
            type: 'post',
            data: _data,
            dataType: 'json',
            success:function(data){
                var result = new JSONFormat(JSON.stringify(data.data_res), 4).toString();

                $(".json-result").html(result);
                var $data = $('.data-table'),
                    $table = '<table><tr><th>字段</th><th>类型</th><th>作用</th><th>描述</th></tr>';
                    if (data['data_res'].code == "200") {
                        for (var i = 0, len = data['field_desc'].length; i < len; i++) {
                            $table += '<tr>';
                            for (var key in data.field_desc[i]) {
                                $table += '<td>' + data.field_desc[i][key] + '</td>';
                            }
                            $table += '</tr>';
                        }
                        $table += '</table>';
                        $data.html('');
                        $data.append($table);
                    } else {
                        $data.html("<span>暂无数据！</span>");
                        return;
                    }
                //result.

            }

        });

    //$.when(ajax).done(function (jsonResult, jsonExplain) {
    //
    //    //返回json结果
    //    var result = new JSONFormat(JSON.stringify(jsonResult[0]), 4).toString();
    //
    //    $(".json-result").html(result);
    //
    //    //返回json说明
    //
    //    var $data = $('.data-table'),
    //        $table = '<table><tr><th>字段</th><th>类型</th><th>作用</th><th>描述</th></tr>';
    //    if (jsonExplain[0].code == "200") {
    //
    //        for (var i = 0, len = jsonExplain[0].res.length; i < len; i++) {
    //            $table += '<tr>';
    //            for (var key in jsonExplain[0].res[i]) {
    //                $table += '<td>' + jsonExplain[0].res[i][key] + '</td>';
    //            }
    //            $table += '</tr>';
    //        }
    //        $table += '</table>';
    //        $data.html('');
    //        $data.append($table);
    //    } else {
    //        $data.html("<span>暂无数据！</span>");
    //        return;
    //    }
    //});
    //
    return false;
}