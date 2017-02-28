10 Seconds Interval Port Monitoring

- PHP 7.0+, using BackgroundProcess
- Run a Slot every Second, each Slot can run multiple Processes, 5 Servers are assigned to each Process
- Loadbalance the Monitoring requests over these Slots, 10 Slots per 10 Seconds, 1 Slot reserved for Offline Servers
- If a Port is not reachable, move it to the Dedicated Offline Slot, move it back afterwards (planned)
- Limit of 20 Users for the Start, 10 Checks for each User
- Email notifications via local Mail function (relayed to MXRoute)
- Block the current Thread if the previous one is still Running
- Database Backend: MySQL
- Frontend: PHP+HTML (PHP 7.0+ / Bootstrap / Font Awesome)

QuickSetup:

1. Create a User+DB, Import the SQL file
2. Update config.php with your Login details and make SURE to change "Domain"
3. Put night.php and remote.php into Crontab to run every 60 seconds
4. Deploy some Remote Servers and add them to the table remote
