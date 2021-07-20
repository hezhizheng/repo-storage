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

    private function __construct()
    {
    }

    /**
     * 单例调用
     * @param $platform
     * @param $token
     * @return mixed|null|StorehouseInterface
     */
    public static function singleton($platform, $token)
    {
        if ( self::$singleton === null )
        {
            self::$singleton = new self::$entityMap[$platform]($token);
        }
        return self::$singleton;
    }

    /** 简单工厂模式调用
     * @param $platform
     * @param $token
     * @return mixed|StorehouseInterface
     */
    public static function create($platform, $token)
    {
        return new self::$entityMap[$platform]($token);
    }
}