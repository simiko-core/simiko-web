# 📋 Simiko User Acceptance Testing (UAT) Documentation

## 📋 Overview

This document outlines the User Acceptance Testing (UAT) scenarios for the **Simiko Student Activity Management System**. UAT ensures that the system meets business requirements and provides a satisfactory user experience for all stakeholders.

## 🎯 Testing Scope

### Test Environment

-   **Base URL**: `http://localhost:8000` (Development)
-   **API Base URL**: `http://localhost:8000/api`
-   **Admin Panel**: `http://localhost:8000/admin`
-   **UKM Panel**: `http://localhost:8000/ukm-panel`

### Test Data

-   **Super Admin**: admin@example.com / password123
-   **UKM Admin**: ukmadmin@example.com / password123
-   **Regular User**: user@example.com / password123

---

## 🧪 Feature 1: User Authentication & Registration

### Test Cases

| **TC-ID** | **Test Case**                        | **Preconditions**   | **Test Steps**                                                                                                                                                                                                      | **Expected Results**                                                                                                      | **Status** | **Notes**                    |
| --------- | ------------------------------------ | ------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------- | ---------- | ---------------------------- |
| TC-001    | User Registration with Valid Data    | User not registered | 1. Navigate to registration page<br>2. Fill name: "John Doe"<br>3. Fill email: "john@test.com"<br>4. Fill password: "password123"<br>5. Fill phone: "08123456789"<br>6. Upload profile photo<br>7. Click "Register" | - Registration successful<br>- User redirected to dashboard<br>- Welcome email sent<br>- Profile photo uploaded           | ⏳ Pending | Include file validation      |
| TC-002    | User Registration with Invalid Email | User not registered | 1. Navigate to registration page<br>2. Fill name: "John Doe"<br>3. Fill email: "invalid-email"<br>4. Fill password: "password123"<br>5. Click "Register"                                                            | - Error message displayed<br>- Form validation prevents submission<br>- User stays on registration page                   | ⏳ Pending | Test email format validation |
| TC-003    | User Login with Valid Credentials    | User registered     | 1. Navigate to login page<br>2. Enter email: "user@example.com"<br>3. Enter password: "password123"<br>4. Click "Login"                                                                                             | - Login successful<br>- Access token generated<br>- User redirected to dashboard<br>- Session established                 | ⏳ Pending | Test token generation        |
| TC-004    | User Login with Invalid Credentials  | User registered     | 1. Navigate to login page<br>2. Enter email: "user@example.com"<br>3. Enter password: "wrongpassword"<br>4. Click "Login"                                                                                           | - Error message displayed<br>- Login failed<br>- User stays on login page<br>- No session created                         | ⏳ Pending | Test security measures       |
| TC-005    | User Logout                          | User logged in      | 1. User is logged in<br>2. Click "Logout" button<br>3. Confirm logout                                                                                                                                               | - Session terminated<br>- Access token revoked<br>- User redirected to login page<br>- Cannot access protected pages      | ⏳ Pending | Test token revocation        |
| TC-006    | Password Reset Functionality         | User registered     | 1. Click "Forgot Password"<br>2. Enter email: "user@example.com"<br>3. Submit form<br>4. Check email for reset link                                                                                                 | - Reset email sent<br>- Reset link generated<br>- User can reset password<br>- New password works                         | ⏳ Pending | Test email delivery          |
| TC-007    | Profile Update                       | User logged in      | 1. Navigate to profile page<br>2. Update name to "Jane Doe"<br>3. Update phone to "08987654321"<br>4. Upload new photo<br>5. Save changes                                                                           | - Profile updated successfully<br>- Changes reflected immediately<br>- New photo uploaded<br>- Data persisted in database | ⏳ Pending | Test file upload             |

---

## 🏛️ Feature 2: UKM Management & Profiles

### Test Cases

