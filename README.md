## controlErrorLog

This Extra adds new features
* an ability to control the error log and view it in a popup window in the backend.
* email notification about changes in the error log. Every time when user loads a page controlErrorLog checks the error log for changes. If they have been the email notification will be sent to the specified email.

[![controlErrorLog](https://file.modx.pro/files/5/7/9/5794dfef698b9cf5a17ae209ff31d4d5s.jpg)](https://file.modx.pro/files/5/7/9/5794dfef698b9cf5a17ae209ff31d4d5.jpg)

Now you will not miss any errors.

If the log is too large you can see last 15 (by default) lines of it. 
[![](https://file.modx.pro/files/8/d/9/8d9d3142f073b544cb17200cf4f279dds.jpg)](https://file.modx.pro/files/8/d/9/8d9d3142f073b544cb17200cf4f279dd.jpg)

You can specify any number of lines in the system settings. 
 
## System settings
* **last_lines** - Displays the specified number of last lines when the error log is too large to display.   
* **auto_refresh** - Check the state of the error log with the specified frequency.   
* **refresh_freq** - Error log refresh frequency in seconds. By default, set to 60 seconds.
* **control_frontend** - If true the email notification is activated. Must be specified the admin email.
* **admin_email** - Admin email to notify about changes in the error log. If empty the notification would not work.
  
## Cron task
Use the script *core/components/controlerrorlog/cron/checkerrorlog.php* to check the error log for new errors.

#### Important!
The error indicator is displayed only for users with permission "error_log_view" and administrators with 'sudo'.

## Bugs and improvements

Feel free to suggest ideas/improvements/bugs on GitHub:
http://github.com/sergant210/controlErrorLog/issues