# INSTALLATION GUIDE
The TKTDATA installation is a very simple process that is divided into two parts. First we do the installation of the files and thereafter we configure the database.

# FILE INSTALLATION
The system runs under php, pleas check that you fulfill the following requirement. The web server needs to run php 5 or higher. It is suggested to use the latest php version that you can download at https://www.php.net/downloads. After you have prepared your web server download the zip and extract it at your preferred location. Navigate you to the file general.php. Configure your system timezone, email, salt string, default currency and restore option. The possible values are described in the comment. Please leaf SYSTEM_VERSION, SYSTEM_NAME and PATH_TO_INI as it stands. Scroll down until your reach the $url variable. If the system is not installed in the root-folder of your web server you can now add at the end of the line your attachment that contains the path to the system root folder. It is important that you have a slash (/) at the end of your attachment. If it is installed in your root-folder leaf it as it is. To connect the system to the database, go back into the root folder of the system and open the file logindata.ini. Replace the required parameters host, username, password and database with your own logindata. You can move the file into another Folder outside the web-server if you want or leaf it where it is. go back into the /general.php file and scroll down until you reach PATH_TO_INI. Replace this path with your actual path to the ini file. Navigate now on to store/faq/index.php and open it. Scroll down until you reacht the contact section, Replace the contact infos with your contact infos.
The system is now successful installed at your web server.

# DATABASE CONFIGURATION
Connect to your database and import the tktdata.sql file. Execute it and all required tables should be created automatically. Otherwise execute every part of the code individually. Yous system is no set up successful and can be used.

# First steps
Visit your webpage. If everything is work you should see a login page. Use as user Admin and as password admin (If salt string not changed, otherwise click on Passwort vergessen and change the email for admin in the database and enter your E-mail). The admin user is a superuser. You should enter the system. Navigate you to the profile folder and renew your password. Navigate to Benutzer->Alle Benutzer and create a new one for you. Your system is now running perfectly.

# First ticket
to create a ticket it is required to generate a group first. Go to Ticket->Gruppen. Create a new group and edit it. Everything is described. Head over to Ticket->Alle Tickets and click on the bottom right on the + symbol. You can now add your ticket. If you have activated under Ticket->Gruppen->Payment the Store and added your Payrexx (https://www.payrexx.com) informations you can visit the store and buy a ticket. (Link can be found at Ticket->Gruppen->payment::Store)

# Coupon
Head over to coupon and  click on the bottom right on the + symbol. You can now use your coupon.

# Use Ticket
To employ the ticket, head over to Scanner and click on QR-Scanner. You can now scan your ticket. If you open the video on fullscreen the scan procedure is optimized for a self-check-in. If the QR-Code is not required you can scan the ticket via Code-Scanner. You need to enter the key that is written under the QR-Code on the ticket.

# Live infos
To get live informations about visitor numbers visit Live->Live and you get the result. You can change visitor numbers manually if you go to Live->Manuell.
