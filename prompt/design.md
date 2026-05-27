---
name: Nexus Editorial
colors:
  surface: '#fcf8fa'
  surface-dim: '#dcd9db'
  surface-bright: '#fcf8fa'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f6f3f5'
  surface-container: '#f0edef'
  surface-container-high: '#eae7e9'
  surface-container-highest: '#e4e2e4'
  on-surface: '#1b1b1d'
  on-surface-variant: '#45464d'
  inverse-surface: '#303032'
  inverse-on-surface: '#f3f0f2'
  outline: '#76777d'
  outline-variant: '#c6c6cd'
  surface-tint: '#565e74'
  primary: '#000000'
  on-primary: '#ffffff'
  primary-container: '#131b2e'
  on-primary-container: '#7c839b'
  inverse-primary: '#bec6e0'
  secondary: '#006a61'
  on-secondary: '#ffffff'
  secondary-container: '#86f2e4'
  on-secondary-container: '#006f66'
  tertiary: '#000000'
  on-tertiary: '#ffffff'
  tertiary-container: '#271901'
  on-tertiary-container: '#98805d'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#dae2fd'
  primary-fixed-dim: '#bec6e0'
  on-primary-fixed: '#131b2e'
  on-primary-fixed-variant: '#3f465c'
  secondary-fixed: '#89f5e7'
  secondary-fixed-dim: '#6bd8cb'
  on-secondary-fixed: '#00201d'
  on-secondary-fixed-variant: '#005049'
  tertiary-fixed: '#fcdeb5'
  tertiary-fixed-dim: '#dec29a'
  on-tertiary-fixed: '#271901'
  on-tertiary-fixed-variant: '#574425'
  background: '#fcf8fa'
  on-background: '#1b1b1d'
  surface-variant: '#e4e2e4'
  official-ink: '#0F172A'
  community-teal: '#0D9488'
  community-indigo: '#4F46E5'
  paper-white: '#F8FAFC'
  surface-border: '#E2E8F0'
  reaction-red: '#EF4444'
typography:
  display-xl:
    fontFamily: Hanken Grotesk
    fontSize: 48px
    fontWeight: '800'
    lineHeight: 56px
    letterSpacing: -0.02em
  display-xl-mobile:
    fontFamily: Hanken Grotesk
    fontSize: 36px
    fontWeight: '800'
    lineHeight: 42px
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Hanken Grotesk
    fontSize: 32px
    fontWeight: '700'
    lineHeight: 40px
  headline-lg-mobile:
    fontFamily: Hanken Grotesk
    fontSize: 24px
    fontWeight: '700'
    lineHeight: 32px
  article-body:
    fontFamily: Literata
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 32px
  ui-medium:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '500'
    lineHeight: 24px
  ui-small:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  label-caps:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '700'
    lineHeight: 16px
    letterSpacing: 0.05em
rounded:
  sm: 0.125rem
  DEFAULT: 0.25rem
  md: 0.375rem
  lg: 0.5rem
  xl: 0.75rem
  full: 9999px
spacing:
  base: 4px
  gutter: 24px
  margin-mobile: 16px
  margin-desktop: 64px
  container-max: 1200px
  article-width: 720px
---

## Brand & Style

The design system is built on a **Dual-Identity Modernist** approach. It balances the authoritative, quiet confidence of a high-end personal editorial with the energetic, inclusive atmosphere of a digital community. 

The aesthetic is "Corporate-Modern meets Editorial":
- **Professional & Inviting:** Uses high-quality typography and generous whitespace to ensure the owner's content feels curated and expert.
- **Community-Focused:** Employs vibrant accents and interactive state changes to encourage participation in the social space.
- **Minimalist Core:** The structural elements remain invisible, allowing the content—whether a long-form essay or a community thread—to take center stage.
- **Strategic Differentiation:** Clear visual cues distinguish "Official" from "Community" to prevent user confusion while maintaining a single cohesive ecosystem.

## Colors

The palette uses a **Split-Brand Logic** to define user context.

