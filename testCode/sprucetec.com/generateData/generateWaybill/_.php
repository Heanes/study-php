<?php
/**
 *
 * Tools
 * User: xiaofeng
 * Date: 2015/8/23
 * Time: 1:18
 */
// declare(strict_types=1);
defined('EMPTY_STRING') or define('EMPTY_STRING', strval(NULL));

/**
 * Class _ trait拼装类
 */
class _
{
    /* traitCli color*/
    const NORMAL            = "[0m";
    const BOLD              = "[1m";
    const UNDERSCORE        = "[4m";
    const REVERSE           = "[7m";
    const BLACK             = "[0;30m";
    const RED               = "[0;31m";
    const GREEN             = "[0;32m";
    const BROWN             = "[0;33m";
    const BLUE              = "[0;34m";
    const CYAN              = "[0;36m";
    const LIGHT_RED         = "[1;31m";
    const LIGHT_GREEN       = "[1;32m";
    const YELLOW            = "[1;33m";
    const LIGHT_BLUE        = "[1;34m";
    const MAGENTA           = "[1;35m";
    const LIGHT_CYAN        = "[1;36m";
    const WHITE             = "[1;37m";

    /* traitSql build_type */
    const SELECT = 'select';
    const INSERT = 'insert';
    const MULTI_INSERT = 5;
    const UPDATE = 'update';
    const UPDATE_SPECIAL = 'update_special';
    const DELETE = 'delete';
    const WHERE = 'where';
    const ORDER = 'order';


    const RE_UTF8CHINESE_ALNUM_ = '/^[\x{4e00}-\x{9fa5}a-zA-Z0-9_]+$/u'; // 汉字 大写 小写 下划线
    const RE_LNG_LAT = '/^[-+]?(180(\.0+)?|((1[0-7]\d)|([1-9]?\d))(\.\d+)?),\s*[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?)$/'; // 合法经纬度
    const RE_INTEGER = '/^[-+]?\d+$/';              // （包含正负号）整数
    const RE_FLOAT_2 = '/^[-+]?\d+(\.\d{1,2})?$/';  // （包含正负号）两位小数
    // 验证手机号
    const RE_CELLPHONE = '/^1[34578][0-9]{1}[0-9]{8}$/';

    use traitFunc;
    use traitSequence;
    use traitArray;
    use traitStr;
    use traitPassword;
    use traitSql;
    use traitComponent;
    use traitFile;
    use traitAsync;
    use traitUtils;
    use traitHttp;
    use traitCli;
    use traitDebug;
    use traitTmp;
    use traitExperimental;

    /**
     * Overwrite traitSql dbQuote
     * @param $str
     * @return string
     * @author xiaofeng
     */
    public static function dbQuote(/*string*/ $str) /*: string*/
    {
        // FIXME : 注释掉
        // return $str;
        return Yii::app()->db->quoteValue($str);
    }
}

/**
 * trait Tmp
 */
trait traitTmp
{

    /**
     * 强类型参数检查
     * @param  mixed $user_arg 待检测变量
     * @param mixed $default_arg 类型不符的默认值(以此变量类型为检测标准)
     * @return null
     * @author xiaofeng
     *
     * FIXME: 递归检测数组
     */
    public static function arg_check($user_arg, $default_arg)
    {
        // 参数类型检测 []
        $user_type = gettype($user_arg);
        $right_type = gettype($default_arg);
        if($user_type === $right_type) {
            return $user_arg;
        } else {
            /*
            if(!empty($traces[1]['function'])) {
                $traces = debug_backtrace();
                trigger_error("{$traces[1]['function']}: arguments must be $right_type, $user_type be given");
                unset($traces);
            }
            */
            return $default_arg;
        }
    }

    /**
     * 生成遍历某个数组执行特定功能的函数
     * @param  callable $callable 对数组每个元素执行的callable
     * @return Closure
     * @author xiaofeng
     */
    public static function map(callable $callable)/* : callable */
    {
        /**
         * @return mixed
         * @internal param array $target_array 遍历的数组
         * @internal param mixed $args 遍历回调函数接受的若干参数
         * @desc 将参数分开是为自解释
         */
        return function(/*array $target_array, array ...$args*/) use($callable)
        {
            $args = func_get_args();
            if(count($args)) {
                $target_array = array_shift($args);
                return array_map('array_map', $target_array, $args);
            }
            return null;
        };
    }

    /**
     * 让任意callable支持多参数
     * @param callable $callable 只支持一个参数
     * @return array 返回该callable返回值的数组
     * @author xiaofeng
     */
    public static function func_args(callable $callable)/* : callable */
    {
        return function(/*...$args*/) use($callable)
        {
            return array_map($callable, func_get_args());
        };
    }
}


/**
 * trait sequence
 */
trait traitSequence
{
    /**
     * xrange
     * @param  int         $start 序列初始元素
     * @param  int         $end   序列结束元素
     * @param  int         $step 步进，默认1
     * @return Generator
     * @author xiaofeng
     * 暂时不支持char
     */
    public static function xrange(/*int*/ $start, /*int*/ $end, /*int*/ $step = 1) /*: \Generator*/
    {
        if(!is_int($start)) {
            $start = 0;
        }
        if(!is_int($end)) {
            $end = 0;
        }
        if(!is_int($step) || $step <= 0) {
            $step = 1;
        }

        if(($start <= $end)) {
            while ($start <= $end) {
                yield $start;
                $start += $step;
            }
        } else if(($start >= $end)) {
            while ($start >= $end) {
                yield $start;
                $start -= $step;
            }
        }
    }

    /**
     * 按传入的序列，按照Excel表头字母的逻辑生成新的序列
     * @param  int    $len 生成序列的长度
     * @param  array  $seq 传入的子序列
     * @return array
     * @author xiaofeng
     * @FIXME 修改成返回\Generator
     */
    public static function seqRange(/*int*/ $len, array $seq = []) /*: array*/
    {
        if(!is_int($len) || $len <= 0) {
            $len = 1;
        }
        if(empty($seq)) {
            $seq = range('A', 'Z');
        }

        $result = [];
        $seq_count = count($seq);

        $i = 0;
        while (list($k, $v) = each($seq)) {
            if($i >= $len) {
                break;
            }

            $result[] = ($i < $seq_count ? '' : $result[intval($i / $seq_count) - 1] ) . $v;

            // reset cycle
            // if(($i+1)%$seq_count === 0) {
            if($k === ($seq_count - 1)) {
                reset($seq);
            }

            $i++;
        }

        return $result;
    }

    /**
     * 根据分页生成自增序列
     * @author xiaofeng
     */
    public static function createPageIndex(&$data, $page, $pageSize, $key = 'index')
    {
        if(!$data || !is_array($data)) {
            return;
        }

        if(isset($data[0][$key])) {
            trigger_error("$key has exist");
        }

        foreach($data as $k => &$row) {
            $row[$key] =  $k + 1 + ($page - 1) * $pageSize;
        }
        unset($row);
    }
}


/**
 * trait Utils
 */
trait traitUtils
{
    /**
     * 坐标验证
     * @param $coor
     * @return bool|string
     * @author xiaofeng
     */
    public static function coorFormat($coor)
    {
        if(!$coor || !is_string($coor)) {
            return false;
        }

        $coor = str_replace('，', ',', $coor);
        if(!preg_match(self::RE_LNG_LAT, $coor)) {
            return false;
        }

        $coorArr = explode(',', $coor);
        list($lng, $lat) = $coorArr;
        $lng = number_format($lng, 5);
        $lat = number_format($lat, 5);
        return "[$lng, $lat]";
    }
    /**
     * 获取类型zeroValue
     * @param $var
     * @return mixed
     * @author xiaofeng
     */
    public static function getZeroValue($var)
    {
        $zeroValueMap = [
            "boolean"       => false,
            "integer"       => 0,
            "double"        => 0.0,
            "string"        => '',
            "array"         => [],
            "object"        => new stdClass,
            "resource"      => 0,
            "NULL"          => null,
            "unknown type"  => null,
        ];

        return $zeroValueMap[gettype($var)];
    }

    /**
     * PRC 时间戳
     * @param int $ts
     * @return int
     * @author xiaofeng
     */
    public static function gmtime($ts = 0)
    {
        date_default_timezone_set('PRC');
        return ($ts === 0) ? time() : $ts;
    }

    /**
     * Numbers of days in given timestamp
     * @param null $timestamp
     * @return bool|string
     *
     * @author xiaofeng
     */
    public static function daysInMonth($timestamp = null)
    {
        return date("t", $timestamp ?: _::gmtime());
    }

    /**
     * @param $startTimestamp
     * @param $endTimestamp
     * @param string $intervalSpec
     * @return int
     *
     * @author xiaofeng
     */
    public static function countTimeInterval($startTimestamp, $endTimestamp, $intervalSpec = "P1D")
    {
        $dz = new DateTimeZone("Asia/Shanghai");
        $sDt = new DateTime("@$startTimestamp", $dz);
        $eDt = new DateTime("@$endTimestamp", $dz);
        // days
        $dp = new DatePeriod($sDt, new DateInterval($intervalSpec), $eDt);
        return iterator_count($dp);
    }

    /**
     * @param $type
     * @param $input
     * @return bool
     *
     * @author xiaofeng
     */
    public static function timeCheck($type, $input)
    {
        switch($type) {
            case "Y-m-d":
                $tmp = explode("-", $input);
                return (is_array($tmp) && count($tmp) === 3 && checkdate($tmp[1], $tmp[2], $tmp[0]));
            case "Y-m":
                $tmp = explode("-", $input);
                return (is_array($tmp) && count($tmp) === 2 && checkdate($tmp[1], 1, $tmp[0]));
        }
    }

    /**
     *
     * @param string $type
     * @param int|null $now
     * @return array|bool
     *
     * @author xiaofeng
     */
    public static function getTimePeriod($type, $now = null)
    {
        if($now !== null) {
            if(is_string($now)) {
                $now = strtotime($now);
            }
            assert(is_int($now));
        }

        // date_default_timezone_set('PRC');
        // $date = getdate();
        switch(strtolower($type)) {
            case 'today':
                // return [strtotime('today'), strtotime('tomorrow') - 1];
                return [strtotime('today'), self::gmtime()];

            case 'day':
                if($now === null) {
                    return [strtotime('today'), strtotime('tomorrow') - 1];
                } else {
                    $stime = strtotime("midnight", $now);
                    $etime = strtotime("tomorrow", $stime) - 1;
                    return [$stime, $etime];
                }

            case 'week':
                if($now === null) {
                    return [strtotime('monday this week'), self::gmtime()];
                } else {
                    // FIXME
                }

            case 'lastweek':
                // fixme
                break;

            case 'month':
                if($now === null) {
                    return [strtotime('first day of this month'), self::gmtime()];
                } else {
                    $stime = strtotime(date("Y-m-01", $now));
                    $etime = strtotime("next month", $stime) - 1;
                    return [$stime, $etime];
                }

            case '3days':
                if($now === null) {
                    // return [strtotime('-3 day'), self::gmtime()];
                    return [strtotime('-2 day', strtotime('today')), self::gmtime()];
                } else {
                    $midnight = strtotime("midnight", $now);
                    return [strtotime('-3 day', $midnight), $midnight - 1];
                }

            case '7days':
                if($now === null) {
                    // return [strtotime('-7 day'), self::gmtime()];
                    return [strtotime('-6 day', strtotime('today')), self::gmtime()];
                } else {
                    $midnight = strtotime("midnight", $now);
                    return [strtotime('-7 day', $midnight), $midnight - 1];
                }
        }

        return false;
    }

