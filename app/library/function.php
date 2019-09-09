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
function curl_get($url, $fields = [], $timeout = 1)
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

function print_cli(...$args)
{
    echo date('Y-m-d H:i:s'), ">> " . implode('', $args), PHP_EOL;
}

function build_ini_string(array $parsed)
{
    $ini = '';
    foreach ($parsed as $key => $item)
    {
        $ini .= '[' . $key . ']' . PHP_EOL;

        foreach ($item as $k => $v)
        {
            $ini .= $k . '=' . $v . PHP_EOL;
        }
    }

    return $ini;
}