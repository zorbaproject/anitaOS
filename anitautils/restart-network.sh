#!/bin/bash

#check if root

#this should be in a loop for all the interfaces (except lo)
interface="wlan0" 
ip addr flush $interface
sleep 1

ifdown --exclude=lo -a
ifup --exclude=lo -a

echo "Riavvio della rete in corso"
systemctl restart networking.service

systemctl restart network-manager.service