    /**
     * 生成指定长度随机串
     * @param int $length
     * @param bool|false $numeric
     * @return string
     */
    public static function random($length = 8, $numeric = false)
    {
        $seed = base_convert(md5(uniqid(mt_rand(), true)), 16, $numeric ? 10 : 35);
        //$seed = base_convert(md5(microtime().$_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
        //$seed = base_convert(base64_encode(uniqid(mt_rand(), true)), 16, $numeric ? 10 : 35);
        $seed = $numeric ? (str_replace('0', '', $seed).'012340567890') : ($seed.'zZ'.strtoupper($seed));
        $hash = '';
        $max = strlen($seed) - 1;
        for($i = 0; $i < $length; $i++) {
            $hash .= $seed{mt_rand(0, $max)};
        }
        return $hash;
    }

    /**
     * exception wrapper for json_encode
     * @param $value
     * @param int $options
     * @param int $depth
     * @return string
     * @throws _Exception
     * @author xiaofeng
     */
    public static function json_encode($value, $options = 0, $depth = 512)
    {
        $json = json_encode($value, $options, $depth);
        if($json !== false) {
            return $json;
        }

        switch (json_last_error()) {
            case JSON_ERROR_NONE:
            case JSON_ERROR_DEPTH:
            case JSON_ERROR_CTRL_CHAR:
            case JSON_ERROR_SYNTAX:
            case JSON_ERROR_STATE_MISMATCH:
            case JSON_ERROR_UTF8:
            default:
                throw new _Exception('json_encode error', _Code::JSON_ENCODE_ERROR);
        }
    }

    /**
     * 用于cookie加密 @20150831
     * >= 5.3 openssl extension
     * @param $str
     * @param $pwd
     * @return string
     * @author xiaofeng
     */
    public static function encrypt($str) {
        $iv = 'yunshankeji';
        $pwd = 'meixiansong';
        return base64_encode(openssl_encrypt(trim($str), 'aes-256-cbc', $pwd, OPENSSL_RAW_DATA, substr(md5($iv), 0, 16)));
    }

    /**
     * 用于cookie解密 @20150831
     * >= 5.3 openssl extension
     * @param $str
     * @param $pwd
     * @return string
     * @author xiaofeng
     */
    public static function decrypt($str) {
        $iv = 'yunshankeji';
        $pwd = 'meixiansong';
        return openssl_decrypt(base64_decode(trim($str)), 'aes-256-cbc', $pwd, OPENSSL_RAW_DATA, substr(md5($iv), 0, 16));
    }

    /**
     * 生成字符安全uniqid
     * @return string
     * @author xiaofeng
     */
    public static function uniqid() /*: string*/
    {
        // md5对随机性无影响，用来消除uniqid中的特殊字符
        return md5(uniqid());
    }

    /**
     * 变量清洗过滤器
     * @param $type
     * @param $var
     * @param array $filters
     * @return bool|mixed
     * @author xiaofeng
     */
    public static function filter(/*string*/ $type, $var, array $filters = [])
    {
        $filters += [
            'bool'      => [FILTER_VALIDATE_BOOLEAN],
            'int'       => [FILTER_SANITIZE_NUMBER_INT, FILTER_VALIDATE_INT],
            'float'     => [FILTER_SANITIZE_NUMBER_FLOAT, FILTER_VALIDATE_FLOAT],
            'double'    => [FILTER_SANITIZE_NUMBER_FLOAT, FILTER_VALIDATE_FLOAT],
            'string'    => [FILTER_SANITIZE_STRING],
            'email'     => [FILTER_SANITIZE_EMAIL, FILTER_VALIDATE_EMAIL],
            'url'       => [FILTER_SANITIZE_URL, FILTER_VALIDATE_URL],
            'ip'        => [FILTER_SANITIZE_STRING, FILTER_VALIDATE_IP],
            'html'      => [FILTER_SANITIZE_FULL_SPECIAL_CHARS],
            'callback'  => [FILTER_CALLBACK],
        ];

        $type = strtolower($type);
        if(!isset($filters[$type])) {
            trigger_error("filter type $type do not support");
            return false;
        }

        if(!is_array($filters[$type])) {
            $filters[$type] = [ $filters[$type] ];
        }

        foreach ($filters[$type] as $flag) {
            $var = filter_var($var, $flag);
            if($var === false) {
                return false;
            }
        }
        return $var;
    }

    /**
     * 从二维数组创建层次结构
     * @param array $array 二维数组
     * @param string $sonId
     * @param string $parentId
     * @param string $childsId 子元素key名称
     * @return array|bool
     * @author xiaofeng
     * 成功返回 [层次结构，二维结构]数组，失败返回false
     */
    public static function createHierarchy(array $array, $sonId, $parentId, $childsId = 'childs')
    {
        $hierarchy = [];
        $refs = [];
        if(empty($array)) {
            return false;
        }
        $fields = array_keys($array[0]);

        foreach($array as $row) {
            if(!is_array($row) || !isset($row[$sonId]) || !isset($row[$parentId])) {
                return false;
            }

            $ref = &$refs[$row[$sonId]];
            foreach($fields as $f) {
                $ref[$f] = $row[$f];
            }

            if($row[$parentId] == 0) {
                $hierarchy[$row[$sonId]] = &$ref;
            } else {
                $refs[$row[$parentId]][$childsId][$row[$sonId]] = &$ref;
            }
        }

        return [$hierarchy, $refs];
    }

    public static function grRound($number, $digit = 2)
    {
        return round($number, $digit);
    }
}




/**
 * trait Sql
 * sql辅助类
 *
 * 20150906 03:08 添加:绑定占位符字段后缀，避免重复
 * FIXME remove build where
 * FIXME 用异常重写错误 _Exception
 * FIXME 解决所有bindArray key重复问题！！！
 * FIXME 传入bindArray需要函数保证不重新赋值，只添加！！！！
 * FIXME 拆分traitSql 为sqlbuild 与 sqlbindbuild 两个trait
 * FIXME 拆分buildArray 为若干个build方法，buildSelect，buildInsert，buildUpdate
 */
trait traitSql
{
    /* build_type */
    /*
    const SELECT = 1;
    const INSERT = 2;
    const UPDATE = 3;
    const DELETE = 4;
    const MULTI_INSERT = 5;
    const WHERE = 6;
    */

    /**
     * 转义
     * @param string $str
     * @return string
     * @author xiaofeng
     */
    /* abstract */ public static function dbQuote(/*string*/ $str) /*: string*/
    {

        if(class_exists('Yii')) {
            return Yii::app()->db->quoteValue($str);
        }

        /*
         * PDO quote
        if(($str = $pdoIns->quote($str, PDO::PARAM_STR)) !== false) {
            return $str;
        }
        */

        /*
        $quote_search = ['\\', "\0", "\n", "\r", "'", '"', "\x1a"];
        $quote_replace = ['\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'];
        return str_replace($quote_search, $quote_replace, $str);
        */
        return addcslashes(str_replace("'", "''", $str), "\000\n\r\\\032");
    }

    /**
     * 根据类型转义
     * @param $var
     * @return mixed
     * @author xiaofeng
     */
    public static function quote($var) /*: string*/
    {
        if(is_string($var)) {
            return "'" . self::dbQuote($var) . "'";
        } else if (is_bool($var)) {
            // 数据库bool字段使用01表示
            return $var ? 1 : 0;
        } else if (is_null($var)) {
            return 'NULL'; // 数据库字段尽量设置成 not null
        } else {
            return $var;
        }
    }

    /**
     * (:占位符)必须使用此方法往bindArray添加bindKey与bindVal
     * ！！！必须先push再拼接字符串
     * 尽量使用非引用而直接返回bindKey的getBindkey方法~...
     * @param $bindArray
     * @param $bindKey
     * @param $bindValue
     * @author xiaofeng
     *
     * 20150911 FIXBUG
     */
    public static function bindArrayPush(&$bindArray, &$bindKey, $bindValue)
    {
        if(!$bindArray) {
            $bindArray = [];
        }

        // bind占位符不支持.
        $bindKey = ':' . str_replace('.', '_', $bindKey);

        if(isset($bindArray[$bindKey])) {
            $bindKey .= '_r' . mt_rand(0, 10);
            self::bindArrayPush($bindArray, $bindKey, $bindValue);
        } else {
            $bindArray[$bindKey] = $bindValue;
        }
    }

    /**
     * 将val压到bindArray中，返回新的可用于拼接sql的key
     * @param $bindArray
     * @param $bindKey
     * @param $bindValue
     * @param bool|true $clear 用于递归的参数...
     * @return mixed|string
     * @author xiaofeng
     */
    public static function getBindkey(&$bindArray, /*string*/ $bindKey, $bindValue, $clear = true)
    {
        if($bindArray === null) {
            $bindArray = [];
        }
        if($clear) {
            $bindKey = str_replace('.', '_', trim($bindKey));
            $bindKey = str_replace(' ', '', trim($bindKey));
        }
        if($bindKey[0] !== ':') {
            $bindKey = ':' . $bindKey;
        }
        if(isset($bindArray[$bindKey])) {
            $bindKey .= '_r' . mt_rand(0, 10);
            return self::getBindkey($bindArray, $bindKey, $bindValue, false);
        } else {
            $bindArray[$bindKey] = $bindValue;
        }
        return $bindKey;
    }

    /**
     * @param $bindArray
     * @param $array
     * @author xiaofeng
     */
    public static function getBindkeys(&$bindArray, $array)
    {
        $bindKeys = [];
        foreach($array as $bk => $bv) {
            /*
            $_bk = $bk;
            $_bv = $bv;
            self::bindArrayPush($bindArray, $_bk, $_bv);
            */
            $bindKeys[] = self::getBindkey($bindArray, $bk, $bv);
        }
        return $bindKeys;
    }

    /**
     * 构建select insert update 对应的array
     * @param  traitSql    $build_type
     * @param  array  $array
     * @return string
     * @author xiaofeng
     */
    public static function buildArray(/*Enum*/ /*int*/ $build_type, array $array) /*: string*/
    {
        // select allow empty array
        if(empty($array) && $build_type !== self::SELECT) {
            // trigger_error(__METHOD__ . ": filed must not be empty");
            return EMPTY_STRING;
        }

        switch ($build_type) {

            // field array
            case self::SELECT:
                return empty($array) ? '*' : implode(', ', $array);

            // assoc array
            case self::INSERT:
                $fields = implode(', ', array_keys($array));
                $values = implode(', ', array_map([__CLASS__, 'quote'], array_values($array)));
                return " ($fields) VALUES ($values) ";

            case self::MULTI_INSERT:
                // FIXME
                trigger_error('not impl');

                // assoc array
            case self::UPDATE:
                $expArr = [];
                foreach ($array as $field => $value) {
                    $expArr[] = "$field = " . self::quote($value);
                }
                return implode(', ', $expArr);
            case self::WHERE:
                // FIXME
                trigger_error('not impl');
            case self::ORDER:
                trigger_error('not impl');
            default:
                return EMPTY_STRING;
        }
    }

