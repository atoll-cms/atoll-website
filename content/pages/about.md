---
title: Why atoll?
excerpt: Goals, architecture and positioning of atoll-cms.
eyebrow: Philosophy
---

## The Problem

Most CMS landscapes force you into one of two corners:

**Too complex.** WordPress, Drupal and co. bring databases, plugin conflicts, weekly security updates and infrastructure built for a Fortune 500 company. For your 12-page agency website, that's like driving a tank to the bakery.

**Too minimal.** Static site generators like Hugo or Eleventy are fast, but without an admin panel, without dynamic features, without the ability for non-developers to manage content. And your client won't learn Git.

## The Solution

atoll is the middle ground: a CMS that delivers professional websites without punishing you with complexity.

- **Flat-File** — No database, no migrations. Content is Markdown files with YAML frontmatter. `git push` is your deployment.
- **PHP-native** — Runs on PHP 8.2+. No Node.js, no Docker, no CI/CD required. Any host with PHP support is enough.
- **Admin panel included** — Your client can manage content in the browser. No extra plugin, no extra cost.
- **Island architecture** — Interactive components (search, forms, galleries) are only loaded when needed. The rest is static HTML.

## Who is atoll for?

**Freelancers and agencies** who want to deliver client projects without infrastructure overhead. A folder on the server, adjust `config.yaml`, done.

**Developers** looking for a CMS that feels like code, not a GUI. Twig templates, hook system, CLI tools — everything you expect.

**Teams** who like Git-based workflows. Flat-file means: branching, code reviews and staging environments work out of the box.

## What atoll is not

atoll is not an enterprise CMS. It has no multi-user role system with 47 permission levels. It has no visual page builder with drag-and-drop. And it will never try to replace Shopify.

atoll is a tool for people who know what they're doing — and want a CMS that stays out of their way.

## Open Source

atoll is MIT-licensed. The entire codebase is public on [GitHub](https://github.com/atoll-cms). There is no "Pro" version, no feature paywall, no artificial restrictions.

Why? Because good tools should be free.
