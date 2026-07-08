# Γ.Α.Σ. Μαχητές Μεσολογγίου — Website

Ισχυρός & αγωνιστικός, ελληνόγλωσσος ιστότοπος για τον **Γ.Α.Σ. Μαχητές Μεσολογγίου** (Γεωργίου Λιακατά 17, Μεσολόγγι) — σύλλογο Taekwondo με τον δάσκαλο **Σωτήρη Λιαμέτη, 7ου Dan** — με πλήρες self-hosted admin panel, χωρίς framework, χωρίς third-party CMS.

- **Δημόσιο site**: hero με CTA & rating, ενότητα σύλλογος, δυναμικά τμήματα (με εικόνα/εικονίδιο & τιμή), αθλητές με ρεκόρ & αγώνες, τρόπαια & διακρίσεις, gallery με lightbox, εβδομαδιαίο πρόγραμμα, blog, φόρμα επικοινωνίας, αξιολογήσεις Google, cookie banner, newsletter, νομικές σελίδες.
- **Admin panel**: dashboard με στατιστικά επισκεψιμότητας, άρθρα (Quill rich editor), τμήματα (με εικόνα/εικονίδιο, ηλικίες, συνδρομή), αθλητές + αγώνες, τρόπαια, πρόγραμμα, gallery (upload/reorder), μηνύματα, newsletter, **πλήρως παραμετροποιήσιμες ρυθμίσεις σε κάθε σελίδα** (hero, sections, backgrounds, CTA, feature cards, timeline, fact-list κ.λπ.), αλλαγή κωδικού.

---

## Tech stack

| Layer          | Choice                                                                 |
| -------------- | ---------------------------------------------------------------------- |
| Language       | PHP 8.2 (no framework)                                                 |
| Database       | MySQL 8 / MariaDB 11                                                   |
| Web server     | Apache 2.4 (mod_rewrite + AllowOverride All)                           |
| Front-end      | Vanilla CSS + Oswald/Inter/Rajdhani + FontAwesome 6                    |
| Rich editor    | Quill 2 (loaded from CDN in admin only)                                |
| Deploy target  | Docker / Coolify / any XAMPP-like PHP host                             |

---

## Table of contents

