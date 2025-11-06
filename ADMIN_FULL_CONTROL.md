# 🔐 Admin Full Control - Complete CRUD Capabilities

## Overview

The admin panel now has **FULL CRUD (Create, Read, Update, Delete)** control over **ALL** system entities:
- ✅ **Users** - Complete management
- ✅ **Orders** - Complete management  
- ✅ **Services** - Complete management
- ✅ **Plans** - Complete management

---

## 👥 User Management (Full CRUD)

### List Users
**Route:** `GET /admin/users`

**Features:**
- View all users (paginated)
- Search by: name, email, phone
- Filter by role (admin/user)
- View statistics: orders count, services count, total spending
- Actions: View, Edit, Delete, Impersonate, View Credentials

### Create User
**Route:** `GET /admin/users/create`

**Form Fields:**
- Name (required)
- Email (required, unique)
- Phone (optional)
- Password (required, min 8 chars, confirmed)
- Role (required: user/admin)

**Process:**
1. Click "Create New User" button
2. Fill in all required fields
3. Set role (User or Admin)
4. Submit form
5. User created with hashed password

### View User
**Route:** `GET /admin/users/{user}`

**Displays:**
- User profile information
- Statistics (orders, services, total spent)
- All orders with status
- All services with credentials
- Action buttons: Edit, Delete, Impersonate, View Credentials

### Edit User
**Route:** `GET /admin/users/{user}/edit`

**Editable Fields:**
- Name
- Email (validates uniqueness)
- Phone
- Role (user/admin)
- Password (optional - leave blank to keep current)

**Process:**
1. Click "Edit" on user page
2. Modify any field
3. Optionally change password
4. Save changes

### Delete User
**Route:** `DELETE /admin/users/{user}`

**Protection:**
- ❌ Cannot delete yourself (current admin)
- ❌ Cannot delete other admin users
- ✅ Can delete regular users

**Process:**
1. Click "Delete" button
2. Confirm deletion
3. User and related data handled according to foreign key constraints

---

## 🛒 Order Management (Full CRUD)

### List Orders
**Route:** `GET /admin/orders`

**Features:**
- View all orders (paginated)
- Filter by status: pending, paid, failed, active, cancelled, complete, succeeded
- Search/sort capabilities
- Actions: View, Edit, Delete

### Create Order
**Route:** `GET /admin/orders/create`

**Form Fields:**
- **User** (required, dropdown) - Select which user
- **Plan** (required, dropdown) - Select hosting plan
- **Customer Name** (required)
- **Customer Email** (required)
- **Customer Phone** (required) - Format: 07xxxxxxxx
- **Domain** (optional) - Hosting domain
- **Price (TZS)** (required) - Order amount
- **Currency** (optional, default: TZS)
- **Status** (required) - pending, paid, failed, active, cancelled, complete, succeeded
- **Payment Reference** (optional) - Transaction ID
- **Gateway Provider** (optional) - M-PESA, TIGO-PESA, AIRTEL-MONEY

**Process:**
1. Click "Create New Order"
2. Select user and plan
3. Fill customer details
4. Set price and status
5. Optionally add payment info
6. Submit - Order created with UUID

**Use Cases:**
- Manually create orders for offline payments
- Test provisioning system
- Import existing orders
- Handle special cases

### View Order
**Route:** `GET /admin/orders/{order}`

**Displays:**
- Order details (ID, UUID, status)
- Customer information
- Plan details
- Payment information
- Gateway provider and reference
- Related service (if provisioned)
- Timeline/history
- Action buttons: Edit, Delete

### Edit Order
**Route:** `GET /admin/orders/{order}/edit`

**All Fields Editable:**
- User assignment
- Plan
- Customer name, email, phone
- Domain
- Price and currency
- Status (can change to any status)
- Payment reference
- Gateway provider

**Process:**
1. Click "Edit" on order page
2. Modify any field
3. Change status to mark as paid/failed/etc
4. Save changes

