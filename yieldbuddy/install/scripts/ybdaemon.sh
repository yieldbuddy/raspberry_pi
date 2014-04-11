#!/bin/sh
i=0
while [ $i -eq 0 ]
do
sudo /home/pi/scripts/test_yb.sh > /dev/null 2>&1 &
/home/pi/scripts/test_network.sh > /dev/null 2>&1 &
sleep 15
done

