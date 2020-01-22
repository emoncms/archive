# Emoncms Nodes Module 

## Update Feb 16: Depreciated in favor of direct [MQTT input script](https://github.com/emoncms/emoncms/blob/master/docs/RaspberryPi/MQTT.md)

Designed around RFM 12/69 RF Network nodes, support for both sending and receiving node data.

Suscribes to emonhub/# MQTT topic, receive CSV with node data & post to Emoncms

![Nodes](http://openenergymonitor.org/emon/sites/default/files/emonPi_nodes.png)

# Prerequisites 

Emoncms, [Emonhub (emon-pi varient)](github.com/openenergymonitor/emonhub) and mosquitto MQTT server should be installed 

# Install

## Install module 

    cd /var/www/emoncms/Modules
    git clone https://github.com/emoncms/nodes
    
Check for database updates in Emoncms admin 

## Install emoncms-nodes-service script: 

    cd /etc/init.d && sudo ln -s /var/www/emoncms/Modules/nodes/emoncms-nodes-service
    sudo chown root:root /var/www/emoncms/Modules/nodes/emoncms-nodes-service
    sudo chmod 755 /var/www/emoncms/Modules/nodes/emoncms-nodes-service
    sudo update-rc.d emoncms-nodes-service defaults
    
## Create a emoncms.conf file for use with the nodes module:
```
sudo touch /home/pi/data/emoncms.conf
sudo chown pi:www-data /home/pi/data/emoncms.conf
sudo chmod 664 /home/pi/data/emoncms.conf
```
