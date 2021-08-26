<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Plugin version and other meta-data are defined here.
 *
 * @package   enrol_remita
 * @copyright 2021 Adetunji Oyebanji
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin = enrol_get_plugin('remita');

global $CFG;

if ($ADMIN->fulltree) {

    $mode_options = [
        $plugin::LIVE_MODE => get_string('mode_live', 'enrol_remita'),
        $plugin::DEMO_MODE => get_string('mode_demo', 'enrol_remita')
    ];
    $settings->add(
        new admin_setting_configselect(
            'enrol_remita/mode',
            get_string('mode', 'enrol_remita'),
            get_string('mode_desc', 'enrol_remita'),
            $plugin::DEMO_MODE,
            $mode_options
        )
    );

    $settings->add(
        new admin_setting_configtext(
            'enrol_remita/public_key',
            get_string('public_key', 'enrol_remita'),
            get_string('public_key_desc', 'enrol_remita'),
            '',
            PARAM_TEXT
        )
    );

    $settings->add(
        new admin_setting_configtext(
            'enrol_remita/merchant_id',
            get_string('merchant_id', 'enrol_remita'),
            get_string('merchant_id_desc', 'enrol_remita'),
            '',
            PARAM_TEXT
        )
    );

    $settings->add(
        new admin_setting_configtext(
            'enrol_remita/servicetype_id',
            get_string('servicetype_id', 'enrol_remita'),
            get_string('servicetype_id_desc', 'enrol_remita'),
            '',
            PARAM_TEXT
        )
    );

    $settings->add(
        new admin_setting_configtext(
            'enrol_remita/api_key',
            get_string('api_key', 'enrol_remita'),
            get_string('api_key_desc', 'enrol_remita'),
            '',
            PARAM_TEXT
        )
    );

    // --- enrol instance defaults -----------------------------
    $settings->add(
        new admin_setting_heading(
            'enrol_remita_defaults',
            get_string('enrolinstancedefaults', 'admin'),
            get_string('enrolinstancedefaults_desc', 'admin')
        )
    );

    $options = array(
        ENROL_INSTANCE_ENABLED  => get_string('yes'),
        ENROL_INSTANCE_DISABLED => get_string('no')
    );
    $settings->add(
        new admin_setting_configselect(
            'enrol_remita/status',
            get_string('status', 'enrol_remita'),
            get_string('status_desc', 'enrol_remita'),
            ENROL_INSTANCE_DISABLED,
            $options
        )
    );

    $currencies = $plugin->get_currencies();
    $settings->add(
        new admin_setting_configselect(
            'enrol_remita/currency',
            get_string('currency', 'enrol_remita'),
            '',
            'NGN',
            $currencies
        )
    );

    $settings->add(
        new admin_setting_configtext(
            'enrol_remita/maxenrolled',
            get_string('maxenrolled', 'enrol_remita'),
            get_string('maxenrolled_help', 'enrol_remita'),
            0,
            PARAM_INT
        )
    );

    if (!during_initial_install()) {
        $options = get_default_enrol_roles(context_system::instance());
        $student = get_archetype_roles('student');
        $student = reset($student);
        $settings->add(
            new admin_setting_configselect(
                'enrol_remita/roleid',
                get_string('defaultrole', 'enrol_remita'),
                get_string('defaultrole_desc', 'enrol_remita'),
                $student->id,
                $options
            )
        );
    }
    $settings->add(
        new admin_setting_configduration(
            'enrol_remita/enrolperiod',
            get_string('enrolperiod', 'enrol_remita'),
            get_string('enrolperiod_desc', 'enrol_remita'),
            0
        )
    );
}
