	

<?php


class block_signinsheet extends block_base {


function has_config() {return true;}

function init() {

    $this->title   = get_string('pluginname', 'block_signinsheet');
    $plugin = new stdClass();
    $plugin->version   = 2013090213;      // The current module version (Date: YYYYMMDDXX)
    $plugin->requires  = 2011070110.00;      // Requires this Moodle version


  }

function get_content() {



    if ($this->content !== NULL) {
      return $this->content;
    }

    global $CFG;
    global $COURSE;
	global $DB;

    $this->content =  new stdClass;

	$blockHidden = get_config('block_signinsheet', 'hidefromstudents');

	//
	// If the admin has selected to hide from students
	//
	if (!empty($blockHidden)) {
		if (has_capability('block/signinsheet:viewblock', $this->context)) {
	   		 $this->content->text = getSignInNav();
		} else {
			
		}
	} else {
		$this->content->text = getSignInNav();
	}


    $this->content->footer = '';

    return $this->content;
  }
}


/*


*/
function getSignInNav(){


	global $USER, $DB, $CFG; 
	$cid = optional_param('id', '', PARAM_INT);
	$bodyHTML = '<img src="'.$CFG->wwwroot. '/blocks/signinsheet/printer.gif"/> <a href="'.$CFG->wwwroot. '/blocks/signinsheet/genlist/show.php?cid='.$cid.'">'. get_string('genlist', 'block_signinsheet').'</a><br>
				
				';


	return $bodyHTML;


}



