#!/bin/sh
set -e

npm install
npm run build
npx serve dist --listen tcp://0.0.0.0:5173 --single
