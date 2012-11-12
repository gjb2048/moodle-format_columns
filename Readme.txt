Introduction
------------
Topic based course format with the ability to arrange the topics in columns except 0.

Required version of Moodle
--------------------------
This version works with Moodle 2.3.2+, version 2012062502.05 (Build: 20121005) and above until the next release.

Installation
------------
 1. Ensure you have the version of Moodle as stated above in 'Required version of Moodle'.  This is essential as the
    format relies on underlying core code that is out of my control.
 2. Put Moodle in 'Maintenance Mode' (docs.moodle.org/en/admin/setting/maintenancemode) so that there are no 
    users using it bar you as the administrator - if you have not already done so.
 3. Copy 'columns' to '/course/format/' if you have not already done so.
 4. If using a Unix based system, chmod 755 on config.php - I have not tested this but have been told that it
    needs to be done.
 5. In 'cnconfig.php' change the value of $CNCFG->defaultcolumns' for setting the default layout, structure and columns respectively for
    new / updating courses as desired by following the instructions contained within.
 6. Login as an administrator and follow standard the 'plugin' update notification.  If needed, go to
    'Site administration' -> 'Notifications' if this does not happen.
 7.  Put Moodle out of Maintenance Mode.

Upgrade Instructions
--------------------
1. Ensure you have the version of Moodle as stated above in 'Required version of Moodle'.  This is essential as the
   format relies on underlying core code that is out of my control.
3. Put Moodle in 'Maintenance Mode' so that there are no users using it bar you as the administrator.
4. In '/course/format/' move old 'columns' directory to a backup folder outside of Moodle.
5. Follow installation instructions above.
6. Put Moodle out of Maintenance Mode.

Uninstallation
--------------
1. Put Moodle in 'Maintenance Mode' so that there are no users using it bar you as the administrator.
2. It is recommended but not essential to change all of the courses that use the format to another.  If this is
   not done Moodle will pick the last format in your list of formats to use but display in 'Edit settings' of the
   course the first format in the list.  You can then set the desired format.
3. In '/course/format/' remove the folder 'columns'.
4. In the database, remove the table 'format_columns_settings' along with the entry for 'format_columns'
   ('plugin' attribute) in the table 'config_plugins'.  If using the default prefix this will be
   'mdl_format_columns_settings' and 'mdl_config_plugins' respectively.
5. Put Moodle out of Maintenance Mode.

Course Backup and Restore Instructions
--------------------------------------
1. Backup as you would any other course.  The layout configuration will be stored with the course settings.
2. Restore as you would any other course.  If you are offered the option of 'Overwrite Course Configuration'
   you must say 'Yes' to have thelayout configuration restored otherwise the restored course will retain the
   layout it previously had or the default in the 'config.php' file as mentioned in the 'Installation'
   instructions above depending on the situation.
3. Note: I believe that if you restore a Columns's course on an installation that does not have the
         format then it will work and become the default course format.  However the column data will not be
         stored if you install Columns's at a later date.

Version Information
-------------------
12th November 2012 - Version 2.3.0.1 - Alpha
  1.  First version.

References
----------
Collapsed Topics Format - Column code migrated from - https://moodle.org/plugins/view.php?plugin=format_topcoll

G J Barnard MSc. BSc(Hons)(Sndw). MBCS. CEng. CITP. PGCE. - 12th November 2012.
Moodle profile: moodle.org/user/profile.php?id=442195.
Web profile   : about.me/gjbarnard