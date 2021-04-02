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
    private static $singleton = null;

    private static $entityMap = [
        'github' => 'Hzz\Github',
        'gitee' => 'Hzz\Gitee',
    ];

    public static function singleton()
    {
        if ( self::$singleton === null )
        {
            self::$singleton = new self();
        }
        return self::$singleton;
    }

    /**
     * @param $platform
     * @param $token
     * @return mixed|StorehouseInterface
     */
    public function implement($platform, $token)
    {
        return new self::$entityMap[$platform]($token);
    }

    /**
     * @param $platform
     * @param $token
     * @return mixed|StorehouseInterface
     */
    public static function create($platform, $token)
    {
        return new self::$entityMap[$platform]($token);
    }
}