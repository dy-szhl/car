	//选中筛选
	function onselect(id,name){
		$("#"+name+"_id_"+id).css("color","red");
		$("."+name+"_id_"+id).css("display","block");
	}
	
	//移除筛选
	function unselect(name){
		var oId = $("#wap_"+name+"_id").val();
		$("#"+name+"_id_"+oId).css("color","#333");
		$("."+name+"_id_"+oId).css("display","none");
	}
	
	function myselectEvent(id,name,flag){
		//筛选样式
		unselect(name);
		onselect(id,name);
		if(typeof outMySelectEvent==='function'){
			outMySelectEvent(id,name,flag)
		}
		$("#wap_"+name+"_id").val(id);
		var t=$("#"+name+"_id_"+id).text();
		if($("#s_"+name+"_id").html()==null){
			 $('.resultList').append("<li><span id='s_"+name+"_id'></span><a href='javascript:send("+flag+");'></a></li>");
		}
		$("#s_"+name+"_id").text(t);
		$("#mingch_"+name+"_id").text(t);
		$("#con_"+name+"_id").val(t);
	}
	
	function myselect(id){
		var wap_type = $("#wap_type").val();
		myselectEvent(id, wap_type,2);
	}
	function myselectReset(list){
		if(list instanceof Array){
			list.map(function(item){
				$("."+item+' .all').show();
				$("."+item+' .single').hide().text('全部');
				unselect(item)
			})
		}
	}

        


  //给元素加事件
    $(function(){

        
        /*筛选点击*/
        $('.filterBtn').bind('click',function(){
            $('.filter').show();
            $('body,html').css({'height':'100%','overflow':'hidden'});
        });
        /*筛选关闭*/
        $('.close').bind('click',function(){
            $('.filter').hide();
            $('body,html').css({'height':'auto','overflow':'auto'});
        });

		$('.goBack').bind('click',function(){
			$('.select').hide();
			$('.filterList').show();
		});

		//下拉点击效果
       $("#select_brand_id").bind('click',function(){//品牌
         	 $('.filterList').hide();
         	 $('#show_brand_id').show();
         	 $('#wap_type').val('brand');
        });
       $("#select_price_id").bind('click',function(){//价格
          	 $('.filterList').hide();
          	 $('#show_price_id').show();
          	 $('#wap_type').val('price');
        });
          $("#select_attr_prov_id").bind('click',function(){//白酒省份
         	 $('.filterList').hide();
         	 $('#show_attr_prov_id').show();
         	 $('#wap_type').val('attr_prov');
         });

        $('.option li.last').unbind('click');
        $('.brandArea li').bind('click',function(){
            $(this).find('a').addClass('on');
            $(this).siblings().find('a').removeClass('on');
        });
        /*返回筛选列表*/
        $('.detailOption li').bind('click',function(){
            $('.select').hide();
            $('.filterList').show();
            $('.result').show();
            $('.brand .all').hide();
            $('.brand .single').show();

        });

    });



        