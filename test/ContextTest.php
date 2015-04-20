<?php

class ContextTest {

    protected $sessArr = [
        'key1'  =>  'value1',
        'key2'  =>  'value2',
        'key3'  =>  [
            'key33_0'   =>  'value3',
            'key33_1'   =>  'value3',
        ],
        'key4'  =>  [
            'key44'     =>  [
                'key444'    =>  ['value4_0', 'value4_1'],
            ],
        ],
        'key5'  =>  [
            'key55'     =>  [
                'key555'    =>  [
                    'key5555'   => ['value5_0', 'value5_1'],
                ],
            ],
            'key56'     =>  'value5_2',
        ],
        'key6'  =>  'value6',
    ];

    public static function main() {
        $ct = new ContextTest();
        $ct->testSession();
        self::echoLine();
        $ct->testGetPostServer();
        self::echoLine();
        $ct->testCookie();
        self::echoLine();
        //$ct->test();
    }

    public function testGetPostServer() {
        parse_str('ka=va&kb[]=vb1&kb[]=vb2&kc[c1]=vc1&kc[c2]=vc2&kd[][]=vd', $output);

        $_GET = $output;
        $_POST = $output;
        $_SERVER = $output;

        $funcs = ['get', 'post', 'server'];

        foreach ($funcs as $func) {
            $this->assertEqual($func, Context::$func('ka'), 'va');
            $this->assertEqual($func, Context::$func([]), null);
            $this->assertEqual($func, Context::$func([], 'default'), 'default');
            $this->assertEqual($func, Context::$func(['ka']), 'va');
            $this->assertEqual($func, Context::$func(['kb', 1]), 'vb2');
            $this->assertEqual($func, Context::$func(['kc', 'c2']), 'vc2');
            $this->assertEqual($func, Context::$func(['kd', 0]), ['vd']);
            $this->assertEqual($func, Context::$func('empty'), null);
            $this->assertEqual($func, Context::$func('empty', 'default'), 'default');
            $this->assertEqual($func, Context::$func(['kb', 3], 'default'), 'default');
            self::echoLine();
        }
    }

    public function testCookie() {
        var_dump(Context::setCookie('name'));
        var_dump(Context::setCookie([]));
        var_dump(Context::setCookie(['name']));
        var_dump(Context::setCookie(['name', 0]));
        var_dump(Context::setCookie(['name', 'key']));
        var_dump(Context::setCookie(['name', 'key', 'value']));
        var_dump(Context::setCookie(['name', 'key', 2]));
    }

    public function testSession() {
        session_start();
        session_destroy();

        // 测试get方法
        Context::setSession('key1', 'value1');
        Context::setSession([], 'value_empty');
        Context::setSession(['key2'], 'value2');
        Context::setSession(['key3', 'key33_0'], 'value3');
        Context::setSession(['key3', 'key33_1'], 'value3');
        Context::setSession(['key4', 'key44', 'key444'], ['value4_0', 'value4_1']);
        Context::setSession(['key5', 'key55', 'key555', 'key5555'], ['value5_0', 'value5_1']);
        Context::setSession(['key5', 'key56'], 'value5_2');
        Context::setSession(['key6', 'key6_0'], 'value6_0');
        Context::setSession(['key6', 'key6_1'], 'value6_1');
        Context::setSession('key6', 'value6');

        $this->assertEqual('setSession', $_SESSION, $this->sessArr);

        // 测试get方法
        $this->assertEqual('getSession', Context::session(), $_SESSION);
        $this->assertEqual('getSession', Context::session('empty'), null);
        $this->assertEqual('getSession', Context::session([]), null);
        $this->assertEqual('getSession', Context::session('empty', 'default'), 'default');
        $this->assertEqual('getSession', Context::session(['empty'], 'default'), 'default');
        $this->assertEqual('getSession', Context::session(['empty', 'a1', 'a2'], 'default'), 'default');
        $this->assertEqual('getSession', Context::session('key1'), 'value1');
        $this->assertEqual('getSession', Context::session(['key3', 'key33_1']), 'value3');
        $this->assertEqual('getSession', Context::session(['key5', 'key55', 'key555']), ['key5555' => ['value5_0', 'value5_1']]);

        // 测试del方法
        Context::delSession('key1');
        Context::delSession('empty');
        Context::delSession([]);
        Context::delSession(['key6']);
        Context::delSession(['key3', 'key33_0']);
        Context::delSession(['key5', 'key555', 'key5555']);
        Context::delSession(['key5', 'empty', 'key5555']);

        unset($this->sessArr['key1']);
        unset($this->sessArr['key6']);
        unset($this->sessArr['key3']['key33_0']);
        unset($this->sessArr['key5']['key555']['key5555']);

        $this->assertEqual('delSession', Context::session(), $this->sessArr);

        // 测试flashSession
        $this->assertEqual('flashSession', Context::flashSession(['key5', 'key56']), 'value5_2');
        unset($this->sessArr['key5']['key56']);

        $this->assertEqual('flashSession', Context::session(), $this->sessArr);
    }

    public function test() {
    }

    protected function assertEqual($msg, $value1, $value2) {
        if ($value1 != $value2) {
            echo $msg . ' : error!<br />';
            var_dump($value1);
            var_dump($value2);
            die();
        }
        echo $msg . ' : success!<br />';
    }

    public static function echoLine() {
        echo '<br>----------------------------------------------------------------------------------------------------</br>';
    }

}
