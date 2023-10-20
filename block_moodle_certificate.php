<?php

class block_moodle_certificate extends block_base
{
    function init()
    {
        $this->title = get_string('pluginname', 'block_moodle_certificate');
    }

    public function has_config() {
        return true;
    }

    function get_content()
    {
        global $CFG, $DB, $USER;

        if ($this->content !== NULL) {
            return $this->content;
        }

        if (!isloggedin()) {
            return $this->content; // Only display if logged in.
        }

        if ($this->context->get_course_context(false)) {
            if (is_guest($this->context)) {
                return $this->content; // Course guests don't see the checklist block.
            }
        } else if (isguestuser()) {
            return $this->content;  // Site guests don't see the checklist block.
        }

        $certificates = $DB->get_records_sql("SELECT c.id,c.fullname
                                                  FROM {course_completions} cc
                                                  JOIN {course} c ON cc.course=c.id
                                                  WHERE cc.userid = ?
                                                  ORDER BY timecompleted DESC", [$USER->id]);
        $rows = '';
        foreach ($certificates as $i=>$certificate)
        {
            $rows .= "<tr>
                        <td class='align-middle text-center'>$certificate->fullname</td>
                        <td class='text-center'>
                            <a href='".$CFG->wwwroot."/blocks/test_certificate/certificate.php?id=$certificate->id&type=pdf' _target='blank' class='btn btn-primary'>"
                            . get_string('download', 'block_moodle_certificate') .
                            "</a>
                        </td>        
                    </tr>";
        }
        $str = "<div>
                    <table class='table table-bordered'>
                        $rows
                    </table>
                </div>";

        $this->content = new stdClass;
        $this->content->text = $str;

        return $this->content;
    }
}