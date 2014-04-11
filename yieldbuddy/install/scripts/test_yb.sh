#Test to see if yieldbuddy is running
PROCESS_NUM=$(ps -ef | grep "yieldbuddy" | grep -v "grep" | wc -l)
if [ $PROCESS_NUM -gt 0 ];
then
	echo "Running."
else
	echo "Not Running. Attemping to restart..."
	cd /var/www/yieldbuddy/
	sudo nice -n 15 sudo /var/www/yieldbuddy/yieldbuddy.py
	exit
fi
