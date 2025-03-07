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
 * Credly's Acclaim Moodle Block Plugin
 * Credly: http://youracclaim.com
 * Moodle: http://moodle.org/
 *
 * @package    block_acclaim
 * @copyright  2020 Credly, Inc. <http://youracclaim.com>
 * @license    https://opensource.org/licenses/MIT
 */

function xmldb_block_acclaim_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();
    /// Add a new column newcol to the mdl_myqtype_options
    if ($oldversion < 2020042200) {

        // Define table block_acclaim_pending_badges to be created.
        $table = new xmldb_table('block_acclaim_pending_badges');

        // Adding fields to table block_acclaim_pending_badges.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('badgetemplateid', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('firstname', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('lastname', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('recipientemail', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('expiration', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table block_acclaim_pending_badges.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for block_acclaim_pending_badges.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table block_acclaim to be renamed to block_acclaim_courses.
        $table = new xmldb_table('block_acclaim');

        // Launch rename table for block_acclaim_courses.
        if ($dbman->table_exists($table)) {
            $dbman->rename_table($table, 'block_acclaim_courses');
        }
        // Acclaim savepoint reached.
        upgrade_block_savepoint(true, 2020042200, 'acclaim');
    }

    if ($oldversion < 2024013100) {
        $table = new xmldb_table('block_acclaim_courses');
        $field = new xmldb_field('badgeurl', XMLDB_TYPE_CHAR, '1000', null, XMLDB_NOTNULL, null, 'none-set', 'badgename');
        if (!$dbman->field_exists($table, $field)) {
            // Add the field but allow null.
            $field->setNotNull(false);
            $dbman->add_field($table, $field);

            // Update all records to have a badge url, defaulting to the value set in lib.php:set_course_badge_template().
            $DB->execute('UPDATE {block_acclaim_courses} SET badgeurl = ?', array("Badge URL Isn't Set"));

            // Change the field to not nullable.
            $field->setNotNull(true);
            $dbman->change_field_notnull($table, $field);
        }

        $records = $DB->get_records(
            'block_acclaim_courses',
            ['badgeurl' => 'none-set'],
            'id'
        );

        if ($records) {
            $api = new block_acclaim_lib();
            foreach ($records as $record) {
                $record->badgeurl = $api->fetch_template_url($record->badgeid);
                $DB->update_record('block_acclaim_courses', $record);
            }
        }

        // Acclaim savepoint reached.
        // Beacon savepoint reached.
        upgrade_block_savepoint(true, 2024013100, 'acclaim');
    }

    if ($oldversion < 2024040800) {
        $table = new xmldb_table('block_acclaim_courses');
        $field = new xmldb_field('badgeurl', XMLDB_TYPE_CHAR, '1000', null, XMLDB_NOTNULL, null, 'none-set', 'badgename');

        $dbman->change_field_type($table, $field);

        // Acclaim savepoint reached.
        upgrade_block_savepoint(true, 2024040800, 'acclaim');
    }

    return true;
}
