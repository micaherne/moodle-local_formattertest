<?php

define('CLI_SCRIPT', 1);
require_once('../../config.php');

$root = $CFG->dirroot.'/lang';
$originalmanager = new core_string_manager_standard($root, $root, array());
$formattermanager = new \local_formattertest\string_manager_messageformatter($root, $root, array());

$count = 10000;

$lazy = get_string('lazystring1', 'local_formattertest', null, true);

$testdata = array(
    array(
        'test',
        array('what' => 'test')
    ),
    array(
        array('what' => 'test'),
        array('what' => 'test')
    ),
    array(
        array('one' => 'one', 'two' => 'two', 'three' => 'three', 'four' => 'four'),
        array('one' => 'one', 'two' => 'two', 'three' => 'three', 'four' => 'four')
    ),
    array(
            (object)array('what' => 'test'),
            (object)array('what' => 'test')
    ),
    array(
            array('what' => $lazy),
            array('what' => $lazy)
    ),
    array(
         null,
         array('count' => 200)
    )
);

mtrace("Runs: " . $count);

purge_all_caches();

foreach ($testdata as $key => $data) {

    $originaldata = $data[0];
    $formatterdata = $data[1];

	mtrace("Test " . $key);

	$start1 = microtime(true);
	for($i = 0; $i < $count; $i++) {
		$x = $originalmanager->get_string('original' . $key, 'local_formattertest', $originaldata);
	}
	mtrace("Original ($x): " . (microtime(true) - $start1));

	$start2 = microtime(true);
	for($i = 0; $i < $count; $i++) {
		$x = $formattermanager->get_string('formatter' . $key, 'local_formattertest', $formatterdata);
	}
	mtrace("Formatter ($x): " . (microtime(true) - $start2));

	$start3 = microtime(true);
	for($i = 0; $i < $count; $i++) {
	    $x = $formattermanager->get_string('original' . $key, 'local_formattertest', $originaldata);
	}
	mtrace("Formatter with original formatting ($x): " . (microtime(true) - $start3));
}