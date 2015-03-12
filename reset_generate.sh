#!/bin/bash

STRING="Will remove the database and reset everything..."


echo $STRING

delete_database="app/console doctrine:schema:drop --force"
create_database="app/console doctrine:schema:update --force"

eval $delete_database
eval $create_database
echo "Y" | app/console doctrine:fixtures:load
create_users="app/console testusers --add 100"
create_campaigns="app/console testcampaigns --add"
create_teammembers="app/console testmember --add 15"

eval $create_users
eval $create_campaigns
eval $create_teammembers
 

for (( i=1; i<= 9; i++))
do
   command="app/console ninecampaignstest --add $i"
   eval $command
done
