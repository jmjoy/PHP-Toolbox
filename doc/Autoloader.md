# Autoloader - 类自动加载器

- 根据文件夹路径自动加载类
- 目前仅支持命名空间的方式

## 用法实例

```php
Autoloader::add('App', 'path/to/App'); // 第一参数name不一定要和第二参数dir的basename相同
Autoloader::register();

$user = new \App\Model\User();
```
