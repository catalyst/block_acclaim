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
 * This file is needed when backing up a course.
 * See https://docs.moodle.org/dev/Backup_API
 * See https://docs.moodle.org/dev/Backup_2.0_for_developers
 *
 * @package    block_acclaim
 * @copyright  2020 Credly, Inc. <http://youracclaim.com>
 * @license    https://opensource.org/licenses/MIT
 */
require_once($CFG->dirroot . '/backup/moodle2/backup_block_task.class.php');
require_once(__DIR__ . '/backup_acclaim_stepslib.php');

class backup_acclaim_block_task extends backup_block_task {
    /**
     * A sequence of steps to execute to back up the database.
     *
     * @override
     */
    protected function define_my_steps() {
        $this->add_step(new backup_acclaim_structure_step('acclaim_structure', 'acclaim.xml'));
    }

    // No-op overrides not used by this plugin.
    protected function define_my_settings() {
    }
    public function get_fileareas() {
        return [];
    }
    public function get_configdata_encoded_attributes() {
        return [];
    }
    public static function encode_content_links($content) {
        return $content;
    }
}
