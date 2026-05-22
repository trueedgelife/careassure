# CareAssure

A multi-tenant care-management platform for UK direct-payment adult social
care. CareAssure connects councils, families, carers, and the people receiving
care — tracking care packages, workforce compliance, shifts, and the direct-
payment money trail in one auditable system.

> **Status:** in active development. Core domain complete (people, workforce,
> care delivery, costing, safeguarding, finance ledger). Not yet production-
> deployed.

---

## What it does

- **People** — service users, their relationships, capacity assessments, and
  LPA / delegate arrangements.
- **Workforce** — carers, effective-dated pay rates, and compliance tracking
  (DBS, right-to-work, training expiry).
- **Care delivery** — care packages, multiple funding sources (LA, NHS CHC,
  client contribution…), scheduled shifts, and statutory reviews.
- **Costing** — shifts are costed against the carer's pay rate *in effect on
  the day worked*, and that cost is snapshotted so later rate changes never
  rewrite history.
- **Safeguarding** — incident logging with severity, status, and safeguarding-
  referral tracking.
- **Finance** — a per-package direct-payment ledger that is **append-only**:
  money in (council/NHS payments) and out (wages, expenses) is recorded as
  immutable transactions; corrections are made by posting reversals, never by
  editing. Expenses auto-post matching debits.

Everything is **tenant-scoped** (each council's data is isolated) and **fully
audited** (every change is logged with who did it and on whose authority).

---

## Stack

- **PHP 8.4**, **Laravel 13**
- **Filament 5.5.2** (admin / portal / council panels)
- **Livewire 4**, **Tailwind 4 / Vite**
- **MySQL** (app), **SQLite in-memory** (tests)
- spatie/laravel-permission (teams mode), spatie/laravel-activitylog,
  spatie/laravel-medialibrary

> Filament is pinned to **5.5.2**: 5.6.x has a `compileOpeningTags` render
> regression on resource pages under Laravel 13 / PHP 8.4. Unpin and retest
> when a 5.6.x fix ships.

---

## Panels

| Panel    | Path       | For                                           |
|----------|------------|-----------------------------------------------|
| Staff    | `/admin`   | Platform & council staff (admins, coordinators)|
| Portal   | `/portal`  | Service users, delegates, carers              |
| Council  | `/council` | Council reviewers (read-only oversight)       |

Access is role-based; a user is denied any panel their role isn't permitted
to enter.

---

## Local setup

Requires PHP 8.4, Composer, Node, and a MySQL database.

```bash
git clone <repo-url> careassure && cd careassure
composer install
npm install && npm run build

cp .env.example .env
php artisan key:generate
# set DB_* values in .env to point at your local MySQL database

php artisan migrate:fresh --seed                 # core data: Demo Council
php artisan db:seed --class=DemoDataSeeder        # five additional demo councils

php artisan serve   # or use Valet / Herd
```

Then visit `/admin` and log in. See **README-logins.md** for all seeded
accounts (every account uses the password `password`).

---

## Testing

Tests run against in-memory SQLite (configured in `phpunit.xml`).

```bash
php artisan test                       # all tests
php artisan test --filter=ShiftCosting # a single suite
```

The suite focuses on domain logic (costing, tenant isolation, the append-only
ledger guard, balance calculation) rather than UI.

---

## License

CareAssure is **source-available** under the **Functional Source License
(FSL-1.1-Apache-2.0)** — see `LICENSE`.

You may use, modify, fork, and contribute to it freely, including for
commercial purposes. You may **not** sell it or offer it as a competing
product or service. Each released version converts to the Apache License 2.0
two years after its release date.

Copyright 2026 Bristol Internet Ltd (trading as Bristol.digital).

---

## Disclaimer

CareAssure is provided "as is", without warranty of any kind. It is a record-
keeping tool and does not replace professional judgement, statutory duties, or
independent verification of compliance, safeguarding, or financial
information. See `DISCLAIMER.md` for the full statement. Anyone operating an
instance is responsible for its configuration, access control, data accuracy,
and compliance with applicable law (including UK GDPR).
