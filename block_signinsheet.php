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
*  block_signinsheet.php
*  The block allows for the generation of custom signin sheets for students
*  based upon the users currently enrolled in the course.
*
 */


class block_signinsheet extends block_base {


function has_config() {return true;}

function init() {

    $this->title   = get_string('pluginname', 'block_signinsheet');
    $plugin = new stdClass();
  }

function get_content() {



    if ($this->content !== NULL) {
      return $this->content;
    }

    global $CFG;
    global $COURSE;
	global $DB;

    $this->content =  new stdClass;

	$blockhidden = get_config('block_signinsheet', 'hidefromstudents');

	//
	// If the admin has selected to hide from students
	//
	if (!empty($blockhidden)) {
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
	$bodyhtml = '<img src="'.$CFG->wwwroot. '/blocks/signinsheet/printer.gif"/> <a href="'.$CFG->wwwroot. '/blocks/signinsheet/genlist/show.php?cid='.$cid.'">'. get_string('genlist', 'block_signinsheet').'</a><br>
				
				';


	return $bodyhtml;


}



