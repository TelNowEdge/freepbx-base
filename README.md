# TelNowEdge/FreePBX bundle to serve many tools

## Install

### With composer require

Currently unavailable

### With git

git clone inside composer vendor dir

```bash

cd /var/www/admin/libraries/Composer/vendor/telnowedge/
git clone freepbx-base

```

Update composer autoload by adding on composer.json

```yaml

"autoload": {
  "psr-4": {
  "TelNowEdge\\FreePBX\\Base\\": "vendor/telnowedge/freepbx-base"
  }
}

```

And finally run

```bash

composer.phar dump-autoload

```
