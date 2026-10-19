# Growya module audit and next work

Audit date: 19 October 2026. Compared the uploaded source against the supplied database blueprint.

This inventory uses **40 grouped workstreams**, rather than counting every table/pivot as a separate module. **28 implemented, 9 schema only, 3 partial**.

“Implemented” means an operational controller/service/UI code path exists and its existing business actions have API routes. It is not a claim that every edge case, live MySQL data set or third-party integration has been certified. “Schema only” means tables/models do not make that feature complete. The test report records exactly what was executed.

| # | Workstream | Status | Finding | Evidence |
|---|---|---|---|---|
| 1 | Accounts, registration and Fortify login | Implemented | Web personal/business choice; Sanctum login/register/me/logout; authenticator check; account APIs. | `app/Actions/Fortify/CreateNewUser.php` |
| 2 | Mobile OTP login | Schema only | otp_codes exists; no send/verify provider or customer OTP flow. | `database/migrations/2026_10_08_063353_create_otp_codes_table.php` |
| 3 | Country, state and city masters | Implemented | Seeders and location models; added paginated location API. | `database/seeders/IndiaLocationSeeder.php` |
| 4 | Business application and profiles | Implemented | Multiple businesses, owner records, pending/approved workflow, profile and locations. | `app/Http/Controllers/BusinessController.php` |
| 5 | Business staff and permissions | Implemented | Business-specific grants, create or attach staff, update/revoke/remove, permission delegation ceiling and audit history. | `app/Http/Controllers/BusinessStaffController.php` |
| 6 | Business hours and public social links | Implemented | Profile links and hours, independently permissioned; these are not OAuth-connected social accounts. | `app/Http/Controllers/BusinessHourController.php` |
| 7 | Business verification and KYC | Implemented | Verification and private document workflow uses growya_kyc_* tables; generic business_documents is not the operational storage. | `app/Http/Controllers/GrowyaBusinessKycController.php` |
| 8 | Categories | Implemented | Global master categories preserved; custom product/service categories scoped by business_id. | `app/Http/Controllers/TenantCatalogController.php` |
| 9 | Products, media, variants, tags, stock and QR | Implemented | Catalog CRUD, galleries, variants, CSV, stock movements, QR generation; QR failure preserves saved product/media. | `app/Http/Controllers/ProductController.php` |
| 10 | Product attributes and values | Implemented | Global defaults plus own attributes; product assignments reject another tenant values. | `app/Http/Controllers/ProductAttributeController.php` |
| 11 | Services and service images | Implemented | Services CRUD and image actions with business scoping. | `app/Http/Controllers/ServiceController.php` |
| 12 | Business gallery | Implemented | Gallery and media workflows with gallery.view/manage. | `app/Http/Controllers/BusinessGalleryController.php` |
| 13 | Marketplace, search, nearby and public showroom | Implemented | Existing discovery/showroom controllers have JSON counterparts. | `app/Http/Controllers/PublicBusinessController.php` |
| 14 | Offers and product/service links | Implemented | Existing offers CRUD and public offers. | `app/Http/Controllers/BusinessOfferController.php` |
| 15 | Business follows | Implemented | Follow/unfollow and own following list. | `app/Http/Controllers/BusinessFollowController.php` |
| 16 | Favourites and wishlist leads | Implemented | Business/product/service wishlist and owner interest workflows. | `app/Http/Controllers/GrowyaWishlistController.php` |
| 17 | Cart and checkout | Implemented | Per-user business carts, quantities, checkout and validation. | `app/Http/Controllers/GrowyaCartCheckoutController.php` |
| 18 | Orders and line snapshots | Implemented | Customer history and seller status workflow; order fields store snapshots. | `app/Services/GrowyaOrderService.php` |
| 19 | Product/service enquiries and follow-ups | Implemented | Existing enquiry, note and follow-up workflows with independent permission groups. | `app/Http/Controllers/ProductEnquiryController.php` |
| 20 | Ratings, review text and photo reviews | Partial | Text/rating, replies and moderation exist; review_images storage exists but no photo-review upload workflow found. | `app/Http/Controllers/BusinessReviewController.php` |
| 21 | Saved customer addresses | Implemented | Existing saved address actions and default selection. | `app/Http/Controllers/GrowyaCustomerAddressController.php` |
| 22 | Appointments | Implemented | Appointment creation, listing and status flow. | `app/Http/Controllers/AppointmentController.php` |
| 23 | Notifications and delivery tracking | Partial | In-app notifications and polling exist; full queued FCM sent/delivered/opened lifecycle needs implementation. | `app/Services/GrowyaNotificationService.php` |
| 24 | FCM device registration and push sender | Schema only | user_devices/model exists; no device API/push provider workflow found. | `app/Models/UserDevice.php` |
| 25 | AI requests and generated media | Schema only | ai_generations/ai_media models and migrations; no provider, worker or generation UI. | `app/Models/AiGeneration.php` |
| 26 | AI wallet and credit ledger | Schema only | Wallet/transaction schema; no transactional debit/refund enforcement service. | `app/Models/AiCreditWallet.php` |
| 27 | Connected social accounts and OAuth | Schema only | Encrypted token casts exist; no OAuth connection/refresh lifecycle. | `app/Models/SocialAccount.php` |
| 28 | Social post creation and publishing | Schema only | Social post/media/platform tables exist; no publishing jobs or external provider integration. | `app/Models/SocialPost.php` |
| 29 | Recurring social schedules and festivals | Schema only | Calendar and recurring scheduling storage; no marketing scheduler workflow. | `app/Models/SocialSchedule.php` |
| 30 | Analytics and daily summaries | Partial | Owner/admin reports query operational tables; daily summary/event aggregation pipeline is still pending. | `app/Http/Controllers/GrowyaAnalyticsController.php` |
| 31 | Event stream, search logs, share links and recently viewed | Schema only | Supporting models/tables exist; no full capture/share-code/browsing-history workflows found. | `app/Models/ActivityEvent.php` |
| 32 | Plans, features and quotas | Implemented | Plan administration, feature entitlements, usage and business overrides. | `app/Services/GrowyaPlanLimitService.php` |
| 33 | Subscriptions and subscription billing documents | Implemented | Requests, admin confirmation, payment records and billing docs; does not settle money through a gateway. | `app/Http/Controllers/GrowyaSubscriptionController.php` |
| 34 | Customer CRM and staff performance | Implemented | Contacts, activity, assignments, kanban, targets, incentives and advanced views use growya_crm_*; generic business_customers/customer_notes are not active storage. | `app/Http/Controllers/GrowyaBusinessCrmController.php` |
| 35 | Generic reports, settings, banners and audit administration | Schema only | Generic tables exist without a complete management workflow. Specialized staff/CRM/KYC/payment audits exist separately. | `app/Models/Setting.php` |
| 36 | Customer/business chat | Implemented | Threads, messages, uploads, assignment, blocking, reports and follow-ups; assigned conversation boundary retained. | `app/Http/Controllers/GrowyaChatController.php` |
| 37 | Support tickets | Implemented | Tickets, replies, internal support workflow and attachments. | `app/Http/Controllers/GrowyaSupportTicketController.php` |
| 38 | Referrals, rewards, coupons and paid promotions | Implemented | Existing manual approval and campaign workflows retained. | `app/Http/Controllers/GrowyaReferralController.php` |
| 39 | Order payment proofs, returns, refunds and delivery tracking | Implemented | Existing manual proofs/review, refund records and shipment events. Gateway settlement and delivery OTP/POD are not implemented. | `app/Http/Controllers/GrowyaPaymentController.php` |
| 40 | Basic finance ledger and reminders | Implemented | Existing income/expense reports, reminders and preferences. Advanced accounting/cash flow remains separate pending work. | `app/Http/Controllers/GrowyaFinanceController.php` |