    /**
     * 构建stmt查询语句
     * @param $build_type
     * @param array $array
     * @param array $bindArray OUT_PARAM
     * @param string $placeholder ':'占位符表示返回:fieldName方式查询语句，否则直接返回占位符
     * @param string $relation build where时用到的关系条件
     * @return string
     * @author xiaofeng
     */
    public static function buildArrayBind(/*Enum*/ /*int*/ $build_type, array $array, &$bindArray, /*string*/ $placeholder = ':', /*string*/$relation = 'AND')
    {
        /**
         * 缺点：
         * PDO::PARAM_BOOL;
         * PDO::PARAM_INT;
         * PDO::PARAM_LOB;
         * PDO::PARAM_STR;
         * PDO::PARAM_NULL;
         * 以上强类型 bindParam与bindValue无法使用
         * 全部以PDO::PARAM_STR对待
         * or 根据gettpe(var)决定bindtype
         */

        $build_type = strtolower($build_type);
        // select allow empty array
        if(empty($array) &&  !in_array($build_type, [self::SELECT, self::WHERE])) {
            // trigger_error(__METHOD__ . ": filed must not be empty");
            return EMPTY_STRING;
        }

        switch ($build_type) {

            // field array
            case self::SELECT:
                return empty($array) ? '*' : implode(', ', $array);

            // assoc array
            case self::INSERT:
                $fields = array_keys($array);
                $fieldsStr = implode(', ', $fields);

                $_ = array_values($array);

                if($placeholder === ':') {
                    // :field 占位符方式
                    /*
                    $fieldsPlaceholders = array_map(function($f) { return ":{$f}_insert"; }, $fields); // add postfix _insert
                    $valuesPlaceholdersStr = implode(', ', $fieldsPlaceholders);
                    // $bindArray = array_combine($fieldsPlaceholders, $bindArray);
                    $_ = array_combine($fieldsPlaceholders, $_);

                    // FIXBUG
                    self::getBindkeys($bindArray, $_);
                    */

                    $valuesPlaceholdersStr = implode(', ', self::getBindkeys($bindArray, $array));

                } else {
                    // ?占位符方式
                    $valuesPlaceholdersStr = implode(', ', array_fill(0, count($fields), $placeholder));
                }
                return " ($fieldsStr) VALUES ($valuesPlaceholdersStr) ";

            case self::MULTI_INSERT:
                $fields = array_keys($array[0]);
                $fieldsStr = implode(', ', $fields);

                if($placeholder === ':') {

                    $insertArr = [];
                    foreach($array as $k => $pairs) {
                        $i = 0;
                        $tmpArr = [];
                        foreach($pairs as $field => $value) {
                            /*
                            $bindKey = ":{$field}_{$k}_{$i}_multi_insert";
                            self::bindArrayPush($bindArray, $bindKey, $value);
                            $tmpArr[] = $bindKey;
                            */
                            $tmpArr[] = self::getBindkey($bindArray, "{$field}_{$k}_{$i}", $value);
                            $i++;
                        }
                        $insertArr[] = '(' . implode(',', $tmpArr)  . ')';
                    }
                    $valuesPlaceholdersStr = implode(',', $insertArr);

                } else {

                    // $values = array_reduce($array, function($carry, $row){ array_push($carry, array_values($row)); }, []);
                    foreach($array as $value) {
                        foreach(array_values($value) as $item) {
                            $bindArray[] = $item;
                        }
                    }
                    $valuesPlaceholdersStr = '(' . implode('), (', array_fill(0, count($array), implode(',', array_fill(0, count($fields), '?')))) . ')';

                }

                return " ($fieldsStr) VALUES $valuesPlaceholdersStr ";

            // assoc array
            case self::UPDATE:
                if($placeholder === ':') {
                    /*
                    // add postfix _update
                    $_ = array_combine(array_map(function($f) { return ":{$f}_update"; }, array_keys($array)), array_values($array));
                    self::getBindkeys($bindArray, $_);
                    return implode(', ', array_map(function($f) { return "$f = :{$f}_update";}, array_keys($array)));
                    */

                    $tmpArr = [];
                    foreach(array_combine(array_keys($array), self::getBindkeys($bindArray, $array))
                            as $bfield => $bvalue) {
                        $tmpArr[] = "$bfield = $bvalue";
                    }
                    return implode(', ', $tmpArr);
                }

                $bindArray = array_values($array);
                return implode(', ', array_map(function($f) use($placeholder) { return "$f = $placeholder";}, array_keys($array)));
            case self::UPDATE_SPECIAL:
                if($placeholder === ':') {
                    $tmpArr = [];
                    foreach(array_combine(array_keys($array), self::getBindkeys($bindArray, $array))
                            as $bfield => $bvalue) {
                        $tmpArr[] = "$bfield = $bfield +  $bvalue";
                    }

                    return implode(', ', $tmpArr);
                }

                $bindArray = array_values($array);
                return implode(', ', array_map(function($f) use($placeholder) { return "$f = $placeholder";}, array_keys($array)));

            case self::WHERE:
                return self::buildWhereBind($array, $bindArray, $relation, $placeholder);
            case self::ORDER:
                $orderByArr = [];
                foreach($array as list($order, $by)) {
                    if(!in_array(strtolower($by), ['asc', 'desc'], true)) {
                        $by = 'ASC';
                    }
                    // FIXBUG Column/table names are part of the schema and cannot be bound.
                    /*
                    $orderByArr[] = _::getBindkey($bindArray, "sort_by_$order", $order) . ' ' .
                        _::getBindkey($bindArray, "sort_order_$order", $by);
                    */

                    // FIXME 应该检测$order字段是否为column字段,防止sql注入
                    /*
                    if(!in_array($order, $allowColumns)) {
                        throw new Exception
                    }
                    */
                    $orderByArr[] = "$order $by";
                }
                return /*" ORDER BY " . */implode(' , ', $orderByArr);
            default:
                return EMPTY_STRING;
        }
    }

    /**
     * 构建like表达式
     * @param  string       $field
     * @param  string       $expression
     * @param  bool         $not
     * @return string
     * @author xiaofeng
     */
    public static function buildLike(/*string*/ $field, /*string*/ $expression, /*bool*/ $not = false) /*: string*/
    {
        if(!is_string($field) || !$field || !is_string($expression)) {
            trigger_error(__METHOD__ . ': ARGUMENT ERROR');
            return EMPTY_STRING;
        }

        $expression = str_replace(['_', '%'], ["\_", "\%"], $expression);
        $expression = str_replace([chr(0) . "\_", chr(0) . "\%"], ['_', '%'], $expression);
        $expression = self::dbQuote($expression);
        $not = $not ? 'NOT' : '';
        return "$field $not LIKE '$expression'";
    }

    /**
     * @param $field
     * @param $value
     * @param $bindArray
     * @param bool|false $not
     * @param string    $placeholder
     * @return string
     * @author xiaofeng
     */
    public static function buildLikeBind(/*string*/ $field, /*string*/ $value, &$bindArray, /*bool*/ $not = false,  /*string*/ $placeholder = '?') /*: string*/
    {
        /*
        if(!is_string($field) || !$field || !is_string($value)) {
            trigger_error(__METHOD__ . ': ARGUMENT ERROR');
            return EMPTY_STRING;
        }
        */

        // FIXME : 处理$value
        $not = $not ? 'NOT' : '';
        if($placeholder === ':') {
            /*
            $bindKey = ":{$field}_like"; // add postfix _like
            self::bindArrayPush($bindArray, $bindKey, '%' . $value . '%');
            return "$field $not LIKE $bindKey";
            */
            return "$field $not LIKE " . self::getBindkey($bindArray, $field, '%' . $value . '%');
        } else {
            $bindArray[] = '%' . $value . '%';
            return "$field $not LIKE ?";
        }
    }

    /**
     * 构建in表达式
     * @param  string       $field
     * @param  array        $array
     * @param  bool         $not
     * @param  string       $placeholder
     * @return string
     * @author xiaofeng
     */
    public static function buildIn(/*string*/ $field, /*array*/ $array, /*bool*/ $not = false,  /*string*/ $placeholder = '?') /*: string*/
    {
        if(!is_string($field) || !$field || empty($array)) {
            return EMPTY_STRING;
        }

        if(!is_array($array)) {
            $array = [$array];
        }

        if (count($array) === 1) {
            return $field . ($not ? ' <> ' : ' = ') . self::quote($array);
        }

        $not = $not ? 'NOT' : '';
        $values = implode(', ', array_map([__CLASS__, 'quote'], $array));
        return "$field $not IN ($values)";
    }

    /**
     * 构建in表达式stmt
     * @param  string       $field
     * @param  array        $array OUT_PARAM
     * @param  array        $bindArray
     * @param  bool         $not
     * @param  string       $placeholder
     * @return string
     * @author xiaofeng
     * 2015-09-06 02:24 添加占位符选项
     */
    public static function buildInBind(/*string*/ $field, /*array*/ $array, &$bindArray, /*bool*/ $not = false, /*string*/ $placeholder = ':') /*: string*/
    {
        /*
        if(!is_string($field) || !$field || empty($array)) {
            trigger_error(__METHOD__ . ': ARGUMENT ERROR');
            return EMPTY_STRING;
        }
        */

        if(!is_array($array)) {
            $array = [$array];
        }

        if($placeholder === ':') {

            if (count($array) === 1) {
                /*
                $bindKey = ":{$field}_in"; // add postfix _in
                self::bindArrayPush($bindArray, $bindKey, $array[0]);
                return $field . ($not ? ' <> ' : ' = ') . $bindKey;
                */
                return $field . ($not ? ' <> ' : ' = ') . self::getBindkey($bindArray, $field, $array[0]);
            }

            $inArr = [];
            foreach($array as $k => $v) {
                /*
                $bindKey = ":{$field}_{$k}_in"; // add postfix _in
                self::bindArrayPush($bindArray, $bindKey, $v);
                $inArr[] = $bindKey;
                */
                $inArr[] = self::getBindkey($bindArray, "{$field}_{$k}", $v);
            }
            $valuesStr = implode(', ', $inArr);

        } else {

            // FIXME to test
            if(is_array($bindArray)) {
                $bindArray = $bindArray + $array;
            } else {
                $bindArray = $array;
            }

            if (count($array) === 1) {
                return $field . ($not ? ' <> ' : ' = ') . "?";
            }
            $valuesStr = implode(', ', array_fill(0, count($bindArray), '?'));
        }

        $not = $not ? 'NOT' : '';
        return "$field $not IN ($valuesStr)";
    }

    public static function buildWhere(array $cond = [], $relation = 'AND')
    {
        // FIXME:
        trigger_error('not impl');
    }

