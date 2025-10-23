# 🚀 Villa Upsell - Render Deployment Roadmap
## Complete Step-by-Step Guide for Render Deployment

---

## 🏗️ **PROJECT ARCHITECTURE OVERVIEW**

### **Your Villa Upsell Project Structure:**
```
📁 villa-upsell-backend/     → Laravel API (Web Service)
📁 villa-upsell-guest/       → React Guest Frontend (Static Site)
📁 villa-upsell-admin/       → React Admin Frontend (Static Site)
```

### **Render Services You'll Create:**
1. **Backend Web Service** (`villa-upsell-backend`)
   - Laravel API with PostgreSQL database
   - Handles payments, notifications, webhooks
   - URL: `https://your-backend-service.onrender.com`

2. **Guest Static Site** (`villa-upsell-guest`)
   - React frontend for guests/customers
   - Handles property access, checkout, payments
   - URL: `https://your-guest-service.onrender.com`

3. **Admin Static Site** (`villa-upsell-admin`)
   - React frontend for property managers
   - Handles property management, vendor setup
   - URL: `https://your-admin-service.onrender.com`

4. **PostgreSQL Database**
   - Shared database for all services
   - Stores properties, orders, users, vendors

### **Environment Variables Setup:**
- **Backend**: All API keys, database connection, app URLs
- **Guest Frontend**: API URL, Stripe publishable key
- **Admin Frontend**: API URL, Stripe publishable key

---

## 📋 **PRE-DEPLOYMENT CHECKLIST**

### ✅ **Completed Tasks:**
- [x] Project cleaned (test commands removed)
- [x] SSL bypass configured for local development only
- [x] SendGrid email integration working
- [x] WhatsApp notifications working
- [x] Wise Profile ID obtained: `58946812`

### 🔄 **Required Before Deployment:**
- [ ] Production API keys (Stripe Live, Twilio, SendGrid)
- [ ] Domain name registered
- [ ] Render account
- [ ] GitHub repository with latest code

---

## 🏗️ **PHASE 1: RENDER ACCOUNT SETUP**

### **Step 1.1: Create Render Account**
1. **Sign up for Render**
   - Go to https://render.com
   - Sign up with GitHub integration
   - Connect your GitHub account
   - Verify your email address

2. **Choose Plan**
   - **Free Plan**: Good for testing (limited resources)
   - **Starter Plan**: $7/month (recommended for production)
   - **Professional Plan**: $25/month (for high traffic)

### **Step 1.2: Prepare GitHub Repository**
1. **Ensure your code is pushed to GitHub:**
   ```bash
   git add .
   git commit -m "Production ready - cleaned for deployment"
   git push origin main
   ```

2. **Verify repository structure:**
   ```
   villa-upsell-backend/
   ├── app/
   ├── config/
   ├── database/
   ├── public/
   ├── routes/
   ├── storage/
   ├── composer.json
   ├── composer.lock
   └── .env.example
   ```

---

## 🗄️ **PHASE 2: DATABASE SETUP**

### **Step 2.1: Create PostgreSQL Database**
1. **In Render Dashboard:**
   - Click "New +" button
   - Select "PostgreSQL"
   - **Name**: `villa-upsell-db`
   - **Database**: `villa_upsell_production`
   - **User**: `villa_user`
   - **Region**: Choose closest to your users
   - **Plan**: Free (for testing) or Starter ($7/month for production)

2. **Wait for database creation** (2-3 minutes)

3. **Copy connection details:**
   - **External Database URL**: `postgresql://villa_user:password@host:port/database`
   - **Internal Database URL**: `postgresql://villa_user:password@host:port/database`
   - **Host**: `dpg-xxxxx-a.oregon-postgres.render.com`
   - **Port**: `5432`
   - **Database**: `villa_upsell_production`
   - **Username**: `villa_user`
   - **Password**: `generated_password`

### **Step 2.2: Test Database Connection**
1. **Install PostgreSQL client locally:**
   ```bash
   # Windows (if not installed)
   # Download from https://www.postgresql.org/download/windows/
   
   # Or use Docker
   docker run -it --rm postgres:13 psql "postgresql://villa_user:password@host:port/database"
   ```

