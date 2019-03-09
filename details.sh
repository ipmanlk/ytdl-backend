#!/bin/bash
URL=$1;
youtube-dl -F $URL | grep -E 'video only' -v | awk '/^([0-9]+)/ {print $1 "+" $2 "+" $3 "+" $7 "+" $NF "|"}'