# Theme Deployment — SiteGround (snelstack.com)

## How it works

- Push to `main` → GitHub Actions builds the theme → rsync deploys to live (`.github/workflows/deploy.yml`)

## SSH into SiteGround manually

```
ssh -p 18765 -i ~/.ssh/siteground_github_actions u2380-bzgwijfjxw0n@gnldm1101.siteground.biz
```

## SSH Credentials

| | |
|--------|-------|
| Hostname | `gnldm1101.siteground.biz` |
| Username | `u2380-bzgwijfjxw0n` |
| Port | `18765` |
| Key | `~/.ssh/siteground_github_actions` (public key staat in Site Tools → Devs → SSH Keys Manager als "github-actions-siteground") |

## Server facts

- Site staat onder `~/www/snelstack.com/public_html/`
- Theme pad: `~/www/snelstack.com/public_html/wp-content/themes/snelstack-fix/`
- SiteGround SSH-poort is `18765` (niet 22)
- `wp-cli` is op SiteGround servers beschikbaar als `wp` (zie ook antiquewarehouse: `/usr/local/bin/wp`)

## Claude Code

Permission-regel om Claude direct te laten SSH'en (via `/permissions` → Allow):

```
Bash(ssh -p 18765 -i ~/.ssh/siteground_github_actions u2380-bzgwijfjxw0n@gnldm1101.siteground.biz *)
```

## Zie ook

- antiquewarehouse: `~/Local Sites/antiquewarehouse/.../docs/deployment-siteground.md` (ander SiteGround-account, zelfde key)
