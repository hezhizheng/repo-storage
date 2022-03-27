<?php

namespace Hzz;

use GuzzleHttp\Client;
use Hzz\org\FileCache;

class Coding implements StorehouseInterface
{
    const REQUEST_URL = "https://e.coding.net/open-api";

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

        $url = self::REQUEST_URL;

        $client = new Client();

        $branch = $putData["branch"] ?? "master";

        $res = $client->post($url, [
            'headers' => [
                'User-Agent' => 'PostmanRuntime/7.26.10',
                'Accept' => 'application/json',
                'Authorization' => 'token '.$this->token,
            ],
            'json' => [
                "Action" => "CreateBinaryFiles",
                "DepotId" => (int) $putData["DepotId"],
                "UserId" => $this->getUserId(),
                "LastCommitSha" => $this->getLastCommitSha($putData),
                "Message" => $putData["message"] ?? 'repo-storage upload',
                "SrcRef" => $branch,
                "DestRef" => $branch,
                "GitFiles" => [
                    [
                        "Content" => $file_base64,
                        "Path" => $path,
                        "NewPath" => "",
                    ]
                ],
            ],
            'verify' => false
        ]);

        $response = json_decode($res->getBody()->getContents(), true);

        if (isset($response["Response"]["Error"])) {
            throw new \Exception("coding 上传失败：" .$res->getBody()->getContents());
        }


        $items = $this->get($putData);
        $count = count($items);
        $data = [];
        if ( $count > 0 )
        {
            $last = $items[$count-1];
            $show_url = "https://".$putData["owner"].".coding.net/p/".$putData["project"]."/d/".$putData["repo"]."/git/raw/".$branch."/".$last["Path"];
            if ( empty($extension) )
            {
                $show_url = $show_url."?download=true";
            }
            $data["url"] = $show_url;
            $data["name"] = $last["Name"];
        }
        return $data;
    }

    public function delete(array $deleteData)
    {
        return [];
    }

    public function get(array $getData)
    {
        $url = self::REQUEST_URL;

        $client = new Client();

        $branch = $getData["branch"] ?? "master";

        $path = $getData["path"];

        $res = $client->post($url,[
            'headers' => [
                'User-Agent' => 'PostmanRuntime/7.26.10',
                'Accept' => 'application/json',
                'Authorization' => 'token '.$this->token,
            ],
            'json' => [
                "Action" => "DescribeGitFiles",
                "DepotId" => (int) $getData["DepotId"],
                "Path" => $path,
                "Ref" => $branch,
            ],
            'verify' => false
        ]);

        $response = json_decode($res->getBody()->getContents(), true);

        return $response["Response"]["Items"] ?? [];
    }

    private function getUserId()
    {
        $cache = new FileCache(['cache_dir' => __DIR__.'/cache']);

        $cache_user_id = $cache->get($this->token);

        if ( !empty($cache_user_id) && $cache_user_id > 0 )
        {
            return (int) $cache_user_id;
        }

        $url = self::REQUEST_URL;

        $client = new Client();

        $res = $client->post($url,[
            'headers' => [
                'User-Agent' => 'PostmanRuntime/7.26.10',
                'Accept' => 'application/json',
                'Authorization' => 'token '.$this->token,
            ],
            'json' => [
                "Action" => "DescribeCodingCurrentUser"
            ],
            'verify' => false
        ]);

        $response = json_decode($res->getBody()->getContents(), true);

        $userId = $response["Response"]["User"]["Id"] ?? 0;

        if ( $userId > 0 )
        {
            $cache->save($this->token,$userId,3155673600);
        }

        return (int) $userId;
    }

    private function getLastCommitSha($putData)
    {
        $url = self::REQUEST_URL;

        $client = new Client();

        $branch = $putData["branch"] ?? "master";

        $res = $client->post($url,[
            'headers' => [
                'User-Agent' => 'PostmanRuntime/7.26.10',
                'Accept' => 'application/json',
                'Authorization' => 'token '.$this->token,
            ],
            'json' => [
                "Action" => "DescribeGitCommits",
                "DepotId" => (int) $putData["DepotId"],
                "PageNumber" => 1,
                "PageSize" => 1,
                "Ref" => $branch,
            ],
            'verify' => false
        ]);

        $response = json_decode($res->getBody()->getContents(), true);

        $sha = $response["Response"]["Commits"][0]["Sha"] ?? "";

        return $sha;
    }
}