**Common Admin Tasks:**
- Mark order as paid (if payment received offline)
- Update customer contact details
- Change plan/price
- Fix payment references
- Update status after issues resolved

### Delete Order
**Route:** `DELETE /admin/orders/{order}`

**Warning:** Deletes order and associated service (if any)

**Process:**
1. Click "Delete" button
2. Confirm deletion
3. Order removed from system

---

## 🖥️ Service Management (Full CRUD)

### List Services
**Route:** `GET /admin/services`

**Features:**
- Grid view of all services
- Filter by status: active, provisioning, suspended, cancelled
- Search by domain, username
- View credentials (username, password, URLs)
- Actions: View, Edit, Delete, Reprovision, Suspend, Activate

### Create Service
**Route:** `GET /admin/services/create`

**Form Fields:**
- **Order** (required, dropdown) - Link to paid/active order
- **Plan** (required, dropdown) - Select hosting plan
- **Domain** (required) - example.com
- **Webuzo Username** (optional) - Leave blank to auto-generate
- **Control Panel URL** (optional) - Webuzo endpoint
- **Status** (required) - requested, provisioning, active, failed, suspended, cancelled

**Process:**
1. Click "Create New Service"
2. Select order (shows only paid/active orders)
3. Select plan
4. Enter domain
5. Set initial status
6. Submit - Service created

**Use Cases:**
- Manually provision services
- Create services for offline orders
- Bypass automatic provisioning
- Test provisioning system

### View Service
**Route:** `GET /admin/services/{service}`

**Displays:**
- Service details (ID, domain, status)
- **Full credentials:**
  - Webuzo username (with copy button)
  - Password (with show/hide toggle & copy)
  - Control panel URL (with copy & open)
- Related order and user
- Plan information
- Provisioning logs (step-by-step)
- Action buttons: Edit, Delete, Reprovision, Activate, Suspend, Send Credentials

### Edit Service
**Route:** `GET /admin/services/{service}/edit`

**Editable Fields:**
- Domain
- Panel username
- Control Panel URL
- Status (provisioning, active, suspended, cancelled)

**Process:**
1. Click "Edit" on service page
2. Modify fields
3. Change status manually
4. Save changes

**Common Admin Tasks:**
- Update domain name
- Change username
- Fix control panel URL
- Manually activate/suspend

### Delete Service
**Route:** `DELETE /admin/services/{service}`

**Warning:** Permanently removes service record. Does NOT delete from Webuzo.

**Process:**
1. Click "Delete" button
2. Confirm deletion
3. Service removed from database

**Note:** To remove from Webuzo panel, must be done manually or via Webuzo API

### Additional Service Actions

#### Re-provision Service
**Route:** `POST /admin/services/{service}/reprovision`

- Sets status to "provisioning"
- Dispatches ProvisionServiceJob to queue
- Attempts to create hosting account again
- Useful for failed provisioning or to reset account

#### Suspend Service
**Route:** `POST /admin/services/{service}/suspend`

- Changes status to "suspended"
- User cannot access control panel
- Admin action only

#### Activate Service
**Route:** `POST /admin/services/{service}/activate`

- Changes status to "active"
- User can access control panel
- Reactivates suspended services

#### Send Credentials
**Route:** `POST /admin/services/{service}/send-credentials`

- Sends email to user with:
  - Control panel URL
  - Username
  - Instructions (password not included for security)
- Useful after provisioning or password reset

---

## 📦 Plan Management (Full CRUD)

### List Plans
**Route:** `GET /admin/plans`

**Features:**
- Card-based display
- Shows: name, price, features, status
- Order count per plan
- Actions: View, Edit, Delete, Activate/Deactivate

### Create Plan
**Route:** `GET /admin/plans/create`

**Form Fields:**
- Name (required)
- Slug (auto-generated from name)
- Price in TZS (required)
- Features (JSON array)
- Active status (checkbox)

**Process:**
1. Click "Create New Plan"
2. Enter plan details
3. Set pricing
4. Add features
5. Set active status
6. Submit - Plan created with auto-generated slug

