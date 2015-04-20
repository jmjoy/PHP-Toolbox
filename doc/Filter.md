# Filter - 验证与净化输入数组的过滤器

- 链式拼接验证与净化规则
- 支持传入数组改键名
- 支持字段为空情况
- 内置一系列验证与净化函数，并支持自定义
- 支持直接传入规则数组

## 最简单的例子

```php
<?php

require_once '../Filter.php';

$filter = new Filter();

$filter->field('money')->validate('金钱不是个数字', 'is_numeric');

if (!$filter->check($_POST)) {
    return print_r($filter->getError());
}
print_r($filter->getResult());
```

# 教程

假设传入参数为$inputs：

    $inputs = array(
        ‘name’  =>  ‘jmjoy’,
        'age'   =>  123,
        'email' =>  'abc@hello.com',
        'time'  =>  1429201830,
    );

首先新建一个Filterduix
