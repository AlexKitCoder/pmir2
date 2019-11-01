<?php
use app\Common\Srp6;
use core\Config;
use core\filter\Filter;
use core\lib\Cookie;
use core\lib\Session;
use app\Common\Math_BigInteger;

if (!function_exists('config')) {
    /**
     * [config 获取和设置配置参数]
     * ------------------------------------------------------------------------------
     * @author  by.fan <fan3750060@163.com>
     * ------------------------------------------------------------------------------
     * @version date:2018-01-04
     * ------------------------------------------------------------------------------
     * @param   string          $name  [参数名]
     * @param   [type]          $value [参数值]
     * @param   string          $range [作用域]
     * @return  [type]                 [description]
     */
    function config($name = '', $value = null, $range = '')
    {
        if (is_null($value) && is_string($name)) {
            return 0 === strpos($name, '?') ? Config::has(substr($name, 1), $range) : Config::get($name, $range);
        } else {
            return Config::set($name, $value, $range);
        }
    }
}

if (!function_exists('input')) {
    /**
     * [input 获取输入数据 支持默认值和过滤]
     * ------------------------------------------------------------------------------
     * @author  by.fan <fan3750060@163.com>
     * ------------------------------------------------------------------------------
     * @version date:2018-01-04
     * ------------------------------------------------------------------------------
     * @param   string          $key    [获取的变量名]
     * @param   string          $filter [过滤方法 int,string,float,bool]
     * @return  [type]                  [description]
     */
    function input($key = '', $filter = '')
    {
        $param = json_decode(ARGV, true);
        unset($param[0]);
        unset($param[1]);
        $array = [];
        foreach ($param as $key => $value) {
            $array[] = $value;
        }
        return $array;
    }
}

if (!function_exists('session')) {
    /**
     * [session]
     * ------------------------------------------------------------------------------
     * @author  by.fan <fan3750060@163.com>
     * ------------------------------------------------------------------------------
     * @version date:2018-01-02
     * ------------------------------------------------------------------------------
     * @param   string          $key   [参数名]
     * @param   string          $value [参数值]
     * @return  [type]                 [description]
     */
    function session($key = null, $value = '_null')
    {

        if (is_null($key) || !$key) {
            return Session::boot()->all();
        } elseif ($key && $value === '_null') {
            return Session::boot()->get($key);
        } elseif ($key && $value !== '_null') {
            return Session::boot()->set($key, $value);
        }
    }
}

if (!function_exists('cookie')) {
    /**
     * [cookie]
     * ------------------------------------------------------------------------------
     * @author  by.fan <fan3750060@163.com>
     * ------------------------------------------------------------------------------
     * @version date:2018-01-05
     * ------------------------------------------------------------------------------
     * @param   string          $key   [参数名]
     * @param   string          $value [参数值]
     * @param   integer         $time  [过期时间]
     * @return  [type]                 [description]
     */
    function cookie($key = null, $value = '_null', $time = 0)
    {
        if (is_null($key) || !$key) {
            return Cookie::boot()->all();
        } elseif ($key && $value === '_null') {
            return Cookie::boot()->get($key);
        } elseif ($key && $value !== '_null') {
            return Cookie::boot()->set($key, $value, $time);
        }
    }
}

if (!function_exists('echolog')) {
    /**
     * [echolog]
     * ------------------------------------------------------------------------------
     * @author  by.fan <fan3750060@163.com>
     * ------------------------------------------------------------------------------
     * @version date:2018-01-05
     * ------------------------------------------------------------------------------
     * @param   string          $string   [内容]
     * @return  [type]                 [description]
     */
    function echolog($string = null, $type = 'no', $save = null, $filename = 'swoole.log')
    {
        if (is_array($string)) {
            $str = $string = var_export($string, true) . PHP_EOL;
        }

        switch ($type) {
            case 'success':
                $str = "\033[32m[" . date('Y-m-d H:i:s') . "]: " . $string . PHP_EOL . "\033[0m";
                break;

            case 'warning':
                $str = "\033[33m[" . date('Y-m-d H:i:s') . "]: " . $string . PHP_EOL . "\033[0m";
                break;

            case 'error':
                $str = "\033[31m[" . date('Y-m-d H:i:s') . "]: " . $string . PHP_EOL . "\033[0m";
                break;

            case 'info':
                $str = "\033[36m[" . date('Y-m-d H:i:s') . "]: " . $string . PHP_EOL . "\033[0m";
                break;

            default:
                $str = "\033[35m[" . date('Y-m-d H:i:s') . "]: " . $string . PHP_EOL . "\033[0m";
                break;
        }

        echo $str;

        $save = $save === null ? env('LOG_SAVE', false) : $save;

        if ($save && ($type == 'error' || $type == 'success')) {

            $logstr   = "[" . date('Y-m-d H:i:s') . "]: " . $string . PHP_EOL;
            $log_file = fopen(RUNTIME_PATH . $filename, 'a');
            fputs($log_file, $logstr);
            fclose($log_file);

            if (count_line(RUNTIME_PATH . $filename) > env('LOG_LINE', 1000)) {
                @unlink(RUNTIME_PATH . $filename);
            }
        }
    }
}