    /**
     * 构建where查询条件
     * @param array $cond 条件数组(like 与 not like 参数不含有%)
     * @param array $bindArray 返回bind数组
     * @param string $relation 关系 ['AND', 'OR']
     * @param string $placeholder
     * @return string
     * @throws _Exception
     * @author xiaofeng
     */
    public static function buildWhereBind(array $cond = [], &$bindArray = [], $relation = 'AND', $placeholder = ':')
    {
        if(empty($cond)) {
            return $relation === 'AND' ? ' 1 = 1 ' : ' 1 = 0 ';
        }

        $condArr = [];
        $relation = strtoupper($relation);
        foreach($cond as $key => $subCond) {
            if(in_array(strtoupper($key), ['AND', 'OR'], true)) {
                $condArr[] = '(' . self::buildWhereBind($subCond, $bindArray, $key, $placeholder) . ')';
                continue;
            }

            if(($_c = count($subCond)) !== 3) {
                throw new _Exception("wrong where condition array: 3 items of subcond is excepted, {$_c} was given", _Code::SQL_BUILD_ERROR);
            }

            list($field, $subRel, $value) = $subCond;
            switch(strtoupper($subRel)) {
                case 'LIKE':
                    $condArr[] = self::buildLikeBind($field, $value, $bindArray, false, $placeholder);
                    break;
                case 'NOT LIKE':
                    $condArr[] = self::buildLikeBind($field, $value, $bindArray, true, $placeholder);
                    break;
                case 'IN':
                    $condArr[] = self::buildInBind($field, $value, $bindArray, false, $placeholder);
                    break;
                case 'NOT IN':
                    $condArr[] = self::buildInBind($field, $value, $bindArray, true, $placeholder);
                    break;
                default:
                    if($placeholder === '?') {
                        $condArr[] = "$field $subRel ?";
                        $bindArray[] = $value;
                    } else if($placeholder === ':') {
                        /*
                        $bindKey = ":{$field}_where"; // add postfix _where
                        self::bindArrayPush($bindArray, $bindKey, $value);
                        $condArr[] = "{$field} {$subRel} {$bindKey}";
                        */
                        $condArr[] = "{$field} {$subRel} " . self::getBindkey($bindArray, $field, $value);
                    }
            }
        }
        return ' ' . implode(" $relation ", $condArr) . ' ';
    }

    /**
     * 检测变量在pdobind中的类型
     * @param string $var
     * @return int PDOtype
     */
    public static function getPdoType($var)
    {
        static $map = [
            'boolean'   =>PDO::PARAM_BOOL,
            'integer'   =>PDO::PARAM_INT,
            'string'    =>PDO::PARAM_STR,
            'resource'  =>PDO::PARAM_LOB,
            'NULL'      =>PDO::PARAM_NULL,
        ];
        $type = gettype($var);
        return isset($map[$type]) ? $map[$type] : PDO::PARAM_STR;
    }
}

/**
 * trait traitDb
 */