2. **Test connection:**
   ```sql
   \dt  -- List tables
   \q   -- Quit
   ```

---

## 🌐 **PHASE 3: BACKEND DEPLOYMENT**

### **Step 3.1: Create Web Service**
1. **In Render Dashboard:**
   - Click "New +" button
   - Select "Web Service"
   - **Connect Repository**: Select your `villa-upsell-backend` repository
   - **Branch**: `main`
   - **Root Directory**: Leave empty (or `villa-upsell-backend` if in subfolder)

### **Step 3.2: Configure Build Settings**
1. **Build Command:**
   ```bash
   composer install --no-dev --optimize-autoloader
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

2. **Start Command:**
   ```bash
   php artisan serve --host=0.0.0.0 --port=$PORT
   ```

3. **Environment**: `PHP`

### **Step 3.3: Configure Environment Variables**
1. **In Render Dashboard, go to your Backend Web Service**
2. **Click "Environment" tab**
3. **Add these environment variables one by one:**

   **Application Variables:**
   ```
   APP_NAME=Villa Upsell
   APP_ENV=production
   APP_KEY=base64:your_generated_app_key
   APP_DEBUG=false
   APP_TIMEZONE=UTC
   APP_URL=https://your-backend-service.onrender.com
   APP_FRONTEND_URL=https://your-guest-frontend-service.onrender.com
   APP_ADMIN_URL=https://your-admin-frontend-service.onrender.com
   ```

   **Database Variables (use values from Phase 2):**
   ```
   DB_CONNECTION=pgsql
   DB_HOST=dpg-xxxxx-a.oregon-postgres.render.com
   DB_PORT=5432
   DB_DATABASE=villa_upsell_production
   DB_USERNAME=villa_user
   DB_PASSWORD=your_generated_password
   ```

   **Mail Configuration:**
   ```
   MAIL_MAILER=sendgrid
   MAIL_FROM_ADDRESS=support@holidayupsell.com
   MAIL_FROM_NAME="Villa Upsell"
   SENDGRID_API_KEY=your_production_sendgrid_key
   ```

   **Stripe Configuration (Production - LIVE KEYS):**
   ```
   STRIPE_KEY=pk_live_your_live_publishable_key
   STRIPE_SECRET_KEY=sk_live_your_live_secret_key
   STRIPE_CONNECT_CLIENT_ID=ca_your_live_connect_client_id
   STRIPE_WEBHOOK_SECRET=whsec_your_live_webhook_secret
   ```

   **Twilio Configuration (Production):**
   ```
   TWILIO_ACCOUNT_SID=your_production_twilio_sid
   TWILIO_AUTH_TOKEN=your_production_twilio_token
   TWILIO_WHATSAPP_FROM=whatsapp:+your_production_whatsapp_number
   ```

   **Wise Configuration:**
   ```
   WISE_API_KEY=your_production_wise_api_key
   WISE_TOKEN=your_production_wise_token
   WISE_PROFILE_ID=58946812
   ```

   **Session & Cache:**
   ```
   SESSION_DRIVER=database
   SESSION_LIFETIME=120
   CACHE_STORE=file
   QUEUE_CONNECTION=database
   ```

   **Logging:**
   ```
   LOG_CHANNEL=stack
   LOG_LEVEL=error
   ```

4. **Click "Save Changes" after adding all variables**

### **Step 3.4: Advanced Settings**
1. **Auto-Deploy**: Enable (deploys on git push)
2. **Pull Request Previews**: Enable (optional)
3. **Health Check Path**: `/api/health` (we'll create this)
4. **Instance Type**: 
   - **Free**: 512MB RAM (for testing)
   - **Starter**: 512MB RAM ($7/month)
   - **Standard**: 1GB RAM ($25/month)

### **Step 3.5: Create Health Check Endpoint**
1. **Create health check route:**
   ```bash
   # Add to routes/api.php
   Route::get('/health', function () {
       return response()->json([
           'status' => 'ok',
           'timestamp' => now(),
           'database' => DB::connection()->getPdo() ? 'connected' : 'disconnected'
       ]);
   });
   ```

2. **Commit and push:**
   ```bash
   git add routes/api.php
   git commit -m "Add health check endpoint"
   git push origin main
   ```

---

## 🎨 **PHASE 4: FRONTEND DEPLOYMENT**

### **Step 4.1: Deploy Guest Frontend (villa-upsell-guest)**
1. **In Render Dashboard:**
   - Click "New +" button
   - Select "Static Site"
   - **Connect Repository**: Select your `villa-upsell-guest` repository
   - **Branch**: `main`
   - **Root Directory**: Leave empty (or `villa-upsell-guest` if in subfolder)

2. **Configure Build Settings:**
   - **Build Command**: `npm install && npm run build`
   - **Publish Directory**: `dist`
   - **Environment**: `Node`

3. **Configure Environment Variables:**
   ```
   VITE_API_URL=https://your-backend-service.onrender.com
   VITE_STRIPE_PUBLISHABLE_KEY=pk_live_your_live_publishable_key
   ```

4. **Name your service**: `villa-upsell-guest`

### **Step 4.2: Deploy Admin Frontend (villa-upsell-admin)**
1. **In Render Dashboard:**
   - Click "New +" button
   - Select "Static Site"
   - **Connect Repository**: Select your `villa-upsell-admin` repository
   - **Branch**: `main`
   - **Root Directory**: Leave empty (or `villa-upsell-admin` if in subfolder)

2. **Configure Build Settings:**
   - **Build Command**: `npm install && npm run build`
   - **Publish Directory**: `dist`
   - **Environment**: `Node`

3. **Configure Environment Variables:**
   ```
   VITE_API_URL=https://your-backend-service.onrender.com
   VITE_STRIPE_PUBLISHABLE_KEY=pk_live_your_live_publishable_key
   ```

4. **Name your service**: `villa-upsell-admin`

### **Step 4.3: Custom Domain Setup**
1. **For Guest Frontend:**
   - Go to your guest static site
   - Click "Settings" tab
   - Click "Custom Domains"
   - Add domain: `your-domain.com` (main website)

2. **For Admin Frontend:**
   - Go to your admin static site
   - Click "Settings" tab
   - Click "Custom Domains"
   - Add domain: `admin.your-domain.com` (admin panel)

3. **Follow DNS instructions for both domains**

---

## 🔗 **PHASE 5: WEBHOOK CONFIGURATION**

### **Step 5.1: Stripe Webhooks**
1. **In Stripe Dashboard:**
   - Go to Webhooks section
   - Click "Add endpoint"
   - **URL**: `https://your-backend-service.onrender.com/api/webhooks/stripe`
   - **Events**: Select these events:
     - `payment_intent.succeeded`
     - `charge.succeeded`
     - `payment_intent.payment_failed`
     - `charge.failed`
   - Copy the **Signing Secret** to Render environment variables

