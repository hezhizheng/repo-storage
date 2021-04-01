<?php
/**
 * Description:
 * Author: DexterHo(HeZhiZheng) <dexter.ho.cn@gmail.com>
 * Date: 2021/3/17
 * Time: 15:03
 * Created by hzz.
 */

namespace Hzz;

class StorageEntity
{
    private static $create = null;

    private static $entityMap = [
        'github' => 'Hzz\Github',
        'gitee' => 'Hzz\Gitee',
    ];

    /**
     * @param $platform
     * @param $token
     * @return mixed|StorehouseInterface
     */
    public static function create($platform, $token)
    {
        if ( static::$create === null  )
        {
            static::$create = new self::$entityMap[$platform]($token);
        }
        return static::$create;
    }
}