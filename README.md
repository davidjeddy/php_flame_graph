# PHP Flame Graphs

## Purpose

Flame graph visualization for [PHP](https://github.com/php) using [xDebug](https://github.com/xdebug/xdebug) and [FlameGraph](https://github.com/brendangregg/FlameGraph).

## Table of Contents

- [PHP Flame Graphs](#php-flame-graphs)
  - [Purpose](#purpose)
  - [Table of Contents](#table-of-contents)
  - [Process](#process)
  - [Documentation](#documentation)
    - [Code of Conduct](#code-of-conduct)
    - [Contributing Guidelines](#contributing-guidelines)
    - [Development Documentation](#development-documentation)
  - [Requirements](#requirements)
  - [Sources](#sources)
  - [Usage](#usage)
    - [Acquire](#acquire)
    - [Execute](#execute)
    - [Update](#update)
    - [Terminate](#terminate)
  - [Versioning](#versioning)
  - [Contributors](#contributors)
  - [Additional Information](#additional-information)

## Process

- Request -> Nginx -> PHP-FPM -> xDebug -> create stack sample -> response

- Load FlameGraph -> select stack -> view trace

## Documentation

### Code of Conduct

Please see [CODE_OF_CONDUCT.md](./CODE_OF_CONDUCT.md).

### Contributing Guidelines

Please see [CONTRIBUTING_GUIDELINES.md](./CONTRIBUTING_GUIDELINES.md).

### Development Documentation

Please see [DEVELOPMENT.md](./DEVELOPMENT.md).

## Requirements

- Container orchestration solution (Docker, Podman, ContainerD)
  - [podman](https://podman.io/) and [podman-compose](https://github.com/containers/podman-compose) recommended
- Linux Bash >= 5.x
- Git

## Sources

- [brendangregg/FlameGraph](https://github.com/brendangregg/FlameGraph)
- [[Linux] Profiling —visualize program bottleneck with Flamegraph](https://medium.com/@techhara/profiling-visualize-program-bottleneck-with-flamegraph-3e0c5855b2fe)
- [How to generate PHP Flamegraphs](https://daniellockyer.com/php-flame-graphs/)

## Usage

### Acquire

```sh
cd /path/to/your/projects
git clone https://github.com/davidjeddy/php_flame_graph.git davidjeddy/php_flame_graph
cd davidjeddy/php_flame_graph
./libs/install.sh
```

### Execute

Start example service.

```sh
podman-compose up -d
podman container ls -a | grep php_flame
```

Generate a request

```sh
curl --head "http://localhost:8080/?XDEBUG_TRIGGER=1"

```

Convert xDebug stack trace to FlameGraph format

```sh
```

Open stack trace in FlameGraph UI

```sh
```

### Update

Update this project

```sh
cd /path/to/your/projects
git pull origin --force

```

### Terminate

```sh
podman-compose down
podman container rm php_flame_graph_nginx_1 || true
podman container rm php_flame_graph_php_1 || true
podman container ls -a | grep php_flame
```

## Versioning

This project follows [SemVer 2.0](https://semver.org/).

```quote
Given a version number MAJOR.MINOR.PATCH, increment the:

1. MAJOR version when you make incompatible API changes,
2. MINOR version when you add functionality in a backwards compatible manner, and
3. PATCH version when you make backwards compatible bug fixes.

Additional labels for pre-release and build metadata are available as extensions to the MAJOR.MINOR.PATCH format.
```

## Contributors

- [davidjeddy](https://github.com/davidjeddy)

## Additional Information

- Adding visual aids to any / all the above sections above is recommended.
- [Contributes](##Contributors) sources from [all-contributors](https://github.com/all-contributors/all-contributors).
- Based on [README Maturity Model](https://github.com/LappleApple/feedmereadmes/blob/master/README-maturity-model.md); strive for a Level 5 `Product-oriented README`.
- This Code of Conduct is adapted from the [Contributor Covenant](https://www.contributor-covenant.org), version 2.0, available at https://www.contributor-covenant.org/version/2/0/code_of_conduct.html.
- [CONTRIBUTING.md](./CONTRIBUTING.md) is based on the [Ruby on Rails Contributing](https://github.com/rails/rails/blob/master/CONTRIBUTING.md) document, credit is due to them.
- [LICENSE](./LICENSE.md) sources from:
  - [https://choosealicense.com](https://choosealicense.com)
  - [https://en.wikipedia.org/wiki/All_rights_reserved](https://en.wikipedia.org/wiki/All_rights_reserved)
