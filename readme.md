## repo-storage 使用代码托管平台(github|coding|gitee)做自己 '私有' 的 '对象存储'

> [github 地址](https://github.com/hezhizheng/repo-storage)

## 功能
- 支持github(jsdelivr加速)/coding/gitee
- 已实现 上传 、删除、文件获取 接口

## 使用
- 安装 `composer require "hzz/repo-storage @dev"`
- github token 获取 -> https://github.com/settings/tokens/new
- gitee token 获取 -> https://gitee.com/profile/personal_access_tokens/new
- Coding 开放平台 -> https://help.coding.net/openapi
```php

// 使用简单工厂模式调用
// gitee
// $entity = \Hzz\StorageEntity::create('gitee',"对应gitee平台的token");
$entity = \Hzz\StorageEntity::create('github',"对应github平台的token");

// 请求参数说明
$_data = [
    'owner' => "", // 用户名
    'repo' => "", // 仓库名称
    'path' => "", // 文件存储的路径
    'file' => "", // 上传时使用文件绝对路径或者base64，删除时使用仓库中对应的文件名
    'sha' => "",  // 删除文件的 sha 标识
];

// 上传
$data["owner"] = "hezhizheng";
$data["repo"] = "static-image-hosting";
$data["path"] = "files";
$data["file"] = "/xxxpath/1.png";
$res = $entity->put($data); // 文件访问地址 github为 $res['content']['cdn_url']  gitee 为 $res['content']['download_url']

// 删除
$data["owner"] = "hezhizheng";
$data["repo"] = "static-image-hosting";
$data["path"] = "files";
$data["file"] = "20210317170512_6051c64896104.png";
$data["sha"] = "213231fd035a1ea05e5ccaba94cfa4d1acd6e81d";
$entity->delete($data);

// 获取文件
$data["owner"] = "hezhizheng";
$data["repo"] = "static-image-hosting";
$data["path"] = "files";
$entity->get($data);

// 使用策略模式调用
$server = new \Hzz\StoreStrategy(new \Hzz\Github("对应github平台的token"));
$server->serve->get($data); $server->serve->put($data); $server->serve->delete($data);
```
详细用法可参考 [tests](./tests) 用例

## License
[MIT](./LICENSE.txt)