trait traitComponent
{
    /**
     * 生成数据字典表格
     * @param $dbhost
     * @param $dbuser
     * @param $dbpwd
     * @param $dbname
     * @return string
     * @author xiaofeng
     */
    public static function dbDict($dbhost, $dbuser, $dbpwd, $dbname)
    {
        $sql = <<<SQL
select distinct
a.TABLE_SCHEMA as '数据库' ,
a.TABLE_NAME as '表名',
a.TABLE_COMMENT as '表说明',
a.COLUMN_NAME as '字段名',
a.COLUMN_TYPE as '类型长度',
a.COLUMN_COMMENT as '字段说明',
IF(a.IS_NULLABLE='yes', '√', '✕')  as '允许空值',
case when a.COLUMN_DEFAULT='' then 'EMPTY STRING' when ISNULL(a.COLUMN_DEFAULT) then 'NULL' else a.COLUMN_DEFAULT end  as '默认值',
IF(a.EXTRA='auto_increment', '√', '✕')  as '自动递增',
IF(b.CONSTRAINT_NAME='PRIMARY', '√', '✕')  as '主键',
a.CHARACTER_SET_NAME as '字符集',
a.COLLATION_NAME as '排序规则'
# ,c.CONSTRAINT_NAME  as '外键名',
# c.REFERENCED_TABLE_NAME as '关联父表',
# c.REFERENCED_COLUMN_NAME as '父表字段',
# d. CONSTRAINT_NAME as '索引名称'
from
(
	SELECT
		T.TABLE_COMMENT,
		C.TABLE_SCHEMA,
		C.TABLE_NAME,
		C.COLUMN_NAME,
		C.COLUMN_TYPE,
		C.COLUMN_COMMENT,
		C.IS_NULLABLE,
		C.COLUMN_DEFAULT,
		C.EXTRA,
		C.CHARACTER_SET_NAME,
		C.COLLATION_NAME
	FROM
		information_schema.`COLUMNS` AS C
	JOIN information_schema.`TABLES` AS T ON C.TABLE_SCHEMA = T.TABLE_SCHEMA
	AND C.TABLE_NAME = T.TABLE_NAME
)
-- INFORMATION_SCHEMA.COLUMNS
as a
left join (select CONSTRAINT_NAME,TABLE_NAME table_name2,COLUMN_NAME col_name2 from INFORMATION_SCHEMA.KEY_COLUMN_USAGE where  CONSTRAINT_NAME='PRIMARY' and TABLE_SCHEMA = @dbname) as b
on a.TABLE_NAME=b.table_name2 and a.COLUMN_NAME=b.col_name2
left join (select CONSTRAINT_NAME,REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME,TABLE_NAME table_name3,COLUMN_NAME col_name3 from INFORMATION_SCHEMA.KEY_COLUMN_USAGE where REFERENCED_COLUMN_NAME!='' and TABLE_SCHEMA = @dbname) as c
on a.TABLE_NAME=c.table_name3 and a.COLUMN_NAME=c.col_name3
left join (select CONSTRAINT_NAME,REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME,TABLE_NAME table_name4,COLUMN_NAME col_name4 from INFORMATION_SCHEMA.KEY_COLUMN_USAGE where REFERENCED_COLUMN_NAME is null and CONSTRAINT_NAME!='PRIMARY' and TABLE_SCHEMA = @dbname) as d
on a.TABLE_NAME=d.table_name4 and a.COLUMN_NAME=d.col_name4
where a.TABLE_SCHEMA = "{$dbname}"
ORDER BY a.TABLE_NAME ASC;
SQL;

        $dsn = "mysql:dbname=$dbname;charset=utf8;host=$dbhost";
        try {
            $conn = new PDO($dsn, $dbuser, $dbpwd);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }
        $result = $conn->query($sql, PDO::FETCH_ASSOC);
        if(!$result) {
            return EMPTY_STRING;
        }

        $dict = [];
        foreach ($result as $row) {
            if (!isset($dict[$row['表名']])) {
                $dict[$row['表名']] = [];
            }
            $dict[$row['表名']][] = $row;
        }

        $tableContent = [];
        foreach ($dict as $tableName => $tableColumns) {
            $tmp = $tableColumns[0];
            $tableContent[] = "<p><h1>{$tmp['表说明']}</h1></p>";
            $tableContent[] = "<p><h2>{$tmp['表名']}</h2></p>";
            $tableContent[] = '<table width="100%" cellspacing="0" cellpadding="0" border="0">';

            foreach ($tableColumns as &$columns) {
                unset($columns['数据库']);
                unset($columns['表名']);
                unset($columns['表说明']);
            }
            unset($columns);

            // header
            $tableContent[] =  '<tr>';
            foreach ($tableColumns[0] as $k => $_) {
                $tableContent[] = "<th>$k</th>";
            }
            $tableContent[] = '</tr>';

            // body
            foreach ($tableColumns as $columns) {
                $tableContent[] =  '<tr>';
                foreach ($columns as $v) {
                    $tableContent[] =  "<td>$v</td>";
                }
                $tableContent[] =  '</tr>';
            }
            $tableContent[] =  '</table>';
        }

        $tableContent = implode(EMPTY_STRING, $tableContent);

        return <<<STYLE
<style>
.dict h1{font-size:20px;border-top: 1px solid rgb(215, 215, 215);padding-top: 15px;}
.dict h2{font-size:15px;}
.dict table {border: 1px solid #c6ccd2;margin-bottom: 20px;table-layout: fixed;text-align: center;}
.dict table th { background: #f3f4f5; color: #000; font-size: 14px; font-weight: 700; height: 29px; text-align: center;}
.dict table th, .dict table td { border: 1px solid #e8eaec; padding: 0 10px; vertical-align: middle;}
.dict table td { line-height: 20px; padding: 7px 10px;}
</style>
<div class="dict">
{$tableContent}
</div>
STYLE;

    }
}

/**
 * trait Debug
 */
trait traitDebug
{
    /**
     * @param $file
     * @param $n
     * @return array
     */
    public static function tail($file, $n) {
        assert($n > 0);
        $lines 	= [];
        $fp 	= fopen($file, 'r');
        $offset = -2;
        $ch 	= '';
        while($n > 0) {
            while($ch != "\n") {
                fseek($fp, $offset, SEEK_END);
                $ch = fgetc($fp);
                $offset--;
            }
            $ch = '';
            $n--;

            $lines[] = fgets($fp);
        }
        fclose($fp);
        return $lines;
    }

    public static function log($msg, $file, $logLevel = 'trace', $traceLevel = 3, $maxsize = 1048576, $maxfiles = 5)
    {
        if($traceLevel) {
            $traces = debug_backtrace();
            $count = 0;
            foreach($traces as $trace) {
                if(isset($trace['file'], $trace['line'])) {
                    $msg.="\nin " . $trace['file'] . ' (' . $trace['line'] . ')';
                    if(++$count >= $traceLevel)
                        break;
                }
            }
        }

        $msg = @date('Y/m/d H:i:s', time())." [$logLevel] $msg\n";

        $fp = @fopen($file, 'a');
        @flock($fp, LOCK_EX);
        if(@filesize($file) > $maxsize) {
            for($i = $maxfiles; $i>0; --$i) {
                $rotateFile = $file . '.' . $i;
                if(is_file($rotateFile)) {
                    if($i === $maxfiles) {
                        @unlink($rotateFile);
                    } else {
                        @rename($rotateFile,$file.'.'.($i+1));
                    }
                }
            }

            if(is_file($file)) {
                @rename($file,$file.'.1');
            }

            @flock($fp,LOCK_UN);
            @fclose($fp);
            @file_put_contents($file, $msg, FILE_APPEND | LOCK_EX);
        } else {
            @fwrite($fp, $msg);
            @flock($fp, LOCK_UN);
            @fclose($fp);
        }
    }

    /**
     * 简易assert
     * @param callable $condFunc
     * @param $msg
     * @author xiaofeng
     */
    public static function assert(callable $condFunc, /*string*/ $msg)
    {
        if($condFunc() !== true) {
            echo "ASSERT FAILED" . PHP_EOL;
            debug_print_backtrace();
            trigger_error($msg);
            exit(-1);
        }
    }

    /**
     * 导出静态方法
     * @param $class
     * @param $method
     * @return Closure
     * @author xiaofeng
     */
    public static function staticExport(/*string*/ $class, /*string*/ $method) /*: Closure*/
    {
        return function(/*...$args*/) use($class, $method){
            $refMethod = new /*\*/ReflectionMethod($class, $method);
            $args = func_get_args();
            if (/*$refMethod->isPublic() && */$refMethod->isStatic()) {
                return $refMethod->invokeArgs(null, $args);
            } else {
                trigger_error('not impl');
                return false;
            }
        };
    }

    /**
     * 输出当前微秒时间
     * @return float
     * @author xiaofeng
     */
    public static function now() /*: float*/ {
        return microtime(true);
    }

    /**
     * 生成一个断点计时器
     * @return Closure
     * @author xiaofeng
     */
    public static function watch() /*: callable*/ {
        $backtrace = [];
        $lastPoint = self::now();

        /**
         * @param  string $desc 计时断点描述
         * @return array 统计数组
         */
        return function(/*string*/ $desc = '') use(&$backtrace, &$lastPoint) {
            $duration = self::now() - $lastPoint;

            // $trace = debug_backtrace()[1];
            // $key = sprintf('%s@line-%d', $trace['file'], $trace['line']);
            $backtrace[/*$key*/] = [
                'desc' => $desc,
                'duration' => sprintf('%fms', $duration * 1000),
            ];

            $lastPoint = self::now();
            return $backtrace;
        };
    }

    /**
     * 针对静态方法的简易测试框架
     * @param $classOrObj string|object 需测试类名称或者对象实例
     * @param $testClassOrObj string|object 测试用例类名称或者对象实例
     * @param string $methodMatchRegPostfix 测试用例匹配方法正则后缀 {待测试方法名称+正则后缀} 默认匹配用例中_Test+数字结尾方法
     * @return array
     * @author xiaofeng
     */
    public static function staticTest($classOrObj, $testClassOrObj, $methodMatchRegPostfix = "_test\w*") /*: array*/
    {
        $result = [
            'uncover'   => [], // 未覆盖
            'success'   => [], // 测试成功
            'fail'      => [], // 测试失败
        ];


        // 获取待测试类信息 只针针对public static 做测试
        $methodFilter = /*\*/ReflectionMethod::IS_PUBLIC | /*\*/ReflectionMethod::IS_STATIC;
        $reflectionClass = new ReflectionClass($classOrObj);
        $methods = $reflectionClass->getMethods($methodFilter);
        $methodNames = array_column($methods, 'name');
        $className = $reflectionClass->getName();
        $methodsCount = count($methods);

        // 获取测试用例信息
        $testMethodFilter = /*\*/ReflectionMethod::IS_PUBLIC;
        $testReflectionClass = new ReflectionClass($testClassOrObj);
        $testMethods = $testReflectionClass->getMethods($testMethodFilter);
        $testMethodNames = array_column($testMethods, 'name');
        // $testClassName = $testReflectionClass->getName();
        // 如果未实例化则实例化
        $testClassIns = is_string($testClassOrObj) ? $testReflectionClass->newInstance() : $testClassOrObj;

        if(!$methodsCount) {
            trigger_error($className . 'no method to test');
            return [];
        }

        // 执行测试用例
        foreach($methodNames as $methodName) {

            // 匹配测试用例
            $methodNameReg = sprintf("/^%s$/", $methodName . $methodMatchRegPostfix);
            $testUnits = array_filter($testMethodNames,
                function($name) use($methodNameReg) {
                    return preg_match($methodNameReg, $name);});

            // 未覆盖
            if(!$testUnits) {
                $result['uncover'][] = $methodName;
                continue;
            }

            // 执行测试用例
            foreach ($testUnits as $testMethodName) {
                $ret = [ 'ok' => false, 'e' => null, 'out' => EMPTY_STRING];

                ob_start();
                try {
                    // 静态调用无法执行非静态方法
                    // $ret['ok'] =  call_user_func([$testClassName, $testMethodName]);
                    $ret['ok'] =  call_user_func([$testClassIns, $testMethodName]);
                } catch (Exception $e) {
                    $ret['e'] = $e;
                } /* catch (RuntimeException $re) {} */
                $ret['out'] = ob_get_clean(); // 当前测试方法的输出内容

                $result{$ret['ok'] ? 'success' : 'fail'}[$testMethodName] = $ret;
            }
        }

        $result['coverrate'] = 1 - round(count($result['uncover']) / $methodsCount, 2);
        return $result;
    }

    /**
     * static_test 的简易封装
     * @param $classNameOrObj
     * @param Object $testObj 测试类实例
     * @param string $postfix 如果testObj为null，则拼接后缀实例化测试用例
     * @return array 去除成功用例的结果
     * @author xiaofeng
     * 可选传入测试用例方便做一些初始化工作
     */
    public static function test($classNameOrObj, $testObj = null, $postfix = '_test') /*: array*/
    {
        $className = is_string($classNameOrObj) ? $classNameOrObj : (new /*\*/ReflectionClass($classNameOrObj))->getName();
        // 实例或者用例类名
        $testObj = $testObj === null ? $className . $postfix : $testObj;
        $result = self::staticTest($className, $testObj/*, '_test\d*'*/);
        // $result['successlist'] = array_keys($result['success']);
        unset($result['success']);
        return $result;
    }
}

trait traitBitwiseFlag
{
    public static function isFlagSet($flag, $flags) {
        return (($flags & $flag) === $flag);
    }

    public static function setFlag($flag, $val, &$flags) {
        if($val) {
            $flags |= $flag;
        } else {
            $flags &= ~$flag;
        }
    }

    // (FLAG_ALL & ~FLAG_A) === (FLAG_ALL ^ FLAG_A)
    private static $FLAG_ALL = 0b111;
    private static $FLAG_A   = 0b1;
    private static $FLAG_B   = 0b10;
    private static $FLAG_C   = 0b100;

    public static function testFlag() {
        $flags = 0b0;

        var_dump(self::isFlagSet(self::$FLAG_A, $flags));
        self::setFlag(self::$FLAG_A, true, $flags);
        var_dump(self::isFlagSet(self::$FLAG_A, $flags));

        var_dump(self::isFlagSet(self::$FLAG_B, $flags));
        self::setFlag(self::$FLAG_B, true, $flags);
        var_dump(self::isFlagSet(self::$FLAG_B, $flags));

        var_dump(self::isFlagSet(self::$FLAG_C, $flags));
        self::setFlag(self::$FLAG_C, true, $flags);
        var_dump(self::isFlagSet(self::$FLAG_C, $flags));
    }

    public static function testFlag1() {
        $flags = self::$FLAG_ALL;

        var_dump(self::isFlagSet(self::$FLAG_A, $flags));
        self::setFlag(self::$FLAG_A, false, $flags);
        var_dump(self::isFlagSet(self::$FLAG_A, $flags));

        var_dump(self::isFlagSet(self::$FLAG_B, $flags));
        self::setFlag(self::$FLAG_B, false, $flags);
        var_dump(self::isFlagSet(self::$FLAG_B, $flags));

        var_dump(self::isFlagSet(self::$FLAG_C, $flags));
        self::setFlag(self::$FLAG_C, false, $flags);
        var_dump(self::isFlagSet(self::$FLAG_C, $flags));
    }
}

/**
 * trait Func
 */
trait traitFunc
{
    public static function in($var, array $inArr = [false, null]) {
        return in_array($var, $inArr, true);
    }

    public static function notIn($var, array $notArr = [false, null])
    {
        return !in_array($var, $notArr, true);
    }

    /**
     * @param $var
     * @return bool
     * @author xiaofeng
     */
    public static function isDigit(/*string*/ $var)
    {
        return (is_numeric($var) && ctype_digit(strval($var)));
    }

    /**
     * @param $var
     * @param $is
     * @param $default
     * @return mixed
     * @author xiaofeng
     */
    public static function isOr($var, $is, $or)
    {
        return ($var === $is ? $or : $var);
    }

    /**
     * @param $var
     * @param int $or
     * @return int
     * @author xiaofeng
     */
    public static function isDigitOr($var, $or = 0)
    {
        return (self::isDigit($var) ? intval($var) : $or);
    }

    /**
     * 如果为假则返回默认值
     * @param $var
     * @param $or
     * @param array $false 为假的列表
     * @return mixed
     * @author xiaofeng
     */
    public static function isFalseOr($var, $or, array $false = [false, null])
    {
        return in_array($var, $false, true) ? $or : $var;
    }

    /**
     * isset检查，可以检查多层
     * @param $arr
     * @param $key key可以是数组
     * @param $or
     * @return mixed
     * @author xiaofeng
     * $arr = ['entity'=>['name'=>'xiaofeng']]
     * setOr($arr, 'entity')
     * setOr($arr, ['entity', 'name'])
     * setOr($arr, ['entity', 'sex'], 1)
     */
    public static function isSetOr(array $arr, $key, $or = null)
    {
        if(is_array($key)) {
            foreach(array_values($key) as $k) {
                if(isset($arr[$k])) {
                    $arr = $arr[$k];
                } else {
                    return $or;
                }
            }
            return $arr;
        } else {
            return isset($arr[$key]) ? $arr[$key] : $or;
        }
    }

    /**
     * @param $var
     * @param array $or
     * @return array
     * @author xiaofeng
     */
    public static function isArrayOr($var, $or = [])
    {
        return is_array($var) ? $var : $or;
    }

    /**
     * call
     * @return mixed
     * @internal param callable $callable
     * @internal param mixed $args 参数序列
     * @author xiaofeng
     */
    public static function call(/*callable $callable, ...$args*/)
    {
        $args = func_get_args();
        if(count($args)) {
            $func = array_shift($args);
            if(is_callable($func)) {
                return call_user_func_array($func, $args);
            }
        }
        return null;
        /*return  call_user_func_array($callable, $args);*/
    }

    /**
     * apply
     * @param  callable $callable
     * @param  array    $args     参数数组
     * @return mixed
     * @author xiaofeng
     */
    public static function apply(callable $callable, array $args = [])
    {
        return  call_user_func_array($callable, $args);
    }

    /**
     * 设置金额为分单位
     */
    public static function setAmount100($iAmount)
    {
        return $iAmount * 100;
    }

    /**
     * 设置金额单位
     */
    public static function getAmount100($iAmount)
    {
        return $iAmount/100;
    }

}

/**
 * trait http
 */
trait traitHttp
{

    /**
     * curl post方式发送json格式Body
     * @param $url
     * @param array $jsonArray
     * @param int $timeout s
     * @param &string $curlErr
     * @return mixed
     * @author xiaofeng
     */
    public static function postJson($url, array $jsonArray, $timeout = 3, &$curlErr = '')
    {
        static $errorCodes = [
            1 => 'CURLE_UNSUPPORTED_PROTOCOL',
            2 => 'CURLE_FAILED_INIT',
            3 => 'CURLE_URL_MALFORMAT',
            4 => 'CURLE_URL_MALFORMAT_USER',
            5 => 'CURLE_COULDNT_RESOLVE_PROXY',
            6 => 'CURLE_COULDNT_RESOLVE_HOST',
            7 => 'CURLE_COULDNT_CONNECT',
            8 => 'CURLE_FTP_WEIRD_SERVER_REPLY',
            9 => 'CURLE_REMOTE_ACCESS_DENIED',
            11 => 'CURLE_FTP_WEIRD_PASS_REPLY',
            13 => 'CURLE_FTP_WEIRD_PASV_REPLY',
            14 => 'CURLE_FTP_WEIRD_227_FORMAT',
            15 => 'CURLE_FTP_CANT_GET_HOST',
            17 => 'CURLE_FTP_COULDNT_SET_TYPE',
            18 => 'CURLE_PARTIAL_FILE',
            19 => 'CURLE_FTP_COULDNT_RETR_FILE',
            21 => 'CURLE_QUOTE_ERROR',
            22 => 'CURLE_HTTP_RETURNED_ERROR',
            23 => 'CURLE_WRITE_ERROR',
            25 => 'CURLE_UPLOAD_FAILED',
            26 => 'CURLE_READ_ERROR',
            27 => 'CURLE_OUT_OF_MEMORY',
            28 => 'CURLE_OPERATION_TIMEDOUT',
            30 => 'CURLE_FTP_PORT_FAILED',
            31 => 'CURLE_FTP_COULDNT_USE_REST',
            33 => 'CURLE_RANGE_ERROR',
            34 => 'CURLE_HTTP_POST_ERROR',
            35 => 'CURLE_SSL_CONNECT_ERROR',
            36 => 'CURLE_BAD_DOWNLOAD_RESUME',
            37 => 'CURLE_FILE_COULDNT_READ_FILE',
            38 => 'CURLE_LDAP_CANNOT_BIND',
            39 => 'CURLE_LDAP_SEARCH_FAILED',
            41 => 'CURLE_FUNCTION_NOT_FOUND',
            42 => 'CURLE_ABORTED_BY_CALLBACK',
            43 => 'CURLE_BAD_FUNCTION_ARGUMENT',
            45 => 'CURLE_INTERFACE_FAILED',
            47 => 'CURLE_TOO_MANY_REDIRECTS',
            48 => 'CURLE_UNKNOWN_TELNET_OPTION',
            49 => 'CURLE_TELNET_OPTION_SYNTAX',
            51 => 'CURLE_PEER_FAILED_VERIFICATION',
            52 => 'CURLE_GOT_NOTHING',
            53 => 'CURLE_SSL_ENGINE_NOTFOUND',
            54 => 'CURLE_SSL_ENGINE_SETFAILED',
            55 => 'CURLE_SEND_ERROR',
            56 => 'CURLE_RECV_ERROR',
            58 => 'CURLE_SSL_CERTPROBLEM',
            59 => 'CURLE_SSL_CIPHER',
            60 => 'CURLE_SSL_CACERT',
            61 => 'CURLE_BAD_CONTENT_ENCODING',
            62 => 'CURLE_LDAP_INVALID_URL',
            63 => 'CURLE_FILESIZE_EXCEEDED',
            64 => 'CURLE_USE_SSL_FAILED',
            65 => 'CURLE_SEND_FAIL_REWIND',
            66 => 'CURLE_SSL_ENGINE_INITFAILED',
            67 => 'CURLE_LOGIN_DENIED',
            68 => 'CURLE_TFTP_NOTFOUND',
            69 => 'CURLE_TFTP_PERM',
            70 => 'CURLE_REMOTE_DISK_FULL',
            71 => 'CURLE_TFTP_ILLEGAL',
            72 => 'CURLE_TFTP_UNKNOWNID',
            73 => 'CURLE_REMOTE_FILE_EXISTS',
            74 => 'CURLE_TFTP_NOSUCHUSER',
            75 => 'CURLE_CONV_FAILED',
            76 => 'CURLE_CONV_REQD',
            77 => 'CURLE_SSL_CACERT_BADFILE',
            78 => 'CURLE_REMOTE_FILE_NOT_FOUND',
            79 => 'CURLE_SSH',
            80 => 'CURLE_SSL_SHUTDOWN_FAILED',
            81 => 'CURLE_AGAIN',
            82 => 'CURLE_SSL_CRL_BADFILE',
            83 => 'CURLE_SSL_ISSUER_ERROR',
            84 => 'CURLE_FTP_PRET_FAILED',
            84 => 'CURLE_FTP_PRET_FAILED',
            85 => 'CURLE_RTSP_CSEQ_ERROR',
            86 => 'CURLE_RTSP_SESSION_ERROR',
            87 => 'CURLE_FTP_BAD_FILE_LIST',
            88 => 'CURLE_CHUNK_FAILED'
        ];

        $ch = curl_init($url);
        $payload = json_encode($jsonArray, JSON_UNESCAPED_UNICODE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, max(0, intval($timeout)));
        $result = curl_exec($ch);
        if($result === false) {
            $curlErr = "curl_error: " . _::isSetOr($errorCodes, curl_errno($ch), curl_errno($ch));
        }
        curl_close($ch);
        return $result;
    }

    public static function postJsonAndFile($url, $jsonArray, $file, $timeout = 3)
    {
        $ch = curl_init($url);
        $payload = json_encode($jsonArray, JSON_UNESCAPED_UNICODE);
        $postFields = [
            'args' => $payload,
            'file' => new CURLFile(realpath($file))
        ];
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_TIMEOUT, max(0, intval($timeout)));
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
     * exception wrapper for gzcompress
     * @param $str
     * @param int $level
     * @return string
     * @throws _Exception
     * @author xiaofeng
     */
    public static function gzip(/*string*/ $str, /*int*/ $level = 9)
    {
        header('Content-Encoding: gzip');
        $gzipStr = gzcompress($str, $level);
        if($gzipStr === false) {
            throw new _Exception('gzcompress error', _Code::GZCOMPRESS_ERROR);
        }
        return "\x1f\x8b\x08\x00\x00\x00\x00\x00" . $gzipStr;
    }

    /**
     * 获取真实ip地址
     * @return string
     */
    public static function realIp()
    {
        static $realip = NULL;

        if ($realip !== NULL) {
            return $realip;
        }

        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

                //取X-Forwarded-For中第一个非unknown的有效IP字符串
                foreach ($arr AS $ip) {
                    $ip = trim($ip);
                    if ($ip != 'unknown') {
                        $realip = $ip;
                        break;
                    }
                }
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $realip = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                if (isset($_SERVER['REMOTE_ADDR'])) {
                    $realip = $_SERVER['REMOTE_ADDR'];
                } else {
                    $realip = '0.0.0.0';
                }
            }
        } else {
            if (getenv('HTTP_X_FORWARDED_FOR')) {
                $realip = getenv('HTTP_X_FORWARDED_FOR');
            } elseif (getenv('HTTP_CLIENT_IP')) {
                $realip = getenv('HTTP_CLIENT_IP');
            } else {
                $realip = getenv('REMOTE_ADDR');
            }
        }

        preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
        $realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';

        return $realip;
    }

    /**
     * @param $ip
     * @return bool
     * @author xiaofeng
     */
    public static function isPrivateIp($ip) {
        // 127.0.0.1 is not private ip but lookback ip range
        // @see http://stackoverflow.com/questions/17150100/validating-non-private-ip-addresses-with-php
        // return !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);

        return (
            ($ip & 0xFF000000) == 0x00000000 || # 0.0.0.0/8
            ($ip & 0xFF000000) == 0x0A000000 || # 10.0.0.0/8
            ($ip & 0xFF000000) == 0x7F000000 || # 127.0.0.0/8
            ($ip & 0xFFF00000) == 0xAC100000 || # 172.16.0.0/12
            ($ip & 0xFFFF0000) == 0xA9FE0000 || # 169.254.0.0/16
            ($ip & 0xFFFF0000) == 0xC0A80000);  # 192.168.0.0/16
    }

    /**
     * @param string $ip
     * @param array $whiteList
     * @return bool
     * @author xiaofeng
     */
    public static function isIpAllow(/*string*/ $ip, array $whiteList = [])
    {
        return (in_array($ip, $whiteList) || self::isPrivateIp(ip2long($ip)));
    }

    public static function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    public static function isPost()
    {
        return isset($_SERVER['REQUEST_METHOD']) && !strcasecmp($_SERVER['REQUEST_METHOD'], 'POST');
    }
}

/* @usage
require('./File.php7');
// http://iallex.com/fix-nginx-ssi-unsafe-uri-was-detected-error/
// unsafe uri
// $file = './../downloadtest/03 秀水街.m4a';

$file = "/vagrant/downloadtest/03 秀水街.m4a";
if(isset($_REQUEST['t'])) {
if($_REQUEST['t'] == 'nginx') {
File::nginx_xsend($file);
} else if ($_REQUEST['t'] == 'part') {
File::part_send($file);
}
} else {
File::send($file);
}
 */

/**
 * File PHP文件下载转发
 * @from http://wiki.nginx.org/XSendfile
 * @from http://www.laruence.com/2012/05/02/2613.html
 * @from http://www.lovelucy.info/x-sendfile-in-nginx.html
 */
trait traitFile
{
    /**
     * 404
     * @author xiaofeng
     */
    private static function not_found()
    {
        header('HTTP/1.1 404 Not Found');
        header("status: 404 Not Found");
        // http_response_code(404);
    }

