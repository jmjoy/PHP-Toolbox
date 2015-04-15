<?php

/**
 * 验证与净化输入数组的过滤器
 * @author JM_Joy
 */
class Filter {

    protected $rules;

    protected $curField;

    protected $result;

    protected $error;

    public function __construct($rules=null) {
        if (!$rules) {
            $this->rules = array();
        } else {
            $this->rules = $rules;
        }
    }

    public function field($fieldName) {
        $this->curField = $fieldName;
        return $this;
    }

    public function alias($aliasName) {
        $this->rules[$this->curField]['alias'] = $aliasName;
        return $this;
    }

    public function emptyMsg($msg='required') {
    }

    public function allowEmpty($allow=false) {
    }

    public function relate($relation='and') {
        $this->rules[$this->curField]['relation'] = $relation;
        return $this;
    }

    public function validate($errMsg, $func, $args=null, $reverse=false) {
        $this->rules[$this->curField]['validate'][] = array(
            'err'       =>  $errMsg,
            'func'      =>  $func,
            'args'      =>  $args,
            'reverse'   =>  $reverse,
        );
        return $this;
    }

    public function sanitize($func, $args=null) {
        $this->rules[$this->curField]['sanitize'][] = array(
            'func'      =>  $func,
            'args'      =>  $args,
        );
        return $this;
    }

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
            if (isset($ruleRow['validate']) && is_array($ruleRow['validate']) &&
                count($ruleRow['validate'])) {

                // 获取验证关系（and 或者 or）
                $relation = 'and';
                if (isset($ruleRow['relation'])) {
                    $relation = $ruleRow['relation'];
                }

                // 验证
                switch ($relation) {
                case 'and':
                    foreach ($ruleRow['validate'] as $validateRow) {
                        if (!$this->validateField($inputs[$ruleKey], $validateRow)) {
                            return false;
                        }
                    }
                    break;

                case 'or':
                    foreach ($ruleRow['validate'] as $validateRow) {
                        if ($this->validateField($inputs[$ruleKey], $validateRow)) {
                            break;
                        }
                    }
                    break;

                default:
                    throw new Exception('Unknow relation of validation');
                }

            }

            // 净化与改名该字段的输入数据
            $key = $ruleKey;
            if (isset($ruleRow['alias'])) {
                $key = $ruleRow['alias'];
            }
            if (isset($ruleRow['sanitize'])) {
                $this->sanitizeField($key, $inputs[$ruleKey], $ruleRow['sanitize']);
            } else {
                $this->result[$key] = $inputs[$ruleKey];
            }

        }

        return true;
    }

    public function getResult() {
        return $this->result;
    }

    public function getError() {
        return $this->error;
    }

    protected function validateField($value, $validateRow) {

    }

    protected function sanitizeField($key, $value, $sanitize) {
        if (is_array($sanitize) && count($sanitize)) {
            foreach ($sanitize as $row) {
                if (!isset($row['func'])) {
                    continue;
                }

                $args = array($value);
                if (isset($row['args']) && $row['args'] !== null) {
                    if (is_array($row['args'])) {
                        foreach ($row['args'] as $arg) {
                            $args[] = $arg;
                        }
                    } else {
                        $args[] = $row['args'];
                    }
                }

                $value = call_user_func_array($row['func'], $args);
            }
        }
        $this->result[$key] = $value;
    }

    protected function setError($fieldName, $errMsg) {
        $this->error = array(
            'field' =>  $filedName,
            'msg'   =>  $errMsg,
        );
    }

}

