#!/bin/bash
docker run --rm -v "$(pwd)/backend":/app -w /app composer:2 composer update laravel/sanctum --no-interaction --ignore-platform-reqs
