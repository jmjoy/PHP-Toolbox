# Context - 单个Http对话的上下文工具类

- 对超全局数组($_GET, $_POST, $_SERVER, $_COOKIE, $_SESSION)进行简单封装
- 支持默认值
- 支持数组键
- 支持闪存Cookie和Flash

## 用法

### 获取方法

分别有 Context::get, Context::post, Context::server, Context::cookie, Context::session,
用法都差不多，下面以get为例：

获取整个$_GET数组：

    Context::get();

获取$__GET[‘name’]:

    Context::get(‘name’);

假如$_GET[‘name’]不存在，会返回默认值null，可以通过修改第二参数改变默认值：

    Context::get(‘name’, ‘default’);

获取$_GET[‘name’][0]或$_GET[‘name’][‘firstname’]，可以传入数组作为键:

    Context::get(array(‘name’, 0));
    Context::get(array(‘name’, ‘firstname’));

如果你使用PHP5.4+，这样写更好看：

    Context::get([‘name’, 0]);
    Context::get([‘name’, ‘firstname’])

### 设置方法

分别有 Context::setCookie， Context::setSession，都需要传入两个参数，
第一参数为键，而setCookie的第二个参数只能是字符串（setcookie的第二参数），
setSession第二参数为除资源类型外的其他值，以setSession为例：

设置$_SESSION[‘name’] = ‘jmjoy’:

    Context::setSession(‘name’, ‘jmjoy’);

设置$_SESSION[‘name’][‘firstname’] = ‘Xia’:

    Context::setSession([‘name’, ‘firstname’], ‘Xia’);

其中setCookie有第三个可选参数，默认为false，表示该cookie是在关闭浏览器后消失，
另外可以传入一个数字表示该cookie在多少秒后消失（setcookie第三个参数time()+?）：

    Context::setCookie([‘name’, ‘firstname’], ‘Xia’, 24*60*60); // 表示该cookie保存一天

### 删除方法

分别有 Context::delCookie, Context::delSession, 需要一个参数作为键，
其实和上面的方法中的键类似：

    Contetx::delSession(‘name’);
    Context::delSession([‘name’, ‘firstname’]);

### 闪存方法

所谓闪存就是只使用一次的数据，比如登陆错误信息，登陆失败后重定向到登陆页面，
显示登陆错误提示信息，当刷新时信息就消失了，原理是获取数据后立即删除数据，
方法有 Context::flashCookie, Context::flashSession, 接收视情况选用，
下面以flashCookie为例：

获取闪存$_COOKIE[‘name’]:

    Context::flashCookie(‘name’);

如果闪存$_COOKIE[‘name’]不存在，默认值同样为null，可以指定第二参数为默认值:

    Context::flashCookie(‘name’, ‘’);

获取$_COOKIE[‘name’][‘firstname’]:

    Context::flashCookie([‘name’, ‘firstname’]);
