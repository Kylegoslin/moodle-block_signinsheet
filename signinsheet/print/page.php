<?php

require_once("../../../config.php");

global $CFG, $DB;
require_login();
$PAGE->set_context(get_system_context());
require_once('../genlist/rendersigninsheet.php');
$PAGE->set_pagelayout('base');
$PAGE->set_url('/blocks/signinsheet/print/page.php');
$logoEnabled = get_config('block_signinsheet', 'customlogoenabled');

echo $OUTPUT->header();

if($logoEnabled){
	printHeaderLogo();
}


$renderType = optional_param('rendertype', '', PARAM_TEXT);
if(isset($renderType)){
	
	if($renderType == 'all' || $renderType == ''){
		
		echo renderAll();
		
	}
	else if($renderType == 'group'){
	
		echo renderGroup();
	
	}
	
} else {

	renderGroup();
}


?>

<script>window.print();</script> 

