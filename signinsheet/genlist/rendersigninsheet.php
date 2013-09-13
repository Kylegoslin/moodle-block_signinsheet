<?php

//require_once("../../../config.php");
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
	
 	$imageURL =  $DB->get_field('block_signinsheet', 'field_value', array('id'=>1), $strictness=IGNORE_MISSING);
	echo '<img src="'.$imageURL.'"/><br><div style="height:30px"></div>';
	
}


/*
 * 
 * 
 *
 * 
 * */ 
function renderGroup(){
		
	global $DB, $cid, $CFG;
	$outputHTML = '';
	
	$cid = required_param('cid', PARAM_INT);
	$selectedGroupId = optional_param('selectgroupsec', '', PARAM_INT);
	
	$appendOrder = '';
	$orderBy = optional_param('orderby', '', PARAM_TEXT);
	
	
		
		if($orderBy == 'byid'){
			$appendOrder = ' order by userid';
		}
		else if($orderBy == 'firstname'){
			$appendOrder = ' order by  (select firstname from '.$CFG->prefix.'user usr where userid = usr.id)';
		}
		else if($orderBy == 'lastname'){
			$appendOrder = ' order by  (select lastname from '.$CFG->prefix.'user usr where userid = usr.id)';
		}
		 else {
			$appendOrder = ' order by userid';
		}
	
	

	
			// Check if we need to include a custom field
	$addFieldEnabled = get_config('block_signinsheet', 'includecustomfield');
	
	
	
	$groupName = $DB->get_record('groups', array('id'=>$selectedGroupId), $fields='*', $strictness=IGNORE_MISSING); 
	
	$query = 'select * from '.$CFG->prefix.'groups_members where groupid = ?' . $appendOrder;
	
	
	
	$result = $DB->get_records_sql($query,array($selectedGroupId));
	$date = date('d-m-y');
	
	
	$courseName = $DB->get_record('course', array('id'=>$cid), 'fullname', $strictness=IGNORE_MISSING); 

	$outputHTML .= '<span style="font-size:25px"> <b>'. get_string('signaturesheet', 'block_signinsheet').'</span><br>';

	$outputHTML .= '<span style="font-size:20px"> <b>'. get_string('course', 'block_signinsheet').':</b> ' .$courseName->fullname.'</span><br><p></p>';
	
	
	$outputHTML .= '<span style="font-size:18px"> <b>'. get_string('date', 'block_signinsheet').':</b> _______________________</span><p></p>';
	
	$outputHTML .= '<span style="font-size:18px"> <b>'. get_string('description', 'block_signinsheet').': __________________________________________________</b> </span><p></p>&nbsp;<p></p>&nbsp;';

	if(isset($groupName)){
		$outputHTML .= '<span style="font-size:18px">'. $groupName->name . '</span><p></p>';
	}

	$outputHTML .= '<table style="border-style: solid;" width="600px"  border="1px"><tr>
					<td style="border-right: thin solid; border-bottom: thin solid" border="1px" width="200"><b>Name</b></td>
				';
	

	
if($addFieldEnabled){
		$fieldId = get_config('block_signinsheet', 'customfieldselect');
		$fieldName = $DB->get_field('user_info_field', 'name', array('id'=>$fieldId), $strictness=IGNORE_MISSING);
		$outputHTML.='<td style="border-right: thin solid; border-bottom: thin solid" border="1px" width="150"><b>'.$fieldName.'</b></td>';
}

//Add custom field text if enabled
$addTextField = get_config('block_signinsheet', 'includecustomtextfield');
if($addTextField){
		$fieldData = get_config('block_signinsheet', 'customtext');
		$outputHTML.='<td style="border-right: thin solid; border-bottom: thin solid" border="1px" width="150"><b>'.$fieldData.'</b></td>';

}	
// Id number field enabled
$addIdField = get_config('block_signinsheet', 'includeidfield');
if($addIdField){
		
		$outputHTML.='<td style="border-right: thin solid; border-bottom: thin solid" border="1px" width="150"><b>'. get_string('idnumber', 'block_signinsheet').' </b></td>';

}	

	
	$outputHTML .= '<td style="border-right: thin solid; border-bottom: thin solid" border="1px"><b>Signature</b></td></tr>';
	
	$colCounter = 0;
	$totalRows = 0;
	
	foreach($result as $face){


		$outputHTML .=  printSingleFace($face->userid, $cid);
	
		
		
		
		
	}
	

	
	$outputHTML .= '</tr></table>';
	
	return $outputHTML;
	
	
}


/*
 * 
 * Render the entire class 
 * 
 * */
