#!/bin/bash

# Stop execution if a step fails
set -e

DOCKER_USERNAME=imnotteixeira # Replace by your docker hub username
IMAGE_NAME=lbaw1842-piu

docker build -t $DOCKER_USERNAME/$IMAGE_NAME -f Dockerfile-piu .
docker push $DOCKER_USERNAME/$IMAGE_NAME
