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

namespace block_acclaim\privacy;

use context_system;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\userlist;
use core_privacy\tests\provider_testcase;

/**
 * Privacy provider tests.
 *
 * @package    block_acclaim
 * @copyright  2020 Credly, Inc. <http://youracclaim.com>
 * @license    https://opensource.org/licenses/MIT
 * @covers     \block_acclaim\privacy\provider
 */
final class provider_test extends provider_testcase {
    /**
     * Insert a pending badge row for a user.
     *
     * @param \stdClass $user
     */
    private function add_pending(\stdClass $user): void {
        global $DB;
        $DB->insert_record('block_acclaim_pending_badges', (object) [
            'userid' => $user->id,
            'badgetemplateid' => '123',
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'recipientemail' => $user->email,
            'expiration' => 0,
        ]);
    }

    public function test_export_and_delete_user_data(): void {
        global $DB;
        $this->resetAfterTest();
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $this->add_pending($user1);
        $this->add_pending($user2);

        $contextlist = provider::get_contexts_for_userid($user1->id);
        $this->assertEquals([context_system::instance()->id], $contextlist->get_contextids());

        $approved = new approved_contextlist($user1, 'block_acclaim', $contextlist->get_contextids());
        provider::export_user_data($approved);
        $this->assertTrue(\core_privacy\local\request\writer::with_context(context_system::instance())->has_any_data());

        provider::delete_data_for_user($approved);
        $this->assertFalse($DB->record_exists('block_acclaim_pending_badges', ['userid' => $user1->id]));
        $this->assertTrue($DB->record_exists('block_acclaim_pending_badges', ['userid' => $user2->id]));
        $this->assertEmpty(provider::get_contexts_for_userid($user1->id)->get_contextids());
    }

    public function test_userlist_and_delete_for_users(): void {
        global $DB;
        $this->resetAfterTest();
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $this->add_pending($user1);
        $this->add_pending($user2);

        $context = context_system::instance();
        $userlist = new userlist($context, 'block_acclaim');
        provider::get_users_in_context($userlist);
        $this->assertEqualsCanonicalizing([$user1->id, $user2->id], $userlist->get_userids());

        provider::delete_data_for_users(new approved_userlist($context, 'block_acclaim', [$user1->id]));
        $this->assertEquals(1, $DB->count_records('block_acclaim_pending_badges'));

        provider::delete_data_for_all_users_in_context($context);
        $this->assertEquals(0, $DB->count_records('block_acclaim_pending_badges'));
    }
}