function renderAll(){
	
	global $DB, $cid, $OUTPUT, $CFG;
	
	
	$appendOrder = '';
	$orderBy = '';
	$cid = required_param('cid', PARAM_INT);
	$orderBy = optional_param('orderby', '', PARAM_TEXT);
	
	

		
		if($orderBy == 'byid'){
			$appendOrder = ' order by userid';
		}
		else if($orderBy == 'firstname'){
			$appendOrder = ' order by  (select firstname from '.$CFG->prefix.'user usr where userid = usr.id)';
		} 
		else if($orderBy == 'lastname'){
			$appendOrder = ' order by  (select lastname from '.$CFG->prefix.'user usr where userid = usr.id)';
		} 
		
		else {
			$appendOrder = ' order by  (select firstname from '.$CFG->prefix.'user usr where userid = usr.id)';
	
		}
	
	$query = "select userid from ".$CFG->prefix."user_enrolments en where en.enrolid IN (select e.id from ".$CFG->prefix."enrol e where courseid= ?)" . $appendOrder;
	
		// Check if we need to include a custom field
	$addFieldEnabled = get_config('block_signinsheet', 'includecustomfield');
	
	// Get the list of users for this particular course
	$result = $DB->get_records_sql($query, array($cid));

	$date = date('d-m-y');
	$courseName = $DB->get_record('course', array('id'=>$cid), 'fullname', $strictness=IGNORE_MISSING); 
	$outputHTML = '';
	
	$outputHTML .= '<span style="font-size:25px"> <b>'. get_string('signaturesheet', 'block_signinsheet').'</span><br>';
	
	
	$outputHTML .= '<span style="font-size:20px"> <b>'. get_string('course', 'block_signinsheet').':</b> ' .$courseName->fullname.'</span><br><p></p>';
	


	
	$outputHTML .= '<span style="font-size:18px"> <b>'. get_string('date', 'block_signinsheet').':</b> _______________________</span><p></p>';
	
	
	$outputHTML .= '<span style="font-size:18px"> <b>'. get_string('description', 'block_signinsheet').': __________________________________________________</b> </span><p></p>&nbsp;<p></p>&nbsp;';
	
	
	$outputHTML .= '<table style="border-style: solid;" width="600px"  border="1px"><tr>
					<td style="border-right: thin solid; border-bottom: thin solid" border="1px" width="200"><b>Name</b></td>
				';
	

	if($addFieldEnabled){
		$fieldId = get_config('block_signinsheet', 'customfieldselect');
		$fieldName = $DB->get_field('user_info_field', 'name', array('id'=>$fieldId), $strictness=IGNORE_MISSING);
		$outputHTML.='<td style="border-right: thin solid; border-bottom: thin solid" border="1px" width="150"><b>'.$fieldName.'</b></td>';
	} else {
		//$outputHTML .= '<td style="border-right: thin solid; border-bottom: thin solid" border="1px" width="150"></td>';
		}
	
//Add custom field text if enabled
$addTextField = get_config('block_signinsheet', 'includecustomtextfield');
if($addTextField){
		$fieldData = get_config('block_signinsheet', 'customtext');
		$outputHTML.='<td style="border-right: thin solid; border-bottom: thin solid" border="1px" width="150"><b>'.$fieldData.'</b></td>';

}	


// Id number field enabled
$addIdField = get_config('block_signinsheet', 'includeidfield');
if($addIdField){
		
		$outputHTML.='<td style="border-right: thin solid; border-bottom: thin solid" border="1px" width="150"><b>'. get_string('idnumber', 'block_signinsheet').' </b></td>';

}	




	$outputHTML .='	<td style="border-right: thin solid; border-bottom: thin solid" border="1px"><b>Signature</b></td>
	</tr>';
	
	$colCounter = 0;
	$totalRows = 0;

	foreach($result as $face){


		$outputHTML .=  printSingleFace($face->userid, $cid);

	}
	
	
	
	$outputHTML .= '</table>';
	
	return $outputHTML;
	
}
/*
 *  Render a single profile face
 * 
 * 
 */
function printSingleFace($uid, $cid){
	global $DB, $OUTPUT;
	
	

	
	
	$singleRec = $DB->get_record('user', array('id'=> $uid), $fields='*', $strictness=IGNORE_MISSING); 
	
	$firstName = $singleRec->firstname;
	$lastname = $singleRec->lastname;

	//$user = $DB->get_record('user', array('id' => $uid));
	
	
	$picOutput = '';
	
	global $PAGE; 
	
	
	
	
	
	
	$outputHTML =  '
				
				
				<tr height="10">
					<td  style="border-right: thin solid;  border-bottom: thin solid" border="1px" width="200">' . $firstName . ' ' . $lastname . '</td>';

	$addFieldEnabled = get_config('block_signinsheet', 'includecustomfield');
	
	// Include additional field data if enabled
	if($addFieldEnabled){
		$fieldId = get_config('block_signinsheet', 'customfieldselect');
		$fieldData = $DB->get_field('user_info_data', 'data', array('fieldid'=>$fieldId, 'userid'=>$uid), $strictness=IGNORE_MISSING);
		$outputHTML .=	'<td style="border-right: thin solid; border-bottom: thin solid" border="1px" width="150">'.$fieldData.'  </td>';
	} else {
	//	$outputHTML .=	'<td style="border-right: thin solid;  border-bottom: thin solid" border="1px" width="150">  </td>';
	}
	
	//Add custom field text if enabled
	$addTextField = get_config('block_signinsheet', 'includecustomtextfield');
	if($addTextField){
			$outputHTML .=	'<td style="border-right: thin solid; border-bottom: thin solid" border="1px" width="150">  </td>';

	}


	// Id number field enabled
	$addIdField = get_config('block_signinsheet', 'includeidfield');
	if($addIdField){
			
			$outputHTML .=	'<td style="border-right: thin solid; border-bottom: thin solid" border="1px" width="150"> '.$singleRec->idnumber.' </td>';

	}	





	$outputHTML .='	<td style=" border-bottom: thin solid"> </td>
				</tr>
				
		';

return $outputHTML;

}
