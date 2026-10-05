# Phase 1 Requirements — MIND Holding Website

Version 1.0 (approved) · Oct 5, 2026 · Prepared by @Ahmed

## 1. Introduction

MIND Holding is a digital solutions company offering software (websites, apps, business systems) and marketing (social media, paid ads) to businesses in Egypt and the Gulf. Today the company sells directly (outbound) and needs a website it can send a prospect after the first call to prove it is a real, capable company. Paid ad campaigns come later, in Phase 2.

**Phase 1 goal:** launch a bilingual website as fast as possible that presents the company's services and solutions catalog professionally and makes it easy for a prospect to request a quote, request a demo, or start a WhatsApp chat.

**Success metrics:**

| Metric | How it is measured |
| --- | --- |
| Quote and demo requests per month | Requests recorded in the admin dashboard |
| Request-to-client conversion rate | Request status in the dashboard ("Won") |
| Use of the site in direct sales | Visits to solution page links sent to prospects (Google Analytics) |

Targets will be set after the first month live, since there is no current baseline.

**In scope for Phase 1:**

- Public pages: Home, Services, Solutions, Work, About, Contact, and legal pages.
- Solutions catalog at launch: 4 flagship and 8 additional solutions, with the rest added from the dashboard.
- Quote request, demo request, callback request, and a WhatsApp button.
- Admin dashboard to manage content, settings, and incoming requests.
- SEO basics and Google Analytics.
- Arabic and English from day one, Arabic as the default.

**Out of scope for Phase 1:**

- Live demos of the flagship solutions: built after launch and added one by one (see section 9).
- Cost estimator, campaign landing pages, Meta Pixel and conversion tracking.
- Full lead management (assignment, notes, reminders, export).
- Blog, careers, and a PDF company profile.
- Final copywriting: the client will provide it later; launch uses draft content (see section 10).

## 2. Site map

The site has 7 main pages in the menu and footer. Services, Solutions and Work each have detail pages, and the solution page is the most important page in Phase 1.

&#91;embedded content: site map · 7 main pages, with detail pages under Services, Solutions and Work\]

## 3. Page specifications

Every page exists in Arabic under `/ar` and in English under `/en`, and every page ends with a clear call to action. All text and images on these pages are editable from the dashboard.

### 3.1 Home

Section order; each section can be enabled or disabled from the dashboard:

| # | Section | Content | Visitor action |
| --- | --- | --- | --- |
| 1 | Hero | Headline that the company combines software and marketing, subheadline, illustration | "Request a quote" and "Chat on WhatsApp" |
| 2 | Quick stats | 3–4 real numbers only (e.g. team years of experience, industries covered, technologies) | — |
| 3 | Our services | Software and Marketing in two groups, with their services | Open the service page |
| 4 | Solutions by industry | Cards for the 4 flagship solutions, plus a link to all solutions | "View solution" |
| 5 | Why MIND Holding | 4–6 differentiators (software and marketing in one place, post-launch support, knowing both Egyptian and Gulf markets…) | — |
| 6 | How we work | 5 steps from first call to launch, with an approximate duration per step | — |
| 7 | Selected work | Real freelance projects only. The section hides automatically if no project is published | "View project" |
| 8 | Technologies | Technology logos grouped by category | — |
| 9 | FAQ | 6–8 questions on price, timeline, support, and payment | — |
| 10 | Closing CTA | Short headline and a short form (name, mobile, service) | "Send request" |

### 3.2 Services

- **Services page:** services in two groups: Software (websites, mobile apps, business systems, e-commerce) and Marketing (social media, paid ads, brand identity, photo and motion).
- **Service page:** description, what the client receives, delivery steps, related solutions or projects, service-specific FAQs, and a quote form with the service preselected.

### 3.3 Solutions

- **Solutions page:** all published solutions, filterable by industry (commerce, business systems, logistics, restaurants, bookings, education, real estate). Flagship solutions appear first with a badge.
- **Solution page:** the most important page in Phase 1, because it is the link sent to a prospect after the call. Content in order:
  1. Solution name and target audience (e.g. "For restaurants and cafés").
  2. The problem it solves, in 3–4 points.
  3. Key features, grouped by user (customer, business owner, staff).
  4. Mockups of the app or dashboard.
  5. Deliverables: website, Android and iOS apps, dashboard, training, support.
  6. "Request a demo" button. Once the demo is ready, a "Try the demo" button appears next to it with the demo link and test credentials.
  7. Solution-specific FAQs and related solutions.

