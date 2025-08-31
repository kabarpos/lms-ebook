# SPA Testing Guide - FilamentPHP Dashboard

## 1. Test Navigasi Dashboard Admin

### Resources yang perlu ditest:
- **CategoryResource** - `/admin/categories`
- **CourseMentorResource** - `/admin/course-mentors`
- **CourseResource** - `/admin/courses`
- **MidtransSettingResource** - `/admin/midtrans-settings`
- **RoleResource** - `/admin/roles`
- **SectionContentResource** - `/admin/section-contents`
- **TransactionResource** - `/admin/transactions`
- **UserResource** - `/admin/users`
- **WhatsappMessageTemplateResource** - `/admin/whatsapp-message-templates`
- **WhatsappSettingResource** - `/admin/whatsapp-settings`

### Custom Pages:
- **Statistik** - `/admin/statistics` (Home page)
- **Data** - `/admin/data`

### Testing Steps:
1. Login ke dashboard admin
2. Klik setiap menu di sidebar
3. Test navigasi antar halaman (List → Create → Edit → View)
4. Perhatikan apakah halaman load tanpa full refresh
5. Check URL bar - pastikan URL berubah tapi tidak ada full page reload

### Expected Behavior:
- ✅ Navigasi smooth tanpa white flash
- ✅ Loading indicator muncul saat navigasi
- ✅ URL berubah sesuai halaman
- ✅ Browser back/forward button berfungsi
- ✅ Tidak ada full page reload

## 2. Console Browser Error Check

### Steps:
1. Buka Developer Tools (F12)
2. Go to Console tab
3. Clear console log
4. Lakukan navigasi di dashboard
5. Monitor error yang muncul

### Common Errors to Watch:
- ❌ JavaScript errors
- ❌ Livewire errors
- ❌ 404 errors untuk assets
- ❌ CORS errors
- ❌ Network timeouts

### Expected Result:
- ✅ Minimal atau tidak ada error
- ✅ Hanya warning yang tidak critical

## 3. Test File Downloads & External Links

### URLs yang dikecualikan dari SPA:
```php
'https://docs.filamentphp.com/*',
'https://github.com/*',
'https://laravel.com/*',
'/admin/export/*',
'/admin/download/*',
'/admin/pdf/*',
'/admin/external-link/*',
```

### Testing Steps:
1. **Test External Links:**
   - Buat link ke dokumentasi Filament
   - Buat link ke GitHub
   - Pastikan link terbuka dengan full page reload

2. **Test Download Actions:**
   - Cari action download di TransactionResource
   - Test export data jika ada
   - Test generate PDF jika ada

3. **Create Test Links:**
   - Tambahkan test link di salah satu halaman
   - Test behavior dengan dan tanpa spaUrlExceptions

### Expected Behavior:
- ✅ External links membuka dengan full page reload
- ✅ Download files berfungsi normal
- ✅ PDF generation tidak terpengaruh SPA

## 4. Performance Monitoring

### Tools untuk Testing:
1. **Browser DevTools:**
   - Network tab untuk monitor request
   - Performance tab untuk measure loading time
   - Lighthouse untuk overall performance score

2. **Metrics to Monitor:**
   - First Contentful Paint (FCP)
   - Largest Contentful Paint (LCP)
   - Time to Interactive (TTI)
   - Total Blocking Time (TBT)

### Testing Scenarios:
1. **Cold Load** (first visit):
   - Measure initial page load time
   - Check asset loading

2. **Warm Navigation** (SPA navigation):
   - Measure subsequent page navigation
   - Compare with full page reload

3. **Network Conditions:**
   - Test dengan Fast 3G
   - Test dengan Slow 3G
   - Test dengan offline simulation

### Expected Results:
- ✅ SPA navigation 2-3x lebih cepat dari full reload
- ✅ Reduced server requests untuk assets
- ✅ Better user experience dengan loading indicators
- ✅ Improved perceived performance

## 5. Testing Checklist

### Pre-Testing:
- [ ] Clear browser cache
- [ ] Disable browser extensions
- [ ] Open DevTools
- [ ] Prepare test scenarios

### During Testing:
- [ ] Record navigation times
- [ ] Screenshot any errors
- [ ] Note user experience issues
- [ ] Test on different browsers

### Post-Testing:
- [ ] Document findings
- [ ] Identify issues
- [ ] Recommend improvements
- [ ] Update spaUrlExceptions if needed

## 6. Common Issues & Solutions

### Issue: JavaScript errors during navigation
**Solution:** Add problematic URLs to spaUrlExceptions

### Issue: File downloads not working
**Solution:** Add download URLs to spaUrlExceptions

### Issue: External links not opening properly
**Solution:** Ensure external domains in spaUrlExceptions

### Issue: Slow SPA navigation
**Solution:** Check for heavy JavaScript, optimize Livewire components

### Issue: Browser back button not working
**Solution:** Verify Livewire wire:navigate implementation

## 7. Performance Benchmarks

### Before SPA (Expected):
- Page load: 800-1500ms
- Full page reload on every navigation
- Multiple asset requests

### After SPA (Target):
- Initial load: 800-1500ms
- Subsequent navigation: 200-500ms
- Cached assets, minimal requests
- Smooth transitions

## 8. Browser Compatibility

Test pada browsers:
- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (if available)
- [ ] Edge (latest)

## 9. Mobile Testing

- [ ] Test pada mobile devices
- [ ] Check touch navigation
- [ ] Verify responsive behavior
- [ ] Test mobile network conditions