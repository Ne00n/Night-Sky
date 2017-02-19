10 Seconds Interval Port Monitoring

- PHP 7.0+, using BackgroundProcess
- Run a Slot every Second, each Slot contains multiple Threads, 5 Servers per one Thread
- Loadbalance the Monitoring requests over these Slots, 10 Slots per 10 Seconds, 1 Slot reserved for Offline Servers
- If a Port is not reachable, move it to the Dedicated Offline Slot, move it back afterwards (planned)
- Limit of 20 Users for the Start, 10 Ports for each User
- Email notifications via local Mail function (relayed to MXRoute)
- Block the current Thread if the previous one is still Running
- Database Backend: MySQL
- Frontend: PHP+HTML (PHP 7.0+ / Bootstrap / Font Awesome)
