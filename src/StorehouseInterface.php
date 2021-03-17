<?php
/**
 * Description:
 * Author: DexterHo(HeZhiZheng) <dexter.ho.cn@gmail.com>
 * Date: 2021/3/17
 * Time: 15:00
 * Created by hzz.
 */

namespace Hzz;

interface StorehouseInterface
{
    public function put(array $putData);
    public function delete(array $deleteData);
    public function get(array $getData);
}