---
title: Get Started
excerpt: From starter template to finished website in minutes.
eyebrow: Getting Started
---

## Clone the starter template

The fastest way to start a new atoll project:

```bash
git clone https://github.com/atoll-cms/atoll-starter my-site
cd my-site
composer install
```

The starter template comes with everything: core, default theme, example pages and a working configuration.

## Start the development server

```bash
php bin/atoll dev 8080
```

Open `http://localhost:8080` in your browser. The dev server watches for file changes and invalidates the cache automatically.

## Create your first page

Create a Markdown file in `content/pages/`:

```markdown
---
title: My First Page
excerpt: A short description for meta tags.
---

## Welcome

Here goes the content of your page in Markdown.
```

The page is immediately available at `http://localhost:8080/my-first-page`.

## Customise the theme

The active theme is set in `config.yaml`:

```yaml
appearance:
  theme: default
```

Theme CSS lives in `themes/<name>/assets/main.css`. Templates can be overridden at site level in `templates/` without modifying the theme.

## Deploy

atoll needs no build step. Simply upload the project folder to your server. Requirements:

- PHP 8.2+ with extensions `json`, `mbstring`, `openssl`
- Write permissions for `cache/`, `backups/`, `content/`
- Apache with `mod_rewrite` or nginx with matching configuration

## Core updates

```bash
php bin/atoll core:check
php bin/atoll core:update:remote
```

A backup is automatically created before every update. If something goes wrong:

```bash
php bin/atoll core:rollback
```

## Help & community

- [Documentation](https://github.com/atoll-cms/atoll-docs) — Detailed guides
- [GitHub Issues](https://github.com/atoll-cms/atoll-core/issues) — Bug reports and feature requests
- [Discussions](https://github.com/atoll-cms/atoll-core/discussions) — Questions and exchange
