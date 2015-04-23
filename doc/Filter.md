# Filter - 验证与净化输入数组的过滤器

- 链式拼接验证与净化规则
- 支持传入数组改键名
- 支持字段为空情况
- 内置一系列验证与净化函数，并支持自定义
- 支持直接传入规则数组

## 教程

### 例子

假设传入参数为$inputs：

    $inputs = array(
        ‘name’  =>  ‘jmjoy’,
        'age'   =>  123,
        'email' =>  'abc@hello.com',
        'time'  =>  1429201830,
        'num'   =>  20,
    );

首先新建一个Filter对象：

    $filter = new Filter();

用正则判断name为2~8的英文数字字符：

    $filter->field('name')
           ->validate('名字不正确', 'Filter::regex', '/^\w{2,8}$/');

判断age必须为整数，范围在0~300之间，并且结果数组字段名改成’my_age’：

    $filter->field('age')
           ->alais('my_age')
           ->validate('年龄不正确', 'Filter::checkInt')
           ->validate('年龄范围应该在0~300之间', 'Filter::numBetween', [0, 300]);

判断email为正确的邮箱格式，且如果在传入数组中不存在这个字段时会提示“您的邮箱不存在”
（默认为“required“）：

    $filter->field('email')
           ->emptyMsg('您的邮箱不存在')
           ->validate('邮箱不正确', 'Filter::checkEmail');

判断time为整数，并且检验成功后转化为mysql时间格式并保存到结果数组：

    $filter->field('time')
           ->validate('时间格式不正确', "Filter::checkInt")
           ->sanitize('Filter::transDate');

判断num为整数，且不在0~19这个范围内：

    $filter->field('num')
           ->validate('年龄范围应该在0~300之间', 'Filter::numBetween', [0, 19], true);

判断empty这个字段即使在不存在时验证通过:

    $filter->field('empty')
           ->allowEmpty();

到了真正要检验的时候了：

    if (!$filter->check($inputs)) {
        $error = $this->getError();
        // ...
    } else {
        $result = $this->getResult();
        // ...
    }
