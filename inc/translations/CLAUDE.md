# Translation System (Snel Translations)

Portable multilingual system — copy to any WordPress theme.

## Model: one post per language

Each language version of a page/post is a **separate WordPress post** with its
own native slug. Translations are linked by a **translation group** (the same
idea as WPML's `trid`). Stored in post meta:

- `_snel_lang` — the language a post is written in (e.g. `en`). Missing = default.
- `_snel_group` — the shared group id across all siblings (the root post's id).

The default language has **no URL prefix** (`/over-ons/`); others get one
(`/en/about-us/`). Each post keeps its own real slug — no slug-translation meta.

## Architecture

```
config/
  languages.php           # Supported languages: code, label, locale, default flag
  slugs-cpt.php           # CPT archive slug translations per language
core/
  LocaleManager.php       # Current language detection; default (option or config)
  Router.php              # Rewrite rules + resolve URL → sibling post per language
  TranslationGroup.php    # Link posts as translations; permalink prefix; archive filter; slug uniqueness
  TermTranslation.php     # Translated term name/description (shared term, meta-backed); frontend get_term swap
  Translator.php          # Static theme-string lookups only (snel__)
urls/
  UrlGenerator.php        # Build language-aware URLs (sibling permalinks)
seo/
  SeoManager.php          # hreflang + <html lang> (canonical/meta deferred to Yoast)
admin/
  create-translation.php  # Editor sidebar data + "Create translation" AJAX (duplicate + AI translate)
  admin-columns.php       # Language column + filter on post list tables
  term-translations.php   # Per-language term Name/Description fields + "Translate with AI" button
  admin-translations.php  # "Snel Translations" admin page (header) + Settings (languages, default)
  translate.php           # AI translation via WordPress AI Client (snel_ai_translate)
language.php              # Entry point — loads everything, defines helpers
translations.php          # Default theme-string translations (UI text)
```

## How it works

1. Non-default languages get a URL prefix via rewrite rules → `lang` query var.
2. `Router::resolveLanguagePost` maps the request to the sibling post written in
   that language (pinning a concrete post id).
3. `TranslationGroup::filterPermalink` injects the `/en/` prefix into
   `get_permalink()` so menus, links, and Yoast's canonical are correct.
4. `TranslationGroup::filterArchives` constrains listings to the current language.

## Creating translations

In the editor, the **Snel Stack sidebar** (only on the default-language post)
has a target-language dropdown + "Create" button. It duplicates the post into a
new draft, links it to the group, and AI-translates the title + block text.
See `admin/create-translation.php`.

## SEO

- Theme outputs **hreflang** (only languages a page is actually translated into)
  and the **`<html lang>`** attribute.
- Canonical, meta description, Open Graph, schema, sitemaps → **Yoast** (or any
  SEO plugin). `SeoManager` only falls back to its own canonical/meta when no
  SEO plugin is active.

## Helper functions

| Function | Purpose |
|----------|---------|
| `snel_get_lang()` / `snel_get_default_lang()` / `snel_get_supported_langs()` | Language config |
| `snel_post_lang($id)` | The language a post is written in |
| `snel_get_translation($id, $lang)` | Sibling post id in a language (0 if none) |
| `snel_get_translations($id)` | All siblings, keyed by language |
| `snel_url($url)` / `snel_lang_url($lang)` | Language-aware URLs |
| `snel_title($id)` / `snel_excerpt($id)` | Native title/excerpt (monolingual) |
| `snel_term_name($term, $lang)` | Term name in current (or given) language |
| `snel_term_description($term, $lang)` | Term description in current (or given) language |
| `snel__($text)` | Static theme-string translation (UI text) |

## Taxonomy terms (shared term, translated label)

Unlike posts, a term is **not** duplicated per language. One term keeps its
native name, slug and description; the translated **name** and **description**
live in term meta (`_snel_name_{lang}`, `_snel_desc_{lang}`). This avoids
duplicate terms, broken relationships and routing changes — only the label
translates.

- Edit translations on the term edit screen (every public taxonomy gets the
  fields). The "Translate with AI" button fills them from the native values.
- On the frontend, `TermTranslation::filterTerm` (a `get_term` filter) swaps
  `name`/`description` into the current language, so `single_cat_title()`,
  `wp_list_categories()`, `get_the_terms()` etc. translate automatically.
- In wp-admin the current language is always the default, so the filter is a
  no-op there — edit screens always show the native term.
- For explicit output use `snel_term_name()` / `snel_term_description()`.
- Limit which taxonomies get fields with the `snel_term_translatable_taxonomies`
  filter.

## Blocks are monolingual

Custom blocks store plain values (no `{nl,en}` objects). A post is in one
language, so blocks just render their content. To make a new Snel block's text
translatable by the Create flow, add its text attribute keys to
`snel_block_text_attrs()` in `admin/create-translation.php`.

## Custom fields / ACF

The Create flow copies all meta to the new translation, then AI-translates the
keys declared in `snel_translatable_meta_keys($post_type)` (in
`admin/create-translation.php`) — the meta counterpart of
`snel_block_text_attrs()`. Add ACF/custom text fields there per project, e.g.
`['subtitle', 'description']`, or use the `snel_translatable_meta_keys` filter.

Scope: flat text/textarea-style meta (plain ACF text, textarea, wysiwyg).
Repeaters and nested groups are NOT translated — copied in the source language,
translate by hand. On AI failure the copied source values are left in place.

## Rules

- Add/remove languages in `config/languages.php`. Default can be overridden in
  Settings (stored in the `snel_default_lang` option).
- Add CPT archive slug translations in `config/slugs-cpt.php` only.
- After changing language config or default, flush rewrite rules
  (Settings → Permalinks → Save). The Settings save does this automatically.
- All `$_POST`/`$_GET`: `wp_unslash()` before `sanitize_*`.

## Menus (`nav-menu.php`)

Build ONE menu in the default language. `snel_nav_item($item)` resolves each
item for the current language:

- **Page/post items** → link + label come from the translation sibling. No
  translation yet → falls back to the default-language page (never a gap).
- **Custom labels** (menu title ≠ page title), **custom links**, taxonomies →
  label via `snel__()` (theme strings); internal paths get the language prefix.

`header.php` calls `snel_nav_item()` directly; other menus (footer) are handled
by a `wp_nav_menu_objects` filter.

## Theme strings

Static UI text (button labels, custom menu labels) uses `snel__()` +
`translations.php` defaults + the `snel_theme_translations` option override.
