#!/bin/bash
docker run --rm -v "$(pwd)/frontend":/app/frontend -v "$(pwd)/backend":/app/backend -w /app/frontend node:24-alpine sh -c "npm install && npm run build"
