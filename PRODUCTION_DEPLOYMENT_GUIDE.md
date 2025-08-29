## 🚀 Production Deployment Guide for Midtrans Integration

### 1. Environment Configuration

**Update your `.env` file for production:**
```env
# Midtrans Production Settings
MIDTRANS_SERVER_KEY=Mid-server-YOUR_PRODUCTION_SERVER_KEY
MIDTRANS_CLIENT_KEY=Mid-client-YOUR_PRODUCTION_CLIENT_KEY
MIDTRANS_IS_PRODUCTION=true
MIDTRANS_SANITIZE=true
MIDTRANS_3DS=true
```

### 2. Database Configuration Update

**Admin Panel: `/admin/midtrans-settings`**
- Create new Midtrans configuration
- Set `is_production` = true
- Use production Server Key and Client Key
- Set as active configuration
- Deactivate sandbox configuration

### 3. Production Webhook URL Configuration

**Midtrans Production Dashboard:**
- URL: https://dashboard.midtrans.com (no "sandbox")
- Login with production account
- Settings → Configuration
- Notification URL: `https://yourdomain.com/booking/payment/midtrans/notification`

### 4. SSL Certificate Required

**Important:** Production webhooks require HTTPS!
- Ensure your domain has valid SSL certificate
- Webhook URL must use `https://` not `http://`

### 5. Testing Production Setup

**Before going live:**
1. Test with Midtrans production test cards
2. Verify webhook notifications reach your server
3. Check transactions appear in `/admin/transactions`
4. Test email and WhatsApp notifications

### 6. Monitoring and Logging

**Enable production logging:**
```php
// In your .env
LOG_LEVEL=info
LOG_CHANNEL=stack

// Monitor webhook logs
tail -f storage/logs/laravel.log | grep "Midtrans"
```

### 7. Security Considerations

**Production Security:**
- Verify Midtrans notification signatures
- Use CSRF protection on webhook endpoints
- Implement rate limiting
- Monitor for suspicious activities

### 8. Backup and Recovery

**Before deployment:**
- Backup current database
- Test rollback procedures
- Document configuration changes
- Prepare monitoring alerts

### 9. Go-Live Checklist

**Pre-launch verification:**
- [ ] Production Midtrans keys configured
- [ ] SSL certificate active
- [ ] Webhook URL set in Midtrans dashboard  
- [ ] Test transactions successful
- [ ] Admin dashboard accessible
- [ ] Email notifications working
- [ ] WhatsApp notifications working
- [ ] Payment success pages working
- [ ] Error handling working
- [ ] Logging configured
- [ ] Monitoring alerts set up

### 10. Common Production Issues

**Potential problems and solutions:**

**Webhook not received:**
- Check SSL certificate validity
- Verify URL accessibility from internet
- Check firewall settings
- Verify Midtrans dashboard configuration

**Payment failures:**
- Verify production API keys
- Check merchant account status
- Verify bank/payment method support
- Check transaction limits

**Email/WhatsApp not sending:**
- Verify SMTP settings for production
- Check WhatsApp API credentials
- Verify notification templates
- Check queue processing

### 11. Rollback Plan

**If issues occur:**
1. Switch Midtrans back to sandbox mode
2. Restore previous database backup
3. Revert webhook URL configuration
4. Monitor and fix issues
5. Re-test before re-deploying