| **TC-ID** | **Test Case**                | **Preconditions**          | **Test Steps**                                                                                                                           | **Expected Results**                                                                                                                            | **Status** | **Notes**                |
| --------- | ---------------------------- | -------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------- | ---------- | ------------------------ |
| TC-008    | View All UKMs                | User logged in             | 1. Navigate to UKMs page<br>2. View UKM list<br>3. Check pagination<br>4. Verify UKM details                                             | - All UKMs displayed<br>10 UKMs per page<br>Each UKM shows name, alias, logo<br>Pagination works correctly                                      | ⏳ Pending | Test data loading        |
| TC-009    | Search UKMs by Name          | User logged in             | 1. Navigate to UKMs page<br>2. Enter search term: "HMIF"<br>3. Submit search<br>4. View results                                          | - Search results displayed<br>Only HMIF-related UKMs shown<br>Search is case-insensitive<br>No results message if none found                    | ⏳ Pending | Test search algorithm    |
| TC-010    | View UKM Basic Profile       | User logged in             | 1. Click on UKM from list<br>2. View basic profile page<br>3. Check displayed information                                                | - UKM name, alias, category shown<br>Logo displayed correctly<br>Description visible<br>Contact information available                           | ⏳ Pending | Test data display        |
| TC-011    | View UKM Full Profile        | User logged in             | 1. Click "View Full Profile"<br>2. Scroll through complete profile<br>3. Check all sections                                              | - Vision & mission displayed<br>Achievements list shown<br>Activity gallery visible<br>Recent posts/events listed<br>Background photo displayed | ⏳ Pending | Test rich content        |
| TC-012    | Register for UKM Membership  | User logged in, UKM exists | 1. Navigate to UKM profile<br>2. Click "Join UKM"<br>3. Confirm registration<br>4. Check status                                          | - Registration submitted<br>Status shows "Pending"<br>UKM admin notified<br>User can track status                                               | ⏳ Pending | Test notification system |
| TC-013    | UKM Admin Profile Management | UKM Admin logged in        | 1. Access UKM panel<br>2. Navigate to profile settings<br>3. Update vision & mission<br>4. Upload background photo<br>5. Save changes    | - Profile updated successfully<br>Changes visible to users<br>Background photo uploaded<br>Data persisted correctly                             | ⏳ Pending | Test admin privileges    |
| TC-014    | UKM Member Management        | UKM Admin logged in        | 1. Access member management<br>2. View pending registrations<br>3. Approve one registration<br>4. Reject another<br>5. Check member list | - Pending registrations listed<br>Approval/rejection works<br>Member count updated<br>Notifications sent to users                               | ⏳ Pending | Test approval workflow   |

---

## 📱 Feature 3: Content Management (Posts & Events)

### Test Cases

