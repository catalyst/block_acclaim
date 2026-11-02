<?php
// This file is part of Credly's Acclaim Moodle Block Plugin
//
// Credly's Acclaim Moodle Block Plugin is free software: you can redistribute it
// and/or modify it under the terms of the MIT license as published by
// the Free Software Foundation.
//
// This script is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// MIT License for more details.
//
// You can find the GNU General Public License at <https://opensource.org/licenses/MIT>.

/**
 * Privacy provider for the Credly block.
 *
 * @package    block_acclaim
 * @copyright  2020 Credly, Inc. <http://youracclaim.com>
 * @license    https://opensource.org/licenses/MIT
 */

namespace block_acclaim\privacy;

use context;
use context_system;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

/**
 * Pending badge rows hold recipient details until they are sent to Credly.
 */
final class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\request\plugin\provider {

    /**
     * Describe the personal data held temporarily and sent to Credly.
     *
     * @param collection $collection
     * @return collection
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table('block_acclaim_pending_badges', [
            'userid' => 'privacy:metadata:pending_badges:userid',
            'firstname' => 'privacy:metadata:pending_badges:firstname',
            'lastname' => 'privacy:metadata:pending_badges:lastname',
            'recipientemail' => 'privacy:metadata:pending_badges:recipientemail',
        ], 'privacy:metadata:pending_badges');
        $collection->add_external_location_link('credly', [
            'issued_to_first_name' => 'privacy:metadata:credly:firstname',
            'issued_to_last_name' => 'privacy:metadata:credly:lastname',
            'recipient_email' => 'privacy:metadata:credly:email',
        ], 'privacy:metadata:credly');
        return $collection;
    }

    /**
     * Pending badges are not tied to a course, so they live in the system context.
     *
     * @param int $userid
     * @return contextlist
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        global $DB;
        $contextlist = new contextlist();
        if ($DB->record_exists('block_acclaim_pending_badges', ['userid' => $userid])) {
            $contextlist->add_system_context();
        }
        return $contextlist;
    }

    /**
     * @param userlist $userlist
     */
    public static function get_users_in_context(userlist $userlist) {
        global $DB;
        if (!$userlist->get_context() instanceof context_system) {
            return;
        }
        $userlist->add_from_sql('userid', 'SELECT userid FROM {block_acclaim_pending_badges} WHERE userid > 0', []);
    }

    /**
     * @param approved_contextlist $contextlist
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;
        $systemcontext = context_system::instance();
        if (!in_array($systemcontext->id, $contextlist->get_contextids())) {
            return;
        }
        $rows = $DB->get_records('block_acclaim_pending_badges', ['userid' => $contextlist->get_user()->id]);
        if (!$rows) {
            return;
        }
        $data = array_map(function ($row) {
            return (object) [
                'firstname' => $row->firstname,
                'lastname' => $row->lastname,
                'recipientemail' => $row->recipientemail,
            ];
        }, array_values($rows));
        writer::with_context($systemcontext)->export_data(
            [get_string('pluginname', 'block_acclaim')],
            (object) ['pendingbadges' => $data]
        );
    }

    /**
     * @param context $context
     */
    public static function delete_data_for_all_users_in_context(context $context) {
        global $DB;
        if ($context instanceof context_system) {
            $DB->delete_records('block_acclaim_pending_badges');
        }
    }

    /**
     * @param approved_contextlist $contextlist
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;
        $systemcontext = context_system::instance();
        if (in_array($systemcontext->id, $contextlist->get_contextids())) {
            $DB->delete_records('block_acclaim_pending_badges', ['userid' => $contextlist->get_user()->id]);
        }
    }

    /**
     * @param approved_userlist $userlist
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;
        if (!$userlist->get_context() instanceof context_system) {
            return;
        }
        [$insql, $params] = $DB->get_in_or_equal($userlist->get_userids(), SQL_PARAMS_NAMED);
        $DB->delete_records_select('block_acclaim_pending_badges', "userid $insql", $params);
    }
}