2. **Test webhook:**
   ```bash
   # Install Stripe CLI locally
   stripe listen --forward-to https://your-backend-service.onrender.com/api/webhooks/stripe
   ```

### **Step 5.2: Twilio Webhooks**
1. **In Twilio Console:**
   - Go to WhatsApp Sandbox settings
   - **Webhook URL**: `https://your-backend-service.onrender.com/api/webhooks/twilio/message`
   - **Status Callback URL**: `https://your-backend-service.onrender.com/api/webhooks/twilio/status`

2. **Test webhook:**
   - Send WhatsApp message to sandbox number
   - Check Render logs for incoming webhook

### **Step 5.3: Wise Webhooks** (if needed)
1. **In Wise Dashboard:**
   - Configure webhook endpoints
   - **URL**: `https://your-backend-service.onrender.com/api/webhooks/wise`

---

## 🔒 **PHASE 6: SSL & SECURITY**

### **Step 6.1: SSL Certificates**
1. **Render automatically provides SSL certificates**
2. **Verify SSL:**
   ```bash
   curl -I https://your-backend-service.onrender.com/api/health
   curl -I https://your-frontend-service.onrender.com
   ```

### **Step 6.2: Security Headers**
1. **Add to your Laravel app** (in `app/Http/Middleware/SecurityHeaders.php`):

   ```php
   <?php

   namespace App\Http\Middleware;

   use Closure;
   use Illuminate\Http\Request;

   class SecurityHeaders
   {
       public function handle(Request $request, Closure $next)
       {
           $response = $next($request);

           $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
           $response->headers->set('X-XSS-Protection', '1; mode=block');
           $response->headers->set('X-Content-Type-Options', 'nosniff');
           $response->headers->set('Referrer-Policy', 'no-referrer-when-downgrade');

           return $response;
       }
   }
   ```

