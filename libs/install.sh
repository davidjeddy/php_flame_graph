#!/bin/bash -e

rm -rf ./libs/brendangregg/FlameGraph || true
git clone https://github.com/brendangregg/FlameGraph.git ./brendangregg
