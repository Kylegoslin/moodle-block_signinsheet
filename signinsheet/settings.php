<?php
defined('MOODLE_INTERNAL') || die;
require_once($CFG->libdir.'/adminlib.php');







$uploaderLink = new moodle_url('/blocks/signinsheet/index.php');
 $settings->add(new admin_setting_configcheckbox('block_signinsheet/customlogoenabled',
     new lang_string('addcustomlogo', 'block_signinsheet'),
        new lang_string('addcustomlogodesc', 'block_signinsheet') . '<br><a href="'. $uploaderLink.'">Click here to Upload</a>', null,
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

 $settings->add(new admin_setting_configcheckbox('block_signinsheet/includecustomtextfield',
     new lang_string('includecustomtextfield', 'block_signinsheet'),
        new lang_string('includecustomtextfielddesc', 'block_signinsheet') , null,
         PARAM_INT));

   $settings->add(new admin_setting_configtext('block_signinsheet/customtext', get_string('customtext', 'block_signinsheet'), get_string('customtextdesc', 'block_signinsheet'), null, PARAM_TEXT));

 $settings->add(new admin_setting_configcheckbox('block_signinsheet/includeidfield',
     new lang_string('idfield', 'block_signinsheet'),
        new lang_string('idfielddesc', 'block_signinsheet') , null,
         PARAM_INT));



  