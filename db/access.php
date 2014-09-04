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
* @package    block_acclaim
* @copyright  2014 Yancy Ribbens <yancy.ribbens@gmail.com>
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/
    $capabilities = array(

        // Can edit objectives
    'block/acclaim:editbadge' => array(
        'captype' => 'write',
        'riskbitmask' => RISK_SPAM,
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'coursecreator' => CAP_ALLOW,
            'manager' => CAP_ALLOW
            )
        ),
    
    'block/acclaim:myaddinstance' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'user' => CAP_ALLOW
        ),
                                                      
        'clonepermissionsfrom' => 'moodle/my:manageblocks'
    ),
                                                                   
    'block/acclaim:addinstance' => array(
        'riskbitmask' => RISK_SPAM | RISK_XSS,
                                                                                
        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        ),
                                                                                                                                         
        'clonepermissionsfrom' => 'moodle/site:manageblocks'
    ),
);
