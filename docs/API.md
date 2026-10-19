# Growya API v1

Base URL: `https://your-domain/api/v1`. Set `Accept: application/json`. For protected requests add `Authorization: Bearer <token>`. Upload endpoints accept multipart/form-data using the same field names as web forms. Validation errors use HTTP 422; unauthorized/forbidden/not-found use 401/403/404.

## Native account endpoints

| Method | Path | Purpose |
|---|---|---|
| POST | /auth/register | Register personal/business intent; returns token and next onboarding URL |
| POST | /auth/login | Email/password + device_name; two_factor_code required for confirmed 2FA users |
| GET | /auth/me | Current safe account fields |
| POST | /auth/logout | Revoke current token only |
| PATCH | /auth/profile | Update name/email/mobile; clears verification when contacts change |
| PUT | /auth/password | Current password + confirmed new password; revokes all API tokens |
| GET/POST | /users | Super-admin user listing/create |
| PATCH/DELETE | /users/{user} | Super-admin account update/deactivate; protects super admins |
| GET | /locations/{countries\|states\|cities} | Paginated active locations; country_id/state_id/search filters |
| GET | /businesses/{business}/my-permissions | Effective permission names in that business |

Token expiry is 30 days and the required application ability is growya. Permissions are checked live against business_users/business_user_permissions; a token does not freeze an earlier permission grant. Login/registration are rate-limited. Confirmed 2FA accounts require a valid authenticator code before issuing a token.

Registration body:

```json
{"name":"Ravi","email":"ravi@example.com","phone":"9876543210","password":"YourStrongPassword123!","password_confirmation":"YourStrongPassword123!","account_type":"business","device_name":"Flutter"}
```

Use account_type personal or business. This does not assign a global privileged role; business verification still applies. Mobile verification by OTP is not implemented.

Login body:

```json
{"email":"ravi@example.com","password":"YourStrongPassword123!","device_name":"Flutter"}
```

## Existing domain modules

`api-endpoints.json` inventories 326 explicitly mapped controller actions. Routes retain the existing path beneath /api/v1: businesses, catalogs, products, services, media, staff, orders, enquiries, CRM, payments, returns, delivery, plans, subscriptions, billing, chat, KYC, support, promotions, referrals, public showroom and marketplace.

For example:

- GET /businesses
- GET /businesses/{business}/products
- POST /businesses/{business}/products
- PUT /businesses/{business}/products/{product}
- GET /businesses/{business}/staff
- POST /businesses/{business}/staff
- PUT /businesses/{business}/staff/{member}
- DELETE /businesses/{business}/staff/{member}
- GET/POST /businesses/{business}/catalog/categories
- GET/POST /businesses/{business}/catalog/attributes
- POST /businesses/{business}/catalog-attributes/{attribute}/values

The API request shapes come from each controller's existing validate rules. GET create/edit endpoints return option data for clients. Lists retain pagination metadata inside data.<collection>. A list of orders is data.orders.data and its pagination is data.orders.total/current_page/etc. Mutation redirects are converted to JSON message/location; the create store action returns 201. No web redirect must be followed by a mobile client. CSV/PDF/file endpoints retain their actual download content types.

Staff creation example (permission IDs come from GET staff response):

```json
{"name":"Sales Manager","email":"manager@example.com","phone":"9876543211","password":"StrongStaffPassword123!","password_confirmation":"StrongStaffPassword123!","role":"manager","permissions":[12,13]}
```

For an existing registered account, send email, role and permissions; do not submit new account credentials. Returned permissions are the assignable business permissions, not a list of global admin powers.

Category example:

```json
{"name":"Gold Rings","category_type":"product","parent_id":null,"is_active":true,"sort_order":0}
```

Attribute example:

```json
{"name":"Purity"}
```

Attribute value example:

```json
{"value":"22KT","sort_order":0}
```

## Compatibility and pending integrations

This release exposes existing domain logic through a JSON action adapter, not a second generic raw-table CRUD API. It retains tenant/participant checks, plan quotas and transaction rules in the controllers/services. The adapter resolves model bindings before invoking the original action and sanitizes payloads recursively; passwords, social tokens and 2FA secrets are excluded. On file-import errors, import_errors contains the invalid rows. Session-bound web CSV error-download links are not usable in a new stateless API request.

A Postman collection is included. Set base_url to https://your-domain/api/v1, log in and set token, then set IDs from your own data. Requests for schema-only AI/social/FCM modules are deliberately not advertised as working integrations. Starter-kit personal Team/appearance/2FA-setup Livewire screens remain web account UI; this update adds account/profile/password endpoints and business staff APIs, not a JSON replacement for those internal UI actions.

The legacy GET /api/user endpoint remains available for active authenticated users. All new clients should use /api/v1/auth/me.
