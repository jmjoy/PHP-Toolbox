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

### 链式方法

field, alias, emptyMsg, allowEmpty, validate, sanitize

其中filed为链式方法的头部，validate、sanitize可以调用多次

validate接收四个参数，前两个是必须的，第一个代表错误信息，当检验不通过时使用;
第二个为验证函数，实际上是调用call_user_func_array()的第一个参数，比如使用PHP自带的检验
函数如“is_numeric”，或者是Filter的类方法或你自己定义的类方法如”Filter::xxx”、“MyClass::xxx”，
或者是所在类的成员方法如[$this, “xxx”];第三个可选参数是要传给检验函数的除要检验的值之外的参数，
如果有多个参数可以使用数组；第四个可选参数表示是够反转检验结果。

sanitize接收两个参数，第二个可选，和validate的第二个和第三个参数类似。

### 检验方法

check, getResult, getError

check返回布尔值，表示检验成功与否。成功的话使用getResult可以得到经过alias和sanitize（如果有）的
结果数组，否则使用getError将得到包含filed和msg两个字段的数组。

### 这个工具类自带的检验&净化方法

#### 检验方法

Filter::regex

Filter::equal

Filter::strLength

Filter::numBetween

Filter::checkInt

Filter::checkFloat

Filter::checkEmail

Filter::checkUrl

Filter::checkPhone

Filter::chLength

#### 净化方法

Filter::transDate
