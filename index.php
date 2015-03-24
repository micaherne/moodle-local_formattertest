<?php

define('CLI_SCRIPT', 1);
require_once('../../config.php');

$root = $CFG->dirroot.'/lang';
$originalmanager = new core_string_manager_standard($root, $root, array());
$formattermanager = new \local_formattertest\string_manager_messageformatter($root, $root, array());

$count = 10000;

$testdata = array(
	'original1' => 'test',
	'original2' => (object) array('test' => 'test'),
	'formatter1' => array('what' => 'something', 'dummy' => 'one', 'dummy2' => 'two', 'dummy3' => 'three', 'dummy4' => 'four'),
	'formatter2' => array('count' => 10000)
);

foreach ($testdata as $string => $a) {

	mtrace("Test: " . $string);

	$start1 = microtime(true);
	for($i = 0; $i < $count; $i++) {
		$x = $originalmanager->get_string($string, 'local_formattertest', $a);
	}
	mtrace("Original ($x): " . (microtime(true) - $start1));

	$start2 = microtime(true);
	for($i = 0; $i < $count; $i++) {
		$x = $formattermanager->get_string($string, 'local_formattertest', $a);
	}
	mtrace("Formatter ($x): " . (microtime(true) - $start2));
}