- **Official/Blog Context:** Anchored by `official-ink` (#0F172A). This deep charcoal-blue provides a sophisticated, authoritative backdrop for the primary blog. Use it for headings, primary navigation, and "Official" badges.
- **Community Context:** Driven by `community-teal` (#0D9488) and `community-indigo` (#4F46E5). These vibrant hues distinguish community-driven content and social interactions.
- **Interaction Palette:** Semantic colors are reserved for functional feedback—red for reactions and warnings, while soft slates manage the neutral UI scaffolding.
- **Backgrounds:** Content is set against `paper-white` (#F8FAFC) to reduce eye strain for long-form reading, with slightly darker containers for the community feed to provide a distinct sense of "space."

## Typography

This design system utilizes a **Hybrid Type System** to maximize readability and hierarchy.

- **Interface & Headlines:** *Hanken Grotesk* provides a sharp, contemporary feel for titles and navigation. Use heavy weights (700-800) for "Official" titles to create a strong editorial presence.
- **Article Reading:** *Literata* is used for all long-form body text. It is a serif font optimized for digital reading, featuring a tall x-height and comfortable line spacing (32px) to prevent reader fatigue.
- **System Labels:** *Inter* handles functional UI elements like buttons, input fields, and metadata.
- **Case Usage:** Use `label-caps` (Inter, All-Caps) for small categorizations like "COMMUNITY" or "OFFICIAL" badges to ensure they are immediately recognizable at a glance.

## Layout & Spacing

The layout uses a **Context-Aware Grid** system.

- **Editorial View:** Articles are centered in a restricted `article-width` (720px) container. This optimizes the line length for the *Literata* body text, ensuring a premium reading experience similar to traditional print journals.
- **Community View:** Uses a standard 12-column fluid grid. Content cards in the community space can span 8 columns (main feed) with a 4-column sidebar for trending tags and notifications.
- **Spacing Rhythm:** Based on a 4px baseline. Use 24px (base * 6) for standard gutters and 48px for vertical section spacing.
- **Breakpoints:**
  - **Desktop (1024px+):** Full 12-column layout with 64px margins.
  - **Tablet (768px - 1023px):** 8-column layout, margins reduced to 32px.
  - **Mobile (< 768px):** Single column layout, 16px horizontal margins. Navigation collapses into a bottom-anchored bar for easy thumb access.

## Elevation & Depth

Visual hierarchy is established through **Tonal Layers** rather than heavy shadows to maintain a clean, modern look.

- **Base Layer:** `paper-white` for the main canvas.
- **Mid Layer:** Subtle grey backgrounds (#F1F5F9) are used for sidebars and community feed backgrounds to "recess" them behind the main content.
- **Top Layer (Interactive):** Cards and floating elements use a very soft, diffused ambient shadow (0px 4px 20px rgba(15, 23, 42, 0.05)) and a 1px solid border (`surface-border`) to appear slightly lifted.
- **Official Content:** "Official" posts have a thicker left-border accent in `official-ink` to denote their status, providing depth through color rather than physical elevation.

## Shapes

The design system adopts a **Soft Geometric** shape language.

- **Soft Edges:** A base roundedness of 0.25rem (4px) is applied to buttons and inputs to keep them looking professional and structured.
- **Containers:** Content cards and profile avatars use `rounded-lg` (0.5rem) to feel more inviting and modern.
- **Interactions:** "Like" and "Reaction" buttons use `rounded-xl` (0.75rem) or full circles to differentiate them from functional navigation buttons, signaling their social nature.
- **Avatars:** User photos are always circular to humanize the community space.

## Components

### Buttons
- **Primary (Official):** Solid `official-ink` background, white text, 4px radius.
- **Secondary (Community):** Solid `community-teal` or `community-indigo` background.
- **Ghost:** `surface-border` outline with `official-ink` text for less prominent actions.

### Cards
- **Editorial Card:** Minimalist, no border, large headline, uses `article-body` for the excerpt. Focuses on imagery.
- **Community Post Card:** White background, 1px `surface-border`, 8px padding. Includes a footer with reaction icons and a comment count.

### Social Features
- **Reactions:** A horizontal pill-shaped container that expands on hover. Selected reactions are highlighted with a soft tint of `community-indigo`.
- **Threading:** Nested comments use a vertical "thread line" (2px width, `surface-border`) to visually connect replies. Replies are indented 24px.

### Input Fields
- Understated design: 1px `surface-border` that transitions to `community-teal` on focus. Labels use `ui-small` (Inter) for clarity.

### AI Indicators
- **Smart Summary:** A special "Sparkle" icon and a light indigo background tint (#EEF2FF) to denote AI-generated content or summaries, clearly distinguishing them from user or owner content.