| **TC-ID** | **Test Case**         | **Preconditions**                   | **Test Steps**                                                                                                                                                                         | **Expected Results**                                                                                                                           | **Status** | **Notes**                |
| --------- | --------------------- | ----------------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------- | ---------- | ------------------------ |
| TC-015    | Create New Post       | UKM Admin logged in                 | 1. Access UKM panel<br>2. Navigate to "Create Post"<br>3. Fill title: "New Activity"<br>4. Add content with formatting<br>5. Upload image<br>6. Publish post                           | - Post created successfully<br>Image uploaded<br>Formatting preserved<br>Post visible to users<br>Appears in feed                              | ⏳ Pending | Test rich text editor    |
| TC-016    | Create Paid Event     | UKM Admin logged in                 | 1. Access UKM panel<br>2. Navigate to "Create Event"<br>3. Fill event details<br>4. Set as paid event<br>5. Configure payment settings<br>6. Set participant limit<br>7. Publish event | - Event created successfully<br>Payment configuration saved<br>Registration token generated<br>Event appears in feed<br>Payment link available | ⏳ Pending | Test payment integration |
| TC-017    | Edit Existing Content | UKM Admin logged in, Content exists | 1. Navigate to content management<br>2. Select existing post/event<br>3. Click "Edit"<br>4. Modify title and content<br>5. Save changes                                                | - Content updated successfully<br>Changes reflected immediately<br>Version history maintained<br>Users see updated content                     | ⏳ Pending | Test content versioning  |
| TC-018    | Delete Content        | UKM Admin logged in, Content exists | 1. Navigate to content management<br>2. Select content to delete<br>3. Click "Delete"<br>4. Confirm deletion                                                                           | - Content deleted successfully<br>Removed from feed<br>Associated files deleted<br>No broken links                                             | ⏳ Pending | Test cascade deletion    |
| TC-019    | View Content Feed     | User logged in                      | 1. Navigate to main feed<br>2. Scroll through content<br>3. Filter by type (posts/events)<br>4. Filter by UKM                                                                          | - All content displayed<br>Filtering works correctly<br>Pagination functional<br>Content sorted by date<br>Images load properly                | ⏳ Pending | Test performance         |
| TC-020    | Content Search        | User logged in                      | 1. Use search function<br>2. Enter keywords<br>3. View search results<br>4. Check relevance                                                                                            | - Search results displayed<br>Relevant content found<br>Search highlights keywords<br>No results message if none                               | ⏳ Pending | Test search relevance    |
| TC-021    | Banner Management     | Super Admin logged in               | 1. Access banner management<br>2. Create new banner<br>3. Select feed to promote<br>4. Set active status<br>5. Save banner                                                             | - Banner created successfully<br>Linked to selected feed<br>Active status saved<br>Banner visible on homepage<br>Rotation works correctly      | ⏳ Pending | Test banner rotation     |

---

## 💰 Feature 4: Event Registration & Payment System

### Test Cases

| **TC-ID** | **Test Case**                | **Preconditions**                    | **Test Steps**                                                                                                                                      | **Expected Results**                                                                                                                                     | **Status** | **Notes**              |
| --------- | ---------------------------- | ------------------------------------ | --------------------------------------------------------------------------------------------------------------------------------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------- | ---------- | ---------------------- |
| TC-022    | Guest Event Registration     | Paid event exists                    | 1. Access event registration link<br>2. Fill personal information<br>3. Complete custom fields<br>4. Submit registration<br>5. Receive confirmation | - Registration successful<br>Anonymous record created<br>Payment transaction generated<br>Confirmation email sent<br>Registration token valid            | ⏳ Pending | Test guest workflow    |
| TC-023    | Payment Method Selection     | Payment transaction created          | 1. Access payment page<br>2. View available payment methods<br>3. Select bank transfer<br>4. View payment details                                   | - Payment methods displayed<br>Bank details shown correctly<br>Account numbers visible<br>Payment instructions clear<br>QR code generated (if available) | ⏳ Pending | Test payment UI        |
| TC-024    | Payment Proof Upload         | Payment transaction pending          | 1. Complete bank transfer<br>2. Take screenshot of transfer<br>3. Upload proof of payment<br>4. Add payment notes<br>5. Submit proof                | - File uploaded successfully<br>File validation passed<br>Proof stored securely<br>Transaction status updated<br>Admin notified                          | ⏳ Pending | Test file validation   |
| TC-025    | Payment Status Tracking      | Payment proof uploaded               | 1. Access status page<br>2. View current status<br>3. Check payment details<br>4. View admin notes                                                  | - Status displayed correctly<br>Payment details visible<br>Admin notes shown<br>Real-time updates<br>Status history available                            | ⏳ Pending | Test real-time updates |
| TC-026    | Payment Verification (Admin) | UKM Admin logged in, Payment pending | 1. Access payment management<br>2. View pending payments<br>3. Review proof of payment<br>4. Mark as "Paid"<br>5. Add verification notes            | - Payment verified successfully<br>Status updated to "Paid"<br>User notified automatically<br>Receipt generated<br>Registration confirmed                | ⏳ Pending | Test admin workflow    |
| TC-027    | Event Capacity Management    | Event with participant limit         | 1. Register multiple participants<br>2. Reach capacity limit<br>3. Attempt additional registration<br>4. Check capacity display                     | - Capacity tracked correctly<br>Registration blocked when full<br>Capacity status updated<br>Waitlist option available<br>Admin notified of capacity     | ⏳ Pending | Test capacity logic    |
| TC-028    | Receipt Generation           | Payment verified                     | 1. Access receipt download<br>2. Generate PDF receipt<br>3. Download receipt<br>4. Verify receipt content                                           | - PDF generated successfully<br>Receipt contains all details<br>Professional formatting<br>Download works correctly<br>Receipt is printable              | ⏳ Pending | Test PDF generation    |

