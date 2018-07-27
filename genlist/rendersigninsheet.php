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
 *
 *
 * rendersigninsheet.php
 * This file is used for rendering the signin sheet HTML. From this, both full class and
 * group based signinsheets can be generated.
 */

global $CFG, $DB;
require_login();



/*
 * 
 * Retrieve and print the logo for the top of the
 * sign in sheet.
 * 
 * */
function printHeaderLogo(){
	
	global $DB;
	
 	$imageurl =  $DB->get_field('block_signinsheet', 'field_value', array('id'=>1), $strictness=IGNORE_MISSING);
	echo '<img src="'.$imageurl.'"/><br><div style="height:30px"></div>';
	
}


/*
 * 
 * 
 *
 * 
 * */ 
function renderGroup($extra){
		
	global $DB, $cid, $CFG;
	$outputhtml = '';
	
	$cid = required_param('cid', PARAM_INT);
	$selectedgroupid = optional_param('selectgroupsec', '', PARAM_INT);
	$extra = optional_param('extra', '', PARAM_INT);
	
	$appendorder = '';
	$orderby = optional_param('orderby', '', PARAM_TEXT);
	
	
		
		if($orderby == 'byid'){
			$appendorder = ' order by userid';
		}
		else if($orderby == 'firstname'){
			$appendorder = ' order by  (select firstname from '.$CFG->prefix.'user usr where userid = usr.id)';
		}
		else if($orderby == 'lastname'){
			$appendorder = ' order by  (select lastname from '.$CFG->prefix.'user usr where userid = usr.id)';
		}
		 else {
			$appendorder = ' order by userid';
		}
	
	

	
	// Check if we need to include a custom field in the list created
	$addfieldenabled = get_config('block_signinsheet', 'includecustomfield');
	
	
	
	$groupname = $DB->get_record('groups', array('id'=>$selectedgroupid), $fields='*', $strictness=IGNORE_MISSING); 
	
	$query = 'select * from '.$CFG->prefix.'groups_members where groupid = ?' . $appendorder;
	
	
	
	$result = $DB->get_records_sql($query,array($selectedgroupid));
	$date = date('d-m-y');
	
	
	$courseName = $DB->get_record('course', array('id'=>$cid), 'fullname', $strictness=IGNORE_MISSING); 
	$courseSName = $DB->get_record('course', array('id'=>$cid), 'shortname', $strictness=IGNORE_MISSING); 
	$outputhtml .= '<span style="font-size:25px"> <b>'. get_string('signaturesheet', 'block_signinsheet').'</span><br>';

	$outputhtml .= '<span style="font-size:20px"> <b>'. get_string('course', 'block_signinsheet').': </b>' .$courseSName->shortname.' - ' .$courseName->fullname.'</span><br><p></p>';
	
	$reportdate = date('l jS \of F Y');
	$outputhtml .= '<span style="font-size:18px"> <b>'. get_string('date', 'block_signinsheet').':</b> '.$reportdate.'</span><p></p>';
	
	$outputhtml .= '<span style="font-size:18px"> <b>'. get_string('description', 'block_signinsheet').': __________________________________________________</b> </span><p></p>&nbsp;<p></p>&nbsp;';

	if(isset($groupname)){
		$outputhtml .= '<span style="font-size:18px">'. $groupname->name . '</span><p></p>';
	}

	$outputhtml .= '<table style="border-style: solid;" width="100%"  border="1px"><tr>
					<td style="border-right: thin solid; border-bottom: thin solid" border="1px" width="25%"><b>'. get_string('personname', 'block_signinsheet').'</b></td>
				';
	

	
    if($addfieldenabled){
            $fieldid = get_config('block_signinsheet', 'customfieldselect');
            $fieldname = $DB->get_field('user_info_field', 'name', array('id'=>$fieldid), $strictness=IGNORE_MISSING);
            $outputhtml.='<td style="border-right: thin solid; border-bottom: thin solid" border="1px" width="20%"><b>'.$fieldname.'</b></td>';
    }

    //Add custom field text if enabled
    $addtextfield = get_config('block_signinsheet', 'includecustomtextfield');
    if($addtextfield){
            $fielddata = get_config('block_signinsheet', 'customtext');
            $outputhtml.='<td style="border-right: thin solid; border-bottom: thin solid" border="1px" width="20%"><b>'.$fielddata.'</b></td>';

    }	
    // Id number field enabled
    $addidfield = get_config('block_signinsheet', 'includeidfield');
    if($addidfield){
            
            $outputhtml.='<td style="border-right: thin solid; border-bottom: thin solid" border="1px" width="15%"><b>'. get_string('idnumber', 'block_signinsheet').' </b></td>';

    }	

    // Add additional mdl_user field if enabled
    $addUserField = get_config('block_signinsheet', 'includedefaultfield');
    if($addUserField){
        $mdlUserFieldName = get_config('block_signinsheet', 'defaultfieldselection');
        $outputhtml.='<td style="border-right: thin solid; border-bottom: thin solid" border="1px" width="20%"><b>'. $mdlUserFieldName.' </b></td>';
    }
	
	$outputhtml .= '<td style="border-right: thin solid; border-bottom: thin solid" border="1px"><b>'. get_string('signature', 'block_signinsheet').'</b></td></tr>';
	
	$colCounter = 0;
	$totalrows = 0;
	
	foreach($result as $ur){
		$outputhtml .=  printSingleUser($ur->userid, $cid);
	}
	
	
	//do we need to print additional lines
	for ($x = 1; $x <= $extra; $x++) {
    	$outputhtml .=  printblank();
	} 

	
	$outputhtml .= '</tr></table>';
	
	return $outputhtml;
	
	
}