2. **Register middleware in `app/Http/Kernel.php`:**
   ```php
   protected $middleware = [
       // ... other middleware
       \App\Http\Middleware\SecurityHeaders::class,
   ];
   ```

---

## 📊 **PHASE 7: MONITORING & LOGS**

### **Step 7.1: Render Logs**
1. **Access logs in Render Dashboard:**
   - Go to your web service
   - Click "Logs" tab
   - Monitor real-time logs

2. **Set up log alerts:**
   - Go to "Alerts" tab
   - Configure alerts for errors

### **Step 7.2: Database Monitoring**
1. **In Render Dashboard:**
   - Go to your PostgreSQL database
   - Monitor connection count
   - Check storage usage

### **Step 7.3: Performance Monitoring**
1. **Monitor metrics:**
   - Response times
   - Memory usage
   - CPU usage
   - Request count

---

## 🧪 **PHASE 8: TESTING & VALIDATION**

### **Step 8.1: Backend API Testing**
1. **Test health endpoint:**
   ```bash
   curl -X GET https://your-backend-service.onrender.com/api/health
   ```

2. **Test authentication:**
   ```bash
   curl -X POST https://your-backend-service.onrender.com/api/register \
     -H "Content-Type: application/json" \
     -d '{"name":"Test User","email":"test@example.com","password":"password123"}'
   ```

3. **Test property access:**
   ```bash
   curl -X GET https://your-backend-service.onrender.com/api/properties/access/test-token
   ```

### **Step 8.2: Database Testing**
1. **Run migrations:**
   ```bash
   # In Render Dashboard, go to your web service
   # Click "Shell" tab and run:
   php artisan migrate --force
   ```

2. **Test database connection:**
   ```bash
   php artisan tinker
   # Then: DB::connection()->getPdo();
   ```

### **Step 8.3: Webhook Testing**
1. **Test Stripe webhook:**
   ```bash
   stripe trigger payment_intent.succeeded
   ```

2. **Test Twilio webhook:**
   - Send WhatsApp message to sandbox number
   - Check Render logs

### **Step 8.4: End-to-End Testing**
1. **Complete payment flow:**
   - Access frontend
   - Add upsells to cart
   - Complete payment
   - Verify notifications

---

## 🚀 **PHASE 9: GO LIVE**

### **Step 9.1: Final Pre-Launch Checklist**
- [ ] All environment variables set
- [ ] Database migrations completed
- [ ] Webhooks configured and tested
- [ ] SSL certificates active
- [ ] Health check endpoint working
- [ ] Frontend and backend communicating
- [ ] Payment flow tested
- [ ] Notifications working

### **Step 9.2: Switch to Production Keys**
1. **Update Stripe keys to live:**
   - Replace test keys with live keys in Render environment variables
   - Update frontend Stripe publishable key

2. **Update Twilio to production:**
   - Replace sandbox credentials with production credentials
   - Update WhatsApp number

3. **Update SendGrid to production:**
   - Replace test API key with production key

### **Step 9.3: Custom Domain Setup**
1. **Backend domain:**
   - Add custom domain: `api.your-domain.com`
   - Update DNS records as instructed by Render

2. **Guest Frontend domain:**
   - Add custom domain: `your-domain.com`
   - Update DNS records

