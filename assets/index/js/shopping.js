$(function () {
    // 数量减
    $(".minus").click(function () {
        var t = $(this).parent().find('.num');
        var origin_num = parseInt(t.text());
        t.text(origin_num - 1);
        if (t.text() <= 1) {
            t.text(1);
        }
        TotalPrice();
        //监听事件
        (origin_num - 1) > 0 && typeof onCartNumChange === 'function' && onCartNumChange(this, -1);
        origin_num === 1 && typeof onCartDelGoods === 'function' && onCartDelGoods(this);
    });
    // 数量加
    $(".plus").click(function () {
        var t = $(this).parent().find('.num');
        var origin_num = parseInt(t.text());
        t.text(origin_num + 1);
        if (t.text() <= 1) {
            t.text(1);
        }
        TotalPrice();
        //监听事件
        var new_num = parseInt(t.text());
        (new_num - 1) > 0 && typeof onCartNumChange === 'function' && onCartNumChange(this, 1);
    });
    /******------------分割线-----------------******/
    // 点击商品按钮
    $(".goodsCheck").click(function () {
        var goods = $(this).closest(".shop-group-item").find(".goodsCheck"); //获取本店铺的所有商品
        var goodsC = $(this).closest(".shop-group-item").find(".goodsCheck:checked"); //获取本店铺所有被选中的商品
        var Shops = $(this).closest(".shop-group-item").find(".shopCheck"); //获取本店铺的全选按钮
        if (goods.length == goodsC.length) { //如果选中的商品等于所有商品
            Shops.prop('checked', true); //店铺全选按钮被选中
            if ($(".shopCheck").length == $(".shopCheck:checked").length) { //如果店铺被选中的数量等于所有店铺的数量
                $("#AllCheck").prop('checked', true); //全选按钮被选中
                TotalPrice();
            } else {
                $("#AllCheck").prop('checked', false); //else全选按钮不被选中
                TotalPrice();
            }
        } else { //如果选中的商品不等于所有商品
            Shops.prop('checked', false); //店铺全选按钮不被选中
            $("#AllCheck").prop('checked', false); //全选按钮也不被选中
            // 计算
            TotalPrice();

        }
        //监听事件
        typeof checkGoods === 'function' && checkGoods($(this), {goods_id: $(this).val()}, true);
    });
    // 点击店铺按钮
    $(".shopCheck").click(function () {
        if ($(this).prop("checked") == true) { //如果店铺按钮被选中
            $(this).parents(".shop-group-item").find(".goods-check").prop('checked', true); //店铺内的所有商品按钮也被选中
            if ($(".shopCheck").length == $(".shopCheck:checked").length) { //如果店铺被选中的数量等于所有店铺的数量
                $("#AllCheck").prop('checked', true); //全选按钮被选中
                TotalPrice();
            } else {
                $("#AllCheck").prop('checked', false); //else全选按钮不被选中
                TotalPrice();
            }
        } else { //如果店铺按钮不被选中
            $(this).parents(".shop-group-item").find(".goods-check").prop('checked', false); //店铺内的所有商品也不被全选
            $("#AllCheck").prop('checked', false); //全选按钮也不被选中
            TotalPrice();
        }
    });
    // 点击全选按钮
    $("#AllCheck").click(function () {
        if ($(this).prop("checked") == true) { //如果全选按钮被选中
            $(".goods-check").prop('checked', true); //所有按钮都被选中
            TotalPrice();
        } else {
            $(".goods-check").prop('checked', false); //else所有按钮不全选
            TotalPrice();
        }
        $(".shopCheck").change(); //执行店铺全选的操作
        //监听事件
        var is_checked = $(this).prop('checked') ? 1 : 0;
        typeof checkGoods === 'function' && checkGoods($(this), {is_checked: is_checked}, false);
    });
    //计算
    TotalPrice();
});

function TotalPrice() {
    var allprice = 0; //总价
    var checked_goods_num =0;//已选中的商品数量

    $(".shop-group-item").each(function () { //循环每个店铺
        // var oprice = 0; //店铺总价
        $(this).find(".goodsCheck").each(function () { //循环店铺里面的商品
            if ($(this).is(":checked")) { //如果该商品被选中
                var num = parseInt($(this).parents(".shop-info").find(".num").text()); //得到商品的数量
                var price = parseFloat($(this).parents(".shop-info").find(".price").text()); //得到商品的单价
                var total = price * num; //计算单个商品的总价
                allprice += total; //计算该店铺的总价
                //所有已选择的商品数量
                checked_goods_num++;
            }
            // $(this).closest(".shop-group-item").find(".ShopTotal").text(oprice.toFixed(2)); //显示被选中商品的店铺总价
            // var oneprice = parseFloat($(this).find(".ShopTotal").text()); //得到每个店铺的总价
            // allprice += oneprice; //计算所有店铺的总价
        });

    });
    //全选按钮选中效果
    if(checked_goods_num===$(".shop-group-item .goodsCheck").length){
        $("#AllCheck").prop('checked',checked_goods_num>0);
    }
    // console.log(allprice);
    $("#AllTotal").text(allprice.toFixed(2)); //输出全部总价
    if(checked_goods_num>0){
        $(".settlement").removeClass('disabled').attr('href',$(".settlement").data('href'))
    }else{
        $(".settlement").addClass('disabled').attr('href','javascript:;')
    }
}
