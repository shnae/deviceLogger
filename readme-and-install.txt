Setup instructions for deviceLogger:

deviceLogger consists of a front-end built with php/mysql and a backend that uses a python script to grab settings/datapoints from the database.

Basic Installation:
copy the settings.txt and devicereader.py files to /etc/devicereader folder on your server.  
copy the entire contents of the php-frontend folder to your web directory (on ubuntu w/apache, usually /var/www/).  You should probably create secure passwords for your accounts and change the settings/passwords for the devicelogger account on sql server and in database.php
run the sql-create-script.sql script on your mysql server to create the database
run apt-get install python-mysqldb && apt-get install lsof 
visudo: add www-data to Sudoers for lsof/devicereader directory

Configuring devices:

Once you have installed the files, you should be able to access the site by browsing to the directory the index.php file is located.
Devices are created by clicking 'Create New Device' and filling out information including the name, hostname/ip, and uri (e.g. index.htm)
To add metrics to your device, click on 'edit this device' on main devices page, click 'create new metric'.  Then, you must specify the xml tag that tracks your value by inputting as an xpath value.  For example, to get to the value for <Temperature>85.4</Temperature> you would use xpath: './/Temperature'.  Much more complex filtering can be obtained by using advanced xpath queries.  

Notes:

This project is nowhere near fully debugged, implemented, secured, or featured.  I do need feedback in order to make it better, or preferably, have others contribute/fix the code. 

Known issues:

1.) General code ugliness
2.) No confirmation dialogs on anything
3.) jqplot graphs don't work well when generating graphs that span days or longer (no day of the week labeling, only hours)
4.) really, lots more than this, but I hope others will find it useful as a start and contribute.

Thanks!
@shanefrench