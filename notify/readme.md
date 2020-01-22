## Installation

1) Notify uses the swiftmailer php library, swiftmailer can be installed globally using pear:

    sudo apt-get install php-pear

    sudo pear channel-discover pear.swiftmailer.org
  
    sudo pear install swift/swift
    
2) Place the notify folder in your emoncms/Modules directory

3) Place cron.php in a folder in a non web-accessible directory such as /home/username/notify

4) Setup cron to call the script every 2 hours:

    crontab -e
    
Add the line:
    
    0 */2 * * * php /home/username/notify/cron.php > /home/username/notify/notify.log
    
5) Log in to your admin account and run database update. 

6) Navigate to Extras>Notify and enter the email address to send the notification to and click enable to complete.




