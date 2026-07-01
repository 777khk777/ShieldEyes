# ShieldEyes Marketing Site

Astro 7 + Tailwind v4 marketing site for shieldeyes.co — a B2B Passive House moisture monitoring product.

## Stack

- **Framework:** Astro 7 (`npm run dev` → localhost:4321)
- **Styling:** Tailwind CSS v4 via `@tailwindcss/vite` — CSS-first config, **no tailwind.config.js**
- **Forms:** Netlify Forms (`data-netlify="true"` attribute — only submits on Netlify, not locally)
- **Hosting:** Netlify (auto-deploys on push to main via `netlify.toml`)
- **Node:** ≥22.12.0

## Deployment

1. Commit changed files (never commit `.DS_Store`, `.claude/`)
2. `git push origin main`
3. Netlify auto-deploys to `shieldeyes.co`

## Brand Colors

Defined in `src/styles/global.css` via `@theme {}`. Use these utility classes — do not use arbitrary hex values:

| Class | Hex | Use |
|---|---|---|
| `bg-green` / `text-green` | `#2D8B57` | Primary CTA, accents, active links |
| `bg-purple` / `text-purple` | `#7B5BAD` | Secondary accents |
| `bg-hero-bg` | `#111827` | Dark hero sections |
| `bg-body-bg` | `#F9FAFB` | Page body background |
| `text-text` | `#1F2937` | Body text |
| `bg-section-bg` | `#F3F4F6` | Alternating light sections |

## Tailwind v4 Notes

- Config lives in `src/styles/global.css` (`@import "tailwindcss"` + `@theme {}`)
- `Layout.astro` imports `global.css` — **do not re-import it in page files**
- No `tailwind.config.js` — do not create one

## File Structure

```
src/
├── components/
│   ├── BaseHead.astro      # <meta>, OG, favicons
│   ├── Header.astro        # Sticky nav with active-link highlight, mobile hamburger
│   ├── Footer.astro        # Dark footer, 3-column
│   └── WaitlistForm.astro  # Netlify form, variant prop: "hero" | "page" | "on-green"
├── layouts/
│   └── Layout.astro        # Base layout (BaseHead + Header + slot + Footer)
├── pages/
│   ├── index.astro         # Home
│   ├── problem.astro       # The Problem
│   ├── how-it-works.astro  # How It Works
│   ├── for-builders.astro  # For Builders
│   ├── waitlist.astro      # Waitlist form
│   ├── about.astro         # About
│   ├── contact.astro       # Contact
│   └── blog/index.astro    # Blog index (empty state — add posts via content collections)
└── styles/
    └── global.css          # Tailwind import + @theme brand tokens
```

## Logo Assets (in `public/`)

- `shieldeyes-lockup.png` — horizontal lockup used in Header
- `shieldeyes-mark.svg` — icon mark used in Footer
- `shieldeyes-favicon.svg` / `shieldeyes-favicon-32.png` / `shieldeyes-favicon-512.png` — favicons

Source files: `/Users/peter_imac/Documents/Claude/Projects/Sheildeyes marketing/Logo images/`

## Page Layout Pattern

Every page uses `<Layout title="..." description="...">`. Section color pattern:

1. Dark hero: `bg-hero-bg`
2. Content: `bg-white`
3. Alternating: `bg-section-bg`
4. CTA: `bg-green`

## WaitlistForm Variants

- `variant="hero"` — green button, white helper text (use on dark `bg-hero-bg` sections)
- `variant="page"` — green button, dark helper text (use on white/light sections)
- `variant="on-green"` — white button with green text (use inside `bg-green` sections)

## Adding Blog Posts

When the first post is ready, set up Astro content collections:
1. Create `src/content/blog/` and add markdown files with frontmatter (`title`, `description`, `pubDate`, optional `heroImage`)
2. Add `src/content.config.ts` with the blog collection schema
3. Update `src/pages/blog/index.astro` to use `getCollection('blog')`
4. Create `src/pages/blog/[...slug].astro` for post routing

## Key Decisions

- Astro 7 (not 6) — installed latest, approved by owner
- No CMS layer — markdown + git workflow (same as UnityHaus blog)
- Netlify Forms for lead capture — no backend required
- Client portal lives separately at `app.shieldeyes.co` (existing PHP app, unrelated repo)
