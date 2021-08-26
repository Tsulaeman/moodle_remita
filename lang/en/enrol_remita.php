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

$string['assignrole'] = 'Assign role';
$string['public_key'] = 'Public Key';
$string['public_key_desc'] = 'Public Key of the selected environment';
$string['api_key'] = 'API Key';
$string['api_key_desc'] = 'API Key of your selected Remita environment';
$string['currency'] = 'Currency';
$string['cost'] = 'Enrol cost';
$string['costerror'] = 'The enrolment cost is not numeric';
$string['defaultrole'] = 'Default role assignment';
$string['defaultrole_desc'] = 'Select role which should be assigned to users during Remita enrolments';
$string['enrolperiod'] = 'Enrolment duration';
$string['enrolperiod_desc'] = 'Default length of time that the enrolment is valid. If set to zero, the enrolment duration will be unlimited by default.';
$string['enrolenddate'] = 'End date';
$string['enrolenddate_help'] = 'If enabled, users can be enrolled until this date only.';
$string['enrolenddaterror'] = 'Enrolment end date cannot be earlier than start date';
$string['enrolmentnew']='New Enrolment';
$string['enrolmentnewuser']='New User Enrolment';
$string['enrolstartdate'] = 'Start date';
$string['enrolstartdate_help'] = 'If enabled, users can be enrolled from this date onward only.';
$string['expiredaction'] = 'Enrolment expiration action';
$string['expiredaction_help'] = 'Select action to carry out when user enrolment expires. Please note that some user data and settings are purged from course during course unenrolment.';
$string['maxenrolled'] = 'Max enrolled users';
$string['maxenrolled_help'] = 'Specifies the maximum number of users that can be enrolled. 0 means no limit.';
$string['maxenrolledreached'] = 'Maximum number of users allowed to enroll has been reached.';
$string['merchant_id'] = 'Merchant ID';
$string['merchant_id_desc'] = 'Your Unique Merchant ID on Remita';
$string['mode'] = 'Connection Mode';
$string['mode_desc'] = 'Select the environment you want to use for the transactions';
$string['mode_demo'] = 'Demo Mode';
$string['mode_live'] = 'Live Mode';
$string['nocost'] = 'There is no cost associated with enrolling in this course!';
$string['pluginname'] = 'Remita';
$string['remita_sorry'] = "Sorry, you can not use the script that way.";
$string['servicetype_id'] = 'Service Type ID';
$string['servicetype_id_desc'] = 'ID for your good/service on Remita';
$string['status'] = 'Allow Remita enrolments';
$string['status_desc'] = 'Allow users to use Remita to enrol into a course by default.';
$string['unenrolselfconfirm'] = 'Do you really want to unenrol yourself from course "{$a}"?';