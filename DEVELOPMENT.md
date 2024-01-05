# DEVELOPMENT

## Code Analysis

## Compliance

## Cost Control

## Linting / Style Guides

## Operations

- [Nginx configuration](https://gist.github.com/xameeramir/a5cb675fb6a6a64098365e89a239541d)

## Process

### Part 1: Start App w/ xDebug and Generate Traffic

```sh
clear

podman-compose down || true
podman container rm php_flame_graph_nginx_1 || true
podman container rm php_flame_graph_php_1 || true
rm ./services/nginx/var/log/nginx/*.log || true
rm ./services/php/var/log/php/*.log || true
rm ./services/php/var/log/xdebug/*.log || true

podman-compose up -d
podman container ls -a | grep php_flame

curl --head "http://localhost:8080/"
curl --head "http://localhost:8080/?XDEBUG_TRIGGER=1"
```

### Part 2: Convert xDebug Trace to FlameGraph Stacktrace

```sh
$ podman exec -it php_flame_graph_php_1 ls -la /tmp

total 22
drwxrwxrwt. 1 root     root     4096 Jan  5 05:39 .
dr-xr-xr-x. 1 root     root       73 Jan  5 04:46 ..
drwxr-xr-x. 5 root     root       47 Jan  4 01:13 pear
-rw-r--r--. 1 www-data www-data 3900 Jan  5 05:40 xdebug.log
-rw-r--r--. 1 www-data www-data  547 Jan  5 05:40 xdebug.profilel.2373089536-1704433198
-rw-r--r--. 1 www-data www-data  583 Jan  5 05:40 xdebug.trace.2373089536-1704433198.xt

$ podman exec -it php_flame_graph_php_1 cat /tmp/xdebug.trace.2373089536-1704433198.xt > trace.xt

$ cat trace.xt

Version: 3.2.1
File format: 4
TRACE START [2024-01-05 05:39:58.500670]
1	0	0	0.002782	395432	{main}	1		/var/www/html/index.php	0	0
2	1	0	0.002832	395432	test	1		/var/www/html/index.php	8	0
3	2	0	0.002837	395432	sleep	0		/var/www/html/index.php	3	1	5
3	2	1	5.003766	395464
3	2	R			0
3	3	0	5.004112	395432	phpinfo	0		/var/www/html/index.php	4	0
3	3	1	5.006257	395672
3	3	R			TRUE
3	4	0	5.006295	395672	xdebug_info	0		/var/www/html/index.php	5	0
3	4	1	5.006406	395672
3	4	R			NULL
2	1	1	5.006433	395672
1	0	1	5.006445	395672
			5.009994	390760
TRACE END   [2024-01-05 05:40:03.507919]
```

```sh
# shell into the CLI service
podman exec -it php_flame_graph_php_cli_1 bash
# Copy in the contents of `trace.xt` into /var/www/html of the php_cli container
vi /var/www/html/trace.xt
```

```sh
$ rm /var/www/html/trace.folded || true;
$ php -f /var/www/html/stackcollapse-xdebug.php trace.xt > /var/www/html/trace.folded;
$ cat /var/www/html/trace.folded;

{main} 0
{main};test -5000000
{main};test;sleep 5000000
{main};phpinfo 0
xdebug_info 0
```

### Part 3: Convert Folded Stacktrace to SVG

```sh
$ podman exec -it php_flame_graph_php_cli_1 cat /var/www/html/trace.folded > trace.folded
$ cat trace.folded
$ ./libs/brendangregg/FlameGraph/flamegraph.pl trace.folded > trace.svg 
```

### Part 4: View SVG

```sh
open trace.svg
```

## Security

- See [./SECURITY](./SECURITY.md)

## References

- [https://daniellockyer.com/php-flame-graphs/](https://daniellockyer.com/php-flame-graphs/)