/*
 * 
 * Render the entire class list.
 * 
 * */
function renderAll($extra){
	
	global $DB, $cid, $OUTPUT, $CFG;
	
	
	$appendorder = '';
	$orderby = '';
	$cid = required_param('cid', PARAM_INT);
	$orderby = optional_param('orderby', '', PARAM_TEXT);
	
	

		
		if($orderby == 'byid'){
			$appendorder = ' order by userid';
		}
		else if($orderby == 'firstname'){
			$appendorder = ' order by  (select firstname from '.$CFG->prefix.'user usr where userid = usr.id)';
		} 
		else if($orderby == 'lastname'){
			$appendorder = ' order by  (select lastname from '.$CFG->prefix.'user usr where userid = usr.id)';
		} 
		
		else {
			$appendorder = ' order by  (select firstname from '.$CFG->prefix.'user usr where userid = usr.id)';
	
		}
	
	$query = "select userid from ".$CFG->prefix."user_enrolments en where en.enrolid IN (select e.id from ".$CFG->prefix."enrol e where courseid= ?)" . $appendorder;
	
    // Check if we need to include a custom field
	$addfieldenabled = get_config('block_signinsheet', 'includecustomfield');
	
	// Get the list of users for this particular course
	$result = $DB->get_records_sql($query, array($cid));

	$date = date('d-m-y');
	
	$courseName = $DB->get_record('course', array('id'=>$cid), 'fullname', $strictness=IGNORE_MISSING); 
	$courseSName = $DB->get_record('course', array('id'=>$cid), 'shortname', $strictness=IGNORE_MISSING); 
	$outputhtml = '';
	$outputhtml .= '<span style="font-size:25px"> <b>'. get_string('signaturesheet', 'block_signinsheet').'</span><br>';

	$outputhtml .= '<span style="font-size:20px"> <b>'. get_string('course', 'block_signinsheet').': </b>' .$courseSName->shortname.' - ' .$courseName->fullname.'</span><br><p></p>';
	
	$reportdate = date('l jS \of F Y');
	$outputhtml .= '<span style="font-size:18px"> <b>'. get_string('date', 'block_signinsheet').':</b> '.$reportdate.'</span><p></p>';
	
	$outputhtml .= '<span style="font-size:18px"> <b>'. get_string('description', 'block_signinsheet').': __________________________________________________</b> </span><p></p>&nbsp;<p></p>&nbsp;';
	
	
	$outputhtml .= '<table style="border-style: solid;" width="100%"  border="1px"><tr>
					<td style="border-right: thin solid; border-bottom: thin solid" border="1px" width="25%"><b>'. get_string('personname', 'block_signinsheet').'</b></td>
				';
	
    // ------------- Add Different fields --------------------------------------------
	if($addfieldenabled){
		$fieldid = get_config('block_signinsheet', 'customfieldselect');
		$fieldname = $DB->get_field('user_info_field', 'name', array('id'=>$fieldid), $strictness=IGNORE_MISSING);
		$outputhtml.='<td style="border-right: thin solid; border-bottom: thin solid" border="1px" width="20%"><b>'.$fieldname.'</b></td>';
	} 
        
    //Add custom field text if enabled
    $addtextfield = get_config('block_signinsheet', 'includecustomtextfield');
    if($addtextfield){
            $fielddata = get_config('block_signinsheet', 'customtext');
            $outputhtml.='<td style="border-right: thin solid; border-bottom: thin solid" border="1px" width="20%"><b>'.$fielddata.'</b></td>';

    }	


    // Id number field enabled
    $addidfield = get_config('block_signinsheet', 'includeidfield');
    if($addidfield){
            
            $outputhtml.='<td style="border-right: thin solid; border-bottom: thin solid" border="1px" width="15%"><b>'. get_string('idnumber', 'block_signinsheet').' </b></td>';

    }	

    // Add additional mdl_user field if enabled
    $addUserField = get_config('block_signinsheet', 'includedefaultfield');
    if($addUserField){
        $mdlFieldName = get_config('block_signinsheet', 'defaultfieldselection');
        $outputhtml.='<td style="border-right: thin solid; border-bottom: thin solid" border="1px" width="20%"><b>'. $mdlFieldName.' </b></td>';
    }


    // Signature block space
	$outputhtml .='	<td style="border-right: thin solid; border-bottom: thin solid" border="1px"><b>'.get_string('signature', 'block_signinsheet').'</b></td>
	</tr>';
	
	$colCounter = 0;
	$totalrows = 0;

	foreach($result as $us){
		$outputhtml .=  printSingleUser($us->userid, $cid);
	}
	
	
	//do we need to print additional lines
	for ($x = 1; $x <= $extra; $x++) {
    	$outputhtml .=  printblank();
	} 
	
	
	$outputhtml .= '</table>';
	
	return $outputhtml;
	
}
/*
 *  Render a single user
 * 
 * 
 */
