## controlErrorLog

This Extra adds a new feature to manager interface - the ability to control the error log and view it in a popup window.

[![controlErrorLog](https://file.modx.pro/files/a/0/4/a0467354d7e042e6d91109cc894ce66cs.jpg)](https://file.modx.pro/files/a/0/4/a0467354d7e042e6d91109cc894ce66c.png)

Now you will not miss any errors.

If the log is too large you can see last 15 (by default) lines of it. 
[![](https://file.modx.pro/files/1/2/b/12b463c3599b26eb852880dd6bb61a81s.jpg)](https://file.modx.pro/files/1/2/b/12b463c3599b26eb852880dd6bb61a81.png)

You can specify any number of lines in the system settings. 
 
## System settings
**last_lines** - Displays the specified number of last lines when the error log is too large to display. 
**auto_refresh** - Check the state of the error log with the specified frequency. 
**refresh_freq** - Error log refresh frequency in seconds. By default, set to 60 seconds.
  
####Important!#### 
The error indicator is displayed only for users with permission "error_log_view" and administrators with 'sudo'.

##Bugs and improvements

Feel free to suggest ideas/improvements/bugs on GitHub:
http://github.com/sergant210/controlErrorLog/issues