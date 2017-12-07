# Night-Sky

10s Interval Port Monitoring

[![Build Status](https://travis-ci.org/Ne00n/Night-Sky.svg?branch=Release)](https://travis-ci.org/Ne00n/Night-Sky)

Night-Sky is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
You should have received a copy of the license along with this
work. If not, see https://creativecommons.org/licenses/by-nc-sa/4.0/

- PHP 7.0+, using BackgroundProcess
- Run a Slot every Second, each Slot can run multiple Processes, 5 Servers are assigned to each Process
- Loadbalance the Monitoring requests over these Slots, 10 Slots per 10 Seconds, 1 Slot reserved for Offline Servers
- If a Port is not reachable, move it to the Dedicated Offline Slot, move it back afterwards (planned)
- Limit of 20 Users for the Start, 10 Checks for each User
- Email notifications via local Mail function (relayed to MXRoute)
- Block the current Thread if the previous one is still Running
- Database Backend: PHP, MariaDB
- Frontend: PHP+HTML (Bootstrap 3 / Font Awesome)

QuickSetup:

1. Create a User+DB, Import the content/sql/night-sky.sql
2. Update content/configs/config.php with your Database details and make SURE to change "Domain", otherwise Cookies wont work.<br />
Make SURE to use a Domain with SSL enabled otherwise you will not get any cookies.
3. Put cron/night.php and cron/remote.php into Crontab to run every 60 seconds
4. Deploy some Remote Servers and add them to the table remote.<br />
The Field IP can be also contain a URL like "check.domain.com", make sure the Domain is reachable over TLS, plain wont work.