1. [Local dev — XAMPP](#local-dev--xampp)
2. [Local dev — Docker Compose](#local-dev--docker-compose)
3. [Deploy on Coolify](#deploy-on-coolify)
4. [Environment variables](#environment-variables)
5. [Post-deploy: first-time install](#post-deploy-first-time-install)
6. [Admin panel — τι μπορείς να παραμετροποιήσεις](#admin-panel--τι-μπορείς-να-παραμετροποιήσεις)
7. [Security notes](#security-notes)
8. [Project structure](#project-structure)

---

## Local dev — XAMPP

1. Αντέγραψε τον φάκελο σε `htdocs/gas_maxites`.
2. Ξεκίνα Apache + MySQL από το XAMPP control panel.
3. Άνοιξε `http://localhost/gas_maxites/install.php`.
4. Όρισε username + κωδικό διαχειριστή. Διέγραψε το `install.php` όταν τελειώσεις.

Τα XAMPP defaults είναι στο `includes/config.php` (host `127.0.0.1`, user `root`, password `root`). Αν η βάση σου έχει άλλον κωδικό, εξήγαγέ τον ως `DB_PASS` πριν ξεκινήσει ο Apache ή άλλαξε την τιμή.

---

## Local dev — Docker Compose

```bash
cp .env.example .env         # τσέκαρε DB_PASS / IP_HASH_SALT
docker compose up --build    # πρώτη φορά: χτίζει το image
```

Μετά επισκέψου `http://localhost:8080` και `http://localhost:8080/install.php` για να στηθεί η βάση.

Τα δεδομένα διατηρούνται σε named volumes (`uploads` για user content, `dbdata` για MariaDB).

---

## Deploy on Coolify

Το repo έχει και **`Dockerfile`** και **`docker-compose.yml`** — το Coolify μπορεί να καταναλώσει οποιοδήποτε.

### Option A: Compose (one-shot)

1. Στο Coolify, δημιούργησε νέα application → **Resource: Docker Compose**.
2. Δείξ' του το repo (`https://github.com/PanagiotisKotsorgios/gas-maxites-website`), branch `main`.
3. Όρισε τις environment variables (βλ. παρακάτω).
4. Deploy — το Coolify χτίζει το image και τρέχει web + db.

### Option B: Dockerfile + external DB (recommended for production)

1. Στο Coolify φτιάξε **MariaDB** (ή MySQL) resource. Κράτα το internal hostname / credentials.
2. Νέα application → **Resource: Dockerfile**. Δείξε στο repo.
3. Στο Environment tab όρισε:
   - `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` — από το DB resource
   - `SITE_URL` — το δημόσιο HTTPS URL
   - `IP_HASH_SALT` — 64 random hex chars (`openssl rand -hex 32`)
4. Storage tab: πρόσθεσε persistent volume στο `/var/www/html/uploads` για να επιζούν οι εικόνες.
5. Deploy.

### Health check

Ο Dockerfile έχει ενσωματωμένο HTTP health check που ελέγχει το `/`. Το Coolify το κατανοεί.

---

## Environment variables

| Variable          | Required? | Default                        | Purpose                                                     |
| ----------------- | --------- | ------------------------------ | ----------------------------------------------------------- |
| `DB_HOST`         | prod      | `127.0.0.1`                    | MySQL host                                                  |
| `DB_NAME`         | prod      | `gas_maxites`                  | Database name                                               |
| `DB_USER`         | prod      | `root`                         | Database user                                               |
| `DB_PASS`         | prod      | `root`                         | Database password                                           |
| `SITE_NAME`       | no        | `Γ.Α.Σ. Μαχητές Μεσολογγίου`  | Displayed name (footer, emails)                             |
| `SITE_NAME_SHORT` | no        | `ΜΑΧΗΤΕΣ`                     | Short brand name (nav bar)                                  |
| `SITE_URL`        | prod      | `http://localhost/gas_maxites` | Absolute URL για links & OG tags                            |
| `IP_HASH_SALT`    | prod      | (dev placeholder)              | Salt για hashing IPs στα analytics — **άλλαξέ το**          |

---

## Post-deploy: first-time install

Φρέσκα containers έρχονται με άδεια βάση. Στην πρώτη επίσκεψη:

1. Άνοιξε `https://your-domain/install.php`.
2. Όρισε το αρχικό admin username + κωδικό (≥ 8 χαρακτήρες).
3. Ο installer φτιάχνει όλους τους πίνακες, ρυθμίσεις, 6 τμήματα, και το εβδομαδιαίο πρόγραμμα.
4. **Διέγραψε το `install.php`** από το container:
   ```bash
   coolify exec <app> rm -f /var/www/html/install.php
   ```

Αν αλλάξει το schema αργότερα, μπορείς να επισκεφθείς ξανά το `install.php` και να πατήσεις **«Ενημέρωση σχήματος»** — είναι idempotent (`CREATE IF NOT EXISTS` + ALTER migrations) και δεν πειράζει υπάρχοντα δεδομένα.

---

## Admin panel — τι μπορείς να παραμετροποιήσεις

Από `/admin/settings.php` **κάθε** κείμενο, εικονίδιο και εικόνα σε όλες τις σελίδες μπορεί να αλλάξει χωρίς να αγγίξεις κώδικα:

- **Home**: hero (title, subtitle, eyebrow, bg, 2 CTA buttons, rating), 4 feature cards (icon/title/body), split section (title/body/bg + 3 stats), programs headings, athletes headings, trophies headings + bg, gallery headings, reviews headings + bg, news headings, CTA band (bg, title, body, κουμπιά).
- **About**: hero bg + eyebrow/title/lead + 3 link cards (icon/title/body).
- **History**: hero bg + hero content + εισαγωγικό HTML + 5 timeline items (year/title/body).
- **Master**: hero bg + όνομα/τίτλος + βιογραφικό HTML + 4 fact-list items (icon/title/body) + 2 CTAs.
- **Schedule / Contact / Athletes / Trophies / Gallery / Blog**: hero bg + eyebrow/title/lead.
- **Programs (τμήματα)**: κάθε τμήμα δέχεται **εικόνα (JPG/PNG/WEBP)** αντί για FontAwesome icon. Το admin panel υποστηρίζει και τα δύο — αν οριστεί εικόνα, εμφανίζεται αντί για εικονίδιο.
- **Contact info / Social / Footer**: διεύθυνση, τηλέφωνα, WhatsApp/Viber, όλα τα social URLs, footer tagline.

Άφησε ένα πεδίο κενό για να χρησιμοποιηθεί η προεπιλογή (θα δεις το placeholder ως υπόδειξη).

---

## Security notes

- Οι κωδικοί hash-άρονται με **bcrypt** (`password_hash(PASSWORD_DEFAULT)`).
- Κάθε admin POST προστατεύεται από **CSRF token**.
- Uploads: MIME + extension whitelist, 5 MB όριο, `.htaccess` στο `uploads/` μπλοκάρει PHP execution.
- Οι φάκελοι `includes/` και `sql/` προστατεύονται από `.htaccess` (`Require all denied`).
- IPs analytics αποθηκεύονται ως **SHA-256 hash + salt**, ποτέ raw — GDPR-friendly.
- Η φόρμα επικοινωνίας γράφει στη βάση — **δεν στέλνεται email**, οπότε δεν χρειάζεται SMTP/SPF/DKIM.

---

## Project structure

```
/                       ← Public entry points
├── index.php           ← Home
├── about.php · history.php · master.php
├── athletes.php · athlete.php · trophies.php
├── schedule.php
├── gallery.php
├── blog.php · post.php
├── contact.php
├── terms.php · privacy.php · cookies.php
├── install.php         ← One-shot installer (delete after use)
├── Dockerfile
├── docker-compose.yml
│
├── includes/           ← Shared PHP: config, db, auth, analytics, header, footer, social, legal shell
├── admin/              ← Admin panel: login, dashboard, posts, athletes, programs, trophies, schedule, gallery, messages, newsletter, settings
├── assets/
│   ├── css/            ← style.css (public) + admin.css
│   ├── js/main.js
│   └── img/            ← Logos
├── uploads/            ← Runtime-generated (persist via volume)
│   ├── gallery/
│   ├── posts/
│   ├── athletes/
│   ├── trophies/
│   └── programs/
└── sql/schema.sql
```

---

## License

Proprietary — © Γ.Α.Σ. Μαχητές Μεσολογγίου. All rights reserved.
