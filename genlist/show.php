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
 * @copyright  2018 Kyle Goslin, Daniel McSweeney
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/* ----------------------------------------------------------------------
 * show.php
 * 
 * Description:
 * This is the main display page used for calling each different reresentation
 * of signinsheets
 * ----------------------------------------------------------------------
 */
require_once("../../../config.php");
global $CFG, $DB;
require_login();
require_once("$CFG->libdir/formslib.php");
require_once('rendersigninsheet.php');

$cid = required_param('cid', PARAM_INT);
$gid = optional_param('gid', '', PARAM_INT);    


/** Navigation Bar **/
$PAGE->navbar->ignore_active();
$rendertype = '';

$selectgroupsec = optional_param('selectgroupsec', '', PARAM_TEXT);  
$extra = optional_param('extra', '', PARAM_TEXT);;

 
if(isset($selectgroupsec)){
	
	if($selectgroupsec == 'all'){
		$rendertype = 'all';
	}
	else if($selectgroupsec == 'group'){
		$rendertype == 'group';
	} 
	
	if(is_numeric($selectgroupsec)) {
		$rendertype = 'group';
	}
	
		
} else {
		$rendertype = 'all';
}

if($rendertype == 'all' || $rendertype == ''){
		$courseName = $DB->get_record('course', array('id'=>$cid), 'shortname', $strictness=IGNORE_MISSING); 
		$PAGE->navbar->add($courseName->shortname, new moodle_url($CFG->wwwroot . '/course/view.php?id=' . $cid));
		$PAGE->navbar->add(get_string('showall', 'block_signinsheet'));
	
}
else if($rendertype == 'group'){
		$courseName = $DB->get_record('course', array('id'=>$cid), 'shortname', $strictness=IGNORE_MISSING); 
		$PAGE->navbar->add($courseName->shortname, new moodle_url($CFG->wwwroot . '/course/view.php?id=' . $cid));
		$PAGE->navbar->add(get_string('showbygroup', 'block_signinsheet'));
}


$PAGE->set_url('/blocks/signinsheet/showsigninsheet/show.php');
$PAGE->set_context(get_system_context());
$PAGE->set_heading(get_string('pluginname', 'block_signinsheet'));
$PAGE->set_title(get_string('pluginname', 'block_signinsheet'));

echo $OUTPUT->header();
echo buildMenu($cid);



$logoenabled = get_config('block_signinsheet', 'customlogoenabled');

if($logoenabled){
	printHeaderLogo();
}


// Render the page
$selectgroupsec = optional_param('selectgroupsec', '', PARAM_TEXT);   
if(isset($selectgroupsec)){
	
	if($selectgroupsec == 'all' || $selectgroupsec == ''){
		 
		echo renderAll($extra);
		
	} else {
		
		echo renderGroup($extra);
	
	}
	
} else {

	echo renderAll($extra);
}

class signinsheet_form extends moodleform {
 
	function definition() {
    global $CFG;
    global $USER, $DB;
    $mform =& $this->_form; // Don't forget the underscore! 
	}
}







/*
 * 
 * Create the HTML output for the list on the right
 * hand side of the showsigninsheet.php page
 * 
 * */
function buildMenu($cid){
	
	global $DB, $CFG, $rendertype;
	
	$orderby = '';
	$orderby = optional_param('orderby', 'firstname', PARAM_TEXT);
	
	
	$outputhtml = '<div style="float:right"><form action="'.$CFG->wwwroot. '/blocks/signinsheet/genlist/show.php?cid='.$cid.'" method="post">
				 '.get_string('orderby', 'block_signinsheet').': <select name="orderby" id="orderby">
								<option value="firstname">' .get_string('firstname', 'block_signinsheet').'</option>
								<option value="lastname">'.get_string('lastname', 'block_signinsheet').'</option>
						  </select>
						  
				 '.get_string('filter', 'block_signinsheet').': <select id="selectgroupsec" name="selectgroupsec">
				 	<option value="all">'.get_string('showall', 'block_signinsheet').'</option>
				 '. buildGroups($cid).'	
				 </select>
				 <input type="submit" value="'.get_string('update', 'block_signinsheet').'"></input>
				</form>

				<script>document.getElementById(\'orderby\').value="'.$orderby.'";</script>
				<span style="float:right">
				
				<form action="../print/page.php" target="_blank">
				'.get_string('blankfields', 'block_signinsheet').': <input type="number" min="0" size="3" name="extra" value="0">
   				<input type="hidden" name="cid" value="'.$cid.'">
				<input type="hidden" name="rendertype" value="'.$rendertype.'">
				
				';
				
				// If a group was selected
				$selectgroupsec = optional_param('selectgroupsec', 'all', PARAM_TEXT); 
	$outputhtml .= '
					<script>document.getElementById(\'selectgroupsec\').value="'.$selectgroupsec.'";</script>
				';
				if(isset($selectgroupsec)){
 					$outputhtml .= '<input type="hidden" name="selectgroupsec" value="'.$selectgroupsec.'">';
				}
				$outputhtml .= '
				<input type="hidden" name="orderby" value="'.$orderby.'">
					
				
   				<input type="submit" value="'.get_string('printbutton', 'block_signinsheet').'">
				</span>
				</form>
				

			    </div>
			    

				</div>
				';
	
	return $outputhtml;
	
}
/*
 * Build up the dropdown menu items with groups that are associated
 * to the currently open course.
 * 
 */
function buildGroups($cid){
	
	global $DB;
	
	$buildhtml = '';
	$groups = $DB->get_records('groups',array('courseid'=>$cid));

	foreach($groups as $group){
		$groupId = $group->id;
		
		$buildhtml.= '<option value="'.$groupId.'">'. $group->name.'</option>';
	}
	
	return $buildhtml;
	
}

$mform = new signinsheet_form();
$mform->focus();
$mform->display();		
echo $OUTPUT->footer();

 $selectgroupsec = optional_param('selectgroupsec', '', PARAM_TEXT); 
	if(isset($selectgroupsec)){
 		$selecteditem = $selectgroupsec;
		echo '<script>
				document.getElementById("selectgroupsec").value = "'.$selecteditem.'"
			  </script>';
	 }

 $orderby = optional_param('orderby', '', PARAM_TEXT);
	if(isset($orderby)){
		$orderitem = $orderby;
		
		echo '<script>
				document.getElementById("orderby").value = "'.$orderitem.'"
			  </script>';
			  
			  if($orderitem == ""){
			  	echo '<script>
				document.getElementById("orderby").value = "firstname";
			  </script>';
				
			  }
	} 
