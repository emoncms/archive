## Rota module

This module saves energy used recorded by a single meter to different kWh/d feeds at different times.

The idea is to be able to save energy used by different teams in a fire station that work on a rota basis with a rota pattern that may only repeat on a 6-7 day basis.

## Installation

1) Download the rota module from github. You can do this by connecting to your PI via ssh and using git to download the rota module.

   ssh pi@PIIPADDRESS

go to the emoncms modules folder:

   cd /var/www/emoncms/Modules

download rota module using git clone:

    git clone https://github.com/emoncms/rota.git

1) Go to Emoncms in your browser and login. Run the database update by clicking on the Admin tab and then database update and check.

2) Replace process_model.php with rota version

    rm /var/www/emoncms/Modules/input/process_model.php

copy rota version in its place

    cp /var/www/emoncms/Modules/rota/process_model.php /var/www/emoncms/Modules/input

## Using rota

Create some feeds:

http://localhost/clean/feed/create.json?name=Red&type=2
http://localhost/clean/feed/create.json?name=Green&type=2
http://localhost/clean/feed/create.json?name=White&type=2

note down the feed id's.

insert into the rota textbox, rota spec of the format

unixtimestamp,feedid   (newline \n)
unixtimestamp,feedid
...
