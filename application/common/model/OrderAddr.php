<?php
namespace app\common\model;



class OrderAddr extends BaseModel
{



    protected function getHidePhoneAttr($value,$data)
    {
        return empty($data['phone'])?'':substr_replace($data['phone'],'****',3,4);
    }



}