    /**
     * 发送前预处理
     * @param  string      $filepath     文件路径
     * @param  string      $filename 客户端可见文件名
     * @return bool
     */
    private static function preSend(/*string*/ $filepath, /*string*/ $filename = null,
        /*string*/ $mimeType='application/octet-stream') /*: bool*/
    {
        if(!is_readable($filepath)) {
            return false;
        }

        // $filename = $filename ?? basename($filepath);
        $filename = $filename === null ? basename($filepath) : $filename;
        $encoded_filename = rawurlencode($filename);
        header("Content-type: $mimeType");
        //处理中文文件名
        $ua = $_SERVER['HTTP_USER_AGENT'];
        if(preg_match('/MSIE/', $ua)) {
            header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
        } else if (preg_match('/Firefox/', $ua)) {
            header("Content-Disposition: attachment; filename*=\"utf8''" . $filename . '"');
        } else {
            header('Content-Disposition: attachment; filename="' . $filename . '"');
        }

        return true;
    }

    /**
     * 从php缓冲区->web服务器缓冲器->浏览器
     * @param  string       $filepath 文件路径
     * @return
     */
    public static function send(/*string*/ $filepath, /*string*/ $filename = null,
        /*string*/ $mimeType='application/octet-stream') /*: bool*/
    {
        if(!self::preSend($filepath, $filename, $mimeType)) {
            self::not_found();
            return false;
        }
        header("Content-Length: " . filesize($filepath));
        readfile($filepath);
        return true;
    }

    /**
     * 未测试!!!
     * 需要Apache的module mod_xsendfile
     * https://tn123.org/mod_xsendfile/
     * @param  string $filepath 文件路径
     * @return
     */
    public static function apacheXsend(/*string*/ $filepath, /*string*/ $filename = null,
        /*string*/ $mimeType='application/octet-stream')/* : bool*/
    {
        if(!self::preSend($filepath, $filename, $mimeType)) {
            self::not_found();
            return false;
        }
        header("X-Sendfile: $filepath");
        return true;
    }

    /**
     * [nginx_xsend description]
     * @param  string $filepath 文件路径
     * @return
     *  nginx 配置
     *  发送 /some/path/protected/iso.img 路径文件的配置
     *  location /protected/ {
     *      internal;  # 路径只能在 Nginx 内部访问，不能用浏览器直接访问防止未授权的下载。
     *      root   /some/path;
     *  }
     *  发送 /some/path/protected/iso.img 路径文件的配置
     *  location /protected/ {
     *      internal;
     *      alias  /some/path/; # 注意最后的斜杠
     *  }
     */
    public static function nginxXsend(/*string*/ $filepath, /*string*/ $filename = null,
        /*string*/ $mimeType='application/octet-stream') /*: bool*/
    {
        if(!self::preSend($filepath, $filename, $mimeType)) {
            self::not_found();
            return false;
        }
        header("X-Accel-Redirect: $filepath");
        return true;
    }