---

## 🏆 Feature 5: Achievement & Gallery Management

### Test Cases

| **TC-ID** | **Test Case**            | **Preconditions**                       | **Test Steps**                                                                                                                                        | **Expected Results**                                                                                                                                                | **Status** | **Notes**               |
| --------- | ------------------------ | --------------------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ---------- | ----------------------- |
| TC-029    | Add New Achievement      | UKM Admin logged in                     | 1. Access achievement management<br>2. Click "Add Achievement"<br>3. Fill title and description<br>4. Upload achievement image<br>5. Save achievement | - Achievement created successfully<br>Image uploaded and optimized<br>Description saved correctly<br>Achievement visible in profile<br>Appears in achievements list | ⏳ Pending | Test image optimization |
| TC-030    | Edit Achievement         | UKM Admin logged in, Achievement exists | 1. Select existing achievement<br>2. Click "Edit"<br>3. Update title and description<br>4. Replace image<br>5. Save changes                           | - Achievement updated successfully<br>New image uploaded<br>Changes reflected immediately<br>Version history maintained<br>Users see updated achievement            | ⏳ Pending | Test content updates    |
| TC-031    | Delete Achievement       | UKM Admin logged in, Achievement exists | 1. Select achievement to delete<br>2. Click "Delete"<br>3. Confirm deletion<br>4. Check achievements list                                             | - Achievement deleted successfully<br>Image file removed<br>Removed from profile<br>List updated correctly<br>No broken references                                  | ⏳ Pending | Test file cleanup       |
| TC-032    | Upload Activity Gallery  | UKM Admin logged in                     | 1. Access gallery management<br>2. Click "Upload Photos"<br>3. Select multiple images<br>4. Add captions<br>5. Upload batch                           | - Multiple images uploaded<br>Captions saved correctly<br>Images optimized automatically<br>Gallery updated immediately<br>Thumbnails generated                     | ⏳ Pending | Test batch upload       |
| TC-033    | Gallery Image Management | UKM Admin logged in, Gallery has images | 1. View gallery images<br>2. Edit image caption<br>3. Reorder images<br>4. Delete specific image<br>5. Save changes                                   | - Caption updated successfully<br>Order changed correctly<br>Image deleted properly<br>Gallery refreshed<br>Changes persisted                                       | ⏳ Pending | Test drag-and-drop      |
| TC-034    | View Achievement Gallery | User logged in                          | 1. Navigate to UKM profile<br>2. View achievements section<br>3. Click on achievement<br>4. View full image<br>5. Read description                    | - Achievements displayed correctly<br>Images load properly<br>Descriptions readable<br>Navigation works smoothly<br>Responsive design                               | ⏳ Pending | Test responsive design  |
| TC-035    | Gallery Lightbox View    | User logged in, Gallery has images      | 1. Click on gallery image<br>2. View lightbox<br>3. Navigate between images<br>4. View image details<br>5. Close lightbox                             | - Lightbox opens correctly<br>Navigation arrows work<br>Image details shown<br>Zoom functionality works<br>Close button functional                                  | ⏳ Pending | Test lightbox features  |