function printSingleUser($uid, $cid){
	global $DB, $OUTPUT;
	
	

	
	
	$singlerec = $DB->get_record('user', array('id'=> $uid), $fields='*', $strictness=IGNORE_MISSING); 
	
	$firstname = $singlerec->firstname;
	$lastname = $singlerec->lastname;

	//$user = $DB->get_record('user', array('id' => $uid));
	
	
	$picoutput = '';
	
	global $PAGE; 
	
	
	$outputhtml =  '
				
				
				<tr height="10">
					<td  style="border-right: thin solid;  border-bottom: thin solid" border="1px" width="20%">' . $firstname . ' ' . $lastname . '</td>';

	$addfieldenabled = get_config('block_signinsheet', 'includecustomfield');
	
	// Include additional field data if enabled
	if($addfieldenabled){
		$fieldid = get_config('block_signinsheet', 'customfieldselect');
		$fielddata = $DB->get_field('user_info_data', 'data', array('fieldid'=>$fieldid, 'userid'=>$uid), $strictness=IGNORE_MISSING);
		$outputhtml .=	'<td style="border-right: thin solid; border-bottom: thin solid" border="1px" width="20%">'.$fielddata.'  </td>';
	}
	
	//Add custom field text if enabled
	$addtextfield = get_config('block_signinsheet', 'includecustomtextfield');
	if($addtextfield){
			$outputhtml .=	'<td style="border-right: thin solid; border-bottom: thin solid" border="1px" width="20%">  </td>';

	}


    // Id number field enabled
    $addidfield = get_config('block_signinsheet', 'includeidfield');
    if($addidfield){
                $outputhtml .=	'<td style="border-right: thin solid; border-bottom: thin solid" border="1px" width="15%"> '.$singlerec->idnumber.' </td>';
    }	


    $addUserField = get_config('block_signinsheet', 'includedefaultfield');
    if($addUserField){
        
        $mdlUserFieldName = get_config('block_signinsheet', 'defaultfieldselection');
        $outputhtml .=	'<td style="border-right: thin solid; border-bottom: thin solid" border="1px" width="20%"> '.$singlerec->$mdlUserFieldName.' </td>';

    }


	$outputhtml .='	<td style=" border-bottom: thin solid"> </td>
				</tr>
				
		';

return $outputhtml;

}

/*
Print a number of blank spaces onto the sheet allowing students who have
not been correctly enrolled in Moodle to sign in.
*/
function printblank(){

		
	$outputhtml =  '<tr height="10"><td  style="border-right: thin solid;  border-bottom: thin solid" border="1px" width="200"> &nbsp;</td>';

	$addfieldenabled = get_config('block_signinsheet', 'includecustomfield');
	
	// Include additional field data if enabled
	if($addfieldenabled){
		$outputhtml .=	'<td style="border-right: thin solid; border-bottom: thin solid" border="1px" width="20%">&nbsp; </td>';
	} 
	
	//Add custom field text if enabled
	$addtextfield = get_config('block_signinsheet', 'includecustomtextfield');
	if($addtextfield){
			$outputhtml .=	'<td style="border-right: thin solid; border-bottom: thin solid" border="1px" width="20%">&nbsp;  </td>';
	}

	// Id number field enabled
	$addidfield = get_config('block_signinsheet', 'includeidfield');
	if($addidfield){
			$outputhtml .=	'<td style="border-right: thin solid; border-bottom: thin solid" border="1px" width="20%"> &nbsp; </td>';
	}	
	$addUserField = get_config('block_signinsheet', 'includedefaultfield');
	if($addUserField){
	
	$outputhtml .=	'<td style="border-right: thin solid; border-bottom: thin solid" border="1px" width="20%"> &nbsp; </td>';

	}


	$outputhtml .='	<td style=" border-bottom: thin solid"> </td>
				</tr>';

	return $outputhtml;

}