### 3.4 Work

- **Work page:** real freelance projects only. If the client does not approve naming them, the project is shown with a generic description (e.g. "E-commerce store for a home-goods brand").
- **Project page:** overview, challenge, solution, images, technologies used, and a live link if available.
- If no project is published, the menu link hides automatically.

### 3.5 About

Company story, vision, team experience in general terms (industries and system types, with no names or screenshots from previous employers), quick stats, and how we work.

### 3.6 Contact

Full quote request form (section 5), phone and WhatsApp numbers for Egypt and the Gulf, email, address, and social links.

### 3.7 Global components

- Header with logo, menu (Home, Services, Solutions, Work, About, Contact), language switcher, and a "Request a quote" button.
- Floating WhatsApp button.
- "Request a callback" modal opened from any CTA button.
- Footer with a short description, service and solution links, contact details, social links, and legal pages.
- 404 page in Arabic and English with links to the main pages.

## 4. Solutions & services catalog at launch

**Flagship solutions:** featured on the home page; each gets a live demo after launch.

| # | Solution | Industry | Target audience | Why it was chosen |
| --- | --- | --- | --- | --- |
| 1 | E-commerce store with mobile app | Commerce | Shops and brands | Most requested solution, easy to run campaigns for |
| 2 | Restaurant & café app (order-ahead and delivery) | Food & hospitality | Restaurants, cafés, chains | Large market in both regions, fast buying decisions |
| 3 | Clinic appointment booking | Bookings & services | Clinics and medical centers | High purchasing power, especially in the Gulf |
| 4 | Integrated ERP system | Business systems | Mid-size and trading companies | Positions the company as capable of large systems |

**Additional solutions at launch:** full pages with content and mockups, no demo.

| # | Solution | Industry |
| --- | --- | --- |
| 5 | Point of sale (POS) | Commerce |
| 6 | Customer & sales management (CRM) | Business systems |
| 7 | Shipping & tracking system | Logistics |
| 8 | Delivery app (customer, driver, dashboard) | Logistics |
| 9 | Gym & fitness management | Bookings & services |
| 10 | Online learning platform | Education |
| 11 | Real estate portal | Real estate & contracting |
| 12 | Contracting & finishing company system | Real estate & contracting |

The remaining solutions from the original list (about 13) are added from the dashboard after launch, using the same solution page template and no extra development.

**Services:**

| Group | Services |
| --- | --- |
| Software | Website design & development, mobile apps (Android and iOS), custom business systems, e-commerce stores |
| Marketing | Social media management, paid ads (Meta, Google, TikTok, Snapchat), brand identity & design, photography & motion graphics |

## 5. Leads & contact

All requests are recorded in one place in the dashboard, together with their source and the page they came from, so we know which pages bring clients.

| Request type | Where it appears | Fields | Required |
| --- | --- | --- | --- |
| Quote request | Contact page, service page | Name, mobile (with country code), email, company, service, approximate budget, start timing, project details | Name, mobile, service |
| Demo request | Solution page | Name, mobile, email, business name, solution (preselected), preferred contact time | Name, mobile |
| Callback request | Modal from any CTA, short form on Home | Name, mobile, need (optional) | Name, mobile |

**Form rules:**

- The mobile field accepts Egypt and Gulf country codes, with Egypt as the default.
- Budget and start-timing options are lists managed from the dashboard.
- After submitting, a thank-you message shows the expected response time and a button to continue on WhatsApp.
- Every request stores: language, page URL, traffic source (UTM if present), and date.

**Spam protection:** Google reCAPTCHA v3 (no challenge for the visitor), a hidden honeypot field, and a limit of 5 requests per hour per device.

**WhatsApp:** a floating button on every page opens a chat with a prefilled message that changes by page, e.g. on a solution page: "I'm interested in the restaurant app solution". The WhatsApp number and default message are editable in settings.

