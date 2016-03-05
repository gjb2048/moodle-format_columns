Introduction
============
Topic based course format with the ability to arrange the topics in columns except 0.

If you find an issue with the format, please see the 'Reporting Issues' section below.

[![Build Status](https://travis-ci.org/gjb2048/moodle-format_columns.svg?branch=master)](https://travis-ci.org/gjb2048/moodle-format_columns)

Required version of Moodle
==========================
This version works with Moodle version 2015111600.00 3.0 (Build: 20151116) and above within the 3.0 branch until the
next release.

Please ensure that your hardware and software complies with 'Requirements' in 'Installing Moodle' on
'docs.moodle.org/30/en/Installing_Moodle'.

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
See Changes.md

References
==========
Collapsed Topics Format - Column code migrated from - https://moodle.org/plugins/view.php?plugin=format_topcoll

G J Barnard MSc. BSc(Hons)(Sndw). MBCS. CEng. CITP. PGCE.
Moodle profile: moodle.org/user/profile.php?id=442195.
Web profile   : about.me/gjbarnard