    /**
     * 支持range的文件下载, 从官网评论找到的并修改
     * @from http://php.net/manual/en/function.readfile.php#86244
     * @param string $filepath 文件路径
     * @param string $filename 客户可见文件名
     * @param string $mimeType
     */
    public static function partSend(/*string*/ $filepath, /*sring*/ $filename = null,
        /*string*/ $mimeType='application/octet-stream') /*: bool*/
    {
        if (!is_readable($filepath)) {
            self::not_found();
            return false;
        }

        $size = filesize($filepath);
        // $filename = $filename ?? basename($filepath);
        $filename = $filename === null ? basename($filepath) : $filename;

        $time = date('r', filemtime($filepath));

        $fm = @fopen($filepath, 'rb');
        if (!$fm) {
            header("HTTP/1.1 505 Internal server error");
            return false;
        }

        $begin = 0;
        $end = $size;

        if (isset($_SERVER['HTTP_RANGE'])) {
            if (preg_match('/bytes=\h*(\d+)-(\d*)[\D.*]?/i', $_SERVER['HTTP_RANGE'], $matches)) {
                $begin = intval($matches[0]);
                if (!empty($matches[1])) {
                    $end = intval($matches[1]);
                }
            }
        }

        if ($begin > 0 || $end < $size) {
            header('HTTP/1.1 206 Partial Content');
        } else {
            header('HTTP/1.1 200 OK');
        }

        header("Content-Type: $mimeType");
        header('Cache-Control: public, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Accept-Ranges: bytes');
        header('Content-Length: ' . ($end - $begin));
        header("Content-Range: bytes $begin-$end/$size");
        header("Content-Disposition: inline; filename=$filename");
        header("Content-Transfer-Encoding: binary\n");
        header("Last-Modified: $time");
        header('Connection: close');

        $cur = $begin;
        fseek($fm, $begin, 0);

        // 检测文件是否读完，检测用户是否断开连接
        while (!feof($fm) && $cur < $end && (connection_status() == 0)) {
            echo fread($fm, min(1024 * 16, $end - $cur));
            $cur += 1024 * 16;
        }

        @fclose($fm);

        return true;
    }
}


trait traitStr
{
    /**
     * @param $target
     * @param $replace
     * @param bool|true $tolowwer
     * @return string
     * @author xiaofeng
     * self::insertBeforeUpper('ClassMethod', '_', true); // class_method
     */
    public static function insertBeforeUpper($target, $replace, $tolowwer = true)
    {
        if(!$target) {
            return '';
        }

        $str = '';
        $target = lcfirst($target);
        for($i = 0, $l = strlen($target); $i < $l; $i++) {
            $char = ord($target[$i]);
            $str .= (($char > 64 && $char < 91) ? $replace : '') . $target[$i];
        }
        return $tolowwer ? strtolower($str) : $str;
    }
}

/**
 * trait ArrHelper
 */
trait traitArray
{
    /**
     * 提取$arr的某一列作为表头生成新的kv数组
     * @param array $arr
     * @param string $key
     * @return array|bool 失败返回false
     */
    public static function array_title(array $arr, /*string*/ $key) /*: array*/ {
        if($arr === []) {
            return [];
        }
        if(!is_string($key) || !$key) {
            //trigger_error(__METHOD__ . ': error arg');
            return false;
        }

        // check arr is unique
        $array_keys = array_column($arr, $key);
        $unique_keys = array_unique($array_keys);
        if(count($array_keys) !== count($unique_keys)) {
            //trigger_error(__METHOD__ . ': duplicated keys');
            return false;
        }

        return array_combine(array_column($arr, $key), array_values($arr));
    }

    public static function array_column_merge(array $arr, $keyKey, $keyValue)
    {
        return array_combine(array_column($arr, $keyKey), array_column($arr, $keyValue));
    }

    /**
     * 字段映射替换
     * @param &array $array
     * @param array $map
     * @author xiaofeng
     */
    public static function array_field_map(&$array, array $map)
    {
        if(!is_array($array)) {
            return;
        }

        if($array) {
            foreach($array as &$row) {
                foreach($map as $old => $new) {
                    if(isset($row[$old])) {
                        $row[$new] = $row[$old];
                        unset($row[$old]);
                    }
                }
            }
            unset($row);
        }
    }

    /**
     * 一维数组value映射
     * @param $array
     * @param array $map
     *
     * @author xiaofeng
     */
    public static function number_array_field_map(&$array, array $map)
    {
        if(!is_array($array) || !$array || !$map) {
            return;
        }
        foreach($array as &$v) {
            if(isset($map[$v])) {
                $v = $map[$v];
            }
        }
        unset($v);
    }

    /**
     * 数组trim
     * @param &$array $array
     * @param array $fields
     * @return bool
     *
     * @author xiaofeng
     */
    public static function array_trim(&$array, array $fields = []) {
        if(!is_array($array) || empty($array)) {
            return false;
        }

        if($fields === []) {
            foreach($array as &$val) {
                $val = trim($val);
            }
            unset($val);
        } else {
            foreach($fields as $field) {
                if(isset($array[$field])) {
                    $array[$field] = trim($array[$field]);
                }
            }
        }
    }
}

/**
 * trait Password
 */
trait traitPassword
{
    /**
     * Timing attack safe string comparison
     * @link http://php.net/manual/en/function.hash-equals.php
     * @param string $known_string <p>The string of known length to compare against</p>
     * @param string $user_string <p>The user-supplied string</p>
     * @return boolean <p>Returns <b>TRUE</b> when the two strings are equal, <b>FALSE</b> otherwise.</p>
     */
    public static function hash_equal(/*string*/ $known_string, /*string*/ $user_string)
    {
        if(function_exists('hash_equals')) {
            return hash_equals($known_string, $user_string);
        } else {
            if(strlen($user_string) != strlen($user_string)) {
                return false;
            } else {
                $res = $user_string ^ $user_string;
                $ret = 0;
                for($i = strlen($res) - 1; $i >= 0; $i--) $ret |= ord($res[$i]);
                return !$ret;
            }
        }
    }

    /**
     * Creates a password hash.
     * @link http://www.php.net/manual/en/function.password-hash.php
     * @param string $password The user's password.
     * @param int $algo A <a href="http://www.php.net/manual/en/password.constants.php" class="link">password algorithm constant</a>  denoting the algorithm to use when hashing the password.
     * @param array $options [optional] <p> An associative array containing options. See the <a href="http://www.php.net/manual/en/password.constants.php" class="link">password algorithm constants</a> for documentation on the supported options for each algorithm.
     * If omitted, a random salt will be created and the default cost will be used.
     * @return string|bool Returns the hashed password, or FALSE on failure.
     */
    public static function password_hash($password, $algo, array $options = null)
    {
        if(function_exists('password_hash')) {
            return password_hash($password, $algo, $options === null ? [] : $options);
        } else {
            trigger_error('not impl');
            return false;
        }
    }

    /**
     * Checks if the given hash matches the given options.
     * @link http://www.php.net/manual/en/function.password-verify.php
     * @param string $password The user's password.
     * @param string $hash A hash created by password_hash().
     * @return boolean Returns TRUE if the password and hash match, or FALSE otherwise.
     */
    public static function password_verify($password, $hash)
    {
        if(function_exists('password_verify')) {
            return password_verify($password, $hash);
        } else {
            trigger_error('not impl');
        }
        return false;
    }

    /*
     * @from http://php.net/manual/en/function.hash-equals.php
     */
    public static function hash_equals($str1, $str2)
    {
        if(function_exists('hash_equals')) {
            return hash_equals($str1, $str2);
        } else {
            if(strlen($str1) != strlen($str2)) {
                return false;
            } else {
                $res = $str1 ^ $str2;
                $ret = 0;
                for($i = strlen($res) - 1; $i >= 0; $i--) $ret |= ord($res[$i]);
                return !$ret;
            }
        }
    }

    // 32char len return
    public static function pwd_hash_md5($password, $salt)
    {
        return md5(md5($password) . $salt);
    }

    public static function pwd_verify_md5($password, $salt, $password_hash)
    {
        // return hash_equals($password_hash, self::pwd_hash_md5($password, $salt));
        return self::hash_equals($password_hash, self::pwd_hash_md5($password, $salt));
    }

    // crypt 安全 > md5
    // 13char len return
    public static function pwd_hash_crypt($password, $salt)
    {
        return crypt($password, $salt);
    }

    public static function pwd_verify_crypt($password, $salt, $password_hash)
    {
        return self::hash_equals($password_hash, self::pwd_hash_crypt($password, $salt));
    }

// hash_equals
// It is important to provide the user-supplied string as the second parameter, rather than the first.

    public static function pwd_strong_check($candidate, &$errmsg)
    {
        $r0='/[a-zA-Z]/'; // alpah
        $r1='/[A-Z]/';  //Uppercase
        $r2='/[a-z]/';  //lowercase
        $r3='/[!@#$%^&*()\-_=+{};:,<.>]/';  // whatever you mean by 'special char'
        $r4='/[0-9]/';  //numbers

        /*
        if(preg_match_all($r1,$candidate, $o) < 2) {
            return false;
        }

        if(preg_match_all($r2,$candidate, $o) < 2) {
            return false;
        }

        if(preg_match_all($r3,$candidate, $o) < 2) {
            return false;
        }
        */

        /*
        if(preg_match_all($r0,$candidate, $o) < 2) {
            $errmsg = '必须字母数字混合';
            return false;
        }

        if(preg_match_all($r4,$candidate, $o) < 2) {
            $errmsg = '必须字母数字混合';
            return false;
        }
        */

        if(strlen($candidate) < 6) {
            $errmsg = '长度小于6';
            return false;
        }

        return true;
    }

}


// @file test_async_request.php
/*
require(__DIR__ . '/Async.php7');
$base_file = 'http://t.cc/Async/test_async_runwith.php';
$get = ['get_key1'=>'get_key1_val','get_key2'=>'get_key2_val'];
$post = ['post_key1'=>'post_key1_val','post_key2'=>'post_key2_val'];
$cookie = ['cookie_key1'=>'cookie_key1_val','cookie_key2'=>'cookie_key2_val'];
$url = $base_file . '?' . http_build_query($get);
$ret = Async::request($url, $post, $cookie);
var_dump($ret);
*/

// @file test_async_runwith.php
/*
// tail -f /usr/local/openresty/nginx/logs/error.log

require(__DIR__ . '/Async.php7');

// register_shutdown_function 测试失败
// 超时时间测试失败

Async::runWith(function() {
    sleep(5);
    error_log(print_r($_GET, true));
    error_log(print_r($_POST, true));
    error_log(print_r($_COOKIE, true));
    // file_put_contents('get'.microtime(), print_r($_GET, true));
    // file_put_contents('post'.microtime(), print_r($_POST, true));
    // file_put_contents('cookie'.microtime(), print_r($_COOKIE, true));
});
*/

/**
 * PHP Async
 *
 * 如果服务器在本机，使用vhost，必须配置hosts
 * 如果使用nginx必须配置：
 * http {
 *     fastcgi_ignore_client_abort on; 客户端主动断掉连接之后，Nginx 会等待后端处理完(或者超时)
 * }
 */
trait traitAsync
{

