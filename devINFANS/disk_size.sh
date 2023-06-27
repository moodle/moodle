#!/bin/sh
# Shell script to monitor or watch the disk space
# It will send an email to $ADMIN, if the (free avilable) percentage 
# of space is >= 90% 
# -------------------------------------------------------------------------
# Copyright (c) 2005 nixCraft project <http://cyberciti.biz/fb/>
# This script is licensed under GNU GPL version 2.0 or above
# -------------------------------------------------------------------------
# This script is part of nixCraft shell script collection (NSSC)
# Visit http://bash.cyberciti.biz/ for more information.
# ----------------------------------------------------------------------
# Linux shell script to watch disk space (should work on other UNIX oses )
# SEE URL: http://www.cyberciti.biz/tips/shell-script-to-watch-the-disk-space.html
# set admin email so that you can get email
ADMIN="GP_INFORMATIQUE@myacademie.eu"
# set alert level 90% is default
ALERT=90
df -H | grep -vE '^Filesystem|tmpfs|cdrom' | tail -n+2 | awk '{ print $5 " " $1 }' | while read output;
do
  #echo $output
  usep=$(echo $output | awk '{ print $1}' | cut -d'%' -f1  )
  partition=$(echo $output | awk '{ print $2 }' )
  if [ $usep -ge $ALERT ]; then
    echo "Running out of space \"$partition ($usep%)\" on formassmat-moodle as on $(date)" | mail -s "Alert disque VM FORMASSMAT-MOODLE: Utilisation disque $usep" $ADMIN
  fi
done
