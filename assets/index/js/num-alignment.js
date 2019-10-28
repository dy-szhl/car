(function($){ 
	var functions = {
		init:function(input, step, max, min, digit){
			var width = input.width()-3;
			var height = input.width()/4;
			var _this = this;
		 	width += 3;
		 	
		 	input.attr("readonly", "readonly");

			//左右调用执行
			$("#" + input.attr('id') + "l").click(function(){
				_this.execute(input, step, max, min, digit, true);
			});
			$("#" + input.attr('id') + "r").click(function(){
				_this.execute(input, step, max, min, digit, false);
			});
		},
		execute:function(input, step, max, min, digit, _do){
			var val = parseFloat(this.format(input.val(), digit));
			var ori = val;
			if(_do) val -= step;
			if(!_do) val += step;
			if(val<min){
				val  =  min;
			}else if(val>max){
				val  =  max;
			}
			input.val(this.format(val, digit));
		},
		format:function(val, digit){
			if(isNaN(val)){ 
				val = 0;
			}
			return parseFloat(val).toFixed(digit);	
		}
	};
	
	
    $(function(){
    	//使用控件必须有以下属性或者引用alignment类
		var inputs = $("input[user_data], input[data_digit], input[data_step], input[data_min], input[data_max], input.alignment");
		inputs.each(function(){
			//预设值数据选择
			var data = {
			            default_data : 	{"step" : 1, "min" : 1, "max" : 99, "digit" : 1}, 
			            aaa : 			{"step" : 5, "min" : 1, "max" : 99, "digit" : 1}, 
						}
			
			var user_data = eval("data." + $(this).attr("user_data"));
			if(user_data == null){
				user_data = data.default_data;
			}
			
			var digit = $(this).attr("data_digit");
			if(digit != null&&!isNaN(parseFloat(digit))){
				digit  =  parseFloat(digit).toFixed(0);
				user_data.digit = parseFloat(digit);
			}
			
			var step = $(this).attr("data_step");
			if(step != null &&!isNaN(parseFloat(step))){
				user_data.step = parseFloat(step);
			}
			var min = $(this).attr("data_min");
			if(min != null &&!isNaN(parseFloat(min))){
				user_data.min = parseFloat(min);
			}
			
			var max = $(this).attr("data_max");
			if(max != null &&!isNaN(parseFloat(max))){
				user_data.max = parseFloat(max);
			}
			//自动装载
	        functions.init($(this), user_data.step, user_data.max, user_data.min, user_data.digit);
	        
	        var data_edit = $(this).attr("data_edit");
	        if(data_edit){
	        	$(this).attr("readonly",null);
	        }
		});
	})  
})(jQuery);