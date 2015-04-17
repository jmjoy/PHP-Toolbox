<?php

require_once '../Filter.php';

class FilterTest extends PHPUnit_Framework_TestCase {

    protected $inputs1 = array(
        'name'  =>  '中文abc',
        'age'   =>  123,
        'email' =>  'abc@hello.com',
        'time'  =>  1429201830,
    );

    public function testCheck1() {
        $filter = new Filter();
        $filter->field('name')
               ->alias('my_name')
               ->validate('name is too long', 'Filter::strLength', [1, 9]);

        $filter->field('time')
               ->validate("isn't not number", "Filter::checkInt")
               ->sanitize('Filter::transDate');

        $this->assertTrue($filter->check($this->inputs1));

        $this->assertNull($filter->getError());

        $this->assertEquals(array(
            'my_name'   =>  '中文abc',
            'time'      =>  '2015-04-17 00:30:30',
        ), $filter->getResult());
    }

}
