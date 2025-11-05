# 🚀 Admin Panel Setup Guide

## Quick Start

Your new modern admin panel is ready to use! Here's everything you need to know to get started.

---

## ✅ Installation Complete!

The following has been installed and configured:

- ✅ Modern admin layout with beautiful UI
- ✅ Dashboard with analytics and charts
- ✅ User management with impersonation
- ✅ Services management with full credentials
- ✅ Orders management
- ✅ Plans management
- ✅ Responsive design for all devices
- ✅ Security features and access control

---

## 🔐 Accessing the Admin Panel

### 1. Login as Admin
```
URL: http://yoursite.com/login
```
Make sure you have an admin user in your database. If not, create one:

```bash
php artisan tinker
```

```php
$user = new App\Models\User();
$user->name = 'Admin';
$user->email = 'admin@hollyn.com';
$user->password = bcrypt('your-password');
$user->role = 'admin';
$user->save();
```

### 2. Navigate to Admin Dashboard
```
URL: http://yoursite.com/admin
```

---

## 📚 Features Overview

### 🏠 Dashboard
Access: `/admin`

View:
- Total orders, active services, revenue stats
- Interactive revenue and orders charts
- Recent activity (orders, services, users)
- Top performing plans

### 👥 Users Management
Access: `/admin/users`

Features:
- Search users by name, email, or phone
- Filter by role (admin/user)
- View detailed user profiles
- **View all user credentials** (Click key icon)
- **Impersonate users** (Click login icon)

**To View User Credentials:**
1. Go to Users Management
2. Click the 🔑 key icon next to any user
3. See all service credentials with copy buttons
4. Toggle password visibility with eye icon

**To Login as User (Impersonate):**
1. Go to Users Management
2. Click the 🔓 login icon next to any non-admin user
3. You'll be redirected to user's dashboard
4. Yellow banner appears at top
5. Click "Return to Admin" when done

### 🖥️ Services Management
Access: `/admin/services`

Features:
- View all hosting services
- **See full credentials** (domain, username, password, panel URL)
- Copy any credential with one click
- Activate/suspend services
- Re-provision services
- Send credentials to users via email
- Access control panels directly

### 🛒 Orders Management
Access: `/admin/orders`

Features:
- View all orders with detailed information
- Filter by status (Paid, Pending, Failed, etc.)
- See customer and payment details
- Track order timeline
- Edit and manage orders

### 📦 Plans Management
Access: `/admin/plans`

Features:
- Beautiful card-based plan display
- Create, edit, delete plans
- Set pricing and duration
- Activate/deactivate plans
- View plan statistics

---

## 🎨 Using the Interface

### Sidebar Navigation
- **Desktop**: Always visible on the left
- **Mobile**: Tap the hamburger menu to open/close

### Search & Filters
- Use the search boxes to find specific records
- Use dropdown filters to narrow results
- Click "Clear" to reset filters

### Copy to Clipboard
- Click any 📋 copy icon next to credentials
- A green notification will confirm the copy
- Works for domains, usernames, passwords, and URLs

### Status Badges
- 🟢 **Green**: Active, Paid, Success
- 🟡 **Yellow**: Pending, Provisioning, Warning
- 🔴 **Red**: Failed, Suspended, Inactive
- 🔵 **Blue**: Info, Processing

---

## 🔒 Security Features

### Role-Based Access
- Only users with `role = 'admin'` can access admin panel
- Regular users are redirected if they try to access admin routes

### Impersonation Safety
- Cannot impersonate other admin users
- Impersonation is tracked in sessions
- Visual banner shows when impersonating
- Easy one-click return to admin

### Credential Protection
- Passwords are hidden by default (click eye to show)
- Encrypted storage using Laravel's encrypted cast
- Copy-only access (passwords don't stay visible)
- Security warnings on credential pages

---

## 💡 Pro Tips

### 1. Quick User Access
From any user, order, or service view, click the user name to quickly navigate to their profile.

### 2. Keyboard Navigation
- Use Tab to navigate between form fields
- Use Enter to submit forms
- Use Escape to close modals

### 3. Bulk Actions
When viewing services or orders:
1. Use filters to narrow your selection
2. Perform actions on filtered results
3. Use the "View All" link to see complete lists

### 4. Mobile Management
The entire admin panel is fully responsive:
- Tap the menu icon to access navigation
- Swipe cards for more options
- All features work on mobile devices

---

## 🎯 Common Tasks

### Task: View a User's Service Credentials
1. Navigate to **Users Management** (`/admin/users`)
2. Find the user (use search if needed)
3. Click the **🔑 Key icon** next to their name
4. View all services with full credentials
5. Click copy icons to copy any field
6. Toggle password visibility with eye icons

### Task: Login as a User
1. Navigate to **Users Management** (`/admin/users`)
2. Find the user you want to impersonate
3. Click the **🔓 Login icon** next to their name
4. Confirm the action
5. You're now viewing as that user
6. Click **Return to Admin** in the yellow banner when done

### Task: Manage a Service
1. Navigate to **Services Management** (`/admin/services`)
2. Find the service (use filters if needed)
3. View full credentials in the card
4. Click action buttons:
   - **View Details**: Full service information
   - **Control Panel**: Direct access to user's panel
   - **Email Credentials**: Send credentials to user
   - **Activate/Suspend**: Change service status
   - **Re-provision**: Re-provision the service

### Task: Create a New Plan
1. Navigate to **Plans Management** (`/admin/plans`)
2. Click **Create New Plan** button
3. Fill in plan details:
   - Name
   - Description
   - Price (in TZS)
   - Period (in months)
   - Active status
4. Click **Save**

---

## 🐛 Troubleshooting

### Issue: Can't Access Admin Panel
**Solution**: Make sure your user has `role = 'admin'` in the database.

```sql
UPDATE users SET role = 'admin' WHERE email = 'your@email.com';
```

### Issue: Impersonation Not Working
**Solution**: Check that the route is properly registered:
```bash
php artisan route:list | grep impersonate
```

### Issue: Credentials Not Showing
**Solution**: Ensure services have the required fields in the database:
- `webuzo_username` or `panel_username`
- `webuzo_temp_password_enc`
- `domain`
- `enduser_url`

### Issue: Charts Not Displaying
**Solution**: Make sure Chart.js is loading. Check browser console for errors.

### Issue: Styles Not Loading
**Solution**: Clear browser cache or run:
```bash
php artisan cache:clear
php artisan view:clear
```

---

## 🔄 Updates and Maintenance

### Clearing Cache
After making changes:
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### Optimizing for Production
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 📞 Support

### Need Help?
- Check the `ADMIN_PANEL_FEATURES.md` for detailed feature documentation
- Review Laravel documentation at https://laravel.com/docs
- Check Tailwind CSS documentation at https://tailwindcss.com/docs

### Common Documentation Links
- **Laravel Routes**: https://laravel.com/docs/routing
- **Laravel Blade**: https://laravel.com/docs/blade
- **Alpine.js**: https://alpinejs.dev/
- **Chart.js**: https://www.chartjs.org/docs/

---

## 🎉 You're All Set!

Your modern admin panel is ready to use. Enjoy managing your Hollyn Online platform with style and efficiency!

**Access your admin panel at: `/admin`**

Happy Managing! 🚀

