---
title: Features
excerpt: Everything atoll includes — and deliberately leaves out.
eyebrow: Product
---

## Content Management

### Flat-File Storage
All content lives as Markdown files with YAML frontmatter in the `content/` folder. No database, no migrations, no dump files. A `cp -r` is enough for a backup.

### Collections
Besides individual pages (`content/pages/`), any number of collections can be defined — blog posts, projects, team members, recipes. Each collection has a listing template and a detail template.

### Data Files
Structured data like navigation, configuration or product lists can be stored as YAML files in `content/data/` and used directly in templates.

### Admin Panel
The built-in admin panel runs as a single-page application on Vue.js — no additional installation, no extra cost. CRUD for pages, media upload, live preview.

## Rendering & Performance

### Zero JavaScript by Default
atoll delivers pure HTML without client-side JavaScript. No framework runtime, no hydration overhead. The resulting page is exactly as fast as the network connection allows.

### Island Architecture
Interactive components are defined as "islands" and only loaded when needed:

- `client: load` — Immediately after page load
- `client: idle` — When the browser is idle
- `client: visible` — When the element becomes visible
- `client: media` — At a specific viewport size
- `client: none` — Server-side only, no client JS

### HTML Cache
Rendered pages are cached as HTML files. Invalidation happens automatically when source files change. Typical response times on cache hit: under 50ms.

### SEO
Meta tags, Open Graph, Twitter Cards and structured data are automatically generated from frontmatter. Sitemap and robots.txt are built in.

## Extensibility

### Theme System
Themes consist of CSS and optional Twig templates. Template resolution follows a clear chain:

1. `templates/` (site-level override)
2. `themes/<active>/templates/` (theme)
3. `core/themes/<active>/templates/` (core theme)
4. `core/themes/default/templates/` (fallback)

### Plugin System
Plugins register via hooks and can bring their own routes, templates and islands. Official plugins:

- **SEO** — Extended meta tags and sitemap configuration
- **Analytics** — Privacy-compliant tracking without external services
- **i18n** — Multilingual routing with hreflang support
- **Forms Pro** — Advanced forms with file upload and validation
- **Tables** — Sortable, filterable data tables as island
- **Visual Editor** — WYSIWYG Markdown editor in the admin panel

### Hook System
Plugins and themes can tap into events:

- `head:meta`, `head:scripts` — Extend the HTML head
- `body:start`, `body:end` — Insert body tags
- `page:before_render`, `page:after_render` — Influence rendering

## Operations & Security

### CLI Tools
All administrative tasks run via CLI:

- `atoll serve` / `atoll dev` — Development server
- `atoll core:update:remote` — Signed core updates
- `atoll core:rollback` — Immediate rollback
- `atoll cache:clear` — Invalidate cache
- `atoll plugin:install` — Manage plugins
- `atoll theme:activate` — Switch themes

### Security
Built-in security features without additional plugins:

- CSRF tokens for all forms
- Rate limiting (configurable per route)
- Content Security Policy headers
- Signed release packages with integrity verification
- IP-based admin access control
- bcrypt-hashed passwords with optional 2FA (TOTP)
- Automatic backups before core updates

### Hosting
atoll runs anywhere PHP 8.2+ is available. No Node.js, no Docker, no build process on the server. Upload, adjust `config.yaml`, done.
