
//数组包含方法
Array.prototype.contains = function ( needle ) {
    for (i in this) {
        if (this[i] == needle) return true;
    }
    return false;
}



function setPayPassword(){
    var defMobile = $("#defMobile").val();
    if(defMobile == null || defMobile == ''){
        window.location.href=window.member_dynUrl+'/usersafe/checkPwdIndex?level=0&opt=bind&type=0';
    }else{
        window.location.href=window.member_dynUrl+'/usersafe/checkPwdIndex?level=2&opt=pay&type=1';
    }
    return;
}

/**
 * 刷新结算页面金额信息
 */
function  refreshPriceInfo(){
    var totalPriceGoods = $("#totalPriceGoods").val();//商品金额
    var couponDiscountPrice =  $('#couponDiscountPrice').val();//优惠券金额
    var discountPrice =  $('#discountPriceGoods').val();//活动金额
    var totalFreightPrice = $('#totalFreightPrice').val();//总配送费
    var useAccountFlag =  $("#useAccountFlag").val();//是否使用余额
    var useCashBackFlag =  $("#useCashBackFlag").val();//是否使用返现
    var backPrice =  $("#backPrice").val();//返现
    var fundPrice =  $("#fundPrice").val();//余额
    //计算总额  红包>返现>余额(余额可抵算运费)
    var showPayPrice = parseFloat(totalPriceGoods).toFixed(2);
    var showDiscountPrice = 0;
    var showCouponPrice = 0;
    var showFundPrice = 0;
    var showBackPrice = 0;
    var showFreightPrice = 0;
    getDiscountDetail();
    //活动
    if(discountPrice !='' && discountPrice>0){
        $("#discountPriceDiv").css("display","block");
        showDiscountPrice = parseFloat(discountPrice).toFixed(2);
        showPayPrice = showPayPrice - showDiscountPrice;
    }else{
        $("#discountPriceDiv").css("display","none");
    }
    if(showPayPrice<0){
        showPayPrice = parseFloat(0).toFixed(2);
    }
    $("#discountPrice").text("￥"+(showDiscountPrice));
    //红包
    if(couponDiscountPrice !='' && couponDiscountPrice>0){
        $("#couponPriceDiv").css("display","block");
        showCouponPrice = parseFloat(couponDiscountPrice).toFixed(2);
        showPayPrice = showPayPrice - showCouponPrice;
    }else{
        $("#couponPriceDiv").css("display","none");
    }
    if(showPayPrice<0){
        showPayPrice = parseFloat(0).toFixed(2);
    }
    $("#couponPrice").text("￥"+(showCouponPrice));

    //运费
    if(totalFreightPrice !='' && totalFreightPrice>0){
        $("#freightPriceDiv").css("display","block");
        showFreightPrice = parseFloat(totalFreightPrice).toFixed(2);
        showPayPrice = parseFloat(showPayPrice) + parseFloat(showFreightPrice);
    }
    $("#freightPrice").text("￥"+(parseFloat(totalFreightPrice).toFixed(2)));
    //余额
    if(showPayPrice>0 && fundPrice>0){
        $(".orderBalance .showBalance").show();
        $(".orderBalance .not").hide();
    }else{
        $(".orderBalance .showBalance").hide();
        $(".orderBalance .not").show();
    }
    if(useAccountFlag == 'true' && fundPrice>0){
        $("#accountPriceDiv").css("display","block");
        showFundPrice = parseFloat(fundPrice).toFixed(2) - showPayPrice;
        if(showFundPrice>0){//余额>应付金额
            showFundPrice = showPayPrice;
            showPayPrice = 0;
        }else{//余额<应付金额
            showFundPrice = fundPrice;
            showPayPrice = showPayPrice - showFundPrice;
        }
        if(showFundPrice == 0){
            $("#accountPriceDiv").css("display","none");
        }
    }else{
        $("#accountPriceDiv").css("display","none");
    }
    if(showPayPrice<0){
        showPayPrice = parseFloat(0).toFixed(2);
    }
    $("#useAccount").text("￥"+(parseFloat(showFundPrice).toFixed(2)));
    $("#payPrice").text("￥"+(parseFloat(showPayPrice).toFixed(2)));
}

function textCounter(field, maxlimit) { //留言剩余字数统计
    if (field.value.length > maxlimit)
        field.value = field.value.substring(0, maxlimit);
    else
        document.getElementById('remLen').innerHTML = maxlimit - field.value.length;
}
/**
 * 继续购买
 */
function goOnBuy(type){
    if(type=='giftNoStock'){
        $('#giftNoStockAlertCheck').val('0');
        $('.popCatBoxbj,#giftNoStockDiv').hide();
        check_order();
    }
    if(type=='stopTypeDelivery'){
        $('#stopTypeDeliveryAlertCheck').val('0');
        $('.popCatBoxbj,#stopTypeDeliveryDiv').hide();
        check_order();
    }
    if(type=='thinkAgain'){
        $('.popCatBoxbj,#stopTypeWeatherDiv,#unableProductsDiv,#giftNoStockDiv,#stopTypeDeliveryDiv').hide();
    }
    $(".Submits").css('background','#fc5a5a').css("pointer-events",'');
    $(".Submits").text("提交订单");
}

/**
 * 验证订单
 */
