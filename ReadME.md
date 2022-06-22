# Night-Sky

Now available in the Arctic Code vault!<br />
10,20,30,60s Interval Port & HTTP(S) IPv4 & IPv6 Monitoring, with Webhooks (Discord...) and/or email notifications.

![Overview](https://i.imgur.com/3N4NWqD.png)

All screenshots: https://imgur.com/a/Z8Dyo

[![Build Status](https://travis-ci.com/Ne00n/Night-Sky.svg?branch=Release)](https://app.travis-ci.com/github/Ne00n/Night-Sky)

Night-Sky is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
You should have received a copy of the license along with this
work. If not, see https://creativecommons.org/licenses/by-nc-sa/4.0/

- PHP 7.3+, recommended is 7.4, using BackgroundProcess
- Run a Slot every Second, each Slot can run multiple Processes, 5 Servers are assigned to each Process
- Loadbalance the Monitoring requests over these Slots, 10 Slots per 10 Seconds, 1 Slot reserved for Offline Servers
- If a Port is not reachable, move it to the Dedicated Offline Slot, move it back afterwards (planned)
- Email notifications via local mailserver
- Block the current Thread if the previous one is still Running
- Backend: PHP, MariaDB
- Frontend: Bootstrap 3, Font Awesome 4.7

QuickSetup:

Beforehand make sure you have a working mailserver running otherwise you need to enable the accounts by hand.<br />
Also you need curl + mtr installed on all machines.

1. Create a User+DB, Import the content/sql/night-sky.sql.
2. Rename content/configs/config.example.php to config.php and regex.example.php to regex.php.
3. Configure content/configs/config.php:
- _Domain needs to be updated to the Domain you want to use, otherwise Cookies wont work.
- _mail_sender needs to be updated, to make contact work properly. The mails will get forwarded to the local mailserver.
- Finally you need to update the Database part, with the Details you created the Database with.
- Make sure to use TLS on your Domain otherwise cookies will not apply on Plain.
- The rest can be edited on your needs.
4. Put cron/night.php and cron/remote.php into Crontab to run every 60 seconds, use a non privileged user for that. => cron.example
5. Run composer<br />
```
composer install --no-dev
```
This will generate night.css and night.js plus install minify, jquery and bootstrap-select<br />
6. Deploy the file check.php on some remote servers and add them to the table remote.<br />
- You can find the file for that in content/remote/<br />
- The Field IP can be also contain a URL like "check.domain.com", make sure the Domain is reachable over TLS, plain wont work.<br>
- The Field IP should not contain "https://" or "/check.php"
