<?php
/**
 * Description:
 * Author: DexterHo(HeZhiZheng) <dexter.ho.cn@gmail.com>
 * Date: 2021/4/1
 * Time: 15:27
 * Created by hzz.
 */

namespace Hzz;


class StoreStrategy
{
    public $serve;

    public function __construct(StorehouseInterface $storehouse)
    {
        $this->serve = $storehouse;
    }
}