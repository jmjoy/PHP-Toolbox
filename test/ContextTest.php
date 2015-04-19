<?php

class ContextTest {

    public static function main() {
        $ct = new ContextTest();
        $ct->testSession();
    }

    public function testGet() {

    }

    public function testSession() {
        Context::setSession(['a1', 'a2', 'a3'], 'aaa');
        var_dump($_SESSION);
    }

}