---

## 📊 Feature 6: Administrative Functions & Reporting

### Test Cases

| **TC-ID** | **Test Case**                 | **Preconditions**     | **Test Steps**                                                                                                                                  | **Expected Results**                                                                                                                    | **Status** | **Notes**                  |
| --------- | ----------------------------- | --------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------- | --------------------------------------------------------------------------------------------------------------------------------------- | ---------- | -------------------------- |
| TC-036    | User Management (Super Admin) | Super Admin logged in | 1. Access user management<br>2. View all users<br>3. Edit user details<br>4. Assign admin role<br>5. Save changes                               | - All users listed<br>User details editable<br>Role assignment works<br>Changes saved successfully<br>User permissions updated          | ⏳ Pending | Test role management       |
| TC-037    | UKM Management (Super Admin)  | Super Admin logged in | 1. Access UKM management<br>2. Create new UKM<br>3. Assign UKM admin<br>4. Configure settings<br>5. Activate UKM                                | - UKM created successfully<br>Admin assigned correctly<br>Settings configured<br>UKM activated<br>Admin notified                        | ⏳ Pending | Test UKM creation          |
| TC-038    | Payment Transaction Reports   | UKM Admin logged in   | 1. Access payment reports<br>2. Filter by date range<br>3. Filter by status<br>4. Export to Excel<br>5. View summary statistics                 | - Reports generated correctly<br>Filtering works properly<br>Excel export successful<br>Statistics accurate<br>Data formatted correctly | ⏳ Pending | Test report generation     |
| TC-039    | System Statistics Dashboard   | Super Admin logged in | 1. Access dashboard<br>2. View user statistics<br>3. View UKM statistics<br>4. View payment statistics<br>5. Check real-time data               | - Statistics displayed correctly<br>Real-time updates work<br>Charts render properly<br>Data accurate<br>Dashboard responsive           | ⏳ Pending | Test dashboard performance |
| TC-040    | Content Moderation            | Super Admin logged in | 1. Access content moderation<br>2. Review flagged content<br>3. Approve/reject content<br>4. Add moderation notes<br>5. Notify users            | - Content reviewed properly<br>Approval/rejection works<br>Notes saved correctly<br>Users notified<br>Moderation log maintained         | ⏳ Pending | Test moderation workflow   |
| TC-041    | System Backup                 | Super Admin logged in | 1. Initiate system backup<br>2. Monitor backup progress<br>3. Verify backup completion<br>4. Test backup restoration<br>5. Check data integrity | - Backup completed successfully<br>Progress tracked correctly<br>Restoration works<br>Data integrity maintained<br>Backup logs created  | ⏳ Pending | Test backup system         |
| TC-042    | API Usage Monitoring          | Super Admin logged in | 1. Access API monitoring<br>2. View API usage statistics<br>3. Check rate limiting<br>4. Monitor error rates<br>5. Generate API reports         | - Usage statistics accurate<br>Rate limiting working<br>Error rates monitored<br>Reports generated<br>Alerts configured                 | ⏳ Pending | Test API monitoring        |

---

## 🔧 Feature 7: System Performance & Security

### Test Cases

