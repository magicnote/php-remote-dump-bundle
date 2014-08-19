<?php

namespace Wusuopu\RemoteDumpBundle\Util;

/**
 * A util for dump a object to string and post to remote.
 */
class DumpUtil
{
    /**
     * @param object $data
     * @param string $url
     */
    static public function postData($data, $url)
    {
        if (!$data) {
            return;
        }

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array("data" => $data)));
            curl_exec($ch);
            curl_close($ch);
        } catch (\Exception $e) {
        }
    }

    /**
     * Post data to remote server.
     *
     * @param object $data
     * @param string $url
     */
    static public function dump($data, $url = "http://127.0.0.1:9090/")
    {
        if (!$data) {
            return;
        }
        ob_start();
        var_dump($data);
        $str = ob_get_contents();
        ob_end_clean();

        static::postData('<pre>' . $str . '</pre>', $url);
    }
}
