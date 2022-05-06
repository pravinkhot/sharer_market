<?php
namespace App\Helpers;

class DataProvider
{
    public static function getContext()
    {
        return stream_context_create(
            [
                'http' => [
                    'header' => [
                        'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; rv:2.2) Gecko/20110201'
                    ]
                ],
                'ssl' => [
                    'verify_peer' => true,
                ],
            ]
        );
    }

    public static function pullDataFromRemote(string $url)
    {
        if (empty($url)) {
            throw new \Exception("Please provide source URL.", 1);
        }

        return @file_get_contents(
            $url,
            false,
            self::getContext()
        );
    }

    public static function putDataFromRemoteToLocal(string $sourceLocationUrl, string $destinationLocationUrl)
    {
        if (empty($sourceLocationUrl)) {
            throw new \Exception("Please provide source location URL.", 1);
        }

        if (empty($destinationLocationUrl)) {
            throw new \Exception("Please provide destination location URL.", 1);
        }

        return @file_put_contents(
            $destinationLocationUrl,
            fopen($sourceLocationUrl, 'r', 0, self::getContext()),
            LOCK_EX,
            self::getContext()
        );
    }
}
