[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

## controlErrorLog

This Extra adds new features
* an ability to control the error log and view it in a popup window in the backend.
* email notification about changes in the error log. Every time when user loads a page controlErrorLog checks the error log for changes. If they have been the email notification will be sent to the specified email.

Advice. Use cron job instead of the online notification.

[![controlErrorLog](https://file.modx.pro/files/5/7/9/5794dfef698b9cf5a17ae209ff31d4d5s.jpg)](https://file.modx.pro/files/5/7/9/5794dfef698b9cf5a17ae209ff31d4d5.jpg)

Now you will not miss any errors.

If the log is too large you can see only the specified number of the last lines (by default 15). 
[![](https://file.modx.pro/files/8/d/9/8d9d3142f073b544cb17200cf4f279dds.jpg)](https://file.modx.pro/files/8/d/9/8d9d3142f073b544cb17200cf4f279dd.jpg)

You can specify any number of lines in the system settings. 

## Error log on the frontend
[![](https://modzone.ru/assets/images/controlerrorlog_front.png)](https://modzone.ru/assets/images/controlerrorlog_front.png)
It's very useful in development mode. It's available only for users with the "error_log_view" permission. 

## System settings
* **admin_email** - Admin email to notify about changes in the error log. If empty the notification would not work.
* **allow_copy_deletion** - Allow to delete copies of the error log instead of clearing them.
* **auto_refresh** - Check the state of the error log with the specified frequency.
* **control_frontend** - If true the error log will be shown on the frontend. 
* **date_format** - Date format in the strftime function style.
* **format_output** - Displays error log in formatted form.
* **last_lines** - Displays the specified number of last lines when the error log is too large to display.   
* **refresh_freq** - Error log refresh frequency in seconds. By default, set to 60 seconds.
* **tpl** - Smarty template for displaying messages from the error log.
* **css_file** - URL of a css file for the frontend.
* **js_file** - URL of a javascript file for the frontend.
  
## Cron task
Use the script *core/components/controlerrorlog/cron/checkerrorlog.php* to check the error log for new errors.

## Permissions
* error_log_view - to view the error log and to download it.
* error_log_erase - to clear the error log.
* error_copy_erase - to clear/delete copies.
* error_log_copy - to make a copy.

#### Important!
The error indicator is displayed only for users with permission "error_log_view" and administrators with "sudo".


## Bugs and improvements

Feel free to suggest ideas/improvements/bugs on GitHub:
http://github.com/sergant210/controlErrorLog/issues