### Edit Plan
**Route:** `GET /admin/plans/{plan}/edit`

**All Fields Editable:**
- Name (slug auto-updates)
- Price
- Features
- Active status

**Process:**
1. Click "Edit" on plan card
2. Modify fields
3. Save changes

### Delete Plan
**Route:** `DELETE /admin/plans/{plan}`

**Protection:** Cannot delete plan if orders exist (foreign key constraint)

**Process:**
1. Click "Delete" button
2. Confirm deletion
3. Plan removed

---

## 🎯 Admin Capabilities Summary

### Full Control Over Everything

| Entity | Create | Read | Update | Delete | Special Actions |
|--------|--------|------|--------|--------|----------------|
| **Users** | ✅ | ✅ | ✅ | ✅ | Impersonate, View Credentials |
| **Orders** | ✅ | ✅ | ✅ | ✅ | Change status, Update payment |
| **Services** | ✅ | ✅ | ✅ | ✅ | Reprovision, Suspend, Activate, Send Credentials |
| **Plans** | ✅ | ✅ | ✅ | ✅ | Activate/Deactivate |

---

## 🔒 Security & Permissions

### Admin-Only Access
- All admin routes protected by `IsAdmin` middleware
- Requires `role = 'admin'` in users table
- Regular users cannot access `/admin/*` routes

### Safety Features
1. **User Deletion:**
   - Cannot delete yourself
   - Cannot delete other admins
   
2. **Order Deletion:**
   - Cascades to associated service
   - Payment events preserved
   
3. **Service Deletion:**
   - Removes from database only
   - Manual Webuzo cleanup required
   
4. **Plan Deletion:**
   - Blocked if orders exist
   - Protects data integrity

### Credential Security
- Passwords encrypted with `Crypt::encryptString()`
- View requires admin role
- Copy-to-clipboard functionality
- Show/hide password toggle

---

## 🎨 User Interface

### Navigation
All CRUD actions accessible from:
1. **Index pages** - List view with action buttons
2. **Detail pages** - Individual entity view
3. **Sidebar** - Quick navigation menu
4. **Breadcrumbs** - Current location

### Common Patterns

#### Create Flow
```
Index Page → "Create New" Button → Form → Save → Detail Page
```

#### Edit Flow
```
Detail Page → "Edit" Button → Form → Save → Detail Page
```

#### Delete Flow
```
Detail/Index Page → "Delete" Button → Confirm → Index Page
```

---

## 📝 Common Admin Tasks

### 1. Create User Manually
```
Admin Panel → Users → Create New User
Fill form → Set role → Save
```

### 2. Create Order for Offline Payment
```
Admin Panel → Orders → Create New Order
Select user & plan → Fill details → Set status: "paid" → Save
```

### 3. Manually Provision Service
```
Option A: Create service directly
Admin Panel → Services → Create New Service

Option B: Create via order
Admin Panel → Orders → Create Order → Set status: "paid"
System auto-creates service

Option C: Trigger provisioning
Admin Panel → Services → View Service → Re-provision
```

### 4. Fix Failed Provisioning
```
Admin Panel → Services → View Service
Check provisioning logs → Fix issue
Click "Re-provision" → Wait for queue job
```

### 5. Suspend User Service
```
Admin Panel → Services → View Service
Click "Suspend" → Service status = suspended
```

### 6. Update User Details
```
Admin Panel → Users → View User → Edit
Update fields → Save
```

### 7. Change Order Status
```
Admin Panel → Orders → View Order → Edit
Change status → Save
```

---

## 🚀 API Routes Reference

### Users
```
GET    /admin/users              - List all users
GET    /admin/users/create       - Create form
POST   /admin/users              - Store new user
GET    /admin/users/{user}       - View user details
GET    /admin/users/{user}/edit  - Edit form
PUT    /admin/users/{user}       - Update user
DELETE /admin/users/{user}       - Delete user
POST   /admin/users/{user}/impersonate - Impersonate
POST   /admin/users/stop-impersonating - Stop impersonating
GET    /admin/users/{user}/credentials - View credentials
```

