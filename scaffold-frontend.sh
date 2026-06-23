#!/bin/bash
docker run --rm -v "$(pwd)":/app -w /app node:24-alpine sh -c "npm install -g @angular/cli@22 && ng new frontend --routing --style=scss --ssr=false --skip-git --defaults"
