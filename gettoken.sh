#!/bin/sh

curl -X POST -H "Content-Type: application/json"  \
-D - -k https://api.teamsnap.com/v2/authentication/login \
-d '{"user": "<id>", "password": "XXXXXXX"}'