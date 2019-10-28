layui.use(['layer'])
$.common={
    //发送验证码
    sendVerify(obj,type,point){
        var phone = point.val()
        var time=60
        if(!phone) {
            alert('请输入手机号码')
            return false;
        }
        if( typeof(type) != 'number') {
            alert('类型异常')
            return false;
        }
        sendAjax('/index/sendSms',{phone:phone,type:type},'post',(res,ele_obj)=>{
            $(obj).addClass('disabled').attr('disabled',"true")
            // alert(result.msg)
            var interval= setInterval(function(){
                if(time>0){
                    $(obj).text('请等待('+time+')')
                }else{
                    clearInterval(interval)
                    $(obj).removeClass('disabled').removeAttr('disabled').text('获取验证码')
                }
                --time;
            },1000)

        })

    },
    //表单提交
    submitForm:function(obj,is_reload,fnc){
        var select_query = typeof obj==='object' ? obj : $("#form");
        is_reload =  is_reload!==undefined;
        sendAjax(select_query.attr('action'),select_query.serialize(),'post',(res,ele_obj)=>{
            if(is_reload){
                window.location.reload()
            }else{
                if(typeof fnc!=='function'){
                    history.back()
                }else{
                    fnc(res,ele_obj)
                }
            }

        })
        return false;
    },
    //待确定请求动作
    waitConfirm:function(tip_msg,url,data,type,func,ele_obj,ajax_add_obj){
        //默认请求成功指定动作
        func = typeof func ==='function' ? func:()=>{
            //默认刷新页面
            window.location.reload()
        }
        if(tip_msg===false){
            sendAjax(url,data,type,func,ele_obj,ajax_add_obj)
        }else{
            layer.confirm(tip_msg,function(){
                sendAjax(url,data,type,func,ele_obj,ajax_add_obj)
            })
        }

    },
    //文件上传
    fileUpload:function(upload,elem,func){
        //执行实例
        var uploadInst =  upload.render({
            elem: elem //绑定元素
            ,before: function(obj){ //obj参数包含的信息，跟 choose回调完全一致，可参见上文。
                layer.load(); //上传loading
            }
            ,done: function(res){
                layer.closeAll('loading'); //关闭loading

                //上传完毕回调
                var item = this.item;
                var res_data = res.hasOwnProperty('data')?res.data:{};
                if(res.code===1){
                    //上传成功
                    if(func!==false){
                        //默认请求成功指定动作
                        func = typeof func ==='function' ? func:(res_data,query_select)=>{
                            //默认
                            console.log(res_data)
                            // console.log(query_select.parents().html())
                            var path = res_data.hasOwnProperty('key')?res_data.key:'';
                            var preview_domain = res_data.hasOwnProperty('preview_domain')?res_data.preview_domain:'';
                            //保存图片路径
                            query_select.prev().val(path)
                            query_select.parent().find('img').attr('src',preview_domain+path)
                        }
                        //执行函数
                        func(res_data,$(item))
                    }

                }else{
                    layui.layer.msg(res.msg)
                }
            }
            ,error: function(){
                layer.closeAll('loading'); //关闭loading
                //请求异常回调
                layui.layer.msg('上传异常!  ')
            }
        });
        return uploadInst
    },
}

//发送请求
function sendAjax(url,data,type,func,ele_obj, ajax_add_obj){
    type = (typeof type===undefined)||(typeof type==='undefined')?'get':type
    ajax_add_obj = ajax_add_obj || {}
    var wiat_time = ajax_add_obj.hasOwnProperty('wait_time')?ajax_add_obj.wait_time:1000
    var hide_tip = ajax_add_obj.hasOwnProperty('hide_tip')?ajax_add_obj.hide_tip:0
    type = type.toLowerCase()==='get'?'get':'post'
    var layer_load;
    var ajax_obj = {
        url:url,
        type:type,
        data:data,
        beforeSend:function(res){
            layer_load = layui.layer.load()
            // console.log(res)
            // console.log('beforeSend')
        },
        success:function(res){
            //console.log(res)
            // console.log('success')
            if(res){
                !hide_tip && layui.layer.msg(res.msg)
                if(res.code===1){
                    //成功
                    typeof func ==='function' && setTimeout(function(){func(res,ele_obj);},wiat_time)
                }else if(res.code===-2 && res.hasOwnProperty('data') && res.data.hasOwnProperty('url')){
                    //绑定手机号
                    if ( res.data.url.length > 0 ) {
                        setTimeout(function(){window.location.href = res.data.url;},wiat_time)
                    }
                } else {
                    //失败

                }
            }else{
                typeof func ==='function' && setTimeout(function(){func(res,ele_obj);},wiat_time)
            }
        },
        error:function(res){
            // console.log(res)
            console.log('error')
        },
        complete:function(res,ts){
            layui.layer.close(layer_load)
            // console.log(res)
            // console.log(ts)
            console.log('complete')
        }
    };
    ajax_obj = Object.assign({},ajax_obj,ajax_add_obj)
    $.ajax(ajax_obj)
}

//获取上一个页面
function redirectMode(){
    //获取当前域名  window.location.host
    var referrer = '';
    if(document.referrer.length>0 && document.referrer.indexOf(window.location.host)>-1){
        //说明在当前域名下操作
        referrer  = document.referrer;
    }

    return referrer
}

//将base64转换为文件
function dataURLtoFile(dataurl, filename) {
    var arr = dataurl.split(','), mime = arr[0].match(/:(.*?);/)[1],
        bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
    while(n--){
        u8arr[n] = bstr.charCodeAt(n);
    }
    return new File([u8arr], filename, {type:mime});
}


