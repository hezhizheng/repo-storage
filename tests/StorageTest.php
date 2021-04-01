<?php
/**
 * Description:
 * Author: DexterHo(HeZhiZheng) <dexter.ho.cn@gmail.com>
 * Date: 2021/3/17
 * Time: 15:34
 * Created by hzz.
 */

class StorageTest extends \PHPUnit\Framework\TestCase
{
    const GITHUB_TOKEN = "271a6104370c50ec82d9b7c495ea9a24b9727550";
    const GITEE_TOKEN = "xxx";

    public function test_github_put()
    {
        $x = \Hzz\StorageEntity::create('github',self::GITHUB_TOKEN);

        $putData["owner"] = "hezhizheng";
        $putData["repo"] = "static-image-hosting";
        $putData["path"] = "files";
        $putData["file"] = "D:\\phpstudy_pro\\WWW\\org\\repo-storage\\tests\\1.png";

        var_dump(2,$x->put($putData));
    }

    public function test_github_delete()
    {
        $x = \Hzz\StorageEntity::create('github',self::GITHUB_TOKEN);

        $putData["owner"] = "hezhizheng";
        $putData["repo"] = "static-image-hosting";
        $putData["path"] = "files";
        $putData["file"] = "20210317170512_6051c64896104.png";
        $putData["sha"] = "213231fd035a1ea05e5ccaba94cfa4d1acd6e81d";

        var_dump(2,$x->delete($putData));
    }


    public function test_github_get()
    {
        $x = \Hzz\StorageEntity::create('github',self::GITHUB_TOKEN);

        $putData["owner"] = "hezhizheng";
        $putData["repo"] = "static-image-hosting";
        $putData["path"] = "files";

        var_dump(2,$x->get($putData));
    }

    public function test_gitee_put()
    {
        $x = \Hzz\StorageEntity::create('gitee',self::GITEE_TOKEN);

        $putData["owner"] = "hezhizheng";
        $putData["repo"] = "pictest";
        $putData["path"] = "files";
        $putData["file"] = "D:\\phpstudy_pro\\WWW\\org\\repo-storage\\tests\\1.png";

        var_dump(2,$x->put($putData));
    }

    public function test_gitee_delete()
    {
        $x = \Hzz\StorageEntity::create('gitee',self::GITEE_TOKEN);

        $putData["owner"] = "hezhizheng";
        $putData["repo"] = "pictest";
        $putData["path"] = "files";
        $putData["file"] = "20210317170911_6051c7378bf8f.png";
        $putData["sha"] = "213231fd035a1ea05e5ccaba94cfa4d1acd6e81d";

        var_dump(2,$x->delete($putData));
    }

    public function test_gitee_get()
    {
        $x = \Hzz\StorageEntity::create('gitee',self::GITEE_TOKEN);

        $putData["owner"] = "hezhizheng";
        $putData["repo"] = "pictest";
        $putData["path"] = "files";

        var_dump(2,$x->get($putData));
    }

    public function test_Strategy()
    {
        $s = new \Hzz\Github(self::GITHUB_TOKEN);

        $putData["owner"] = "hezhizheng";
        $putData["repo"] = "static-image-hosting";
        $putData["path"] = "files";

        $x = new \Hzz\StoreStrategy($s);

        var_dump(2,$x->serve->get($putData));
    }
}