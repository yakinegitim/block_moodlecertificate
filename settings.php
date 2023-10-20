<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configtext('block_moodle_certificate/title',
        new lang_string('titlesetting', 'block_moodle_certificate'),
        new lang_string('titlesettingdescription', 'block_moodle_certificate'),
        new lang_string('titlesetting', 'block_moodle_certificate'), PARAM_RAW_TRIMMED, 255));

    $settings->add(new admin_setting_configtext('block_moodle_certificate/subtext',
        new lang_string('subtextsetting', 'block_moodle_certificate'),
        new lang_string('subtextsettingdescription', 'block_moodle_certificate'),
        new lang_string('subtextsetting', 'block_moodle_certificate'), PARAM_RAW_TRIMMED, 255));
}
