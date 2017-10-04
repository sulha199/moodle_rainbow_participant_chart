<?php

require_once(dirname(__FILE__) . '/../../config.php');
// require_once($CFG->dirroot.'/blocks/completion_progress/lib.php');

defined('MOODLE_INTERNAL') || die;

class block_participantschart extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_participantschart');
    }

    function has_config() {
        return true;
    }

    function get_content() {
        global $CFG, $USER, $DB, $OUTPUT;

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        if (!empty($this->config->text)) {
            $this->content->text = $this->config->text;
        } else {
            $this->content->text = '';
            $this->content->text .= HTML_WRITER::tag('div', '', array('id' => "container"));

            $this->page->requires->js('/blocks/participantschart/thirdparty/highcharts.js', true);
            $this->page->requires->js('/blocks/participantschart/thirdparty/highcharts-more.js', true);
            $this->page->requires->js('/blocks/participantschart/thirdparty/solid-gauge.js', true);

            //$limit = ($this->config->limit)?"":0;

            $result = $this->get_participants();

            $this->page->requires->js_call_amd('block_participantschart/chartrender', 'drawChart', array($result));
        }

        return $this->content;
    }

    private function get_participants($limit = 0) {
        global $DB;

        $limit = (int) $limit;
        $limit = ($limit) ? 'limit ' . $limit : '';
        $sql = "
                    SELECT
                    c.fullname, 
                    count(u.id)                                
                    FROM 
                    {role_assignments} ra 
                    JOIN {user} u ON u.id = ra.userid
                    JOIN {role} r ON r.id = ra.roleid
                    JOIN {context} cxt ON cxt.id = ra.contextid
                    JOIN mdl_course c ON c.id = cxt.instanceid
                    WHERE ra.userid = u.id             
                    AND ra.contextid = cxt.id
                    AND cxt.contextlevel =50
                    AND cxt.instanceid = c.id
                    AND  roleid = 5
                    group by c.id
                    ORDER BY c.fullname
                    
                
        ";
        $result = $DB->get_records_sql_menu($sql);

        $participants = array();

        foreach ($result as $key => $value) {

            $participants[] = array($key, (int) $value);
        }

        return $participants;
    }

    public function applicable_formats() {
        return array(
            'site-index' => true,
            'course-view' => true,
            'course-view-social' => false,
            'mod' => true,
            //'mod-quiz' => false
        );
    }
}
