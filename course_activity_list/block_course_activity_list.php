<?php
class block_course_activity_list extends block_base {
   public function init() {
       $this->title = get_string('course_activity_list', 'block_course_activity_list');
   }

   public function applicable_formats() {
       return array('course' => true);
   }

   public function get_content() {
       global $USER, $COURSE, $DB;

       if ($this->content !== null) {
           return $this->content;
       }

       $this->content = new stdClass();
       $this->content->text = '';

       $courseid = $COURSE->id;
       $activities = $DB->get_records('course_modules', array('course' => $courseid));

       $html = '<ul class="course-activity-list">';
       foreach ($activities as $activity) {
           $cm = get_coursemodule_from_id('', $activity->id, 0, false, MUST_EXIST);
           $completion = new completion_info($COURSE);
           $activity_completion = $completion->get_data($cm,$USER->id);

           $activity_name = $cm->name;
           $date_created = date('d-M-Y', $cm->added);
		 
		   $moduleUrl = new moodle_url('/mod/' . $cm->modname . '/view.php', ['id' => $cm->id]);

           $html .= '<li>';
           $html .= '<a href="' . $moduleUrl . '">';
           $html .= $cm->id . '-' . $activity_name . '-' . $date_created;

           if ($activity_completion && $activity_completion->completionstate === COMPLETION_COMPLETE) {
               $html .= ' - Completed';
           }

           $html .= '</a>';
           $html .= '</li>';
       }
       $html .= '</ul>';

       $this->content->text = $html;
       $this->content->footer = '';

       return $this->content;
   }
}