3. **Admin Frontend domain:**
   - Add custom domain: `admin.your-domain.com`
   - Update DNS records

4. **Update environment variables in Backend:**
   ```env
   APP_URL=https://api.your-domain.com
   APP_FRONTEND_URL=https://your-domain.com
   APP_ADMIN_URL=https://admin.your-domain.com
   ```

5. **Update environment variables in Guest Frontend:**
   ```env
   VITE_API_URL=https://api.your-domain.com
   ```

6. **Update environment variables in Admin Frontend:**
   ```env
   VITE_API_URL=https://api.your-domain.com
   ```

### **Step 9.4: Final Deployment**
1. **Trigger deployment:**
   ```bash
   git add .
   git commit -m "Production deployment - custom domains"
   git push origin main
   ```

2. **Monitor deployment:**
   - Watch logs in Render Dashboard
   - Verify all services are running

---

## 📞 **PHASE 10: POST-DEPLOYMENT**

### **Step 10.1: Backup Strategy**
1. **Database backups:**
   - Render provides automatic backups for paid plans
   - For free plan, set up manual backups

2. **Code backups:**
   - Your code is backed up in GitHub
   - Tag releases for easy rollback

### **Step 10.2: Monitoring Setup**
1. **Set up uptime monitoring:**
   - Use services like UptimeRobot
   - Monitor both frontend and backend

2. **Error tracking:**
   - Consider adding Sentry or Bugsnag
   - Monitor application errors

### **Step 10.3: Performance Optimization**
1. **Enable caching:**
   ```bash
   # Add to Render environment variables
   CACHE_STORE=redis
   ```

2. **Optimize images:**
   - Compress images in frontend
   - Use CDN for static assets

---

## 🆘 **TROUBLESHOOTING GUIDE**

### **Common Issue 1: Build Failures**
```bash
# Check build logs in Render Dashboard
# Common fixes:
# 1. Update composer.json dependencies
# 2. Check PHP version compatibility
# 3. Verify all required extensions are available
```

### **Common Issue 2: Database Connection Issues**
```bash
# Check database URL format
# Ensure all environment variables are set correctly
# Verify database is running and accessible
```

### **Common Issue 3: Webhook Failures**
```bash
# Check webhook URLs are correct
# Verify SSL certificates are working
# Check Render logs for incoming webhook requests
```

### **Common Issue 4: Environment Variable Issues**
```bash
# Ensure all required variables are set
# Check for typos in variable names
# Verify values are correct (no extra spaces)
```

### **Common Issue 5: Frontend Build Issues**
```bash
# Check Node.js version compatibility
# Verify all dependencies are in package.json
# Check build command is correct
```

---

## 📋 **RENDER DEPLOYMENT CHECKLIST**

### **Pre-Deployment:**
- [ ] Render account created
- [ ] GitHub repository ready
- [ ] Production API keys obtained
- [ ] Domain registered

### **Database:**
- [ ] PostgreSQL database created
- [ ] Connection details saved
- [ ] Database tested

### **Backend:**
- [ ] Web service created
- [ ] Build settings configured
- [ ] Environment variables set
- [ ] Health check endpoint added
- [ ] Deployment successful

### **Frontend:**
- [ ] Static site created
- [ ] Build settings configured
- [ ] Environment variables set
- [ ] Deployment successful

### **Webhooks:**
- [ ] Stripe webhook configured
- [ ] Twilio webhook configured
- [ ] Wise webhook configured (if needed)
- [ ] All webhooks tested

### **Security:**
- [ ] SSL certificates active
- [ ] Security headers configured
- [ ] Environment variables secured

### **Testing:**
- [ ] API endpoints tested
- [ ] Database connection tested
- [ ] Webhooks tested
- [ ] End-to-end flow tested

### **Go Live:**
- [ ] Production API keys active
- [ ] Custom domains configured
- [ ] DNS records updated
- [ ] Final deployment completed
- [ ] All functionality verified

---

## ⏱️ **ESTIMATED TIMELINE**

