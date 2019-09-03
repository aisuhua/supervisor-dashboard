<?php
/**
 * 发送POST请求
 *
 * @param string $url
 * @param mixed $fields
 * @param int $timeout
 * @throws Exception
 * @return string
 */
function curl_post($url, $fields, $timeout = 1)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    $data = curl_exec($ch);
    curl_close($ch);

    if ($data === false)
    {
        throw new Exception(curl_error($ch));
    }

    return $data;
}

/**
 * 发送GET请求
 *
 * @param string $url
 * @param array $fields
 * @param int $timeout
 * @throws Exception
 * @return string
 */
function curl_get($url, $fields, $timeout = 1)
{
    $ch = curl_init();
    if (!empty($fields))
    {
        $url .= "?" . http_build_query($fields);
    }
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    $data = curl_exec($ch);
    curl_close($ch);

    if ($data === false)
    {
        throw new Exception(curl_error($ch));
    }

    return $data;
}