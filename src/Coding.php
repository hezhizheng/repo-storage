<?php

namespace Hzz;

use GuzzleHttp\Client;

class Coding implements StorehouseInterface
{
    const REQUEST_URL = "https://e.coding.net/open-api";

    private $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    private function postFile($putData)
    {
        $extension = pathinfo($putData["file"])["extension"] ?? '';

        $fileName = $extension != "" ? date("YmdHis") . "_" . uniqid() . "." . $extension : date("YmdHis") . "_" . uniqid();

        $path = $putData["path"] . "/" . $fileName;

        $file_base64 = file_exists($putData["file"]) ? base64_encode(file_get_contents($putData["file"])) : $putData["file"];

        $url = self::REQUEST_URL;

        $client = new Client();

        $branch = $putData["branch"] ?? "master";

        $UserId = $this->getUserId();
        $LastCommitSha = $this->getLastCommitSha($putData);

        $res = $client->post($url, [
            'headers' => [
                'User-Agent' => 'PostmanRuntime/7.26.10',
                'Accept' => 'application/json',
                'Authorization' => 'token '.$this->token,
            ],
            'json' => [
                "Action" => "CreateBinaryFiles",
                "DepotId" => (int) $putData["DepotId"],
                "UserId" => $UserId,
                "LastCommitSha" => $LastCommitSha,
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
            $msg = json_encode(compact('response','res'));
            throw new \Exception("coding 上传失败：" .$msg);
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

    public function put(array $putData)
    {
        return $this->pessimisticLock(time(),function () use($putData){
            return $this->postFile($putData);
        });
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
        $f = __DIR__."/".md5($this->token).".cache";
        $cache_user_id = @file_get_contents($f);

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
            file_put_contents($f, $userId,FILE_APPEND|LOCK_EX);
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

    private function pessimisticLock(string $file_name, callable $func)
    {
        $file_name = __DIR__."/".$file_name;

        $fp = fopen($file_name, "a+");

        try {
            if (flock($fp, LOCK_EX)) { // 强占锁 ，阻塞，执行完$function 才解锁
                $func = $func();
                flock($fp, LOCK_UN);
            }
            fclose($fp);
            @unlink($file_name);
        } catch (\Throwable $exception) {
            flock($fp, LOCK_UN);
            fclose($fp);
            @unlink($file_name);
            throw new \Exception($exception->getMessage());
        }

        return $func;
    }
}