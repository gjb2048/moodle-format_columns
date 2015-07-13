Introduction
============
Topic based course format with the ability to arrange the topics in columns except 0.

If you find an issue with the format, please see the 'Reporting Issues' section below.

Required version of Moodle
==========================
This version works with Moodle version 2015051100.00 2.9 (Build: 20150511) and above within the 2.9 branch until the
next release.

Please ensure that your hardware and software complies with 'Requirements' in 'Installing Moodle' on
'docs.moodle.org/29/en/Installing_Moodle'.

Supporting Columns development
==============================
If you find Columns useful and beneficial, please consider sponsoring by:

PayPal - Please contact me via my 'Moodle profile' (above) for details as I am an individual and therefore am unable
to have 'buy me now' buttons under their terms.

Flattr - https://flattr.com/profile/gjb2048

I develop and maintain for free and any sponsorships to assist me in this endeavour are appreciated.

Installation
============
 1. Ensure you have the version of Moodle as stated above in 'Required version of Moodle'.  This is essential as the
    format relies on underlying core code that is out of my control.
 2. If upgrading from Moodle 2.3 or a previous release, please see 'Upgrade Instructions' below.
 3. Put Moodle in 'Maintenance Mode' (docs.moodle.org/en/admin/setting/maintenancemode) so that there are no 
    users using it bar you as the administrator - if you have not already done so.
 4. Copy 'columns' to '/course/format/' if you have not already done so.
 5. In 'cnconfig.php' change the value of defaultcolumns' for setting the default layout, structure and columns respectively for
    new / updating courses as desired by following the instructions contained within.
 6. Login as an administrator and follow standard the 'plugin' update notification.  If needed, go to
    'Site administration' -> 'Notifications' if this does not happen.
 7. Put Moodle out of Maintenance Mode.

Upgrade Instructions
====================
1. Ensure you have the version of Moodle as stated above in 'Required version of Moodle'.  This is essential as the
   format relies on underlying core code that is out of my control.
3. Put Moodle in 'Maintenance Mode' so that there are no users using it bar you as the administrator.
4. In '/course/format/' move old 'columns' directory to a backup folder outside of Moodle.
5. Follow installation instructions above.
6. Perform a 'Purge all caches' under 'Home -> Site administration -> Development -> Purge all caches'.
7. Put Moodle out of Maintenance Mode.

Uninstallation
==============
1. Put Moodle in 'Maintenance Mode' so that there are no users using it bar you as the administrator.
2. It is recommended but not essential to change all of the courses that use the format to another.  If this is
   not done Moodle will pick the last format in your list of formats to use but display in 'Edit settings' of the
   course the first format in the list.  You can then set the desired format.
3. In '/course/format/' remove the folder 'columns'.
4. In the database, remove the entry for 'format_columns' ('plugin' attribute) in the table 'config_plugins'.  
   If using the default prefix this will be 'mdl_config_plugins'.
5. Put Moodle out of Maintenance Mode.

Course Backup and Restore Instructions
======================================
1. Backup as you would any other course.  The number of columns will be stored with the course settings.
2. Restore as you would any other course.  If you are offered the option of 'Overwrite Course Configuration'
   you must say 'Yes' to have the number of columns restored otherwise the restored course will retain the
   number of columns it previously had or the default in the 'cnconfig.php' file as mentioned in the 'Installation'
   instructions above depending on the situation.
3. Note: I believe that if you restore a Columns's course on an installation that does not have the
         format then it will work and become the default course format.  However the column data will not be
         stored if you install Columns's at a later date.

Reporting Issues
================
Before reporting an issue, please ensure that you are running the latest version for your release of Moodle.  The primary
release area is located on https://moodle.org/plugins/view.php?plugin=format_columns.  It is also essential that you are
operating the required version of Moodle as stated at the top - this is because the format relies on core functionality that
is out of its control.

All 'Columns' does is integrate with the course page and control it's layout, therefore what may appear to be an issue
with the format is in fact to do with a theme or core component.  Please be confident that it is an issue with 'Columns'
but if in doubt, ask.

I operate a policy that I will fix all genuine issues for free.  Improvements are at my discretion.  I am happy to make bespoke
customisations / improvements for a negotiated fee. 

When reporting an issue you can post in the course format's forum on Moodle.org (currently 'moodle.org/mod/forum/view.php?id=47')
or contact me direct (details at the bottom).

It is essential that you provide as much information as possible, the critical information being the contents of the format's 
version.php file.  Other version information such as specific Moodle version, theme name and version also helps.  A screen shot
can be really useful in visualising the issue along with any files you consider to be relevant.

Version Information
===================
Version 2.9.1.1
  1. Update readme.

Version 2.9.1
  1. First version for Moodle 2.9.

16th November 2014 Version 2.8.1
  1. Stable version for Moodle 2.8.

10th November 2014 Version 2.8.0.1 - Release Candidate
  1. Release candidate for Moodle 2.8 - NOT for production servers.

20th May 2014 Version 2.7.1 - Stable.
  1. Stable release for M2.7.
  2. Fixed hidden sections break flow in Bootstrap V3 based themes.