**Notifications:** every new request sends an instant email to the sales address set in settings, with all request details and a direct WhatsApp link to the prospect.

## 6. Admin dashboard

A bilingual dashboard (with RTL) so every content change can be made without a developer. Every text field is entered in both languages side by side on the same screen.

**Users & roles:**

| Role | Permissions |
| --- | --- |
| Administrator | Everything, including users, settings, and deletion |
| Content editor | Services, solutions, work, pages, FAQs, media |
| Sales | View requests and change their status only |

**Screens:**

| Screen | What it does |
| --- | --- |
| Dashboard | New requests today, this week, and this month; last 10 requests; most-requested solutions and services |
| Requests | All requests, filterable by type, status, date, and service. Statuses: New, Contacted, Won, Not interested, Spam. Call and WhatsApp buttons next to each request |
| Solutions | Add, edit, publish, hide, and reorder solutions; mark flagships; add the demo link and test credentials when ready |
| Solution industries | Manage the industries used to filter solutions |
| Services | Manage services in both groups (Software and Marketing) |
| Work | Add projects, with an option to hide the client name |
| Home content | Enable, hide, and reorder home sections; edit hero text, stats, differentiators, process steps, and technologies |
| Pages | About, Privacy, Terms |
| FAQs | General FAQs and FAQs linked to a specific service or solution |
| Settings | Company name, logo, phone and WhatsApp numbers for Egypt and the Gulf, emails, address, social links, lead notification email, budget and start-timing lists, Google Analytics ID |
| SEO | Title, description, and share image for every page, solution, and service |
| Users | Add users and assign roles |

**General rules:** any content can be saved as a draft before publishing. Images are compressed and converted to WebP on upload. Deletion is soft and recoverable.

## 7. Non-functional requirements & tech

| Area | Requirement |
| --- | --- |
| Speed | Pages load in under 3 seconds on mobile 4G; mobile Lighthouse score 85+ for Home and the solution page |
| Mobile | Fully responsive, mobile-first, since most links will be opened from WhatsApp |
| Languages | Arabic default and right-to-left, English left-to-right; the language switcher stays on the same page |
| SEO | Server-rendered pages, title and description per page, bilingual hreflang links, sitemap, and a share image that shows when a link is sent on WhatsApp or LinkedIn |
| Security | SSL certificate, dashboard login with a limit on failed attempts, spam protection (section 5) |
| Reliability | Daily backup of data and images, kept for 14 days |
| Analytics | Google Analytics 4 from day one, with an event for every request and every WhatsApp click |

**Tech:**

| Part | Technology |
| --- | --- |
| Public website | Next.js with Tailwind CSS |
| API & dashboard | Laravel |
| Database | MySQL |
| Hosting | VPS or suitable hosting; decision in section 10 |

**Visual identity:**

| Item | Value |
| --- | --- |
| Logo | Approved MIND Holding logo: red sphere, the MIND wordmark, and the line Information Technology & Media |
| Colors | Red `#E31E24` primary for buttons and key elements, dark red `#B5161D` for gradients and hover, black `#0A0A0A` for headings and dark sections, white background |
| Fonts | Poppins for English (close to the logo tagline), IBM Plex Sans Arabic for Arabic |
| Style | White background with black sections; red reserved for buttons and key elements so it stays distinctive |

Color codes are approximated from the logo image and will be confirmed against the original brand file if one exists.

**Logo files:** the available file is `mind_logo.png`, high resolution on a white background. The design team will redraw it as SVG (vector) at the start of the design stage and produce a transparent version, a white version for dark backgrounds, and the sphere alone as the site icon (favicon). The thin white cuts through the letters can disappear at small sizes, so the standalone icon is used in tight spaces. The new versions are presented to the client for approval with the design.

## 8. Acceptance criteria

Phase 1 is accepted when all of the following pass user acceptance testing (UAT) with the client:

