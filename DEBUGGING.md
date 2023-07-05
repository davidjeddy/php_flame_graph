# Debugging

The following steps are executed on the container host.

```sh
cd /path/to/project/root
# PHP xDebug major version

# (optional) If OS != Linux, power cycle container vm 
podman machine stop && podman machine start

# Build the PHP image
podman-compose build

# restart all running containers, then list running containers
podman-compose down && podman-compose up -d && podman ps

# copy the updated stackcollapse-xdebug.php into the php container
podman cp ./services/php/tmp/ php_flame_graph_php_1:/

# Generate some traces
time && curl --head "http://localhost:8080/?XDEBUG_TRIGGER=1"
time && curl --head "http://localhost:8080/?XDEBUG_TRIGGER=1"
time && curl --head "http://localhost:8080/?XDEBUG_TRIGGER=1"

# shell into php container
podman exec -it php_flame_graph_php_1 bash
```

The following steps are executed inside the container.

```sh
# PHP xDebug major version. Do we want to test process the v2 or v3 sample trace?
export XDV=3
printenv | sort

# change ownership of the sample
chown www-data:www-data /tmp -R
ls -la /tmp
mv /tmp/stackcollapse-xdebug.php /var/www/html/brendangregg/FlameGraph/stackcollapse-xdebug.php

# test run xdebug trace ETL
clear && php -f /var/www/html/brendangregg/FlameGraph/stackcollapse-xdebug.php /tmp/xdebug.v${XDV}.trace.sample.xt

# output ETL results to a file
php -f /var/www/html/brendangregg/FlameGraph/stackcollapse-xdebug.php /tmp/xdebug.v${XDV}.trace.sample.xt > /tmp/xdebug.v${XDV}.trace.sample.folded

# Validate result
cat /tmp/xdebug.v${XDV}.trace.sample.folded
# {main} 22571
# {main};str_split 63.999999999995
# {main};ret_ord 25.999999999998
# {main};ret_ord;ord 19.999999999999

# test run flamegraph graph generation
./brendangregg/FlameGraph/flamegraph.pl /tmp/xdebug.v${XDV}.trace.sample.folded

# execute graph generation
./brendangregg/FlameGraph/flamegraph.pl /tmp/xdebug.v${XDV}.trace.sample.folded > /var/www/html/graph.svg

# validate
cat /var/www/html/graph.svg
```

View `localhost:8080/graph.svg` in a browser client.
