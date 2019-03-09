#!/bin/bash
URL=$1;
youtube-dl $URL -q --print-json -s