# atoll-website

Public website for atoll-cms, built with atoll itself.

## Local development

```bash
composer install
php bin/atoll dev 8080
```

## Static export

```bash
php scripts/export-static.php dist
```

For subpath deployments (for example GitHub Pages project sites), set:

```bash
ATOLL_EXPORT_BASE_URL="https://atoll-cms.github.io/atoll-website" php scripts/export-static.php dist
```

## Deployment

GitHub Actions builds a local static `dist/` from the atoll runtime and deploys that artifact to GitHub Pages.