| **TC-ID** | **Test Case**                     | **Preconditions**           | **Test Steps**                                                                                                                                       | **Expected Results**                                                                                                                  | **Status** | **Notes**                  |
| --------- | --------------------------------- | --------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------- | ---------- | -------------------------- |
| TC-043    | Load Testing                      | System deployed             | 1. Simulate 100 concurrent users<br>2. Monitor response times<br>3. Check memory usage<br>4. Monitor database performance<br>5. Test under peak load | - Response time < 2 seconds<br>Memory usage stable<br>Database performs well<br>No crashes under load<br>Graceful degradation         | ⏳ Pending | Test scalability           |
| TC-044    | Security Testing - Authentication | System accessible           | 1. Test SQL injection attempts<br>2. Test XSS attacks<br>3. Test CSRF protection<br>4. Test authentication bypass<br>5. Test session management      | - All attacks blocked<br>Security headers present<br>CSRF tokens working<br>Authentication secure<br>Sessions managed properly        | ⏳ Pending | Test security measures     |
| TC-045    | File Upload Security              | File upload enabled         | 1. Upload malicious files<br>2. Test file type validation<br>3. Test file size limits<br>4. Test virus scanning<br>5. Test secure storage            | - Malicious files rejected<br>File types validated<br>Size limits enforced<br>Files scanned for viruses<br>Secure storage implemented | ⏳ Pending | Test file security         |
| TC-046    | API Security Testing              | API endpoints active        | 1. Test unauthorized access<br>2. Test token validation<br>3. Test rate limiting<br>4. Test input validation<br>5. Test error handling               | - Unauthorized access blocked<br>Tokens validated properly<br>Rate limiting working<br>Input sanitized<br>Errors handled securely     | ⏳ Pending | Test API security          |
| TC-047    | Database Security                 | Database accessible         | 1. Test database encryption<br>2. Test backup encryption<br>3. Test access controls<br>4. Test audit logging<br>5. Test data integrity               | - Data encrypted at rest<br>Backups encrypted<br>Access controls working<br>Audit logs maintained<br>Data integrity verified          | ⏳ Pending | Test database security     |
| TC-048    | Mobile Responsiveness             | Mobile device available     | 1. Test on mobile browser<br>2. Test responsive design<br>3. Test touch interactions<br>4. Test mobile navigation<br>5. Test mobile performance      | - Responsive design works<br>Touch interactions smooth<br>Navigation intuitive<br>Performance acceptable<br>No horizontal scrolling   | ⏳ Pending | Test mobile UX             |
| TC-049    | Cross-Browser Compatibility       | Multiple browsers available | 1. Test on Chrome<br>2. Test on Firefox<br>3. Test on Safari<br>4. Test on Edge<br>5. Test on mobile browsers                                        | - All browsers supported<br>Consistent functionality<br>No major differences<br>Responsive design works<br>Features accessible        | ⏳ Pending | Test browser compatibility |

---

## 📋 Test Execution Guidelines

### Test Environment Setup

1. **Database**: Fresh database with seed data
2. **Storage**: Clean file storage directory
3. **Cache**: Cleared application cache
4. **Sessions**: Fresh session storage
5. **Logs**: Clean log files

### Test Data Management

-   Use dedicated test accounts
-   Create test UKMs and content
-   Maintain test data consistency
-   Clean up after test execution

### Defect Reporting

-   **Severity Levels**: Critical, High, Medium, Low
-   **Priority Levels**: P1, P2, P3, P4
-   **Status Tracking**: Open, In Progress, Fixed, Verified, Closed

### Test Completion Criteria

-   All test cases executed
-   All critical defects resolved
-   Performance benchmarks met
-   Security requirements satisfied
-   User acceptance criteria met

---

## 📊 Test Metrics

### Coverage Metrics

-   **Feature Coverage**: 100% of core features tested
-   **Code Coverage**: Minimum 80% code coverage
-   **API Coverage**: 100% of API endpoints tested
-   **UI Coverage**: All user interfaces tested

### Performance Metrics

-   **Response Time**: < 2 seconds for all operations
-   **Throughput**: Support 100+ concurrent users
-   **Availability**: 99.9% uptime
-   **Error Rate**: < 1% error rate

### Quality Metrics

-   **Defect Density**: < 5 defects per 1000 lines of code
-   **Test Pass Rate**: > 95% test pass rate
-   **User Satisfaction**: > 4.5/5 rating
-   **Accessibility**: WCAG 2.1 AA compliance

---

**Document Version**: 1.0  
**Last Updated**: December 2024  
**Prepared By**: Simiko QA Team  
**Approved By**: Project Manager
