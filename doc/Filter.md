Filter - 验证与净化输入数组的过滤器
=======

最简单的例子
----
‘’’
<?php

require_once '../Filter.class.php';

$filter = new Filter();

$filter->field('money')
       ->validate('金钱不是个数字', 'is_numeric');

if (!$filter->check()) {
    return print_r($filter->getError());
}
print_r($filter->getResult());
‘’’
