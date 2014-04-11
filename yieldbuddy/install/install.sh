#!/bin/sh
echo "Welcome to the yieldbuddy installer."
echo ""
echo "Copying site to /var/www/yieldbuddy (As with most steps, this will take some time)"
sudo mkdir /var/www/
sudo cp -R ../../yieldbuddy /var/www/yieldbuddy
echo ""
echo "Copying scripts to /home/pi/scripts..."
sudo mkdir /home/pi/scripts/
sudo cp ./scripts/test_network.sh /home/pi/scripts/test_network.sh
sudo cp ./scripts/test_yb.sh /home/pi/scripts/test_yb.sh
sudo cp ./scripts/ybdaemon.sh /home/pi/scripts/ybdaemon.sh
sudo chmod +x /home/pi/scripts/test_yb.sh
sudo chmod +x /home/pi/scripts/test_network.sh
sudo chmod +x /home/pi/scripts/ybdaemon.sh
echo ""
echo "Installing ybdaemon to /etc/init.d/ybdaemon as start-up daemon"
sudo cp -R ./scripts/yieldbuddy /etc/init.d/yieldbuddy
sudo chmod +x /etc/init.d/yieldbuddy
sudo update-rc.d yieldbuddy defaults
echo ""
echo "Linking /var/www/ to homefolder..."
sudo ln -s /var/www/ /home/pi/www/
echo ""
echo "Changing file permissions..."
sudo chmod 751 /var/www/yieldbuddy
sudo chmod 750 /var/www/yieldbuddy/*
sudo chmod 777 /var/www/yieldbuddy/Command
sudo chmod 775 /var/www/yieldbuddy/index.html
sudo chmod 751 /var/www/yieldbuddy/restart_mtn
sudo chmod 751 /var/www/yieldbuddy/stop_motion
sudo chmod 751 /var/www/yieldbuddy/start_motion
sudo chmod 751 /var/www/yieldbuddy/yieldbuddy.py
sudo chmod +x /var/www/yieldbuddy/restart_mtn
sudo chmod +x /var/www/yieldbuddy/stop_motion
sudo chmod +x /var/www/yieldbuddy/start_motion
sudo chmod +x /var/www/yieldbuddy/yieldbuddy.py
sudo chmod 751 /var/www/yieldbuddy/www/
sudo chmod 755 /var/www/yieldbuddy/www/*
sudo chmod 751 /var/www/yieldbuddy/www/img/
sudo chmod 751 /var/www/yieldbuddy/www/java/
sudo chmod 751 /var/www/yieldbuddy/www/settings/
sudo chmod 751 /var/www/yieldbuddy/www/sql/
sudo chmod 751 /var/www/yieldbuddy/www/upload
sudo chmod 751 /var/www/yieldbuddy/www/users/
echo ""
read -p "Would you like to patch '/boot/cmdline.txt' (Frees up the serial interface)? (y/n) " REPLY
if [ "$REPLY" == "y" ]; then
sudo cp ./config/cmdline.txt /boot/cmdline.txt
fi
echo ""
read -p "Would you like to patch '/etc/inittab' (Frees up the serial interface)? (y/n) " REPLY
if [ "$REPLY" == "y" ]; then
sudo cp ./config/inittab /etc/inittab
fi
echo ""
echo "Updating apt-get..."
echo ""
sudo apt-get update
echo ""
echo "Installing networking packages..."
echo ""
sudo apt-get -y install ifupdown ifplugd wicd-curses
echo ""
read -p "Would you like to setup a wireless network? (y/n) " REPLY
if [ "$REPLY" == "y" ]; then
echo "Starting wireless network manager."
sudo wicd-curses
clear
fi
echo ""
echo "Setting up serial device..."
echo ""
sudo apt-get -y install python-serial minicom
echo ""
echo "Attempting to test serial device... This can be very touchy!  SO READ THE INSTRUCTIONS CAREFULLY:"
read -p "*** You will have to exit this program after around 10 seconds using ***CTRL+A (let go) then 'q'***, select 'YES' to *NOT* reset the device. Press any key to continue. ***" REPLY
minicom -b 115200 -o -D /dev/ttyAMA0
echo ""
echo "Installing Web Server packages - this will take some time!"
echo ""
sudo apt-get -y install python-sqlite nginx #apache2 #python-mysqldb
echo ""
read -p "Would you like to copy site setting and overwrite nginx config files in '/etc/nginx' (Setups up yieldbuddy site with PHP)? (y/n) " REPLY
if [ "$REPLY" == "y" ]; then
sudo cp ./config/nginx/* /etc/nginx
echo "Setup for document root: /var/www/    If you want, change '/etc/nginx/sites-enabled/default' to set the website's root directory.  ie  'root /mnt/usb'"
fi
echo ""
echo "Installing PHP"
echo ""
sudo apt-get -y install php5  php5-cli php5-fpm php5-sqlite
echo ""
read -p "Would you like to patch '/etc/php5/fpm/pool.d/www.conf' (Properly redirects PHP requests)? (y/n) " REPLY
if [ "$REPLY" == "y" ]; then
sudo cp ./config/php5/www.conf /etc/php5/fpm/pool.d/www.conf
fi
echo "Installing PyCrypto 2.6 - this will take quite a bit of time!  Go grab a coffee."
echo "(Step 1/3: Installing python-dev):"
sudo apt-get -y install python-dev
echo "(Step 2/3: Building PyCrypto 2.6):"
cd ./pycrypto-2.6
sudo python ./setup.py build
echo "(Step 3/3: Installing PyCrypto 2.6):"
sudo python ./setup.py install
echo ""
echo "Installing Motion (Webcam Server)..."
echo ""
sudo apt-get -y install motion
echo ""
read -p "Would you like to overwrite '/etc/motion/motion.conf' with the default yieldbuddy settings? (y/n) " REPLY
if [ "$REPLY" == "y" ]; then
cd ../.
sudo cp ./config/motion.conf /etc/motion/motion.conf
echo ""
read -p "Would you like to start the motion web server now? (y/n) " REPLY
if [ "$REPLY" == "y" ]; then
sudo mkdir /var/run
sudo mkdir /var/run/motion
sudo touch /var/run/motion/motion.pid
sudo motion
fi
fi
echo ""
echo "Installing SQLite3..."
echo ""
sudo apt-get -y install sqlite
echo ""
read -p "Would you like to copy SQLiteManager to '/var/www/SQLiteManager'...? (y/n) " REPLY
if [ "$REPLY" == "y" ]; then
echo ""
sudo cp -R ./SQLiteManager /var/www/SQLiteManager
fi
echo ""
echo ""
echo ""
echo "Congrats.  You should now see a web interface at <Raspberry Pi's IP Address>/yieldbuddy/."
echo ""
echo ""
echo ""
echo "*** IMPORTANT LAST STEPS: ***"
echo "Make sure to click the 'Restore Defaults' button on the 'System' page of the web interface **everytime** your upload new firmware to the Arduino."
echo ""
echo "To Access /var/www/yieldbuddy, type 'sudo su' first, then 'cd /var/www/yieldbuddy'  now run './yieldbuddy.py'"
echo ""
echo "If you're going to use a usb drive (recommended - due to SD cards not doing that great with so many reads/writes) then change the /etc/nginx/sites-enabled/default' to set the website's root directory."
echo "You will have to edit the 'ybdaemon.sh', 'test_yb.sh' script (in the /home/pi/scripts folder) and '/etc/init.d/yieldbuddy' to match the new path of yieldbuddy."
echo "Then just copy the yieldbuddy and SQLiteManager folders from /var/www/ to your new document root (ie. /mnt/usb)"
echo ""
echo "Once you get everything working the way you want it, type 'crontab -e'and add '*/2 * * * * /home/pi/scripts/test_network.sh'  and '*/1 * * * * /home/pi/scripts/test_yb.sh'.  These scripts act like daemons; one tests your network connection and the other restarts yieldbuddy.py if it stops running for some reason.   Note: The '*/2 * * * *' is for running the script every 2 minutes."
echo ""
read -p "Would you start yieldbuddy now...? (y/n) " REPLY
if [ "$REPLY" == "y" ]; then
cd /var/www/yieldbuddy
sudo python /var/www/yieldbuddy/yieldbuddy.py
fi
