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
 * Remita enrolment plugin.
 *
 * This plugin allows you to set up paid courses.
 *
 * @category Enrol_Plugin
 *
 * @package Enrol_Remita
 *
 * @author Adetunji Oyebanji <tunji.oyebanji2015@gmail.com>
 *
 * @copyright 2021 Adetunji Oyebanji
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @link none
 */

use enrol_remita\remita;

defined('MOODLE_INTERNAL') || die();

class enrol_remita_plugin extends enrol_plugin
{
    public $merchantId;
    public $apiKey;
    public $serviceTypeId;
    public $url;
    public $publickey;

    const LIVE_MODE = 1;
    const DEMO_MODE = 2;

    /**
     * Intitialize the data here
     */
    public function __construct()
    {

        $this->merchantId = $this->get_config('merchant_id');
        $this->apiKey = $this->get_config('api_key');
        $this->serviceTypeId = $this->get_config('servicetype_id');
        $this->publickey = $this->get_config('public_key');
    }


    /**
     * Lists all currencies available for plugin.
     *
     * @return $currencies.
     */
    public function get_currencies()
    {
        $codes = array('NGN', 'USD');
        $currencies = array();
        foreach ($codes as $c) {
            $currencies[$c] = new lang_string($c, 'core_currencies');
        }
        return $currencies;
    }

    /**
     * Return public key of this enrol plugin.
     *
     * @return string
     */
    public function get_base_url()
    {
        return $this->get_config('mode') === self::LIVE_MODE ?
            "https://login.remita.net" :
            "https://remitademo.net";
    }

    /**
     * Defines if user can be managed from admin.
     *
     * @param  stdClass $instance course enrol instance
     * @return bool - true means it is possible to change enrol period and status in user_enrolments table
     */
    public function allow_manage(stdClass $instance)
    {
        return has_capability(
            'enrol/remita:manage',
            context_course::instance($instance->courseid)
        );
    }

    /**
     * Returns optional enrolment information icons.
     *
     * This is used in course list for quick overview of enrolment options.
     *
     * We are not using single instance parameter because sometimes
     * we might want to prevent icon repetition when multiple instances
     * of one type exist. One instance may also produce several icons.
     *
     * @param array $instances all enrol instances of this type in one course
     *
     * @return array of pix_icon
     */
    public function get_info_icons(array $instances) {
        $found = false;
        foreach ($instances as $instance) {
            if ($instance->enrolstartdate != 0 && $instance->enrolstartdate > time()) {
                continue;
            }
            if ($instance->enrolenddate != 0 && $instance->enrolenddate < time()) {
                continue;
            }
            $found = true;
            break;
        }
        if ($found) {
            return [
                new pix_icon(
                    'icon',
                    get_string('pluginname', 'enrol_remita'),
                    'enrol_remita'
                )
            ];
        }
        return array();
    }

    /**
     * Is it possible to hide/show enrol instance via standard UI?
     *
     * @param  stdClass $instance
     * @return bool
     */
    public function can_hide_show_instance($instance)
    {
        $context = context_course::instance($instance->courseid);
        return has_capability('enrol/remita:config', $context);
    }

    /**
     * Is it possible to delete enrol instance via standard UI?
     *
     * @param  stdClass $instance
     * @return bool
     */
    public function can_delete_instance($instance)
    {
        $context = context_course::instance($instance->courseid);
        return has_capability('enrol/remita:config', $context);
    }

    /**
     * Return whether or not, given the current state, it is possible to edit an instance
     * of this enrolment plugin in the course. Used by the standard editing UI
     * to generate a link to the edit instance form if editing is allowed.
     *
     * @param  stdClass $instance
     * @return boolean
     */
    public function can_edit_instance($instance)
    {
        $context = context_course::instance($instance->courseid);
        return has_capability('enrol/' . $instance->enrol . ':config', $context);
    }

    /**
     * Defines if 'enrol me' link will be shown on course page.
     *
     * @param  stdClass $instance of the plugin
     * @return bool(true or false)
     */
    public function show_enrolme_link(stdClass $instance)
    {
        return ($instance->status == ENROL_INSTANCE_ENABLED);
    }

