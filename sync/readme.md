#Sync emoncms module

This module makes it possible to download data from a remote emoncms account.

## How to setup and use the module.

1) Install this module in your emoncms/Modules folder: 

$ git clone https://github.com/emoncms/sync.git

2) Log in to your local emoncms account and goto Admin. Run the 'Update & check' database tool.

3) Click on the Sync menu item in the top bar to bring up the sync page.

4) Enter you remote emoncms account WRITE apikey. A list of the feeds in your remote account should now appear 

5) Select the feeds you wish to download

6) In terminal navigate to the sync module in your emoncms installation:

$ cd /var/www/emoncms/Modules/sync

7) Run the import.php script to start downloading the feed data

$ php import.php

Thats it! your data should now be downloading
