Add parameter to enable ZTS: --enable-maintainer-zts

Compile extension: phpize, ./configure --enable-pthreads, make, make install

Install extension: echo "extension=pthreads.so" >> /etc/php.ini

MariaDB:

tar -xf mariadb-10.0.11.tar.gz
sudo apt-get install bison cmake libncurses5-dev automake autoconf libtool zlib1g-dev openssl unzip libxml2-dev libboost-dev libjudy-dev -y
rm CMakeCache.txt
cmake CMakeList.txt
make all
sudo make install

Nginx:

./configure \
	--prefix=/usr \
	--conf-path=/etc/nginx/nginx.conf \
	--error-log-path=/var/log/nginx/error.log \
	--http-log-path=/var/log/nginx/access.log \
	--pid-path=/var/run/nginx.pid \
	--lock-path=/var/lock/nginx.lock \
	--with-http_ssl_module \
	--user=www-data \
	--group=www-data \
    --with-http_stub_status_module \
	--with-http_gzip_static_module \
	--without-mail_pop3_module \
	--without-mail_imap_module \
	--without-mail_smtp_module

  make
  make install

  /etc/init.d/nginx

  #! /bin/sh

### BEGIN INIT INFO
# Provides:          nginx
# Required-Start:    $all
# Required-Stop:     $all
# Default-Start:     2 3 4 5
# Default-Stop:      0 1 6
# Short-Description: starts the nginx web server
# Description:       starts nginx using start-stop-daemon
### END INIT INFO

PATH=/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin
DAEMON=/usr/sbin/nginx
NAME=nginx
DESC=nginx

test -x $DAEMON || exit 0

# Include nginx defaults if available
if [ -f /etc/default/nginx ] ; then
    . /etc/default/nginx
fi

set -e

. /lib/lsb/init-functions

case "$1" in
  start)
    echo -n "Starting $DESC: "
    start-stop-daemon --start --quiet --pidfile /var/run/$NAME.pid \
        --exec $DAEMON -- $DAEMON_OPTS || true
    echo "$NAME."
    ;;
  stop)
    echo -n "Stopping $DESC: "
    start-stop-daemon --stop --quiet --pidfile /var/run/$NAME.pid \
        --exec $DAEMON || true
    echo "$NAME."
    ;;
  restart|force-reload)
    echo -n "Restarting $DESC: "
    start-stop-daemon --stop --quiet --pidfile \
        /var/run/$NAME.pid --exec $DAEMON || true
    sleep 1
    start-stop-daemon --start --quiet --pidfile \
        /var/run/$NAME.pid --exec $DAEMON -- $DAEMON_OPTS || true
    echo "$NAME."
    ;;
  reload)
      echo -n "Reloading $DESC configuration: "
      start-stop-daemon --stop --signal HUP --quiet --pidfile /var/run/$NAME.pid \
          --exec $DAEMON || true
      echo "$NAME."
      ;;
  status)
      status_of_proc -p /var/run/$NAME.pid "$DAEMON" nginx && exit 0 || exit $?
      ;;
  *)
    N=/etc/init.d/$NAME
    echo "Usage: $N {start|stop|restart|reload|force-reload|status}" >&2
    exit 1
    ;;
esac

exit 0

# chmod +x /etc/init.d/nginx
# update-rc.d -f nginx defaults

mkdir /etc/nginx/{sites-available,sites-enabled}

Nginx config for vhost

server {
    listen 80;

    root /path/to/your/website;
    index index.php index.html index.htm;

    server_name mydomainname.com www.mydomainname.com;

    location / {
            try_files $uri $uri/ /index.php;
    }

    location ~ \.php$ {
            try_files $uri =404;
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
            include fastcgi_params;
            fastcgi_pass unix:/var/run/php5-fpm.sock;
    }
}