    /**
     * This add 'Edit' icon on admin panel to allow edit existing instance
     * Has possibility to add more icons for additional functionality
     * Create icon and add to $icons array
     *
     * @param  stdClass $instance course enrol instance
     * @return icons - List on icons that will be added to plugin instance
     */
    public function get_action_icons(stdClass $instance)
    {
        global $OUTPUT;

        if ($instance->enrol !== 'remita') {
            throw new coding_exception('invalid enrol instance!');
        }
        $context = context_course::instance($instance->courseid);

        $icons = array();

        if (has_capability('enrol/remita:config', $context)) {
            $editlink = new moodle_url("/enrol/remita/edit.php", array('courseid' => $instance->courseid, 'id' => $instance->id));
            $icons[] = $OUTPUT->action_icon(
                $editlink,
                new pix_icon(
                    't/edit',
                    get_string('edit'),
                    'core',
                    array('class' => 'iconsmall')
                )
            );
        }
        return $icons;
    }

    /**
     * Does this plugin allow manual unenrolment of all users?
     * All plugins allowing this must implement 'enrol/xxx:unenrol' capability
     *
     * @param  stdClass $instance course enrol instance
     * @return bool - true means user with 'enrol/xxx:unenrol' may unenrol others freely,
     * false means nobody may touch user_enrolments
     */
    public function allow_unenrol(stdClass $instance)
    {
        $context = context_course::instance($instance->courseid);
        if (has_capability('enrol/remita:unenrolself', $context)) {
            return true;
        }
    }

    /**
     * Returns list of unenrol links for all enrol instances in course.
     *
     * @param  object $instance Instance
     * @return moodle_url or NULL if self unenrolment not supported
     */
    public function get_unenrolself_link($instance)
    {
        global $USER, $CFG, $DB;
        $name = $this->get_name();
        if ($instance->enrol !== $name) {
            throw new coding_exception('invalid enrol instance!');
        }
        if ($instance->courseid == SITEID) {
            return null;
        }
        if (!enrol_is_enabled($name)) {
            return null;
        }
        if ($instance->status != ENROL_INSTANCE_ENABLED) {
            return null;
        }
        if (!file_exists("$CFG->dirroot/enrol/$name/unenrolself.php")) {
            return null;
        }
        $context = context_course::instance($instance->courseid, MUST_EXIST);
        if (!has_capability("enrol/$name:unenrolself", $context)) {
            return null;
        }
        if (!$DB->record_exists(
            'user_enrolments',
            array(
                'enrolid' => $instance->id,
                'userid' => $USER->id,
                'status' => ENROL_USER_ACTIVE
            )
        )) {
            return null;
        }
        return new moodle_url("/enrol/$name/unenrolself.php", array('enrolid' => $instance->id));
    }

    /**
     * Sets up navigation entries.
     *
     * @param  stdClass $instancesnode
     * @param  stdClass $instance
     * @return void
     */
    public function add_course_navigation($instancesnode, stdClass $instance)
    {
        if ($instance->enrol !== 'remita') {
            throw new coding_exception('Invalid enrol instance type!');
        }

        $context = context_course::instance($instance->courseid);
        if (has_capability('enrol/remita:config', $context)) {
            $managelink = new moodle_url('/enrol/remita/edit.php', array('courseid' => $instance->courseid, 'id' => $instance->id));
            $instancesnode->add($this->get_instance_name($instance), $managelink, navigation_node::TYPE_SETTING);
        }
    }

    /**
     * Does this plugin allow manual unenrolment of a specific user?
     * All plugins allowing this must implement 'enrol/xxx:unenrol' capability.
     *
     * This is useful especially for synchronisation plugins that.
     * do suspend instead of full unenrolment.
     *
     * @param stdClass $instance course enrol instance
     * @param stdClass $ue       record from user_enrolments table, specifies user
     *
     * @return bool - true means user with 'enrol/xxx:unenrol' may unenrol this user,
     * false means nobody may touch this user enrolment
     */
    public function allow_unenrol_user(stdClass $instance, stdClass $ue)
    {
        return $this->allow_unenrol($instance);
    }

    /**
     * Returns link to page which may be used to add new instance of enrolment plugin in course.
     *
     * @param  int $courseid
     * @return moodle_url page url
     */
    public function get_newinstance_link($courseid)
    {
        $context = context_course::instance($courseid, MUST_EXIST);
        if (!has_capability('moodle/course:enrolconfig', $context) or !has_capability('enrol/remita:config', $context)) {
            return null;
        }
        // Multiple instances supported - different cost for different roles.
        return new moodle_url(
            '/enrol/remita/edit.php',
            array('courseid' => $courseid)
        );
    }

