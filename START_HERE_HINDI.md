# Growya update — पहले यह पढ़ें

इस package में मौजूदा project का updated source, compiled assets, permissions, API routes, tests और module audit है।

## क्या बदला है

- Active super admin सभी businesses देख/संभाल सकता है। Owner सिर्फ अपने business का पूरा access रखता है।
- Owner नया staff account बना सकता है, registered account जोड़ सकता है, permissions बदल सकता है और staff को अपने business से हटा सकता है।
- Manager/staff को सिर्फ उसी business की दी गई permissions मिलती हैं। Manager अपने पास मौजूद permissions से ज्यादा आगे नहीं दे सकता। Owner और अपना access बदलना protected है।
- Orders, payments, returns, delivery, CRM, finance, offers, gallery और दूसरे modules की अलग permissions हैं। Products की permission अपने-आप orders/payments access नहीं देती।
- हर business की अपनी product/service categories और attributes/values हैं। Platform defaults उपलब्ध रहते हैं। दूसरे business की category/value assign नहीं की जा सकती।
- Registration पर Personal / My business विकल्प है। Business चुनने पर application form खुलता है; admin approval पहले जैसा जरूरी है।
- Business controllers के मौजूदा actions की versioned JSON APIs, अलग login/logout/profile endpoints और Postman collection जोड़ी गई है।
- Staff permissions का change history save होता है। Subscription staff/product limits पहले की तरह लागू हैं।

## Existing website पर लगाने के steps

पहले database और media का backup लें और staging copy पर चलाएँ। अपना `.env`, मौजूदा `APP_KEY`, database और uploaded media बनाए रखें।

```bash
composer install
php artisan optimize:clear
php artisan migrate --force
php artisan db:seed --class=BusinessPermissionSeeder --force
```

इस update के लिए `migrate:fresh` न चलाएँ। नई migration categories/attributes में nullable business_id जोड़ती है; पुरानी entries global masters बनी रहती हैं।

Compiled assets package में हैं। दोबारा build करना हो:

```bash
npm ci
npm run build
```

Business खोलकर **Team & permissions** से access दें। Screen खोलने के लिए view और बदलाव के लिए create/update/manage permissions चुनें। Free fallback में एक active non-owner staff की मौजूदा limit है; ज्यादा staff के लिए plan/override चाहिए।

## APIs

Base URL: `https://your-domain/api/v1`

- `POST /auth/register`
- `POST /auth/login`
- `GET /auth/me`
- `POST /auth/logout`
- `GET /businesses/{business}/my-permissions`

Protected requests में `Authorization: Bearer TOKEN` लगाएँ। बाकी routes, fields और examples के लिए `docs/API.md` तथा `docs/Growya_API.postman_collection.json` देखें। Domain actions मौजूदा controllers की validation/transactions इस्तेमाल करते हैं। Mutation response में message/location आता है; resource के लिए list/show API इस्तेमाल करें।

Starter-kit की personal Teams/settings Livewire screens अलग account infrastructure हैं; business staff access इनसे नहीं मिलता। उनके internal invitation/member UI actions का अलग JSON rewrite इस package में नहीं किया गया है।

## कितना काम बाकी है

Audit में 40 grouped workstreams हैं: **28 में implementation मौजूद, 3 partial, 9 schema-only**। Table बन जाने को पूरा module नहीं माना है।

मुख्य बाकी काम: mobile OTP, FCM push/device registration, AI generation/credits, social OAuth/publishing/scheduling, festival calendar, daily analytics aggregation, review photos और generic admin modules। Gateway settlement, shipping/GST engine, delivery OTP/POD और advanced accounting भी अभी शामिल नहीं हैं।

हर module का evidence और अगले काम का क्रम: `docs/MODULE_STATUS.md`। कौन से checks चले और उनकी सीमाएँ: `docs/VALIDATION.md`।
