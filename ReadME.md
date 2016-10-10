10 Seconds Interval Port Monitoring

- Threading (pthreads PHP), CLI
- Run a Slot every Second, each Slot contains multiple Threads, 5 Servers per one Thread
- Loadbalance the Monitoring requests over these Slots, 10 Slots per 10 Seconds, 1 Slot reserved for Offline Servers
- If a Port is not reachable, move it to the Dedicated Offline Slot, move it back afterwards
- Limit of 20 Users for the Start, 10 Ports for each User
- EMail notifications via MXRoute
- Block the current Thread if the previous one is still Running
