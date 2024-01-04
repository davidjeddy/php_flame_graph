# Runbook

-----

**Error**

The specific text of the error goes here.

**When**

Detail the actions or events that lead up to the error occurring.

**Fix**

Process to correct the error.

-----

**Error**


The following steps are executed on the container host.

```sh
cd /path/to/project/root

# Clone upstream project
# REMOVE after iterating on php image build
git clone https://github.com/brendangregg/FlameGraph.git ./services/shared/var/www/html/brendangregg/FlameGraph

# (optional) If OS != Linux, power cycle container vm 
podman machine stop && podman machine start

# Build the PHP image
podman-compose build

# restart all running containers, then list running containers
podman-compose down && podman-compose up -d && podman ps

# copy the updated stackcollapse-xdebug-v3.php into the php container
podman cp ./services/php/tmp php_flame_graph_php_1:/

# Generate some traces
## TODO 502 bad gateway
time && curl --head "http://localhost:8080/?XDEBUG_TRIGGER=1"
time && curl --head "http://localhost:8080/?XDEBUG_TRIGGER=1"
time && curl --head "http://localhost:8080/?XDEBUG_TRIGGER=1"
```

Shell into the running php container.

```sh
podman exec -it php_flame_graph_php_1 bash
```

These steps are executed inside the container.

```sh
# PHP xDebug major version. Do we want to test process the v2 or v3 sample trace?
export XDV=3 && printenv | sort

# change ownership of the sample
chown www-data:www-data /tmp/ -R && ls -la /tmp

# test run xdebug trace ETL
clear && php -f /tmp/stackcollapse-xdebug-v${XDV}.php /tmp/xdebug.v${XDV}.trace.sample.xt
# {main} 22571
# {main};str_split 63.999999999995
# {main};ret_ord 25.999999999998
# {main};ret_ord;ord 19.999999999999

# output ETL results to a file
php -f /var/www/html/brendangregg/FlameGraph/stackcollapse-xdebug-v3.php /tmp/xdebug.v${XDV}.trace.sample.xt > /tmp/xdebug.v${XDV}.trace.sample.folded

# test run flamegraph graph generation
./brendangregg/FlameGraph/flamegraph.pl /tmp/xdebug.v${XDV}.trace.sample.folded
# ...
# <title>all (22,681 samples, 100%)</title><rect x="10.0" y="85" width="1180.0" height="15.0" fill="rgb(223,172,43)" rx="2" ry="2" />
# <text  x="13.00" y="95.5" ></text>
# </g>
# <g >
# <title>ret_ord (46 samples, 0.20%)</title><rect x="1184.3" y="53" width="2.4" height="15.0" fill="rgb(246,195,34)" rx="2" ry="2" />
# <text  x="1187.28" y="63.5" ></text>
# </g>
# </g>
# </svg>

# execute graph generation
./brendangregg/FlameGraph/flamegraph.pl /tmp/xdebug.v${XDV}.trace.sample.folded > /var/www/html/graph.svg

# validate
cat /var/www/html/graph.svg
# ...
# <text  x="13.00" y="79.5" >{main}</text>
# </g>
# <g >
# <title>str_split (64 samples, 0.28%)</title><rect x="1186.7" y="53" width="3.3" height="15.0" fill="rgb(217,93,48)" rx="2" ry="2" />
# <text  x="1189.67" y="63.5" ></text>
# </g>
# </g>
# </svg>
```

View `localhost:8080/graph.svg` in a browser client.
