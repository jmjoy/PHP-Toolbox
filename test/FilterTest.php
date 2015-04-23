<?php

require_once '../Filter.php';

class FilterTest extends PHPUnit_Framework_TestCase {

    protected $inputs1 = array(
        'name'      =>  '中文abc',
        'age'       =>  123,
        'email'     =>  'abc@hello.com',
        'err_email'  =>  'abc.bcd@hello@com',
        'time'  =>  1429201830,
    );

    public function testCheck1() {
        $filter = new Filter();

        $filter->field('name')
               ->alias('my_name')
               ->validate('name is too long', 'Filter::strLength', [1, 9]);

        $filter->field('time')
               ->validate("isn't not number", "Filter::checkInt")
               ->validate("isn't not numeric", 'is_numeric')
               ->sanitize('Filter::transDate');

        $this->assertTrue($filter->check($this->inputs1));

        $this->assertNull($filter->getError());

        $this->assertEquals(array(
            'my_name'   =>  '中文abc',
            'time'      =>  '2015-04-17 00:30:30',
        ), $filter->getResult());
    }

    public function testCheck2() {
        $filter = new Filter();

        $filter->field('err_email')
               ->validate('is correct email', 'Filter::checkEmail', null, true);

        $filter->field('time')
               ->validate('incorrect time', 'Filter::regex', '/^\d+$/');

        $this->assertTrue($filter->check($this->inputs1));

        $this->assertEquals($filter->getResult(), [
            'err_email'  =>  'abc.bcd@hello@com',
            'time'       =>  1429201830,
        ]);
    }

    public function testAlias() {
        $filter = new Filter();

        $filter->field('name')->alias('my_name');

        $this->assertTrue($filter->check($this->inputs1));

        $this->assertEquals($filter->getResult(), [
            'my_name'   =>  '中文abc',
        ]);
    }

}
