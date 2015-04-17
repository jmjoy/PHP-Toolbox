<?php

/**
 * 验证与净化输入数组的过滤器
 * @author JM_Joy
 */
class Filter {

    /**
     * 存放构造方法或链式方法产生的验证|净化规则数组
     * @var array
     */
    protected $rules;

    /**
     * 链式方法使用的用于存放当前字段名的临时变量
     * @var string
     */
    protected $curField;

    /**
     * 验证|净化成功后保存的结果数组
     * @var array
     */
    protected $result;

    /**
     * 验证失败后保存的错误信息数组
     * @var array
     */
    protected $error;

    /**
     * 构造函数
     * @param array $rules 可选，如果直接传入就不用链式方法了
     */
    public function __construct($rules=null) {
        if (!$rules) {
            $this->rules = array();
        } else {
            $this->rules = $rules;
        }
    }

    /**
     * 链式方法的头部，设置当前操作的字段名
     * @param string $fieldName 字段名
     * @return $this
     */
    public function field($fieldName) {
        $this->curField = $fieldName;
        return $this;
    }

    /**
     * 链式方法，设置字段在结果数组中的名字
     * @param string $aliasName 别名
     * @return $this
     */
    public function alias($aliasName) {
        $this->rules[$this->curField]['alias'] = $aliasName;
        return $this;
    }

    /**
     * 链式方法，设置当输入数组该字段不存在时的错误信息
     * @param string $msg 错误信息
     * @return $this
     */
    public function emptyMsg($msg) {
        $this->rules[$this->curField]['emptyMsg'] = $msg;
        return $this;
    }

    /**
     * 链式方法，设置字段在结果数组中的名字
     * @param string $aliasName 别名
     * @return $this
     */
    public function allowEmpty($allow) {
        $this->rules[$this->curField]['allowEmpty'] = $allow;
        return $this;
    }

    /**
     * 链式方法，添加一条验证规则
     * @param string $errMsg 验证失败后的错误信息
     * @param callback|string|array $func 要调用的函数，和call_user_func_array的第一个参数相同
     * @param mixed|array $args 要传入的参数，要传入多个参数的时候使用数组
     * @param bool $reverse 要不要反转验证结果
     * @return $this
     */
    public function validate($errMsg, $func, $args=null, $reverse=false) {
        $this->rules[$this->curField]['validate'][] = array(
            'err'       =>  $errMsg,
            'func'      =>  $func,
            'args'      =>  $args,
            'reverse'   =>  $reverse,
        );
        return $this;
    }

    /**
     * 链式方法，添加一条净化规则
     * @param callback|string|array $func 要调用的函数，和call_user_func_array的第一个参数相同
     * @param mixed|array $args 要传入的参数，要传入多个参数的时候使用数组
     * @return $this
     */
    public function sanitize($func, $args=null) {
        $this->rules[$this->curField]['sanitize'][] = array(
            'func'      =>  $func,
            'args'      =>  $args,
        );
        return $this;
    }

    /**
     * 根据验证|净化数组检查输入数组
     * @param array $inputs 传入数组
     * @return bool 验证成功，返回真，否则为假
     */
    public function check($inputs) {
        // 循环验证所有规则
        foreach ($this->rules as $ruleKey => $ruleRow) {
            // 判断输入的数据有没有这个字段
            if (!isset($inputs[$ruleKey])) {
                // 判断允不允许字段不存在时就不验证
                $allowEmpty = false;
                if (isset($ruleRow['allowEmpty'])) {
                    $allowEmpty = $ruleRow['allowEmpty'];
                }

                // 不允许输入数据的字段为空
                if (!$allowEmpty) {
                    // 获取字段为空时的错误信息
                    $emptyMsg = 'required';
                    if (isset($ruleRow['emptyMsg'])) {
                        $emptyMsg = $ruleRow['emptyMsg'];
                    }
                    $this->setError($ruleKey, $emptyMsg);
                    return false;
                }

                // 不允许输入数据的字段为空，跳过验证
                continue;
            }

            // 这个字段有验证规则
            if (isset($ruleRow['validate']) && is_array($ruleRow['validate'])) {
                // 所有规则都通过才算验证成功
                foreach ($ruleRow['validate'] as $validateRow) {
                    if (!$this->validateField($inputs[$ruleKey], $validateRow)) {
                        $this->setError($ruleKey, $validateRow['err']);
                        return false;
                    }
                }
            }

            // 净化与改名该字段的输入数据
            $key = $ruleKey;
            if (isset($ruleRow['alias'])) {
                $key = $ruleRow['alias'];
            }
            // 净化
            if (isset($ruleRow['sanitize'])) {
                $this->sanitizeField($key, $inputs[$ruleKey], $ruleRow['sanitize']);
            } else {
                $this->result[$key] = $inputs[$ruleKey];
            }

        }

        // 验证成功
        return true;
    }

    /**
     * 验证成功后调用，获取净化后的结果数组
     * @return array
     */
    public function getResult() {
        return $this->result;
    }

    /**
     * 验证失败后调用，获取错误信息数组
     * @return array 字段field表示验证失败的字段，msg表示错误信息
     */
    public function getError() {
        return $this->error;
    }

