Description
===========
This is a Moodle Block Plugin used to issue Acclaim badges when a course is successfully completed.

Installation
============
To install this block plugin:

<ol>
  <li>Clone this repository</li>
  <li>Remove directory .git and .gitignore </li>
  <li>Zip the repository directory block_acclaim and name acclaim.zip</li>
  <li>Login to Moodle as Administrator</li>
  <li>Navigate to: Site Administration > Plugins > Install Plugins</li>
  <li>Under Plugin type, choose “Block”</li>
  <li>Drag and drop acclaim.zip</li>
  <li>Select “Install Plugin From Zip”</li>
  <li>Next, select “Install plugin!”  If all validations pass.</li>
  <li>Choose “Upgrade Moodle database now”.</li>
  <li>Press continue, when prompted to upgrade version.</li>
  <li>Finally, enter the Acclaim URL, your Organization ID and Token</li>
  <li>Lastly, choose “Save changes”.</li>
</ol>

Usage
=====
Once the Block Plugin has been installed, you can attach a Badge to any of your Moodle Courses as follows.

<ol>
  <li>Begin by logging in as Administrator, teacher, or course creator.</li>
  <li>Turn on editing.</li>
  <li>Next, select the course you wish to attach a badge to from Navigation > Current Course.  You must be in a course view to add the Acclaim Block because it operates in that course context.</li>
  <li>Add the Acclaim Block from “Add A Block” drop down.</li>
  <li>Next, click “Select Badge” in the Acclaim Block to choose which Acclaim badge will be issued upon course completion. </li>
  <li>Select a badge to be issued by selecting the “Acclaim Badges” drop down.  This list is generated by connecting to the Acclaim platform using the credentials defined during installation.</li>
  <li>Optionally select a badge expiration date by choosing the date, and checking “Enable”</li>
  <li>Lastly, select “Save Changes”</li>
</ol>

Additional Settings
==================
In addition to configuring the Acclaim plugin, you will also need to configure how a course is successfully completed.

<ol>
  <li>First, navigate to Home > Navigation > My Courses > Select Course > Edit Settings</li>
  <li>Enable completion tracking, and select save.</li>
  <li>Finally, edit the Criteria that will trigger a course completion.  This can be done by navigating to administration > course administration > select criteria.  For example, course grade.</li>
</ol>

<b>Note:</b> If course completion tracking is missing from the course settings, enable completion tracking: Administration > Course administration > Edit Settings > Enable completion tracking.

Further documentation on course completion tracking can be found here:
https://docs.moodle.org/23/en/Course_completion_settings

Unit Tests
==========
This plugin includes unit tests to verify functionality.

<ol>
  <li>Create env variable named token with value of a valid token on Acclaim. </li>
  <li>vendor/bin/phpunit acclaim_lib_test my/tests/acclaim_test.php </li>
</ol>

To Do
=====
<ol>
  <li>The stage environent and org ID is hard coded in the unit tests.  This should be changed to something like a env variable.</li>
  <li> Allow a course to have a badge set to none (or delete). </li>
  <li> Create language file so this plugin can be used on platforms other then english.</li>
</ol>


Update 20160314
===============
<ol>
  <li>Replaced entering API URL with dropdown with production and sandbox as choices</li>
  <li>Get all active templates when more than 50 and sorted alphabetically</li>
</ol>
