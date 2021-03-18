## repo-storage 使用代码托管平台(github|gitee)做自己 '私有' 的 '对象存储'

> [github 地址](https://github.com/hezhizheng/repo-storage)

## 功能
- 支持gihub(jsdelivr加速)/gitee
- 已实现 上传 、删除、文件获取 接口

## 使用
- 安装 `composer require "hzz/repo-storage @dev"`
- github token 获取 -> https://github.com/settings/tokens/new
- gitee token 获取 -> https://gitee.com/profile/personal_access_tokens/new
```php

// gitee
// $entity = \Hzz\StorageEntity::create('gitee',"对应gitee平台的token");
$entity = \Hzz\StorageEntity::create('github',"对应github平台的token");

// 请求参数说明
$_data = [
    'owner' => "", // 用户名
    'repo' => "", // 仓库名称
    'path' => "", // 文件存储的路径
    'file' => "", // 上传时使用文件绝对路径，删除时使用仓库中对应的文件名
    'sha' => "",  // 删除文件的 sha 标识
];

// 上传
$date["owner"] = "hezhizheng";
$date["repo"] = "static-image-hosting";
$date["path"] = "files";
$date["file"] = "/xxxpath/1.png";
$res = $entity->put($date); // 文件访问地址 github为 $res['content']['cdn_url']  gitee 为 $res['content']['download_url']

// 删除
$date["owner"] = "hezhizheng";
$date["repo"] = "static-image-hosting";
$date["path"] = "files";
$date["file"] = "20210317170512_6051c64896104.png";
$date["sha"] = "213231fd035a1ea05e5ccaba94cfa4d1acd6e81d";
$entity->delete($date);

// 获取文件
$date["owner"] = "hezhizheng";
$date["repo"] = "static-image-hosting";
$date["path"] = "files";
$entity->get($date);

```
详细用法可参考 tests 用例

## License
[MIT](./LICENSE.txt)
