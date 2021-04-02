<?php
/**
 * Description:
 * Author: DexterHo(HeZhiZheng) <dexter.ho.cn@gmail.com>
 * Date: 2021/3/17
 * Time: 14:31
 * Created by hzz.
 */

namespace Hzz;

use GuzzleHttp\Client;

class Github implements StorehouseInterface
{
    const REQUEST_URL = "https://api.github.com/repos/%s/%s/contents/%s";

    private $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function put(array $putData)
    {
        $extension = pathinfo($putData["file"])["extension"] ?? '';

        $fileName = $extension != "" ? date("YmdHis") . "_" . uniqid() . "." . $extension : date("YmdHis") . "_" . uniqid();

        $path = $putData["path"] . "/" . $fileName;

        $file_base64 = file_exists($putData["file"]) ? base64_encode(file_get_contents($putData["file"])) : $putData["file"];

        $url = sprintf(self::REQUEST_URL, $putData["owner"], $putData["repo"], $path) . "?access_token=" . $this->token;

        $client = new Client();

        $res = $client->put($url, [
            'headers' => [
                'User-Agent' => 'PostmanRuntime/7.26.10',
                'Accept' => 'application/vnd.github.v3+json',
            ],
            'json' => [
                "message" => $putData["message"] ?? 'repo-storage upload',
                "content" => $file_base64,
            ],
            'verify' => false
        ]);

        $response = json_decode($res->getBody()->getContents(), true);

        if (!isset($response["content"]["path"])) {
            throw new \Exception("github 上传失败");
        }

        // cdn 加速地址
        $response["content"]["cdn_url"] = "https://cdn.jsdelivr.net/gh/" . $putData["owner"] . "/" . $putData["repo"] . "@master/" . $response["content"]["path"];

        return $response;
    }

    public function delete(array $deleteData)
    {
        $path = $deleteData["path"] . "/" . $deleteData["file"];

        $url = sprintf(self::REQUEST_URL, $deleteData["owner"], $deleteData["repo"], $path) . "?access_token=" . $this->token;

        $client = new Client();

        $res = $client->delete($url, [
            'headers' => [
                'User-Agent' => 'PostmanRuntime/7.26.10',
                'Accept' => 'application/vnd.github.v3+json',
            ],
            'json' => [
                "message" => $deleteData["message"] ?? 'repo-storage delete',
                "sha" => $deleteData["sha"],
            ],
            'verify' => false
        ]);

        $response = json_decode($res->getBody()->getContents(), true);

        return $response;
    }

    public function get(array $getData)
    {
        $url = sprintf(self::REQUEST_URL, $getData["owner"], $getData["repo"], $getData["path"]) . "?access_token=" . $this->token;

        $client = new Client();

        $res = $client->get($url,[
            'verify' => false
        ]);

        $response = json_decode($res->getBody()->getContents(), true);

        // cdn 加速地址
        if ( is_array($response) )
        {
            foreach ( $response as &$item )
            {
                $item["cdn_url"] = "https://cdn.jsdelivr.net/gh/" . $getData["owner"] . "/" . $getData["repo"] . "@master/" . $item["path"];
            }
        }

        return $response;
    }
}