- [ ] Every page in section 3 works in Arabic and English, and the language switcher stays on the same page.
- [ ] All 12 solutions and 8 services are published with complete draft content in both languages.
- [ ] A new solution added from the dashboard appears on the site with no developer involvement.
- [ ] All 3 forms record the request in the dashboard, and the notification email arrives within a minute.
- [ ] The WhatsApp button opens the right chat with the page-specific message, on mobile and desktop.
- [ ] A solution page link shared on WhatsApp shows its title, description, and image.
- [ ] Changing the hero, contact numbers, or logo in settings is reflected on the site.
- [ ] The Work section hides automatically when no project is published.
- [ ] A Sales user cannot edit content, and a Content editor cannot see settings or users.
- [ ] Mobile Lighthouse score is 85+ for Home and the solution page.
- [ ] The site works on the latest two versions of Chrome, Safari, and Edge, and on Android and iPhone.
- [ ] Google Analytics records visits and request and WhatsApp events.

## 9. Timeline & next phases

Durations are estimates and start from the day this document is approved. Any delay in client review or approval shifts what follows by the same amount.

| Week | Stage | Deliverables | Needed from the client |
| --- | --- | --- | --- |
| 0 | Requirements approval | This document approved | Review and approve within 2 days |
| 1 | Design | Final design of Home and the solution page first for approval, then the remaining pages (no wireframes, per client decision) | Approve design within 2 days |
| 1–2 | Content | Draft copy for all pages and the 12 solutions in Arabic, then translation | Review content in parallel with development |
| 2–4 | Development | Website, dashboard, and requests, with a demo at the end of each week | Attend the weekly demo |
| 5 | Testing & launch | Internal QA, UAT, go-live on the domain, 1-hour dashboard training | Domain, final contact numbers and emails, UAT |

**After launch: live demos for the flagship solutions.** Demos are built one at a time in this order: e-commerce store, restaurant app, clinic booking, then ERP. Each demo takes one to two weeks depending on the team's existing code. Once a demo is done, its link is added to the solution page from the dashboard.

**Phase 2 (before ads start):** cost estimator, campaign landing pages, Meta Pixel, Google Ads and conversion tracking, full lead management, blog, and a PDF company profile. It gets its own requirements document.

## 10. Decisions, assumptions & open questions

**Decisions made in the discovery meetings:**

| # | Decision |
| --- | --- |
| 1 | The site's goal is to generate quote requests and convert them into clients |
| 2 | Market is Egypt and the Gulf, with the audience segmented by industry |
| 3 | Two phases: a site for direct sales, then inbound lead generation with ads |
| 4 | No fictional projects and no work from previous employers. Instead: freelance projects, team experience, and demos |
| 5 | Flagship solutions: e-commerce store, restaurant app, clinic booking, ERP |
| 6 | Speed is the priority, with the minimum scope that makes the site usable in sales |
| 7 | Company name is MIND Holding |

**Assumptions (in effect until the client confirms or changes them):**

- Visual identity is taken from the company logo (section 7); colors are confirmed against the original brand file if one exists.
- Our team writes draft copy in Arabic and translates it; the client reviews and edits.
- Arabic and English from day one.
- Contact details are placeholders until the final ones are entered in settings before launch.
- No public prices on the site in Phase 1; pricing goes through the quote request.

**Decisions after document review:**

- **Domain:** the new site replaces the current one on the same domain, `mindholding.net`. Old site URLs redirect (301) to the matching new pages so no traffic or previously shared links are lost.
- **Request recipients:** assigned from the admin dashboard.

**Approved contact details:**

| Item | Value |
| --- | --- |
| Address | 8 Mohammed Tawfik Diab, Nasr City, Cairo, Egypt |
| Landline | +202 22746241 |
| Mobile (Egypt) | +20 111 564 6730 |
| Dubai WhatsApp | +971 50 336 5403 |
| Email | info@mindholding.net |

**Proposals approved by the client:**

- WhatsApp button routes by visitor country: Gulf visitors reach the Dubai number, everyone else the Egyptian mobile.
- Default notification email `info@mindholding.net` until recipients are set in the dashboard.
- Launch without the Work section; projects are added once their owners approve.
- Demo order as in section 9, revisited after the team assesses existing code.
- Hosting on a single VPS for the website and the API.

**Site name:** the site appears as **MIND Holding**, so the name matches the domain `mindholding.net` and the email.