## Recommended next implementation order

1. Device registration and FCM delivery, with queued notification jobs and delivery result tracking.
2. Activity capture and daily analytics aggregation, with tenant-aware reporting reads.
3. AI wallet debit/refund ledger, then provider-backed text/image generation.
4. OAuth-connected social accounts and token refresh, then queued publish/retry handlers.
5. Recurring publishing and festival calendar, followed by review-photo uploads and generic admin modules.
6. Mobile OTP provider integration and the customer browsing/share-history workflows.

## Previously deferred scope

Business-scoped permissions were explicitly requested in this update and have been implemented for the existing business modules. No Razorpay/payment settlement, shipping/GST engine, delivery OTP/proof of delivery or advanced accounting system has been added. Existing basic finance/delivery/payment-proof code was retained and permissioned. Future business features should call BusinessAccessService and use business_id at every data boundary.

## API boundary

The app's existing domain controller workflows are exposed through a versioned JSON compatibility adapter. Native auth/account/location endpoints are dedicated controllers. Forms become JSON option payloads, and downloads retain CSV/PDF/image types. This preserves existing transactions and validation rather than introducing a second set of business rules. Starter-kit personal Teams/settings remain account UI infrastructure and are distinct from business_users; their Livewire invitation/member-management actions were not rewritten as business APIs. They never grant business access.
