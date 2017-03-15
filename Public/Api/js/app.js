
$(function(){
	var $doc=$(document),
		$drop=$('.dropdown'),
		$txt =$drop.find('.drop-txt'),
		$list=$drop.find('.drop-list');
	$drop.on('click',function(e){
		var target=e.target;
		if(target.tagName=='SPAN'){
		
			if($list.css('display')=='none'){
				$list.slideDown(100);
				$txt.addClass('on');
			}else{
				$list.slideUp(100);
				$txt.removeClass('on');
			}
		}else if(target.tagName=='LI'){
			var _id=$(target).attr('data-id'),
				_val=$(target).attr('data-value');
				$list.slideUp(100);
				$txt.removeClass('on').find('span').html(_val);
				
				//loadHTML
				loadHTML(parseInt(_id));
		}
		e.stopPropagation();
	});
	
	$doc.on('click',function(){
		$list.slideUp(100);
		$txt.removeClass('on');
	});

	
	//触发上传
	(function(){
		$('[data-ele=formHtml]').on('click','#uploadbtn',function(){
			$('#uploadfile').trigger('click');
		}).on('change','#uploadfile',function(){
			console.log($(this).val());
			$('#file').val($(this).val());
		});
	})();
	
});