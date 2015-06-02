<?php
// This file is part of Moodle - http://moodle.org/
//
// Signinsheet is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Signinsheet is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
 
 
/**
 *
 * @package    block_signinsheet
 * @copyright  2013 Kyle Goslin, Daniel McSweeney
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

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

//extra lines to print?
$extra = optional_param('extra', '', PARAM_TEXT);;

$rendertype = optional_param('rendertype', '', PARAM_TEXT);
if(isset($rendertype)){
	
	if($rendertype == 'all' || $rendertype == ''){
		
		echo renderAll($extra);
		
	}
	else if($rendertype == 'group'){
	
		echo renderGroup($extra);
	
	}
	
} else {

	renderGroup();
}


?>

<script>window.print();</script> 