function check_order(){
    var orderFormSubmitFlag = $('#orderFormSubmitFlag').val();
    //防止重复提交
    if(orderFormSubmitFlag == "false"){
        return;
    }
    $(".Submits").css("background","#dcdde1").css("pointer-events","none");
    $(".Submits").text("提交中");
    var address_id = $(".addConsignee").attr("addressId");
    if(null ==address_id || typeof(address_id)=='undefined'){
        alert('收货地址不能为空');
        return;
    }

    //无法下单
    var unableProductsCount = $('#unableProductsCount').val();
    if(unableProductsCount >0){
        $('.popCatBoxbj,#unableProductsDiv').show();
        return;
    }
    //设置订单提交状态
    $('#orderFormSubmitFlag').val("false");
    var payWayType = $('#payWayType').val();
    var form = {};
    form.payPassWord= $('#checkPayPassword').val();
    form.addressId= $('.addConsignee ').attr('addressId');
    form.paymentChannel= payWayType =='1'?'138':'100';
    form.paymentChannelName= '手机支付宝';
    form.useAccoutMoney= useAccountFlag == 'true'?1:0;
    form.useCashback= useCashBackFlag == 'true'?1:0;
    form.useCoupon= $('#couponIds').val();
    form.invoiceType= $('#defInvoiceType').attr('updateVal');
    form.invoiceKinds= $('#defInvoiceKind').attr('updateVal');
    if($('#defInvoiceType').attr('updateVal') == '2'){
        form.taxpayerId= $('#defInvoiceTaxpayerId').attr('updateVal');
        form.invoiceTitle= $('#defInvoiceTop').attr('updateVal');
    }else{
        form.invoiceTitle = '个人';
    }
    form.invoiceContent= $('#defInvoiceContent').attr('updateVal');
    form.orderPS= $('#orderPS').val();
    form.giftOrder= $('#orderGift').val();
    if($('#defInvoiceKind').attr('updateVal')=='1'){
        form.invoiceMobile= getRealPhone($('#defInvoiceMobile').attr('updateVal'));
    }else if($('#defInvoiceKind').attr('updateVal')=='0'){
        form.invoiceType= null;
        form.invoiceContent= null;
    }
    var stockoutGifts = $("#stockoutGifts").val();
    var noStockGifts = $("#noStockGifts").val();
    if(stockoutGifts!=null && stockoutGifts!=''){
        if(noStockGifts!=null && noStockGifts!=''){
            stockoutGifts = stockoutGifts + "," + noStockGifts;
        }
    }else{
        stockoutGifts = noStockGifts;
    }
    form.stockoutGifts= stockoutGifts;
    form.isBuyImmediately= $("#isBuyImmediately").val();
    $.ajax({
        type : "POST",
        url : '/m_v1/order/beforeSubmit.htm',
        contentType:'application/x-www-form-urlencoded',
        data :form,
        dataType : "json",
        success : function(data) {
            if(data != null && data.message != null){
                if(data.message == "SUCCESS"){
                    window.location.href = '/m_v1/order/submit.htm?orderSn=' + data.orderSn;
                    return;
                }else{
                    if(data.message == 'loginout'){
                        window.location.href = window.loginUrl+"/user/login?retUrl=" + encodeURI(window.checkoutUrl + "/m_v1/trade/check_out?stockoutGifts=" + $("#stockoutGifts").val()+ '&isBuyImmediately=' + $("#isBuyImmediately").val() + '&goods_number=' + $("#goods_number").val()  + '&goods_id=' + $("#goods_id").val());
                        return;
                    }else{
                        popEffect(data.message);
                        recoverSubmitOrder();
                    }
                }
            }
            else{
                popEffect("系统异常请稍后重试!");
                recoverSubmitOrder();
            }
        },
            error:function(e){
                popEffect("操作失败,请稍后重试!!!");
                recoverSubmitOrder();
            }
    });
}
/**
 * 获取用户地址列表
 */
function getUserAddress(id,isLoadCoupons,isLoadFreight,isWaitCoupon){
    var info = null;
    jQuery.ajax({
        type:'post',
        url:'/m_v1/order/getUserAddress.htm?addressId='+id,
        dataType:'json',
        cache:false,
        async:false,
        success:function(data){
            if(data!=null && data.message == 'loginout'){
                window.location.href = window.loginUrl+"/user/login?retUrl=" + encodeURI(window.checkoutUrl + "/m_v1/trade/check_out?stockoutGifts=" + $("#stockoutGifts").val()+ '&isBuyImmediately=' + $("#isBuyImmediately").val() + '&goods_number=' + $("#goods_number").val()  + '&goods_id=' + $("#goods_id").val());
                return;
            }else if(data != null && data.addressInfoList != null && data.addressInfoList.length > 0){
                info = data.defNewAddressBo;
                $("#showAddressList").show().attr("eniv",info.supportEinv);
                $("#showAddAddress").hide();
                $(".addConsignee").attr("addressId",info.addressId);
                $(".addConsignee").attr("addressRegionId",info.rgnRegionID);
                $("#choiceAddressConsignee").text('收货人:'+info.consignee);
                $("#choiceAddressMobile").text(info.mobile);
                $("#choiceAddress").text(info.addressName + info.addressMore);
                $("#showAddressInfo").text('送至：'+info.addressName + info.addressMore);
                if(info.defaultAddress){
                    $("#showAddressList .oAddress .def").show().text('默认');
                }else{
                    $("#showAddressList .oAddress .def").hide();
                }
                $('.AddressUl').html("");
                $('.AddressUl').append(getAddressHtml(data.addressInfoList,id));
                var hascheck = false;
                $(".AddressUl .addrItem").each(function(index,item){
                    if($(item).hasClass('selected')){
                        hascheck = true;
                    }
                })
                if(!hascheck){
                    $('.addrItem').removeClass('selected').find('.addrRadio i').removeClass('oIcon');
                    $(".AddressUl .addrItem ").eq(0).addClass('selected').find('.addrRadio i').addClass('oIcon');
                }
            }
            else{
                $("#showAddressList").hide();
                $("#showAddAddress").show();
            }
            checkAddressForSubmit(info == null?'':info.rgnRegionID);
            loadGoods(info == null?500:info.rgnRegionID,isLoadCoupons,isLoadFreight,isWaitCoupon);
            isShowCashExt();
            if($("#showAddressList").attr('eniv')=="true"){
                //默认地址支持电子发票
                choseInvoiceKind(0);
            }else{//默认地址不支持电子发票
                choseInvoiceKind(1);
            }
            showInvoiceKind($("#defInvoiceKind").attr('updateVal'));
            if($("#defInvoiceType").attr('updateVal') == '1' || $("#defInvoiceType").attr('updateVal') == '0'){
                $('.orderInvNumTit,.orderInvNum,#invoicePro').hide();
                $("#defInvoiceType").attr('updateVal',1).val(1);
            }else{
                $('.orderInvNumTit').show();
                var taxpayerId = $("#defInvoiceTaxpayerId").attr('updateVal');
                $('.orderInvNum').show().text(taxpayerId==''?"无":taxpayerId);
                if(taxpayerId==''){
                    $("#invoicePro").show();
                }else{
                    $("#invoicePro").hide();
                }
            }
            if($("#defInvoiceContent").attr('updateVal') == null || $("#defInvoiceContent").attr('updateVal') == ""){
                $("#defInvoiceContent").attr('updateVal','酒水').val("酒水");
            }
            $('.orderInvoice').text(getInvoiceText($("#defInvoiceKind").attr('updateVal'),$("#defInvoiceType").attr('updateVal'),$("#defInvoiceContent").attr('updateVal'),$("#defInvoiceTop").attr('updateVal')));
            showPayWay();
        },
        error:function(){
            alert("获取用户地址失败，请稍后重试!!!");
        }
    });
}

/**
 * 封装地址列表信息
 * @param data
 * @param id
 * @returns {string}
 */