- **Phase 1-2 (Setup & Database)**: 30 minutes
- **Phase 3-4 (Backend & Frontend)**: 1-2 hours
- **Phase 5-6 (Webhooks & Security)**: 30 minutes
- **Phase 7-8 (Monitoring & Testing)**: 1 hour
- **Phase 9-10 (Go Live & Post-Deployment)**: 30 minutes

**Total Estimated Time**: 3-4 hours

---

## 💰 **COST ESTIMATION**

### **Free Tier (Testing):**
- Backend Web Service: Free (with limitations)
- PostgreSQL Database: Free (with limitations)
- Guest Static Site: Free
- Admin Static Site: Free
- **Total**: $0/month

### **Production Tier:**
- Backend Web Service: $7/month (Starter)
- PostgreSQL Database: $7/month (Starter)
- Guest Static Site: Free
- Admin Static Site: Free
- **Total**: $14/month

### **High Traffic Tier:**
- Backend Web Service: $25/month (Standard)
- PostgreSQL Database: $25/month (Standard)
- Guest Static Site: Free
- Admin Static Site: Free
- **Total**: $50/month

---

## 🎯 **SUCCESS CRITERIA**

Your Render deployment is successful when:
- [ ] Backend API responds correctly
- [ ] Frontend loads and functions properly
- [ ] Database connections work
- [ ] Webhooks receive and process events
- [ ] Email notifications are delivered
- [ ] WhatsApp notifications are sent
- [ ] Payment processing works end-to-end
- [ ] SSL certificates are active
- [ ] Custom domains work
- [ ] Performance is acceptable

---

## 📝 **ENVIRONMENT VARIABLES SUMMARY**

### **Backend Web Service Environment Variables:**
```env
# Application
APP_NAME=Villa Upsell
APP_ENV=production
APP_KEY=base64:your_generated_app_key
APP_DEBUG=false
APP_URL=https://your-backend-service.onrender.com
APP_FRONTEND_URL=https://your-guest-service.onrender.com
APP_ADMIN_URL=https://your-admin-service.onrender.com

# Database
DB_CONNECTION=pgsql
DB_HOST=dpg-xxxxx-a.oregon-postgres.render.com
DB_PORT=5432
DB_DATABASE=villa_upsell_production
DB_USERNAME=villa_user
DB_PASSWORD=your_generated_password

# API Keys
SENDGRID_API_KEY=your_production_sendgrid_key
STRIPE_KEY=pk_live_your_live_publishable_key
STRIPE_SECRET_KEY=sk_live_your_live_secret_key
STRIPE_CONNECT_CLIENT_ID=ca_your_live_connect_client_id
STRIPE_WEBHOOK_SECRET=whsec_your_live_webhook_secret
TWILIO_ACCOUNT_SID=your_production_twilio_sid
TWILIO_AUTH_TOKEN=your_production_twilio_token
TWILIO_WHATSAPP_FROM=whatsapp:+your_production_whatsapp_number
WISE_API_KEY=your_production_wise_api_key
WISE_TOKEN=your_production_wise_token
WISE_PROFILE_ID=58946812
```

### **Guest Frontend Environment Variables:**
```env
VITE_API_URL=https://your-backend-service.onrender.com
VITE_STRIPE_PUBLISHABLE_KEY=pk_live_your_live_publishable_key
```

### **Admin Frontend Environment Variables:**
```env
VITE_API_URL=https://your-backend-service.onrender.com
VITE_STRIPE_PUBLISHABLE_KEY=pk_live_your_live_publishable_key
```

### **How to Set Environment Variables in Render:**

1. **For Backend Web Service:**
   - Go to your backend service in Render Dashboard
   - Click "Environment" tab
   - Add each variable one by one
   - Click "Save Changes"

2. **For Guest Static Site:**
   - Go to your guest static site in Render Dashboard
   - Click "Environment" tab
   - Add the 2 variables
   - Click "Save Changes"

3. **For Admin Static Site:**
   - Go to your admin static site in Render Dashboard
   - Click "Environment" tab
   - Add the 2 variables
   - Click "Save Changes"

**Ready to start with Phase 1?** 🚀