23rd April 2014 Version 2.7.0.1 - BETA
  1. First beta version for Moodle 2.7beta.
  2. Copied 'assist layout' styles from Collapsed Topics 2.6.1.4 and 2.6.1.5

20th February 2014 Version 2.6.1.1
  1. Fixed slight coding fault in 'print_multiple_section_page' of 'renderer.php'.
  2. Refactored 'print_single_section_page' in 'renderer.php' for maintenance.
  3. Refactoring for the 'Elegance' theme: https://github.com/moodleman/moodle-theme_elegance.
  4. Fixed slight context call issue in 'lib.php'.

18th November 2013 Version 2.6.1
  1. Stable release for Moodle 2.6.

14th November 2013 Version 2.6.0.1
  1. Initial BETA code for Moodle 2.6.
  2. Changes for 'Accessibility' based upon MDL-41252.
  3. Fully implemented MDL-39542.

19th August 2013 Version 2.5.1.2
  1. Fixed issue with the 'float: left' CSS style when used to ensure that the columns were displayed correctly in the
     'vertical' column orientation.  The fix is to use 'display: inline-block' instead but this does not work in IE7, so as
     it does in IE8+ and other browsers I'm going to have to go with it.  Thanks to Ed Przyzycki for reporting this via
     Collapsed Topics.

20th June 2013 Version 2.5.1.1.
  1. Fixed issue with null '$context' in 'renderer.php' thanks to 'Jez H' for reporting this.
  2. Implemented MDL-39764 to fix maxsections < numsections issue.
  3. Added small icon which shows up when updating.
  4. Changes to 'renderer.php' because of MDL-21097.
  5. Reversed the order of the history in this file for easy reading.

14th May 2013 Version 2.5.1 - Stable
  1. First stable version for Moodle 2.5 stable.

12th May 2013 Version 2.5.0.2 - Beta
  1. Minor typo fixes.
  2. Tidied up 'format.php' to use same logic for styles as in Collapsed Topics.
  3. Changes for MDL-39542.

8th May 2013 - Version 2.5.0.1 - Beta
  1. Fixed "When in 'Show one section per page' mode and the column orientation is set to 'Horizontal' the sections on the main
     page do not fill their correct width.  This is due to the use of the 'section_summary()' method which needs to be changed
     within the format to set the calculated width on the 'li' tag." because the core fix I submitted on MDL-39099 has now
     been integrated.  Thus requiring version 2013050200.00 2.5beta+ (Build: 20130502).
  2. Removed '.jumpmenu' from styles.css because of MDL-38907.
  3. Moved 'float: left' to styles.css for Henrik Thorn - CONTRIB-4198.
  4. Improvements for MDL-34917.
  5. Realised that Tablets have more space, so allow two columns even when two or more are set.
  6. Updated core API calls to M2.5.
  7. Moved all 'cnconfig.php' default functionalty to 'Site Administration -> Plugins -> Course formats -> Columns'
     so that defaults can be changed by the administrator from within Moodle without resorting to code changes.
  8. Added capability 'format/columns:changecolumns' to editing teachers and managers such that site administrators can choose to
     disable functionality through roles if they wish.  In order for this to work the version number must be updated.
  9. Added automatic 'Purge all caches' when upgrading.  If this appears not to work by lack of display etc. then perform a
     manual 'Purge all caches' under 'Home -> Site administration -> Development -> Purge all caches'.

6th March 2013 - Version 2.4.1 - Stable.
  1. Fixed issue with strings referencing Collapsed Topics format thanks to Jago Brown.
  2. Made stable as appears to be so.

24th February 2013 - Version 2.4.0.6 - Beta - Do not install on production sites.
  1. Changes because of MDL-37976.
  2. Changes because of MDL-37901.

29th January 2013 - Version 2.4.0.5 - Beta - Do not install on production sites.
  1. Implemented the ability to set horizontal and vertical column orientation.
  2. If you find that the format works well and has no issues, please let me know so that I can make it stable.

10th January 2013 - Version 2.4.0.4 - Beta - Do not install on production sites.
  1. Tidied up code to avoid use of globals.
  2. Removed installation instruction about file permissions on config.php which is not required.

19th December 2012 - Version 2.4.0.3 - Beta - Do not install on production sites.
  1. Updated 'section_nav_selection()' in 'renderer.php' in line with course format refactoring by Marina Glancy.
  2. Minor refactor to remove redundant parameter on 'section_nav_selection()'.

12th December 2012 - Version 2.4.0.2 - Beta - Do not install on production sites.
  1. Fix for related CONTRIB-4065.

10th December 2012 - Version 2.4.0.1 - Beta - Do not install on production sites.
  1. First Moodle 2.4 version.

23rd November 2012 - Version 2.3.1 - Stable
  1. First stable version.

12th November 2012 - Version 2.3.0.1 - Alpha - Do not install on production sites.
  1. First version.

References
==========
Collapsed Topics Format - Column code migrated from - https://moodle.org/plugins/view.php?plugin=format_topcoll

G J Barnard MSc. BSc(Hons)(Sndw). MBCS. CEng. CITP. PGCE.
Moodle profile: moodle.org/user/profile.php?id=442195.
Web profile   : about.me/gjbarnard