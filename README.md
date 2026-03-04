# atoll-website

Public website for atoll-cms, built with atoll itself.

## Local development

```bash
composer install
php bin/atoll serve 8080
```

## Static export

```bash
php scripts/export-static.php dist
```

## Deployment

GitHub Actions builds a local static `dist/` from the atoll runtime and deploys that artifact to GitHub Pages.