function  getAddressHtml(data,id) {
    var str = "";
    $.each(data, function (index, info) {
        var ischeck = false;
        str = str + "<li addrid='" + info.addressId +"'addRegionId='"+ info.rgnRegionID + "'eniv='"+ info.supportEinv + "' class='addrItem ";
        if(id == info.addressId){
            str = str + "selected'";
            ischeck = true;
        }else if(id == 0 && info.defaultAddress){
            str = str + "selected' defAddress ='true' ";
            ischeck = true;
        }else{
            str = str + "'";
        }
        str = str + "><div class=\"addrCon\"><div class=\"addrRadio\"><i ";
        if(ischeck) {
            str = str + "class=\"oIcon\"";
        }
        str = str + "></i></div><div class=\"addrInfo\">";
        str = str + "<div class=\"addrConsignee clearfix\">";
        str = str + "<span>" + info.consignee + "</span>";
        str = str + "<em>" + info.mobile + "</em></div>";
        str = str + "<div class=\"oAddress\">";
        if (info.defaultAddress) {
            str = str + "<em class=\"def\">默认</em>";
        }
        str = str + info.addressName + info.addressMore + "</div></div></div><div class=\"addrOpe\">";
        str = str + "<a class='delBtn' href='javascript:void(0);'";
        if(ischeck){//选中地址不能删除
            str = str + "style='display: none'";
        }
        str = str + "><i class=\"oIcon\"></i><span>删除</span></a>";
        str = str +  "<a class=\"editBtn\" href=\"javascript:void(0);\"><i class=\"oIcon\"></i><span>编辑</span></a></div></li>";
    });
    return str;
}
/**
 * 加载商品信息<br>
 * isloadGoods 是否加载红包列表
 * @param isWaitCoupond 是否等候红包加载完成在加载优惠卷
 * @param isLoadFreight 是否加载运费
 */
function loadGoods(regionId,isLoadCoupons,isLoadFreight,isWaitCoupond){
    var stockoutGifts = $("#stockoutGifts").val();
    $.ajax({
        url:'/m_v1/trade/loadGoods?regionId='+regionId + '&isBuyImmediately=' + $("#isBuyImmediately").val() + '&goods_number=' + $("#goods_number").val()  + '&goods_id=' + $("#goods_id").val() ,
        data:{stockoutGifts:stockoutGifts},
        dataType:'html',
        async:false,
        success:function(data){
            $("#productDiv").empty();
            $("#productDiv").html(data);
            $("#totalPrice").text($("#totalPriceGoods").val());
            getDiscountDetail();
            getNoStockGiftIds();
            setProductList();
            /*if(isLoadCoupons){//是否加载红包
                loadCoupons(regionId,isLoadFreight);
            }else if(isLoadFreight && !isWaitCoupond){//加载运费不加载红包
                loadFreight(regionId);
            }*/
            loadCoupons(regionId,true);
        }
    });
}

/**
 * 加载优惠卷列表<br>
 */
function loadCoupons(regionId,isLoadFreight){
    $.ajax({
        url:'/m_v1/order_inner/favourable.htm?regionId='+regionId+ '&isBuyImmediately=' + $("#isBuyImmediately").val(),
        dataType:'html',
        async:false,
        success:function(data){
            $("#pageCoupon").html(data);
            checkCouponInfo();
            if(isLoadFreight){
                loadFreight(regionId);
            }
        }
    });
}

/**
 * 加载运费<br>
 */
function loadFreight(regionId){
    //选择的红包id
    var checkCouponIds = $("#couponIds").val();
    //用户的返现金额
    var useBackPrice = $("#backPrice").val();
    if($('#useCashBackFlag').val() != 'true'){
        useBackPrice = 0;
    }
    $.ajax({
        url:'/m_v1/order_inner/getExpensesPrice.htm?regionId='+regionId+'&checkCouponIds='+checkCouponIds+'&cashbackPrice='+useBackPrice+ '&isBuyImmediately=' + $("#isBuyImmediately").val(),
        dataType:'json',
        success:function(data){
            if(data.code == 0){//成功
                var freightObj = data.data;
                //运费总金额
                var freightPrice = freightObj.expensesPrice;
                $("#freightPrice").text("￥"+freightPrice.toFixed(2));
                $("#totalFreightPrice").val(freightPrice);
                //运费详情
                $('.yfWrap').empty();
                if(freightObj.shopItems.length>0){
                    $('.yfWrap').append(getFreightHtml(freightObj.shopItems));
                }
                if(freightPrice >0){
                    $('.yfIcon').show();
                }else{
                    $('.yfIcon').hide();
                }
                refreshPriceInfo();
            }else{
                alert("加载运费失败："+data.msg);
            }
        }
    });
}
/**
 * 设置选择的红包数量和优惠总金额信息<br>
 */
function checkCouponInfo(){
    var avaiableCount = $("#avaiableCount").val();
    var couponInfo="";
    var selecedCount = $("#selecedCount").val();
    /* 总酒票为0 */
    if(avaiableCount == 0){
        couponInfo="<p><span>无酒票可用</span></p>";
        var totalCount = $("#totalCount").val();
        if(totalCount == 0){
            $("#showPageCoupon").addClass("not");
        }
    }else{
        if(selecedCount == undefined || typeof (selecedCount) == 'undefined' ||selecedCount == 0){
            couponInfo = "<p><span>不使用酒票</span></p>";
        }else{
            var discountPrice = $("#couponDiscountPrice").val();
            couponInfo = "<p class='orderCouText'><em><i class='oIcon2'></i>使用"+selecedCount+"张</em><span>-<font>"+discountPrice+"</font>元</span></p>";
        }
    }
    //如果没选择酒票，酒票金额不显示
    if(selecedCount == undefined || typeof (selecedCount) == 'undefined' || selecedCount==0){
        $("#couponPriceDiv").css("display","none");
    }else{
        $("#couponPriceDiv").css("display","block");
    }
    $("#showPageCoupon .itemInfo").html(couponInfo);
    $("#couponPrice").text("￥"+(parseFloat($("#couponDiscountPrice").val()).toFixed(2)));
}

function changeRegion(parentId,type){
    if(type == 1 ||type == 2) {
        $.ajax({
            type: "POST",
            url: "/m_v1/order/getNextRegionList.htm?parentId=" + parentId,
            data: null,
            dataType: "json",
            success: function (data) {
                if (data != null) {
                    var item = "";
                    var changeType = 0;
                    if (type == 1) {
                        item = $('.cityItem_2');
                        changeType = 2;
                    } else if (type == 2) {
                        item = $('.cityItem_3');
                        changeType = 3;
                    }
                    item.empty();
                    $.each(data.nextRegionInfo, function (i, value) {
                        item.append("<li onclick='changeRegion(" + value.iD + "," + changeType + ")'><span>" + value.name + "</span><i class='oIcon'</i></li>");
                    });
                }
            },
            error: function () {
                alert('调用选择省现错误。。。');
            }
        })
    }else{
        $("#districtId").val(parentId);
    }
}

