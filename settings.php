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
 * settings.php
 * This file is used to display the settings for the signinsheet block.
 * From these settings the user can add an image to the top of the signinsheet.
 * and add custom columns.
 */
defined('MOODLE_INTERNAL') || die;
require_once($CFG->libdir.'/adminlib.php');







$uploaderlink = new moodle_url('/blocks/signinsheet/index.php');
 $settings->add(new admin_setting_configcheckbox('block_signinsheet/customlogoenabled',
     new lang_string('addcustomlogo', 'block_signinsheet'),
        new lang_string('addcustomlogodesc', 'block_signinsheet') . '<br><a href="'. $uploaderlink.'">Click here to Upload</a>', null,
         PARAM_INT));
   

     $settings->add(new admin_setting_configcheckbox('block_signinsheet/hidefromstudents',
     new lang_string('hidefromstudents', 'block_signinsheet'),
        new lang_string('hidefromstudents_desc', 'block_signinsheet') , null,
         PARAM_INT));
  
    
	 

    
	global $DB;
	$result = $DB->get_records('user_info_field');

  if($result){
   $settings->add(new admin_setting_configcheckbox('block_signinsheet/includecustomfield',
     new lang_string('customfield', 'block_signinsheet'),
        new lang_string('customfielddesc', 'block_signinsheet') , null,
         PARAM_INT));

    	$options = array();
    	foreach ($result as $item){
    		$options[$item->id] = $item->name;
    	}

    $settings->add(new admin_setting_configselect('block_signinsheet/customfieldselect', 
    				 new lang_string('customfieldselect', 'block_signinsheet'),
                       get_string('selectcustomfield', 'block_signinsheet'), null, $options));

  }
// Include a custom text field column
 $settings->add(new admin_setting_configcheckbox('block_signinsheet/includecustomtextfield',
     new lang_string('includecustomtextfield', 'block_signinsheet'),
        new lang_string('includecustomtextfielddesc', 'block_signinsheet') , null,
         PARAM_INT));

   $settings->add(new admin_setting_configtext('block_signinsheet/customtext', get_string('customtext', 'block_signinsheet'), get_string('customtextdesc', 'block_signinsheet'), null, PARAM_TEXT));

// Include a default user field
 $settings->add(new admin_setting_configcheckbox('block_signinsheet/includedefaultfield',
     new lang_string('includedefaultfield', 'block_signinsheet'),
        new lang_string('includedefaultfield', 'block_signinsheet') , null,
         PARAM_INT));

// Get list of user fields
$options = array(

'username'=>'username',
'idnumber'=>'idnumber',
'firstname'=>'firstname',
'lastname'=>'lastname',
'email'=>'email',
'icq'=>'icq',
'skype'=>'skype',
'yahoo'=>'yahoo',
'aim'=>'aim',
'msn'=>'msn',
'phone1'=>'phone1',
'phone2'=>'phone2',
'institution'=>'institution',
'department'=>'department',
'address'=>'address',
'city'=>'city',
'country'=>'country',
'lang'=>'lang',
'calendartype'=>'calendartype',
'lastnamephonetic'=>'lastnamephonetic',
'firstnamephonetic'=>'firstnamephonetic',
'middlename'=>'middlename',
'alternatename'=>'alternatename'

    );

$settings->add(new admin_setting_configselect('block_signinsheet/defaultfieldselection', 
               get_string('selectedfield', 'block_signinsheet'),
               get_string('selectdefaultfield', 'block_signinsheet'), 'all', $options));




 $settings->add(new admin_setting_configcheckbox('block_signinsheet/includeidfield',
     new lang_string('idfield', 'block_signinsheet'),
        new lang_string('idfielddesc', 'block_signinsheet') , null,
         PARAM_INT));



  