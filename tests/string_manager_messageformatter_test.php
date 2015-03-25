<?php

use local_formattertest\string_manager_messageformatter;
defined('MOODLE_INTERNAL') || die();

class string_manager_messageformatter_test extends advanced_testcase {

	public function setUp() {
		global $CFG;
		$root = $CFG->dirroot.'/lang';
		$this->manager = new string_manager_messageformatter($root, $root, array());
	}

	public function testConstruct() {
		$this->assertTrue(true);
		$this->assertInstanceOf('core_string_manager', $this->manager);
	}

	public function testOriginalStyle() {
		$test1 = $this->manager->get_string('phpunit1', 'local_formattertest', 'test');
		$this->assertEquals('This is a test', $test1);

		$test2 = $this->manager->get_string('phpunit2', 'local_formattertest', (object) array('test' => 'test'));
		$this->assertEquals('This is a test', $test2);
	}

	public function testFormatter() {
		$test1 = $this->manager->get_string('phpunit3', 'local_formattertest', array('what' => 'something'));
		$this->assertEquals('I have something', $test1);

		$test2 = $this->manager->get_string('phpunit3', 'local_formattertest', (object) array('what' => 'something'));
		$this->assertEquals('I have something', $test2);

		$obj = get_string('lazystring1', 'local_formattertest', null, true);
		$test3 = $this->manager->get_string('phpunit4', 'local_formattertest', array('what' => $obj));
		$this->assertEquals('I have lazy string', $test3);

		$test4 = $this->manager->get_string('phpunit5', 'local_formattertest', array('count' => 1));
		$this->assertEquals('I have 1 thing', $test4);

		$test5 = $this->manager->get_string('phpunit5', 'local_formattertest', array('count' => 10));
		$this->assertEquals('I have 10 things', $test5);
	}

}