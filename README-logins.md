# CareAssure — Login Reference

All seeded accounts use the password: **`password`**

Logins follow the convention:

```
{role}{n}@{council-slug}.example.com
```

e.g. `admin1@east-frederique.example.com`, `carer2@bartolettitown.example.com`.

To (re)build all demo data:

```bash
php artisan migrate:fresh --seed                 # core: Demo Council (Margaret, Tom, Sarah)
php artisan db:seed --class=DemoDataSeeder       # the five councils below
```

The council names, slugs, and all logins are fixed and stable across reseeds.
(Person names and detail fields inside each council are randomised per reseed
unless faker is seeded — see DemoDataSeeder.)

---

## Panels

| Panel    | URL        | Who gets in                                 |
|----------|------------|---------------------------------------------|
| Staff    | `/admin`   | super_admin, tenant_admin, care_coordinator |
| Portal   | `/portal`  | service_user, delegate, carer               |
| Council  | `/council` | council_reviewer (read-only)                |

A user is rejected (403) from a panel their role isn't allowed into.

---

## Platform

| Login                  | Role        | Panel    |
|------------------------|-------------|----------|
| `super@example.com`    | super_admin | `/admin` |

(The original core seed also creates `super@trueedge.life`.)

---

## Councils

Slugs are fixed. Replace `{slug}` in the convention with the slug below.
Counts vary deliberately — some councils are large, some sparse, some have
intentionally incomplete records to surface null-handling bugs.

| Council                  | Slug              | Size / character        | Active |
|--------------------------|-------------------|-------------------------|--------|
| Demo Council             | `demo-council`    | Hand-built reference    | Yes    |
| East Frederique Council  | `east-frederique` | Large (20 service users)| Yes    |
| Bartolettitown Council   | `bartolettitown`  | Healthy / complete      | Yes    |
| West Sarai Council       | `west-sarai`      | Messy (sparse records)  | Yes    |
| Rauberg Council          | `rauberg`         | Small                   | Yes    |
| Lake Jalen Council       | `lake-jalen`      | Nearly empty            | **No** |

---

### Demo Council — `demo-council`

The original hand-seeded council from the core build. Contains Margaret Hughes
(service user), Tom Davies (carer), Sarah (daughter/delegate), a fully costed
care package, dual funding, a DP account with transactions, a review and an
incident. Logins from the original build:

| Login                            | Role            | Panel      |
|----------------------------------|-----------------|------------|
| `admin@demo-council.local`       | tenant_admin    | `/admin`   |
| `reviewer@demo-council.local`    | council_reviewer| `/council` |
| `carer@demo-council.local`       | carer           | `/portal`  |

---

### East Frederique Council — `east-frederique` (large)

2 admins, 1 coordinator, 4 carers, 1 reviewer, 20 service users.

| Login                                      | Role             | Panel      |
|--------------------------------------------|------------------|------------|
| `admin1@east-frederique.example.com`       | tenant_admin     | `/admin`   |
| `admin2@east-frederique.example.com`       | tenant_admin     | `/admin`   |
| `coordinator1@east-frederique.example.com` | care_coordinator | `/admin`   |
| `carer1@east-frederique.example.com`       | carer            | `/portal`  |
| `carer2@east-frederique.example.com`       | carer            | `/portal`  |
| `carer3@east-frederique.example.com`       | carer            | `/portal`  |
| `carer4@east-frederique.example.com`       | carer            | `/portal`  |
| `reviewer1@east-frederique.example.com`    | council_reviewer | `/council` |
| `serviceuser1@east-frederique.example.com` | service_user     | `/portal`  |
| `serviceuser2@east-frederique.example.com` | service_user     | `/portal`  |
| `delegate1@east-frederique.example.com`    | delegate         | `/portal`  |

---

### Bartolettitown Council — `bartolettitown` (healthy)

2 admins, 1 coordinator, 3 carers, 1 reviewer, 6 service users.

| Login                                     | Role             | Panel      |
|-------------------------------------------|------------------|------------|
| `admin1@bartolettitown.example.com`       | tenant_admin     | `/admin`   |
| `admin2@bartolettitown.example.com`       | tenant_admin     | `/admin`   |
| `coordinator1@bartolettitown.example.com` | care_coordinator | `/admin`   |
| `carer1@bartolettitown.example.com`       | carer            | `/portal`  |
| `carer2@bartolettitown.example.com`       | carer            | `/portal`  |
| `carer3@bartolettitown.example.com`       | carer            | `/portal`  |
| `reviewer1@bartolettitown.example.com`    | council_reviewer | `/council` |
| `serviceuser1@bartolettitown.example.com` | service_user     | `/portal`  |
| `serviceuser2@bartolettitown.example.com` | service_user     | `/portal`  |
| `delegate1@bartolettitown.example.com`    | delegate         | `/portal`  |

---

### West Sarai Council — `west-sarai` (messy — sparse/partial records)

1 admin, 1 coordinator, 2 carers, 1 reviewer, 8 service users.
Profiles here deliberately omit DOB/address; one carer has no rates, one is
inactive; service user 1 is assessment-only (no care package).

| Login                                  | Role             | Panel      |
|----------------------------------------|------------------|------------|
| `admin1@west-sarai.example.com`        | tenant_admin     | `/admin`   |
| `coordinator1@west-sarai.example.com`  | care_coordinator | `/admin`   |
| `carer1@west-sarai.example.com`        | carer (inactive) | `/portal`  |
| `carer2@west-sarai.example.com`        | carer (no rates) | `/portal`  |
| `reviewer1@west-sarai.example.com`     | council_reviewer | `/council` |
| `serviceuser1@west-sarai.example.com`  | service_user     | `/portal`  |
| `serviceuser2@west-sarai.example.com`  | service_user     | `/portal`  |
| `delegate1@west-sarai.example.com`     | delegate         | `/portal`  |

---

### Rauberg Council — `rauberg` (small)

1 admin, 1 carer, 3 service users. No coordinator, no reviewer.

| Login                              | Role         | Panel     |
|------------------------------------|--------------|-----------|
| `admin1@rauberg.example.com`       | tenant_admin | `/admin`  |
| `carer1@rauberg.example.com`       | carer        | `/portal` |
| `serviceuser1@rauberg.example.com` | service_user | `/portal` |
| `serviceuser2@rauberg.example.com` | service_user | `/portal` |
| `delegate1@rauberg.example.com`    | delegate     | `/portal` |

---

### Lake Jalen Council — `lake-jalen` (nearly empty, INACTIVE)

1 admin, 1 service user. Tenant is marked inactive (`is_active = false`) — use
to test empty states and inactive-tenant behaviour.

| Login                                 | Role         | Panel    |
|---------------------------------------|--------------|----------|
| `admin1@lake-jalen.example.com`       | tenant_admin | `/admin` |
| `serviceuser1@lake-jalen.example.com` | service_user | `/portal`|

---

## Notes

- **Counts above are the maximum the blueprint creates.** Some service-user
  logins only exist for the first 1–2 service users per council; the rest are
  records without a login. Delegates are only created for service user 1.
- **Inactive accounts/tenants** may be blocked at login by design — that's
  expected behaviour to verify, not a bug.
- If a login fails with "incorrect credentials", confirm the user exists:
  `php artisan tinker --execute="App\Models\User::pluck('email');"`
- A 403 means the login worked but that role can't access that panel — also
  expected.