(function($){
    $.fn.Switch = function(option,closeName){
        return this.each(function(){
            var $this= $(this);
            $this.bind('click',function(){
                if(!$this.hasClass(closeName)){
                    $this.addClass(closeName);
                }else{
                    $this.removeClass(closeName);
                }
                return false;
            });
        })
    }
    $.fn.accounSwitch = function(option,closeName){
        return this.each(function(){
            var $this= $(this);
            $this.bind('click',function(){
                if(!$this.hasClass(closeName)){
                    //判断是否设置支付密码
                    if($('#isPayPassword').val() == 'false'){
                        $('#accoun-switch-btn').addClass('s-close');
                        //弹出设置密码
                        showDialog.showPop("您还没设置支付密码，为了账户安全,请到我的账户-个人信息-账号安全下设置支付密码","以后再说","去设置",setPayPassword,null);
                        return;
                    }
                    $('#useAccountFlag').val('true');
                }else{
                    $('#useAccountFlag').val('false');
                }
                refreshPriceInfo();
            });
        })
    }
    $.fn.cashBackSwitch = function(option,closeName){
        return this.each(function(){
            var $this= $(this);
            $this.bind('click',function(){
                if(!$this.hasClass(closeName)){
                    //判断是否设置支付密码
                    if($('#isPayPassword').val() == 'false'){
                        $('#cashBack-switch-btn').addClass('s-close');
                        //弹出设置密码
                        showDialog.showPop("您还没设置支付密码，为了账户安全,请到我的账户-个人信息-账号安全下设置支付密码","以后再说","去设置",setPayPassword,null);
                        return;
                    }
                    $('#useCashBackFlag').val('true');
                }else{
                    $('#useCashBackFlag').val('false');
                }
                loadFreight($(".addConsignee").attr("addressRegionId"));
            });
        })
    }
    $.fn.defaultAddressSwitch = function(option,closeName){
        return this.each(function(){
            var $this= $(this);
            $this.bind('click',function(){
                if(!$this.hasClass(closeName)){
                    $('#defaultAddress').val('open');
                    $this.removeClass(closeName);
                }else{
                    $('#defaultAddress').val('close');
                    $this.addClass(closeName);
                }
            });
        })
    }

})(jQuery)
$(function(){
    //支付
    $('.payList .select li i').unbind('click').bind("click",function(){
        $(this).addClass('on');
        $(this).parent().siblings().find("i").removeClass('on');
    });
    //发票
    $('.bill .select li i').bind("click",function(){
        $(this).addClass('on');
        $(this).parent().siblings().find("i").removeClass('on');
    });
    //红包
    $('.bonusList .ticket i').bind("click", function() {
        $(this).addClass('on');
        $(this).parent().siblings().find("i").removeClass('on');
    });


    $('.submitMask .write span').bind('click',function(){
        $(this).prev().focus();
        $(this).hide();
    });
    //选择支付方式
    $('#onLine,#notOnLine').bind('click',function(){
        $(this).parent().find($('.iiItems')).removeClass('selected').find('i').removeClass('oIcon');
        $(this).addClass('selected').find('i').addClass('oIcon');
        if(this.id == 'onLine'){
            $("#payWayType").val(1);
        }else{
            if($('.orderGift label').hasClass('selected')){
                changeToOnLionAndNoInvoice();
                $(".jxProMask1,.giftProPop").show();
                $("#giftText").text("礼品订单不支持货到付款！");
            }else{
                $("#payWayType").val(0);
            }
        }
        refreshPriceInfo();
    });

    //余额等 使用开关
    $('.switch-btn').Switch(this,'s-close');
    //余额开关
    $('#accoun-switch-btn').accounSwitch(this,'s-close');
    //返现开关
    $('#cashBack-switch-btn').cashBackSwitch(this,'s-close');
    //默认地址开关
    $('#defaultAddress-switch-btn').defaultAddressSwitch(this,'s-close');

	/***
	返回按钮及页面title
	页面id标识：填写订单pageOrder；收货地址pageAddressList； 编辑收货地址pageEditAddress； 新增收货地址pageAddAddress；优惠券pageCoupon；发票信息pageInvoice
	***/
	var addAddrFromPayPage = false;
	var pageurl = location.href;
	if (location.hash != '') {
		location.hash = '';
	}
	$("#pageback").off('click').on('click',function(){
		var tag = location.hash.replace(/#/g, '');
		if (tag=='') {
			tag = 'pageOrder';
		}
		if (tag == 'pagePay') {
			//填写订单页返回按钮功能，若是history.back()，则无需此判断
		}
        if(tag == 'pageOrder'){
		    window.location.href = encodeURI($('#backUrl').val());
            return;
        }
        if(tag == 'pageInvoice'){
            recoverInvoiceInfo();
            history.back();
        }
        else {
			history.back();
		}
	});
	$(window).on('hashchange',function(e) {
		try {
			var tag = location.hash.replace(/#/g, ''),
			_title = '填写订单';
			if (tag=='') {
				tag = 'pageOrder';
			}
			$('.mainBody').children('div:not(.cityPopBox,.jxProMask,.jxProPop)').hide();
			if(tag == 'pageEditAddress'){
				$('#pageAddAddress').show()
			}else{
				$('#' + tag).show();
			}
            $('#instructions').hide();
			if (tag == 'pageOrder') {
				_title = '填写订单';
			} else {
				if (tag == 'pageAddressList') {
					_title = '收货地址';
				} else if (tag == 'pageEditAddress') {
					_title = '编辑收货地址';
				} else if (tag == 'pageAddAddress') {
					_title = '新增收货地址';
				} else if (tag == 'pageCoupon') {
					_title = '酒票';
                    $('#instructions').show();
				} else if (tag == 'pageInvoice') {
					_title = '发票信息';
				} else {
					_title = '填写订单';
				}
				document.body.scrollTop = 0;
				window.scrollTo(0, 0);
			}
			if(tag == 'pageOrder'){
				document.title = '赠酒网-' + _title;
			}else{
				document.title = _title;
			}
			$('#page_title').html(_title);
		} catch(e) {

		}
	});
	//订单页->地址列表
	$("#showAddressList").on('click',function(){
		goPage('pageAddressList');
	});
	//订单页->添加地址
	$("#showAddAddress").on('click',function(){
		getAddressDetail(0);
		addAddrFromPayPage = true;
	});
	//订单页->优惠券
	$("#showPageCoupon").on('click',function(){
	    //初始化选择的红包
        var couponIds = $("#couponIds").val();
        var couponIdArray = new Array();
        if(couponIds != undefined && typeof (couponIds) != 'undefined'){
            var couponIdList = couponIds.split(",");
            for (var i = 0;  i < couponIdList.length;i++){
                couponIdArray.push(couponIdList[i]);
            }
        }
        $(".couponList .couponItem ").each(function(index,item){
            var couponid = $(item).attr("couponid");
            if(couponIdArray.contains(couponid)){
                $(item).addClass("selected");
            }else{
                $(item).removeClass("selected");
            }
        })
		goPage('pageCoupon');
	});
	//订单页->发票
	$("#showPageInvoice").on('click',function(){
        if($('.orderGift label').hasClass('selected')){
            changeToOnLionAndNoInvoice();
            $(".jxProMask1,.giftProPop").show();
            $("#giftText").text("礼品订单不支持开具发票！");
        }else{
            goPage('pageInvoice');
        }
	});

/***地址列表start***/

    //切换地址
	$('#pageAddressList').delegate('.addrCon','click',function(){
		$('.addrItem').removeClass('selected').find('.addrRadio i').removeClass('oIcon');
        $('.addrItem').find('.delBtn').show();
		$(this).parent().addClass('selected').find('.addrRadio i').addClass('oIcon');
        $(this).parent().find(".delBtn").hide();
        if($(this).parent().attr('defaddress')!=undefined && $(this).parent().attr('defaddress')=='true'){
            $("#showAddressList .oAddress em").show().text('默认');
        }else{
            $("#showAddressList .oAddress em").hide();
        }
        $('.addConsignee').attr("addressId",$(this).parent().attr('addrid'));
        $('.addConsignee').attr("addressRegionId",$(this).parent().attr('addregionid'));
        $("#choiceAddress").text($(this).find('.oAddress').text().replace("默认",""));
        $("#showAddressInfo").text('送至：'+$(this).find('.oAddress').text().replace("默认",""));
        $("#choiceAddressConsignee").text('收货人:'+$(this).find('.addrInfo .addrConsignee span').text());
        $("#choiceAddressMobile").text($(this).find('.addrInfo .addrConsignee em').text());
        loadGoods($(this).parent().attr('addRegionId'),false,true,false);
        isShowCashExt();
        showInvoiceKind($("#defInvoiceKind").attr("updateVal"));
        updateInvoiceInfo($("#defInvoiceKind").attr("updateVal"),$("#defInvoiceContent").attr("updateVal"),$("#defInvoiceType").attr("updateVal"),$("#defInvoiceTop").attr("updateVal"),$("#defInvoiceTaxpayerId").attr("updateVal"),$("#defInvoiceMobile").attr("updateVal"));
        recoverInvoiceInfo();
        showPayWay();
        if($('.orderGift label').hasClass('selected')) {
            changeToOnLionAndNoInvoice();
        }
		goPage('pageOrder'); //地址列表->订单页
	});

    $('#pageAddressList').on('click','.editBtn',function(){
        var addressId = $(this).parents('li').attr('addrid');
        getAddressDetail(addressId);
    });

/***地址列表end***/
/***优惠券start***/
//可用不可用切换
    $("#pageCoupon").on("click",".couponTab span",function(){
        var index = $('.couponTab span').index($(this));
        $(this).addClass("on").siblings().removeClass("on");
        $('.couponList').eq(index).show().siblings('.couponList').hide();
    });
    //选中红包事件
    $("#pageCoupon").on("click",".couponItem",function(){
        var couponItem = $(this);
        if(couponItem.hasClass('selected')){//取消
            couponItem.removeClass('selected');
        }else{//选中
            //使用范围 0:全场（跨店铺）,1:自营,2:店铺
            var couponType = couponItem.attr("couponScopeType");
            //已选中的红包id
            //var couponid = couponItem.attr("couponid");
            //遍历选中红包
            $("#avaiableList li.selected").each(function(index,item){
                if(couponType == 0){//全场
                    //$(item).removeClass('selected');
                }else{//单店
                    var itemCouponScopeType = $(item).attr("couponScopeType");
                    if(itemCouponScopeType == 0){
                        $(item).removeClass('selected');
                    }else{
                        //店铺ID
                        var shopid = couponItem.attr("shopid");
                        var itemShopid = $(item).attr("shopid");
                        if(itemShopid == shopid){
                            $(item).removeClass('selected');
                        }
                    }
                }
            })
            couponItem.addClass('selected');
        }
    });
    //使用说明
    $("#instructions").on('click',function() {
        $.ajax({
            type: "GET",
            url: '/m_v1/order_inner/couponInstructions.htm',
            data: null,
            dataType: "json",
            success: function (data) {
                var context = "";
                var size = data.data.titels.length;
                for (var i = 0; i < size; i++) {
                    context += "<p>" + data.data.titels[i].toString() + "</p>";
                }
                $(".popCouponBox .tex").empty();
                $(".popCouponBox .tex").append(context);
                $(".popCouponBoxbj,.popCouponBox").show();
                $("body").css({"overflow": "hidden"}
                )
            },
            error: function () {
                popEffect('请求失败稍后重试!');
                return;
            }
        });
    });

    //点击确定事件
    $("#pageCoupon").on("click",".bottomBtn .Btn",function () {
        var couponIds='';
        var totalPrice = 0;
        var count = 0;
        //遍历选中红包
        $("#avaiableList li.selected").each(function(index,item){
            count++;
            //选中的红包id
            var couponid = $(item).attr("couponid");
            couponIds += couponid+",";
            var price = $(item).attr("price");
            totalPrice = totalPrice + parseFloat(price);
        });
        $("#selecedCount").val(count);
        $("#couponDiscountPrice").val(totalPrice);
        if(couponIds.charAt(couponIds.length - 1) == ','){
            couponIds = couponIds.substring(0,couponIds.length - 1);
        }
        $("#couponIds").val(couponIds);
        checkCouponInfo();
        refreshPriceInfo();
        loadFreight($(".addConsignee").attr("addressRegionId"));
        goPage('');
    })

	$("#popCouponClose,.popCouponBoxbj").on('click',function(){
		pcouClose();
	});

	function pcouClose(){
		$(".popCouponBoxbj,.popCouponBox").hide();
		$("body").css({"overflow":"auto"});
	}
/***优惠券end***/
/***发票start***/
	var invName = 0;
	var $invTitle = $('#invTitle');
	$('.invoiceTab li').bind('click',function(){
		invName=$(this).attr('name');
		$(this).addClass('on').siblings().removeClass('on');
        showInvoiceKind(invName);
	});
	//发票内容
	$('.invoiceInfo label').bind('click',function(){
		var $this=$(this);
		selectFn($this);
	});
	//发票抬头
	$(document).bind('click',function(event){
		$('.invTitleList').hide();
		event.stopPropagation();
	});
	$invTitle.bind('focus',function(){
		$(this).siblings('.invTitleList').show();
	});
	$('.invTitleList li').bind('click',function(){
		var tex = $(this).text();
		$(this).parents('.invTitleList').hide().siblings('input').val(tex);
        $("#ideNum").val($(this).attr("TaxpayerId"));
	});
	$(".invoiceTitle").bind('click',function(event){
		event.stopPropagation();
	});
	$('.titDel').bind('click',function(){
		$(this).siblings('input').val('').focus();
	});
	$(".invType label").bind("click",function(){
		var $this=$(this);
		var invType = $this.index()
		selectFn($this);
		invType==0?$(".companyName,.identityNum,.payPro").hide():$(".companyName,.identityNum,.payPro").show();
	});
	//提交
	$('#invoiceSubmit').bind('click',function(){
        var kind = $(".invoiceTab .on").attr("name");
        var content = $(".invoiceInfo").find("label.selected").text();
        var type = $(".invType").find("label.selected").attr('value');
        var top = $.trim($("#invTitle").val());
        var taxpayerId = $.trim($("#ideNum").val());
        var mobile = $("#phoneNum").val();
		if(kind != '0'){
			if(type == 2){ //单位
				if(top == null||top == ''){
					popEffect('请输入单位名称！');
					return;
				}else{
					//验证发票title格式
					var regex = /^[\u4e00-\u9fa5_A-Za-z0-9-]*$/;
					if(!regex.test(top)){
						popEffect('输入单位名称含有非法字符,请重新输入！');
						return;
					}
				}
				if(taxpayerId == null||taxpayerId == ''){
					/*popEffect('请输入纳税人识别号！');
					return;*/
				}else{
					var pattern=/^[0-9a-zA-Z]*$/;
					if(taxpayerId!=''&&!pattern.test(taxpayerId))
					{
						popEffect('请输入正确的纳税人识别号！');
						return;
					}
				}
            }
            if(kind =='1'){
                if(mobile == null||mobile == ''){
                    popEffect('请输入收票人手机号！');
                    return;
                }else{
                    var patternPho=/^(1)[0-9]{10}$/;
                    if(mobile!=''&&!isHiddenPhone(mobile)&&!patternPho.test(mobile))
                    {
                        popEffect('请输入11位正确手机号！');
                        return;
                    }
                }
            }
		}
        updateInvoiceInfo(kind,content,type,top,taxpayerId,mobile);
        goPage("");
	});

    $('#phoneNum').bind('focus',function(){
        $("#phoneNum").val("");
    });

/***发票end***/
})

//********************* 新版本js ************//

function goPage(tag) { //页面与页面之间的跳转（除返回按钮外），参数tag为要跳转到的页面id
    if (tag == 'pageOrder') {
        if (location.hash.indexOf('pageAddAddress') > 0 && addAddrFromPayPage) {
            history.go( - 2);
            addAddrFromPayPage = false;
        } else {
            history.back();
        }
    } else {
        location.hash = tag;
    }
};

function selectFn(obj){
    obj.siblings().removeClass('selected').find('i').removeClass('oIcon');
    obj.addClass('selected').find('i').addClass('oIcon');
}

function saveAddress(){
    if($('#addrSubmit').attr('class').indexOf('btn-not')>0){
        return;
    }
    var consignee = $('#uersName').val();
    var mobile = $('#mobilePhone').val();
    var phone = $('#telPhone').val();
    var addressMore = $('#address_area').val();
    var addressId = $('#addrSubmit').attr("addressId");
    var rgnRegionID = $('#districtId').val();
    var addressName = $('#address_city').text();
    var isMain ;
    if($('#defaultAddress-switch-btn').hasClass("s-close")){
        isMain = 0;
    }else{
        isMain = 1;
    }
    if(checkName() && checkPhone() && checkTel() && checkProvince() && checkArea()){
        //验证地址数量
        $.ajax({
            type : "POST",
            url : "/m_v1/order/updateUserAddress.htm",
            data:{addressId:addressId,consignee:consignee,rgnRegionID:rgnRegionID,addressName:addressName,addressMore:addressMore,zipCode:"",mobile:mobile,phone:phone,isMain:isMain},
            async: false,
            dataType : "json",
            success : function(data) {
                if (data != null) {
                    if(data.message != null && data.message != 'success'){
                        if(data.message == 'loginout'){
                            window.location.href = window.loginUrl+"/user/login?retUrl=" + encodeURI(window.checkoutUrl + "/m_v1/trade/check_out?stockoutGifts=" + $("#stockoutGifts").val()+ '&isBuyImmediately=' + $("#isBuyImmediately").val() + '&goods_number=' + $("#goods_number").val()  + '&goods_id=' + $("#goods_id").val());
                            return;
                        }
                        popEffect(data.message);
                        return;
                    }else{
                        getUserAddress(data.addressId,false,true,false);
                        goPage(''); //地址列表->添加地址
                    }
                }
            },
            error : function() {
                alert('服务器繁忙，请稍后重试');
            }
        });
    }
}


/**
 * 加载地址详情信息
 * @param addressId
 */
function  getAddressDetail(addressId){
    addressId = addressId == null?0:addressId;
    jQuery.ajax({
        type: 'post',
        url: '/m_v1/order/getUserAddressDetail.htm?addressId='+addressId,
        dataType: 'json',
        cache: false,
        async: false,
        success: function (data) {
            if (data != null) {
                if(addressId!=0 && (data.orderDetail != null || data.orderDetail != 'undefined' || data.orderDetail != undefined)){
                    $('#uersName').val(data.orderDetail.consignee);
                    $('#mobilePhone').val(data.orderDetail.mobile.substring(0,3)+"****"+data.orderDetail.mobile.substring(7,11));
                    $('#addressMobile').val(data.orderDetail.mobile.substring(0,3)+"****"+data.orderDetail.mobile.substring(7,11));
                    $('#telPhone').val(data.orderDetail.phone);
                    $('#address_area').val(data.orderDetail.addressMore);
                    $('#addrSubmit').attr("addressId",data.orderDetail.iD);
                    $('#districtId').val(data.regionInfo.ppRegionId);
                    if (data.orderDetail.isMain) {
                        $('#defaultAddress-switch-btn').removeClass("s-close");
                    } else {
                        $('#defaultAddress-switch-btn').addClass("s-close");
                    }
                }else{
                    $('#uersName').val("");
                    $('#mobilePhone').val("");
                    $('#addressMobile').val("");
                    $('#telPhone').val("");
                    $('#address_area').val("");
                    $('#addrSubmit').attr("addressId",0);
                    $('#districtId').val("");
                    $('#defaultAddress-switch-btn').addClass("s-close");
                }
                var address_city = getRegionInfoList(data.regionInfo,1,addressId) + getRegionInfoList(data.regionInfo,2,addressId) + getRegionInfoList(data.regionInfo,3,addressId);
                $('#address_city').text(address_city);
                if(addressId==0){
                    $('#addrSubmit').addClass("btn-not");
                    goPage('pageAddAddress'); //地址列表->添加地址
                }else{
                    $('#addrSubmit').removeClass("btn-not");
                    goPage('pageEditAddress'); //地址列表->编辑地址
                }
            }
        }
    });
}

/**
 * 获取三级地址信息
 * @param date 集合
 * @param type 1:省 2 市 3：区
 * @param addressId 地址id
 */
function getRegionInfoList(date,type,addressId){
    var address_city = "";
    var item ;
    var list ;
    var regionId = 0;
    if(type == 1){
        item = $('.cityItem_1');
        list = date.firRegList;
        regionId = date.regionId;
    }else if(type == 2){
        item = $('.cityItem_2');
        list = date.secRegList;
        regionId = date.pRegionId;
    }else if(type == 3){
        item = $('.cityItem_3');
        list = date.thiRegList;
        regionId = date.ppRegionId;
    }
    var isCheck = false;
    if(addressId == null || addressId == 0){
        isCheck =true;
    }
    item.empty();
    $.each(list, function (index, info){
        if(info.iD == 33 || info.iD == 34 || info.iD == 35){
            return true;
        }
        if(!isCheck && info.iD == regionId){
            address_city = address_city + info.name+" ";
            item.append("<li class='on' onclick='changeRegion( " + info.iD + "\,"+ type+")'><span>"+info.name+"</span><i class='oIcon'</i></li>");
        }else{
            item.append("<li onclick='changeRegion(" + info.iD + "\,"+ type+")'><span>"+info.name+"</span><i class='oIcon'</i></li>");
        }
    });
    return address_city;
}

//提交验证
function checkName(){
    var pattern=/^[\u4e00-\u9fa5a-zA-Z]*$/;
    var nameValue = $("#uersName").val();
    if(nameValue=='' || nameValue==null ){
        popEffect('收货人姓名不允许为空！');
        return false;
    }
    if(nameValue!=""&&!pattern.test(nameValue))
    {
        popEffect('收货人姓名只允许输入汉字或英文字母！');
        return false;
    }
    return true;
};

function checkPhone(){
    var pattern=/^(1)[0-9]{10}$/;
    var phoneValue = $("#mobilePhone").val();
    var addressMobile = $("#addressMobile").val();
    if(phoneValue=='' || phoneValue==null ){
        popEffect('手机不允许为空！');
        return false;
    }
    if(phoneValue !=addressMobile && phoneValue!=""&&!pattern.test(phoneValue))
    {
        popEffect('手机格式不正确，请重新输入！');
        return false;
    }
    return true;
};

function checkTel(){
    var pattern=/^[0-9\-]*$/;
    var telValue = $("#telPhone").val();
    if(telValue!=""&&!pattern.test(telValue))
    {
        popEffect('固定电话只能输入数字 “-”');
        return false;
    }
    return true;
};

function checkArea(){
    var pattern=/^[\u4e00-\u9fa5a-zA-Z0-9_!,，！?\？()\（\）{}\【\】\[\]\-。.]*$/;
    var areaValue = $("#address_area").val().replace(/\s/g,'');
    $("#address_area").val(areaValue);
    if(areaValue==null||areaValue==""){
        popEffect('详细地址不能为空！');
        return false;
    }
    if(areaValue!=""&&!pattern.test(areaValue))
    {
        popEffect('详细地址不能包含敏感字符！');
        return false;
    }
    return true;
};

function checkProvince(){
    var districtId = $("#districtId").val();
    var addresscity = $('#address_city').text();
    if(districtId == null||districtId =='' || addresscity == null || addresscity == ""){
        popEffect('所在区域不能为空！');
        return false;
    }
    return true;
};

/**
 * 切换发票类型
 * @param type 1：不支持电子发票  0：支持电子发票
 */
function choseInvoiceKind(type){
    var allCheckedIsJxOnly = $("#allCheckedIsJxOnly").val();//当前地址是否支持电子发票
    var kind = $("#defInvoiceKind").attr("updateVal")=='1'?2:$("#defInvoiceKind").attr("updateVal");
    if(type==1){
        //当前地址不支持电子发票
        $(".invoiceTab .t_1").addClass('on').siblings().removeClass('on');
        $(".invoiceTab .t_2").addClass("disabled off");
        $("#defInvoiceKind").attr("updateVal",kind);
    }else{
        if(allCheckedIsJxOnly == "true" ){
            $(".invoiceTab .t_2").removeClass("disabled off");
            if(!$('.orderGift label').hasClass('selected') && firstGetInv && $(".invoiceTab .on").attr("name") == '2' && $(".invType").find("label.selected").attr('value') == '1'){
                $(".invoiceTab .t_2").addClass('on').siblings().removeClass('on');
                $("#defInvoiceKind").attr("updateVal",1);
            }
        }else{//不支持
            $(".invoiceTab .t_1").addClass('on').siblings().removeClass('on');
            $(".invoiceTab .t_2").removeClass('on').addClass("disabled off");
            $("#defInvoiceKind").attr("updateVal",kind);
        }
    }
    if( $("#defInvoiceKind").val()==0){
        $("#defInvoiceKind").attr("updateVal",0);
        $(".invoiceTab .t_3").addClass('on').siblings().removeClass('on');
    }
    firstGetInv = false;
}

/**
 * 根据发票类型控制显示内容
 * @param type 1:电子 2：纸质 3：发票
 */
function showInvoiceKind(type){
    if(type==2){
        $('.invoiceWrap .invoiceItems').show();
        $('.invPhoneWrap').hide();
    }else if(type==1){
        $('.invoiceWrap .invoiceItems').show();
        $('.payPro').show();
    }else if(type==0){
        $('.invoiceWrap .invoiceItems').hide();
        $('.payPro').hide();
    }
    if(type == 1 || type == 2){
        $(".invType").find("label.selected").attr('value')==1?$(".companyName,.identityNum,.payPro").hide():$(".companyName,.identityNum,.payPro").show();
    }
}

//重置发票信息
function  recoverInvoiceInfo(){
    var kind = $("#defInvoiceKind").attr("updateVal");
    var content = $("#defInvoiceContent").attr("updateVal");
    var type = $("#defInvoiceType").attr("updateVal");
    var top = $("#defInvoiceTop").attr("updateVal");
    var taxpayerId = $("#defInvoiceTaxpayerId").attr("updateVal");
    var mobile =  $("#defInvoiceMobile").attr("updateVal");
    $(".invoiceTab li[name="+kind+"]").addClass('on').siblings().removeClass("on");
    selectFn($(".invoiceInfo label:contains("+content+")"));
    selectFn($(".invType label[value="+type+"]"));
    $("#invTitle").val(top);
    $("#ideNum").val(taxpayerId);
    $("#phoneNum").val(mobile);
    showInvoiceKind(kind);
}

//更新发票信息
function  updateInvoiceInfo(kind,content,type,top,taxpayerId,mobile){
    $("#defInvoiceKind").attr("updateVal",kind);
    $("#defInvoiceContent").attr("updateVal",content);
    $("#defInvoiceType").attr("updateVal",type);//发票类型
    $("#defInvoiceTop").attr("updateVal",top);//单位名称
    $("#defInvoiceTaxpayerId").attr("updateVal",taxpayerId);//纳税人识别号
    $("#defInvoiceMobile").attr("updateVal",mobile);
    if(type == '1'){
        $('.orderInvNumTit,.orderInvNum,#invoicePro').hide();
    }else{
        $('.orderInvNumTit').show();
        $('.orderInvNum').show().text(taxpayerId==''?"无":taxpayerId);
        if(taxpayerId==''){
            $("#invoicePro").show();
        }else{
            $("#invoicePro").hide();
        }
    }
    if(kind == '0'){
        $('.orderInvNumTit,.orderInvNum,#invoicePro').hide();
    }
    $('.orderInvoice').text(getInvoiceText(kind,type,content,top));
}

/**
 * 获取结算发票展示信息
 * @param kind
 * @param type
 * @param top
 * @returns {*}
 */
function  getInvoiceText(kind,type,content,top){
    var text = "";
    if(kind =='1'){
        text +="电子";
    }else if(kind =='2'){
        text +="纸质";
    }else if(kind =='0'){
        return "不需要发票";
    }
    text += "("+content+" - ";
    if(type == '1'){
        text += "个人";
    }else if (type == '2'){
        text += top;
    }
    text += ")";
    return text;
}


/**
 * 展示支付方式
 */
function showPayWay(){
    if($("#supportCashOnDelivery").val()=='false'){//如果不支持货到付款，隐藏货到付款
        $('#notOnLine').hide();
        checkOnLinePay();
    }else {
        if($('.orderGift label').hasClass('selected')){//如果支持货到付款但选择礼品卡，提示，选中在线支付
            checkOnLinePay();
        }else{
            $('#notOnLine').show();
        }
    }
}
//选择在线支付
function  checkOnLinePay(){
    $('.orderPay .iiItems').removeClass('selected').find('i').removeClass('oIcon');
    $('#onLine').addClass('selected').find('i').addClass('oIcon');
    $("#payWayType").val(1);
}

//选中礼品卡修改为在线支付和不需要发票
function changeToOnLionAndNoInvoice(){
    showPayWay();
    updateInvoiceInfo(0,"酒水",1,"","","");
    recoverInvoiceInfo();
}

//是否允许使用余额等
function isShowCashExt(){
    if($("#allCheckedIsJxOnly").val()=='false'){//
        $(".orderCashback,.orderBalance,.orderGift").hide();
    }else{
        $(".orderCashback,.orderBalance,.orderGift").show();
    }
}

//结算验证地址
function checkAddressForSubmit(addressId){
    if(addressId==null||addressId==''){
        //新建地址提示弹窗
        showDialog.showPop("您还没有创建收货地址，是否立即新建？","取消","新建",getAddressDetail,null);
        return;
    }else{
        return;
    }
}

//获取无货赠品id
function getNoStockGiftIds(){
    var ids = '';
    $('#giftNoStockDiv .popCatList .clearfix').each(function(index,item){
        if(typeof($(item).attr('goodId')) == undefined){
            return true;
        }
        ids = ids +$(item).attr('goodId') + ",";
    });
    ids = ids.substring(0,ids.length-1);
    $('#noStockGifts').val(ids);
}
//获取用户发票真实手机号
function getRealPhone(phone){
    if(isHiddenPhone(phone)){
        return $.trim($("#real-mobile").val());
    }else{
        return phone;
    }
}
//是否为加密手机号
function isHiddenPhone(str){
    var realmobile = $.trim($("#real-mobile").val());
    var show= realmobile.substring(0,3)+"****"+realmobile.substring(7,11);
    return show == str;
}
//展示发票商品
function showPhoneIfRealHave(){
    var realmobile = $.trim($("#real-mobile").val());
    var show= realmobile.substring(0,3)+"****"+realmobile.substring(7,11);
    if(realmobile!=""){
        $("#phoneNum").val(show);
        $("#defInvoiceMobile").val(show).attr('updateVal',show);
    }
}

//获取运费详情信息
function  getFreightHtml(freightData){
    var str = '';
    for(var index = 0; index < freightData.length ; index++){
        var info = freightData[index];
        str = str + "<div class='item'><div class='yfTitle'><p class='left'>";
        if(info.jiuxianSelf){
            str = str + "<i><img src='/ordernew/wap/images/jx.png'></i><span>赠酒自营</span></p>";
        }else{
            str = str + "<i><img src='/ordernew/wap/images/dsf.png'></i><span>" + info.shopName + "</span></p>";
        }
        str = str + "<p class='right'>运费:<span>" + info.price  + "</span></p></div>";
        str = str + "<div class='swiper-container J_silderTwo swiper-container-horizontal' id='sSlider_1'><div class='swiper-wrapper'>";
        if($(info.productImgList).length > 0){
            $(info.productImgList).each(function (i, item){
                str = str + "<div class='swiper-slide swiper-slide-active'><a href='#'><img src='" + item + "'></a></div>";
            });
        }
        str = str + "</div></div></div>";
    }
    return str
}

function getDiscountDetail(){
    var str = "";
    $("div[name=shopInfo]").each(function(index,item){
        var shopName = $(item).attr("shopName");
        var shopDiscountPrice = $(item).attr("shopDiscountPrice");
        if(parseFloat(shopDiscountPrice)>0){
            str = str + "<div class='oItem' ><div class='itemName'>" + shopName + "活动优惠</div><div class='itemInfo red'><p><em>-</em><span>￥"+ shopDiscountPrice +"</span></p></div></div>";
        }
    });
    $("#discountPriceDiv").empty();
    $("#discountPriceDiv").append(str).show();
}


function giftProPopColse() {
    $(".jxProMask1,.giftProPop").hide();
}

//回复提交订单按钮可点击
function recoverSubmitOrder() {
    $('#orderFormSubmitFlag').val("true");
    $(".Submits").css('background','#fc5a5a').css("pointer-events",'');
    $(".Submits").text("提交订单");
}