    /**
     * 单一条验证规则验证某一个字段
     * @param mixed $value 某一个字段的值
     * @param array $validateRow 单一条验证规则
     * @return bool 返回验证成功与否
     */
    protected function validateField($value, $validateRow) {
        // 关键的报错信息或验证方法都没有，直接说验证成功得了
        if (!isset($validateRow['err']) || !isset($validateRow['func'])) {
            return true;
        }

        // 执行验证
        $bool = $this->callUserFunc($value, $validateRow);

        // 反转验证结果
        if ($validateRow['reverse']) {
            return !$bool;
        }

        // 返回验证结果
        return $bool;
    }

    /**
     * 净化某一个字段
     * @param string $key 经过别名处理后的键名
     * @param mixed $value 某个字段的值
     * @param array $sanitize 某个字段的整个净化规则数组
     */
    protected function sanitizeField($key, $value, $sanitize) {
        // 判断验证规则是否存在
        if (is_array($sanitize) && count($sanitize)) {
            // Reduce净化这个这段的值
            foreach ($sanitize as $row) {
                if (!isset($row['func'])) {
                    continue;
                }

                $value = $this->callUserFunc($value, $row);
            }
        }
        // 将净化后的结果放进结果数组
        $this->result[$key] = $value;
    }

    /**
     * 调用验证|净化规则中的函数
     * @param mixed $value 某字段的值
     * @param array $row 某一行验证|净化规则
     * @return mixed 验证规则返回布尔值，净化规则返回净化后的值
     */
    protected function callUserFunc($value, $row) {
        // 将某字段的值作为第一个参数
        $args = array($value);

        // 如果args是单个值，就是函数的第二个参数；如果是数组，则其元素依次为第二个参数之后的参数
        if (isset($row['args']) && $row['args'] !== null) {
            if (is_array($row['args'])) {
                foreach ($row['args'] as $arg) {
                    $args[] = $arg;
                }
            } else {
                $args[] = $row['args'];
            }
        }

        // 函数调用
        return call_user_func_array($row['func'], $args);
    }

    /**
     * 设置错误信息数组
     * @param string $fieldName 字段名
     * @param string $errMsg 错误信息
     */
    protected function setError($fieldName, $errMsg) {
        $this->error = array(
            'field' =>  $fieldName,
            'msg'   =>  $errMsg,
        );
    }

    // ====================================================================================================
    // 验证函数
    // ====================================================================================================

    /**
     * 正则表达式验证
     * @param mixed $value
     * @param string $reg 正则表达式
     * @return bool
     */
    public static function regex($value, $reg) {
        if (!preg_match($reg, $value)) {
            return false;
        }
        return true;
    }

    /**
     * 相等验证
     * @param mixed $value
     * @param mixed $value2 要比较的值
     * @param bool $stric 是否要全等比较
     * @return bool
     */
    public static function equal($value, $value2, $stric=false) {
        if ($stric) {
            return $value === $value2;
        }
        return $value == $value2;
    }

    /**
     * 字符串长度验证
     * @param mixed $value
     * @param integer $min 长度最小值
     * @param integer $max 长度最大值
     * @param string $charset 字符编码，默认是utf-8
     * @return bool
     */
    public static function strLength($value, $min, $max, $charset='utf-8') {
        $len = mb_strlen($value, $charset);
        if ($len >= $min && $len <= $max) {
            return true;
        }
        return false;
    }

    /**
     * 数字大小验证
     * @param mixed $value
     * @param float $min 最小值
     * @param float $max 最大值
     * @return bool
     */
    public static function numBetween($value, $min, $max) {
        if (!is_numeric($value)) {
            return false;
        }
        $value = floatval($value);
        if ($value >= $min && $value <= $max) {
            return true;
        }
        return false;
    }

    /**
     * 整数校验
     * @param mixed $value
     * @return bool
     */
    public static function checkInt($value) {
        if (filter_var($value, FILTER_VALIDATE_INT) === false) {
            return false;
        }
        return true;
    }

    /**
     * 浮点数校验
     * @param mixed $value
     * @return bool
     */
    public static function checkFloat($value) {
        if (filter_var($value, FILTER_VALIDATE_FLOAT) === false) {
            return false;
        }
        return true;
    }

    /**
     * 邮箱地址校验
     * @param mixed $value
     * @return bool
     */
    public static function checkEmail($value) {
        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            return false;
        }
        return true;
    }

    /**
     * URL地址校验
     * @param mixed $value
     * @return bool
     */
    public static function checkUrl($value) {
        if (filter_var($value, FILTER_VALIDATE_URL) === false) {
            return false;
        }
        return true;
    }

    /**
     * 手机号码校验
     * @param mixed $value
     * @return bool
     */
    public static function checkPhone($value) {
        return self::regex($value, '/^1\d{10}$/');
    }

    /**
     * 验证中文长度
     * @param mixed $value
     * @return bool
     */
    public static function chLength($value, $min, $max) {
        $reg = sprintf('/^[\x{4e00}-\x{9fa5}]{%d,%d}$/u', $min, $max);
        if (!preg_match($reg, $value)) {
            return false;
        }
        return true;
    }

    /**
     * 将时间戳转化成数据库时间格式
     * @param mixed $value
     * @return bool
     */
    public static function transDate($value) {
        return date('Y-m-d H:i:s', $value);
    }

}