    /**
     * asyncExecute PHP异步执行任务
     * @param string $url  执行任务的url地址
     * @param array $post_arr 需要post提交的数据POST
     * @param array $cookie_arr cookie数据用于登录等的设置
     * @param callable|null $cookie_encoder cookie编码函数，必须是可逆方法，可用于加密
     * @param int $time_out 超时 默认3s
     * @param null $errno
     * @param null $errstr
     * @return bool
     * 注意 urlencode
     */
    public static function request(/*string*/ $url, array $post_arr = [], array $cookie_arr = [], callable $cookie_encoder = null, /*int*/ $time_out = 3, &$errno = null, &$errstr = null) /*: bool*/
    {
        $url = filter_var(filter_var($url, FILTER_SANITIZE_URL), FILTER_VALIDATE_URL);
        if(!$url) {
            $errstr = 'url error';
            return false;
        }

        $method = 'GET'; //可以通过POST或者GET传递一些参数给要触发的脚本
        $url_array = parse_url($url); //获取URL信息，以便平凑HTTP HEADER
        $host   = isset($url_array['host']) ? $url_array['host'] : '';
        $port   = isset($url_array['port']) ? $url_array['port'] : 80;
        $path   = isset($url_array['path']) ? $url_array['path'] : '/';
        $query  = isset($url_array['query']) ? $url_array['query'] : '';

        $fp = @fsockopen($host, $port, $errno, $errstr, $time_out);
        if (!$fp) {
            $errstr = 'fsockopen error';
            return false;
        }

        $getPath = $path . ($query ? '?' . $query : '');
        if (!empty($post_arr)) {
            $method = 'POST';
        }

        // $request        = EMPTY_STRING; // 请求实体
        // $request_header = EMPTY_STRING; // 请求实体header
        $request_body   = EMPTY_STRING; // 请求实体body

        $request_header = $method . ' ' . $getPath;
        $request_header .= " HTTP/1.1\r\n";
        $request_header .= "Host: " . $url_array['host'] . "\r\n"; //HTTP 1.1 Host域不能省略

        ///*以下头信息域可以省略
        $request_header .= "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13 \r\n";
        $request_header .= "Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,q=0.5 \r\n";
        $request_header .= "Accept-Language: en-us,en;q=0.5 \r\n";
        $request_header .= "Accept-Encoding: gzip,deflate \r\n";
        //*/

        $request_header .= "Connection: Close\r\n";

        if (!empty($cookie_arr)) { // request header
            // $cookie_encoder = $cookie_encoder ?? 'base64_encode';
            $cookie_encoder = $cookie_encoder === null ? 'base64_encode' : $cookie_encoder ;
            $_cookie = EMPTY_STRING;
            foreach ($cookie_arr as $k => $v) {
                $_cookie .= $k . "=" . $v . "; ";
            }
            // $_cookie = http_build_cookie($cookie_arr);
            // $cookie_str = "Cookie: " . $_cookie . " \r\n"; // 不编码传递Cookie
            // 接收端需要 解码，比如：base64_decode(header(cookie_arr))
            $cookie_str = "Cookie: " . $cookie_encoder($_cookie) . " \r\n"; // 传递Cookie

            $request_header .= $cookie_str;
        }

        if (!empty($post_arr)) {  // request header + body
            $_post = EMPTY_STRING;
            foreach ($post_arr as $k => $v) {
                $_post .= $k . '=' . $v. '&';
            }
            $request_body = $_post; // post请求 request_body即post数据
            // $_post = http_build_query($post_arr); // ?

            $post_str = "Content-Type: application/x-www-form-urlencoded\r\n"; // POST数据
            $post_str .= "Content-Length: " . strlen($request_body) . "\r\n"; // POST数据的长度
            $request_header .= $post_str;

            // $post_str .= "\r\n";
            // $post_str .= $_post . "\r\n\r\n"; //传递POST数据
            // $request_header .= $post_str;
        }

        $request = $request_header . "\r\n" . $request_body;

        fwrite($fp, $request);
        fclose($fp);
        return true;
    }

    /**
     * request 的cookie解码，应该与request配合
     * @param  callable|null $decoder
     * @return array
     */
    public static function cookieDecode(callable $decoder) /*: array*/ {
        $_cookie = [];

        $http_cookie = filter_input(INPUT_SERVER, 'HTTP_COOKIE', FILTER_SANITIZE_STRING);
        if(!$http_cookie) {
            return $_cookie;
        }

        $cookie_str = call_user_func($decoder, $http_cookie);

        if(!$cookie_str) {
            return $_cookie;
        }
        $kv_pair = explode(';', trim(trim($cookie_str), ';'));
        if(!$kv_pair) {
            return $_cookie;
        }
        foreach($kv_pair as $kv) {
            list($k, $v) = explode('=', trim($kv));
            $_cookie[$k] = $v;
            // $_COOKIE[$k] = $v;
        }

        // $_COOKIE = $_cookie + $_COOKIE;
        $_COOKIE = $_cookie;

        return $_cookie;
    }

    /**
     * 异步调用接受方法必须使用此方法包裹
     * @param callable $worker
     * @param callable $cookie_decoder cookie解码，与requestcookie编码配合
     * @param int $time_out 默认取消脚本超时限制
     *
     * 超时时间 测试无效，原因未知
     */
    public static function runWith(callable $worker, callable $cookie_decoder = null, $time_out = 0)
    {
        if(!is_callable($worker)) {
            return;
        }

        $func = function() use($worker) {
            ob_start();
            call_user_func($worker);

            // FIXME :　可以在此处加入回调机制+日志机制
            // ob_end_clean();
            $err = ob_get_clean();
            if($err) {
                error_log($err);
            }
        };

        ignore_user_abort(true);   // 如果客户端断开连接，不会引起脚本abort.
        // 自此处开始计时 不包括流，数据库，系统调用耗时
        // 安全模式下失效
        set_time_limit($time_out); // 取消脚本执行延时上限

        // 解码cookie
        // $cookie_decoder = $cookie_decoder ?? 'base64_decode';
        $cookie_decoder = $cookie_decoder === null ? 'base64_decode' : $cookie_decoder;

        if(is_callable($cookie_decoder)) {
            self::cookieDecode($cookie_decoder);
        }

        // set_time_limit对register_shutdown_function无效
        // register_shutdown_function($func); // 任务放在这里, 保证执行

        // 执行异步任务
        call_user_func($func);
    }

}


/**
 * trait Experimental
 */
trait traitExperimental
{
    /**
     * 从Closure获取执行期间输出
     * @param callable $closure
     * @param array $args
     * @return string
     * @author xiaofeng
     */
    public static function getBuffer(callable $closure, /*...$args*/ array $args = []) /*: string*/
    {
        ob_start();
        $oldContent = ob_get_length() ? ob_get_clean() : strval(null);
        call_user_func_array($closure, $args);
        $content = ob_get_clean();
        // 旧数据重新放回buffer
        echo $oldContent;
        return $content;
    }

    /**
     * 通过执行特定opcodes的闭包
     * @param array $opcodes
     * @return bool|Closure
     * @author xiaofeng
     */
    public static function newVM(array $opcodes) /*: Closure*/
    {
        if(empty($opcodes)) {
            return false;
        }

        // 递归调用，注意最大允许递归层级的设置
        $closure = function(array $expression) use($opcodes, &$closure) {
            // $expression_str = '';
            // $expression_str = json_encode($expression);

            if (empty($expression)) {
                trigger_error('expression is empty');
                exit(-1);
            }

            // $self = __FUNCTION__;
            $self = $closure;
            $op = array_shift($expression);
            $operator = null;

            if(isset($opcodes[$op])) {
                $operator = $opcodes[$op];
            } else if(is_callable($op)) { // 支持php函数库
                $operator = $op;
            } else {
                trigger_error("$op no support");
                exit(-1);
            }

            // 操作符对应操作数个数检查
            $needOperandNums = (new ReflectionFunction($operator))->getNumberOfParameters();
            $realOperandNums = count($expression);
            if($needOperandNums !== $realOperandNums) {
                trigger_error("$op need $needOperandNums operands, but $realOperandNums gives");
                exit(-1);
            }

            foreach($expression as &$operand) {
                if(is_array($operand)) {
                    $operand = $self($operand, true);
                }
            }
            unset($operand);

            $result = call_user_func_array($operator, $expression);

            // if(!$is_rec) {
            $args = implode(', ', array_map(
                function($val) {
                    return self::getBuffer(function() use($val) { var_dump($val); });
                },
                $expression
            ));
            $expression_str = "$op($args)";
            $result_str = self::getBuffer(function() use($result) { var_dump($result); });
            echo "exec: $expression_str  = $result_str\n";
            // }

            return $result;
        };

        return $closure;
    }
}

/**
 * trait Cli
 */
trait traitCli
{
    public static function is_cli() {
        return (PHP_SAPI === 'cli');
    }

    public static function is_win() {
        return PHP_OS === 'WINNT';
    }

    public static function eol(/*int*/ $n = 1) {
        return str_repeat(PHP_EOL, $n);
    }

    // linux only
    public static function clear() {
        // exec(self::is_win() ? 'cls' : 'clear');
        echo chr(27)."[H".chr(27)."[2J";
    }

    public static function backspace(/*int*/ $n = 1) {
        // "\x08" === chr(8)
        echo str_repeat(chr(8), $n);
    }

    /**
     * Pause
     * @return string
     * @author xiaofeng
     */
    public static function pause(){
        static $f;
        if ($f === null) {
            $f = fopen("php://stdin","r");
        }
        return fgets($f);
    }

    /**
     * 转圈等待
     * @return
     * while (true) {
     *   usleep(100000);
     *   cli::wait();
     * }
     */
    public static function wait(){
        static $i;
        $i++;
        echo ['|', '/', '-', '\\'][($i % 4)] . chr(8);
    }

    /**
     * 输出彩色字符 linux only
     * @param  string  $text   输出文本
     * @param  string  $color  颜色枚举
     * @param  bool $output 是否输出
     * @return string
     */
    public static function color_echo($text, /*类常量*/ $color = null, $return = false){
        $color = $color === null ? "[0m" : $color;
        $colorStr = chr(27)."$color$text".chr(27).chr(27)."[0m".chr(27);
        if ($return) {
            return $colorStr;
        }
        echo $colorStr;
        return EMPTY_STRING;
    }

    // @from http://stackoverflow.com/questions/4320081/clear-php-cli-output
    // replace_echo("First Ln\nTime: " . time() . "\nThird Ln");
    public static function replace_echo($str) {
        $numNewLines = substr_count($str, "\n");
        echo chr(27) . "[0G"; // Set cursor to first column
        echo $str;
        echo chr(27) . "[" . $numNewLines ."A"; // Set cursor up x lines
    }

    public static function replaceable_echo($message, $force_clear_lines = NULL) {
        static $last_lines = 0;

        if(!is_null($force_clear_lines)) {
            $last_lines = $force_clear_lines;
        }

        $term_width = exec('tput cols', $toss, $status);
        if($status) {
            $term_width = 64; // Arbitrary fall-back term width.
        }

        $line_count = 0;
        foreach(explode("\n", $message) as $line) {
            $line_count += count(str_split($line, $term_width));
        }

        // Erasure MAGIC: Clear as many lines as the last output had.
        for($i = 0; $i < $last_lines; $i++) {
            // Return to the beginning of the line
            echo "\r";
            // Erase to the end of the line
            echo "\033[K";
            // Move cursor Up a line
            echo "\033[1A";
            // Return to the beginning of the line
            echo "\r";
            // Erase to the end of the line
            echo "\033[K";
            // Return to the beginning of the line
            echo "\r";
            // Can be consolodated into
            // echo "\r\033[K\033[1A\r\033[K\r";
        }

        $last_lines = $line_count;

        echo $message."\n";
    }

    public static function readline() {
        static $f;
        if ($f === null) {
            $f = fopen("php://stdin","r");
        }
        return trim(fgets($f)); // trime \r\n
    }

    public static function readline2() {
        if (PHP_OS == 'WINNT') {
            echo '$ ';
            $line = stream_get_line(STDIN, 1024, PHP_EOL);
        } else {
            $line = readline('$ ');
        }
        return $line;
    }

    /**
     * 获取 memory_limit
     * @param  [type] $MB [description]
     * @return int  当前memory_limit
     */
    public static function memory_limit($MB = null){
        if (is_int($MB)) {
            ini_set('memory_limit',"{$MB}M");
        }
        return (int)trim(ini_get("memory_limit"), 'M');
    }
}
