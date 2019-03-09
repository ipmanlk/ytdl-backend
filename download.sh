#!/bin/bash
URL=$1;
CODE=$2;
MD5=$3;
youtube-dl $URL -f $CODE  -o "./down/$MD5.%(ext)s" | grep "Destination:"