if (!function_exists('AUTH_LOG')) {
    /**
     * [AUTH_LOG]
     * ------------------------------------------------------------------------------
     * @author  by.fan <fan3750060@163.com>
     * ------------------------------------------------------------------------------
     * @version date:2018-01-05
     * ------------------------------------------------------------------------------
     * @param   string          $string   [内容]
     * @return  [type]                 [description]
     */
    function AUTH_LOG($string = null, $type = 'no', $save = null, $filename = 'auth.log')
    {
        $save = $save === null ? env('LOG_SAVE', false) : false;
        echolog($string, $type, $save, $filename);
    }
}

if (!function_exists('WORLD_LOG')) {
    /**
     * [WORLD_LOG]
     * ------------------------------------------------------------------------------
     * @author  by.fan <fan3750060@163.com>
     * ------------------------------------------------------------------------------
     * @version date:2018-01-05
     * ------------------------------------------------------------------------------
     * @param   string          $string   [内容]
     * @return  [type]                 [description]
     */
    function WORLD_LOG($string = null, $type = 'no', $save = null, $filename = 'world.log')
    {
        $save = $save === null ? env('LOG_SAVE', false) : false;
        echolog($string, $type, $save, $filename);
    }
}

if (!function_exists('count_line')) {
    function count_line($file)
    {
        $fp = fopen($file, "r");
        $i  = 0;
        while (!feof($fp)) {
            //每次读取2M
            if ($data = fread($fp, 1024 * 1024 * 2)) {
                //计算读取到的行数
                $num = substr_count($data, "\n");
                $i += $num;
            }
        }
        fclose($fp);
        return $i;
    }
}

if (!function_exists('import')) {
    /**
     * [import 加载第三方类库]
     * ------------------------------------------------------------------------------
     * @Autor    by.fan
     * ------------------------------------------------------------------------------
     * @DareTime 2017-06-29
     * ------------------------------------------------------------------------------
     * @param    [type]     $folder [目录] 多级目录用'/'间隔
     * @param    [type]     $name   [名称]
     * @param    [type]     $class  [类]  可不填,不填为引入文件
     * @return   [type]             [description]
     *
     * 加载类库: import('PHPMailer','PHPMailerAutoload','PHPMailer')
     */
    function import($folder, $name, $class = null)
    {
        //参数处理
        if (!is_string($name)) {
            return false;
        }

        $file_path = $folder . '/' . $name . '.php';
        if (!file_exists($file_path)) {
            return false;
        }

        require_once $file_path;
        if (!class_exists($class)) {
            return false;
        }

        return new $class(); //实例化模型
    }
}

if (!function_exists('http_curl')) {
    /**
     * [http_curl 获取]
     * ------------------------------------------------------------------------------
     * @author  by.fan <fan3750060@163.com>
     * ------------------------------------------------------------------------------
     * @version date:2018-06-12
     * ------------------------------------------------------------------------------
     * @param   [type]          $url [description]
     * @return  [type]               [description]
     */
    function http_curl($param = [])
    {
        if (!$param || !$param['url']) {
            return 'url为必填';
        }

        // 初始化
        $ch = curl_init();

        // 设置浏览器的特定header
        $header = [
            "Connection: keep-alive",
            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8",
            "Upgrade-Insecure-Requests: 1",
            "DNT:1",
            "Accept-Language: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8",
        ];

        if (!empty($param['header'])) {
            $header = $param['header'];
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        //访问网页
        curl_setopt($ch, CURLOPT_URL, $param['url']);

        //代理服务器设置
        if (!empty($param['proxy'])) {
            curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC); //代理认证模式
            curl_setopt($ch, CURLOPT_PROXY, $param['proxy'][0]); //代理服务器地址
            curl_setopt($ch, CURLOPT_PROXYPORT, $param['proxy'][1]); //代理服务器端口
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $param['proxy'][2] . ":" . $param['proxy'][3]); //http代理认证帐号，username:password的格式
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP); //使用SOCKS5代理模式
        }

        //浏览器设置
        $user_agent = 'User-Agent: Mozilla/5.0 (Linux; Android 5.0; SM-G900P Build/LRX21T) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.87 Mobile Safari/537.36';
        if (!empty($param['user_agent'])) {
            $user_agent = $param['user_agent'];
        }

        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);

        if (!empty($param['autoreferer'])) {
            //重定向
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            //多级自动跳转
            curl_setopt($ch, CURLOPT_AUTOREFERER, true);
            //设置跳转location 最多10次
            curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        }

        //来源
        if (!empty($param['referer'])) {
            curl_setopt($ch, CURLOPT_REFERER, $param['referer']);
        }

        //cookie设置
        if (!empty($param['cookiepath'])) {
            curl_setopt($ch, CURLOPT_COOKIEJAR, $param['cookiepath']); //存储cookies
            curl_setopt($ch, CURLOPT_COOKIEFILE, $param['cookiepath']); //发送cookie
        }

        //是否显示头信息
        if (!empty($param['showheader'])) {
            curl_setopt($ch, CURLOPT_HEADER, 1);
        }

        //是否post提交
        if (!empty($param['data'])) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST'); // 请求方式
            curl_setopt($ch, CURLOPT_POST, true); // post提交
            curl_setopt($ch, CURLOPT_POSTFIELDS, $param['data']); // post的变量
        }

        //超时设置
        $timeout = isset($param['timeout']) && (int) $param['timeout'] ? $param['timeout'] : 30;
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

        //是否为https请求
        if (!empty($param['https'])) {
            // 针对https的设置
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        }

        //获取内容不直接输出
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // 执行
        $response = curl_exec($ch);

        //关闭
        curl_close($ch);

        if (!empty($param['returndecode'])) {
            $response = json_decode($response, true);
        }

        return $response;
    }
}

