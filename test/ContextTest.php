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
    }

    public function testGet() {

    }

    public function testSession() {
        session_start();
        session_destroy();

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

        var_dump(Context::session('key1'));
        var_dump(Context::session(['key1']));
        var_dump(Context::session(['key2']));
        var_dump(Context::session('key3'));
        var_dump(Context::session('key1'));
        var_dump(Context::session('key1'));

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

}


