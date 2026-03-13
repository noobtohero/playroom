# Online Course Platform
Video / Slide / Podcast Learning Platform

Tech Stack:
- Backend: CodeIgniter 4
- Frontend: Bootstrap 5 + Vanilla JS / AlpineJS
- Database: MySQL / MariaDB
- Streaming: HLS (.m3u8)
- Storage: Local / Object Storage (S3 compatible)
- Player: HLS.js

Goal:
สร้างระบบเรียนออนไลน์ที่รองรับ
- Video streaming (HLS m3u8)
- Slide presentation
- Podcast / audio lesson
- Course access control
- Admin management

เน้น:
- ป้องกัน download / copy ให้ได้มากที่สุด
- ใช้งานง่าย
- รองรับการขยายในอนาคต

---

# 1. System Overview

System consists of 4 main modules

1. Public Website
2. Learning Platform
3. Course Access Control
4. Admin Backend

```
Users
  │
  ▼
Frontend (BS5 + JS)
  │
  ▼
CI4 Application
  │
  ├── Auth System
  ├── Course System
  ├── Media Delivery
  ├── Admin CMS
  │
  ▼
Database
  │
  ▼
Storage (Video / Slide / Audio)
```

---

# 2. Content Types

Platform รองรับ content หลายรูปแบบ

## 2.1 Video Lesson

Format:

```
HLS streaming
.m3u8
.ts segments
```

Example

```
/storage/videos/course1/lesson1/

playlist.m3u8
segment001.ts
segment002.ts
segment003.ts
```

Player

```
hls.js
```

---

## 2.2 Slide Lesson

รูปแบบ

```
PDF
Image slides
HTML slides
```

Display

```
JS slide viewer
PDF.js
```

Features

- disable right click
- disable text select
- watermark user id

---

## 2.3 Podcast Lesson

```
mp3
m4a
```

Player

```
HTML5 audio player
```

---

# 3. Course Structure

Hierarchy

```
Course
  ├── Section
  │      ├── Lesson
  │      ├── Lesson
  │      └── Lesson
```

Example

```
JavaScript Mastery

Section 1: Basic
  Lesson 1: Introduction (video)
  Lesson 2: Variables (slide)

Section 2: Advanced
  Lesson 3: Async (video)
```

---

# 4. Course Access Types

Course สามารถกำหนดประเภทการเข้าถึง

### FREE

เข้าดูได้ทันที

### PAID

ต้องซื้อก่อน

### REDEEM CODE

ใช้ code แลก

### ADMIN GRANT

admin ให้สิทธิ์ manually

---

# 5. Anti Download Strategy

**ไม่สามารถป้องกัน 100%**  
แต่สามารถทำให้ download ยากมาก

Strategy:

### 1. HLS streaming

แทนการใช้ mp4

```
video.mp4 ❌
playlist.m3u8 + ts segments ✅
```

---

### 2. Tokenized streaming URL

เช่น

```
/stream/lesson/123?token=asdadad
```

token

```
user_id
lesson_id
expiry
hash
```

---

### 3. Signed URL

video segment ใช้ signed link

```
segment.ts?token=xxxx
```

---

### 4. Disable browser features

JS protection

```
disable right click
disable ctrl+s
disable devtools (partial)
```

---

### 5. Watermark

overlay บน video

```
user email
timestamp
```

เช่น

```
john@example.com
2026-03-14
```

---

### 6. Slide protection

- disable text select
- canvas rendering
- watermark

---

### 7. Rate limit

จำกัด

```
1 account = 1-2 devices
```

---

# 6. Authentication System

User table

```
users
```

fields

```
id
name
email
password
role
status
created_at
```

roles

```
student
admin
```

---

# 7. Course Database Design

## courses

```
id
title
slug
description
thumbnail
type (free/paid/code)
price
status
created_at
```

---

## sections

```
id
course_id
title
sort_order
```

---

## lessons

```
id
course_id
section_id
title
type (video/slide/podcast)
content_path
duration
sort_order
status
```

---

## enrollments

```
id
user_id
course_id
source

source =
free
purchase
code
admin
```

---

## redeem_codes

```
id
code
course_id
max_use
used_count
expire_at
status
```

---

## purchases

(offline payment)

```
id
user_id
course_id
payment_method
slip_image
status
created_at
approved_by
```

status

```
pending
approved
rejected
```

---

# 8. Admin Backend

Admin panel features

## Dashboard

```
total users
total courses
revenue
recent purchases
```

---

## Course Management

admin สามารถ

```
create course
edit course
delete course
upload thumbnail
```

---

## Section Management

```
add section
sort section
```

---

## Lesson Management

lesson types

```
video
slide
podcast
```

upload

```
video (m3u8 folder)
slide (pdf/images)
audio
```

---

## Enrollment Management

admin สามารถ

```
grant course
revoke course
```

---

## Payment Approval

workflow

```
student upload slip
↓
admin review
↓
approve
↓
enroll user
```

---

## Redeem Code Management

admin สามารถ

```
generate code
limit usage
set expire date
```

---

# 9. File Storage Structure

```
storage/

videos/
  course_id/
      lesson_id/
          playlist.m3u8
          segment.ts

slides/
  course_id/
      lesson_id/
          slide.pdf

podcast/
  course_id/
      lesson_id/
          audio.mp3
```

---

# 10. Media Delivery Architecture

video ไม่ควรเสิร์ฟตรงจาก public folder

ควรใช้

```
/stream/lesson/{id}
```

flow

```
user request video
↓
CI4 check permission
↓
generate token
↓
serve m3u8
```

---

# 11. Frontend Pages

Public

```
/courses
/course/{slug}
/login
/register
```

---

Student

```
/dashboard
/my-courses
/course/{slug}/learn
/lesson/{id}
```

---

Admin

```
/admin
/admin/courses
/admin/lessons
/admin/users
/admin/purchases
/admin/codes
```

---

# 12. Learning Page Layout

```
----------------------------------
Video Player
----------------------------------

Lesson List

Section 1
 - lesson
 - lesson

Section 2
 - lesson
 - lesson
```

---

# 13. Future Features

planned expansion

### payment gateway

```
PromptPay
Stripe
Omise
```

---

### DRM

เช่น

```
Widevine
Fairplay
```

---

### mobile app

```
Flutter
React Native
```

---

### progress tracking

```
lesson progress
course completion
```

---

### certificate

```
PDF certificate
```

---

# 14. Security

basic protection

```
CSRF
XSS filter
password hash
rate limit
login attempt limit
```

---

# 15. Development Phases

Phase 1

```
Auth
Course
Lesson
Video streaming
Admin CMS
```

---

Phase 2

```
Redeem code
Offline payment
Slide lesson
Podcast
```

---

Phase 3

```
Progress tracking
Certificate
Payment gateway
```

---

# 16. Tech Recommendations

Video player

```
hls.js
```

---

Slide viewer

```
pdf.js
```

---

UI enhancement

```
AlpineJS
```

---

Tables

```
Tabulator
```

---

# End