### Orders
```
GET    /admin/orders              - List all orders
GET    /admin/orders/create       - Create form
POST   /admin/orders              - Store new order
GET    /admin/orders/{order}      - View order details
GET    /admin/orders/{order}/edit - Edit form
PUT    /admin/orders/{order}      - Update order
DELETE /admin/orders/{order}      - Delete order
```

### Services
```
GET    /admin/services                      - List all services
GET    /admin/services/create               - Create form
POST   /admin/services                      - Store new service
GET    /admin/services/{service}            - View service details
GET    /admin/services/{service}/edit       - Edit form
PUT    /admin/services/{service}            - Update service
DELETE /admin/services/{service}            - Delete service
POST   /admin/services/{service}/reprovision      - Re-provision
POST   /admin/services/{service}/suspend          - Suspend
POST   /admin/services/{service}/activate         - Activate
POST   /admin/services/{service}/send-credentials - Email credentials
```

### Plans
```
GET    /admin/plans            - List all plans
GET    /admin/plans/create     - Create form
POST   /admin/plans            - Store new plan
GET    /admin/plans/{plan}     - View plan details
GET    /admin/plans/{plan}/edit - Edit form
PUT    /admin/plans/{plan}     - Update plan
DELETE /admin/plans/{plan}     - Delete plan
```

---

## 💡 Best Practices

### For Creating Orders Manually
1. Always link to an existing user
2. Set appropriate status (pending if awaiting payment, paid if already received)
3. Include payment_ref if you have transaction ID
4. Fill customer contact details correctly
5. Double-check price matches the plan

### For Creating Services Manually
1. Link to a paid/active order
2. Ensure domain is valid and available
3. Leave username blank for auto-generation
4. Set status to "provisioning" to trigger automatic setup
5. Or set to "active" if manually provisioned

### For Editing Existing Records
1. Always check related records first
2. Be careful changing order status - affects service provisioning
3. When editing service username, update Webuzo as well
4. Document changes if significant

### For Deleting Records
1. Check for related records first
2. Orders: Will delete associated service
3. Services: Doesn't affect Webuzo - manual cleanup needed
4. Users: Only non-admin users, and not yourself
5. Plans: Cannot delete if orders exist

---

## 🎓 Training Tips

### For New Admins
1. Start by viewing existing records to understand data structure
2. Practice creating test users and orders
3. Test provisioning flow from order to active service
4. Learn to read provisioning logs for troubleshooting
5. Familiarize with status flows and what they mean

### Common Mistakes to Avoid
1. ❌ Don't delete orders with active services without checking first
2. ❌ Don't change service status without understanding provisioning state
3. ❌ Don't delete your own admin account
4. ❌ Don't create orders with wrong user or plan
5. ❌ Don't forget to set payment_ref for paid orders

### Troubleshooting Guide
1. **Service stuck in "provisioning":**
   - Check provisioning logs
   - Verify Webuzo connection
   - Re-provision if needed

2. **Order not creating service:**
   - Ensure order status is "paid"
   - Check if service already exists
   - Manually create service if needed

3. **User cannot access panel:**
   - Check service status (must be "active")
   - Verify credentials
   - Check enduser_url is correct

---

## 🎉 Summary

Admin now has **COMPLETE CONTROL** over the entire system:

✅ **Create** any user, order, service, or plan from scratch
✅ **Read** all data with full details and credentials
✅ **Update** any field on any entity at any time
✅ **Delete** any record (with appropriate safety checks)

**Plus Special Powers:**
- 🔓 Impersonate any user
- 🔑 View all service credentials
- 🔄 Re-provision services
- ⏸️ Suspend/activate services
- ✉️ Email credentials to users
- 📊 Comprehensive analytics

**The admin panel is now a COMPLETE management system with FULL CRUD capabilities!**