if (!function_exists('getuuid')) {
/**
 * 生成uuid
 * @return string
 */
    function getuuid()
    {
        $uuid = '';
        if (function_exists('uuid_create') === true) {
            $uuid = uuid_create(1);
        } else {
            $data    = openssl_random_pseudo_bytes(16);
            $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
            $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
            $uuid    = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        }
        return $uuid;
    }
}

if (!function_exists('env')) {
    /**
     * Gets the value of an environment variable.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    function env($key, $default = null)
    {
        $value = getenv($key);

        if ($value === false) {
            return $default;
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return;
        }

        if (($valueLength = strlen($value)) > 1 && $value[0] === '"' && $value[$valueLength - 1] === '"') {
            return substr($value, 1, -1);
        }

        return $value;
    }
}

if (!function_exists('getIP')) {
    //[getIP 获取客户端IP
    function getIP()
    {
        global $ip;
        if (getenv("HTTP_CLIENT_IP")) {
            $ip = getenv("HTTP_CLIENT_IP");
        } else if (getenv("HTTP_X_FORWARDED_FOR")) {
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        } else if (getenv("REMOTE_ADDR")) {
            $ip = getenv("REMOTE_ADDR");
        } else {
            $ip = "Unknow";
        }

        return $ip;
    }
}

if (!function_exists('PackInt')) {
    //PackInt
    function PackInt($int, $type = 8)
    {
        return \app\Common\int_helper::PackInt($int, $type);
    }
}

if (!function_exists('UnPackInt')) {
    //UnPackInt
    function UnPackInt($int, $type = 8)
    {
        return \app\Common\int_helper::UnPackInt($int, $type);
    }
}

if (!function_exists('GetBytes')) {
    //GetBytes
    function GetBytes($string)
    {
        return \app\Common\int_helper::getBytes($string);
    }
}

if (!function_exists('ToStr')) {
    //ToStr
    function ToStr($bytes)
    {
        return \app\Common\int_helper::toStr($bytes);
    }
}

if (!function_exists('HexToDecimal')) {
    //HexToDecimal
    function HexToDecimal($Hex)
    {
        return \app\Common\int_helper::HexToDecimal($Hex);
    }
}

function String2Hex($string)
{
    $hex = '';
    for ($i = 0; $i < strlen($string); $i++) {
        $hex .= dechex(ord($string[$i]));
    }
    return $hex;
}

function strtohex($data)
{
    $Srp6 = new Srp6();
    $data = $Srp6->BigInteger($data, 256)->toHex();
    return $data;
}

function hextostr($hex)
{
    $Srp6 = new Srp6();
    $data = $Srp6->BigInteger($hex, 16)->toString();
    return $data;
}

function randomNumber($Bytes_length = 6)
{
    return (new Math_BigInteger())->_random_number_helper($Bytes_length)->toHex();
}

function multidimensional_search($parents, $searched)
{
    if (empty($searched) || empty($parents)) {
        return false;
    }

    foreach ($parents as $key => $value) {
        $exists = true;
        foreach ($searched as $skey => $svalue) {
            $exists = ($exists && isset($parents[$key][$skey]) && $parents[$key][$skey] == $svalue);
        }

        if ($exists) {
            return $key;
        }
    }

    return false;
}

function deep_in_array($value, $array)
{
    foreach ($array as $item) {
        if (!is_array($item)) {
            if ($item == $value) {
                return true;
            } else {
                continue;
            }
        }

        if (in_array($value, $item)) {
            return true;
        } else if (deep_in_array($value, $item)) {
            return true;
        }
    }
    return false;
}

function msectime()
{
    list($msec, $sec) = explode(' ', microtime());
    $msectime         = (float) sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
}

//16进制小端字节序
function Littleendian($str)
{
    $Srp6 = new Srp6();
    return $Srp6->BigInteger(strrev($Srp6->BigInteger(pack('h*', $str), 256)->toHex()), 16);
}

function cut_str($str, $start = '', $end = '')
{
    if ($start) {
        $str = stristr($str, $start, false);
        $str = substr($str, strlen($start));
    }
    $end && $str = stristr($str, $end, true);
    return $str;
}

function gbktoutf8($str)
{
    return  mb_convert_encoding($str, 'utf8', 'gb2312');
}

function utf8togbk($str)
{
    return  mb_convert_encoding($str, 'gb2312', 'utf8');
}