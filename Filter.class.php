<?php

/**
 * 验证与净化输入数组的过滤器
 * @author JM_Joy
 */
class Filter {

    private $ruleArr = array();

    private $curField = null;

    private $result = null;

    private $error = null;


    /**
     * 以字段名新建一个规则（链式方法的头部）
     * @param string $filedName 要验证的数组的某个字段的键名
     */
    public function newField($filedName) {
        $this->curField = $filedName;
        return $this;
    }

    public function alias($filedName) {

    }

    public function validate($errMsg, $func, $args=null, $not=false) {

    }

    public function sanitize($func, $args=null) {

    }

    public function validateRelate($relation='and') {

    }

    public function check($inputs) {

    }

    public function getResult() {

    }

    public function getError() {

    }

}