    /**
     * Creates course enrol form, checks if form submitted.
     * and enrols user if necessary. It can also redirect.
     *
     * @param    stdClass $instance
     * @redirect redirects to the custom enrolment page.
     */
    public function enrol_page_hook(stdClass $instance)
    {
        global $CFG, $OUTPUT, $SESSION, $USER, $DB;

        ob_start();

        if ($DB->record_exists('user_enrolments', array('userid' => $USER->id, 'enrolid' => $instance->id))) {
            return ob_get_clean();
        }

        if ($instance->enrolstartdate != 0 && $instance->enrolstartdate > time()) {
            return ob_get_clean();
        }

        if ($instance->enrolenddate != 0 && $instance->enrolenddate < time()) {
            return ob_get_clean();
        }

        $course = $DB->get_record('course', array('id' => $instance->courseid));
        $context = context_course::instance($course->id);

        $shortname = format_string($course->shortname, true, array('context' => $context));
        $strloginto = get_string("loginto", "", $shortname);
        $strcourses = get_string("courses");

        // Pass $view=true to filter hidden caps if the user cannot see them
        if ($users = get_users_by_capability(
            $context,
            'moodle/course:update',
            'u.*',
            'u.id ASC',
            '',
            '',
            '',
            '',
            false,
            true
        )) {
            $users = sort_by_roleassignment_authority($users, $context);
            $teacher = array_shift($users);
        } else {
            $teacher = false;
        }

        if ((float) $instance->cost <= 0 ) {
            $cost = (float) $this->get_config('cost');
        } else {
            $cost = (float) $instance->cost;
        }

        if (abs($cost) < 0.01) { // no cost, other enrolment methods (instances) should be used
            echo '<p>'.get_string('nocost', 'enrol_remita').'</p>';
        } else {

            // Calculate localised and "." cost, make sure we send Remita the same value,
            // please note Remita expects amount with 2 decimal places and "." separator.
            $localisedcost = format_float($cost, 2, true);
            $cost = format_float($cost, 2, false);

            if (isguestuser()) { // force login only for guest user, not real users with guest role
                $wwwroot = $CFG->wwwroot;
                echo '<div class="mdl-align">
                        <p>'.get_string('paymentrequired').'</p>';
                echo '<p>
                        <b>'.get_string('cost').": $instance->currency $localisedcost".'</b>
                    </p>';
                echo '<p>
                        <a href="'.$wwwroot.'/login/">'.get_string('loginsite').'</a>
                    </p>';
                echo '</div>';
            } else {
                //Sanitise some fields before building the Remita form
                $coursefullname  = format_string($course->fullname, true, array('context'=>$context));
                $courseshortname = $shortname;
                $userfullname    = fullname($USER);
                $userfirstname   = $USER->firstname;
                $userlastname    = $USER->lastname;
                $useremail       = $USER->email;
                $instancename    = $this->get_instance_name($instance);
                // $customfields    = $this->get_custom_fields();

                $public_key = $this->publickey;
                // $reference = $this->getHashedToken();
                $base_url = $this->get_base_url();
                $url = $base_url . "/remita/exapp/api/v1/send/api/echannelsvc/merchant/api/paymentinit";
                $bundle_url = $base_url . "/payment/v1/remita-pay-inline.bundle.js";

                $description = "Payment for $courseshortname";
                $reference = $this->getReference($USER->username);
                $remita_consumer_token = $this->apiHash($reference, $cost);
                // $response = $remita->generateRRR();
                // $rrr = $response->RRR;

                include $CFG->dirroot.'/enrol/remita/enrol.html';
            }
        }
        return $OUTPUT->box(ob_get_clean());
    }

    /**
     * Generate a payment reference also known as transactionId or
     *
     * @param [type] $str
     * @return void
     */
    public static function getReference($str)
    {
        return md5($str . time() . random_int(PHP_INT_MIN, PHP_INT_MAX));
    }

    /**
     * Returns the apiHash
     *
     * @return string
     */
    public function apiHash($reference, $cost)
    {
        $str = $this->merchantId
            . $this->serviceTypeId
            . $reference
            . $cost
            . $this->apiKey;

        $hash = hash('sha512', $str);
        return $hash;
    }

}