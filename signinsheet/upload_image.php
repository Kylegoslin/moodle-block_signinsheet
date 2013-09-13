<?php


require('../../config.php');
global $CFG, $DB;
$formPath = "$CFG->libdir/formslib.php";
require_once($formPath);

$PAGE->navbar->ignore_active();
$PAGE->navbar->add(get_string('settings', 'block_signinsheet'), new moodle_url('../../admin/settings.php?section=blocksettingsigninsheet'));
$PAGE->navbar->add(get_string('uploadimage', 'block_signinsheet'));


$PAGE->set_url('/blocks/cmanager/course_new.php');
$PAGE->set_context(get_system_context());
$PAGE->set_heading(get_string('pluginname', 'block_signinsheet'));


 class signinsheet_uploader_form extends moodleform {
 
 
    function definition() {
    	$mform = $this->_form;
		$mform->addElement('filepicker', 'userfile', get_string('file'), null,
                   array('maxbytes' => $maxbytes, 'accepted_types' => '*'));
		
	//$mform->addElement('file', 'groupdocs_file', get_string('groupdocs_file', 'block_groupdocs'));
     //   $mform->addElement('filemanager', 'attachment_filemanager', get_string('attachment', 'glossary'), null, $attachmentoptions);
      //  $mform->addHelpButton('attachment_filemanager', 'attachment', 'glossary');
	
     $this->add_action_buttons();
  //$this->set_data($currententry);
	}
	
 }
$mform = new signinsheet_uploader_form();

if ($mform->is_cancelled()) {
   redirect(new moodle_url('/admin/settings.php?section=blocksettingsigninsheet'));
	
} else if ($fromform = $mform->get_data()) {
	
$success = $mform->save_file('userfile', '/signinsheet', true);
$storedfile = $mform->save_stored_file('userfile', 1, 'signinsheet', 'content', 0, '/', null, true);
// ---------------------------------------------------------------------------

/*
  $context = context_system::instance();
  $timenow = time();




$maxfiles = 99;                // TODO: add some setting
$maxbytes = $course->maxbytes; // TODO: add some setting

$definitionoptions = array('trusttext'=>true, 'subdirs'=>false, 'maxfiles'=>$maxfiles, 'maxbytes'=>$maxbytes, 'context'=>$context);
$attachmentoptions = array('subdirs'=>false, 'maxfiles'=>$maxfiles, 'maxbytes'=>$maxbytes);
   $entry = new stdClass();
	
    $entry->concept          = trim($entry->concept);
    $entry->definition       = '';          // updated later
    $entry->definitionformat = FORMAT_HTML; // updated later
    $entry->definitiontrust  = 0;           // updated later
    $entry->timemodified     = $timenow;
    $entry->approved         = 0;
    $entry->usedynalink      = isset($entry->usedynalink) ?   $entry->usedynalink : 0;
    $entry->casesensitive    = isset($entry->casesensitive) ? $entry->casesensitive : 0;
    $entry->fullmatch        = isset($entry->fullmatch) ?     $entry->fullmatch : 0;

    if ($glossary->defaultapproval or has_capability('mod/glossary:approve', $context)) {
        $entry->approved = 1;
    }

    if (empty($entry->id)) {
        //new entry
        $entry->id = $DB->insert_record('glossary_entries', $entry);
        }


   $entry = file_postupdate_standard_filemanager($fromform, 'attachment', $attachmentoptions, $context, 'mod_glossary', 'attachment', 0);

*/
// ----------------------------------------------------------------------------

} else {



 
}










echo $OUTPUT->header();
echo $mform->display();
echo $OUTPUT->footer();
?>
