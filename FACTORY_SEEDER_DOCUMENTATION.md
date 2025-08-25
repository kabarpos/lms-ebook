# Factory dan Seeder Documentation - LMS EBook Project

## 📋 Ringkasan Analisis Fitur dan CRUD

### Fitur Utama yang Dianalisis:

1. **User Management** - Manajemen pengguna dengan role-based access
2. **Category Management** - Manajemen kategori kursus
3. **Course Management** - Manajemen kursus lengkap dengan relasi
4. **Course Benefits** - Manajemen benefit/keuntungan kursus
5. **Course Sections & Content** - Struktur pembelajaran bertingkat
6. **Course Mentors** - Manajemen mentor untuk setiap kursus
7. **Course Students** - Manajemen enrollment siswa
8. **Pricing Plans** - Paket harga berlangganan
9. **Transactions** - Sistem pembayaran dan transaksi
10. **Role & Permission** - Kontrol akses berbasis peran

## 🏭 Factory Files yang Dibuat

### 1. CategoryFactory.php
```php
- Menghasilkan 8 kategori unik (Web Development, Mobile Development, dll)
- Auto-generate slug dari nama kategori
- Soft delete support
```

### 2. PricingFactory.php
```php
- 4 paket harga berlangganan (Basic, Premium, Pro, Ultimate)
- Durasi 1-12 bulan dengan harga yang realistis
- Format harga dalam IDR (99.000 - 799.000)
```

### 3. CourseFactory.php
```php
- 10 nama kursus yang realistis dan bervariasi
- Thumbnail placeholder yang menarik
- Deskripsi lengkap dengan 3 paragraf
- 30% kemungkinan menjadi kursus populer
- Relasi dengan Category
```

### 4. CourseBenefitFactory.php
```php
- 10 jenis benefit yang relevan
- Data realistis seperti "Lifetime Access", "Certificate", dll
- Relasi dengan Course
```

### 5. CourseSectionFactory.php
```php
- 8 nama section yang logis (Introduction, Basic Concepts, dll)
- Position ordering untuk struktur yang teratur
- Relasi dengan Course
```

### 6. SectionContentFactory.php
```php
- 8 tipe konten yang beragam (Video, Reading, Quiz, dll)
- Content dengan 2 paragraf + URL video (60% kemungkinan)
- Nama yang deskriptif dengan 3 kata
- Relasi dengan CourseSection
```

### 7. CourseMentorFactory.php
```php
- 5 profil mentor yang profesional
- Deskripsi dengan pengalaman industri
- 85% kemungkinan aktif
- Relasi dengan User dan Course
```

### 8. CourseStudentFactory.php
```php
- Enrollment siswa ke kursus
- 90% kemungkinan aktif
- Relasi dengan User dan Course
```

### 9. TransactionFactory.php
```php
- Booking ID unik dengan format TRX + 8 karakter
- Perhitungan pajak 11% (PPN)
- 4 metode pembayaran yang realistis
- Status paid/unpaid dengan proof
- Periode berlangganan yang akurat
```

### 10. UserFactory.php (Updated)
```php
- 8 profesi yang relevan untuk platform edukasi
- Photo placeholder yang menarik
- Occupation field yang sesuai target user
```

## 🌱 Seeder Files yang Dibuat

### 1. CategorySeeder.php
- **Data**: 5 kategori utama
- Web Development, Mobile Development, Data Science, UI/UX Design, Digital Marketing

### 2. PricingSeeder.php
- **Data**: 4 paket harga
- Basic (1 bulan - Rp 99.000)
- Premium (3 bulan - Rp 249.000)
- Pro (6 bulan - Rp 449.000)
- Ultimate (12 bulan - Rp 799.000)

### 3. UserSeeder.php
- **Data**: 10 users total
- 3 instructor dengan profil profesional
- 7 student menggunakan factory
- Role assignment otomatis

### 4. CourseSeeder.php
- **Data**: 5 kursus lengkap dengan relasi
- Setiap kursus memiliki:
  - 3-5 benefits
  - 3-6 sections dengan 2-4 content per section
  - 1 mentor yang di-assign
  - 3-7 student enrollment

### 5. TransactionSeeder.php
- **Data**: 8 transaksi dengan status beragam
- Mix antara paid dan unpaid
- Periode berlangganan yang bervariasi
- Perhitungan pajak yang akurat

### 6. DatabaseSeeder.php (Updated)
- **Urutan eksekusi yang optimal**:
  1. RolePermissionSeeder (setup role)
  2. AdminSeeder (admin users)
  3. CategorySeeder (master data)
  4. PricingSeeder (master data)
  5. UserSeeder (instructors & students)
  6. CourseSeeder (courses dengan relasi)
  7. TransactionSeeder (transaksi)

## 📊 Data yang Dihasilkan

### Jumlah Record per Tabel:
- **Categories**: 5 records
- **Courses**: 5 records
- **Users**: 14 records (3 admin + 3 instructor + 7 student + 1 existing admin)
- **Transactions**: 8 records
- **Course Benefits**: 19 records (3-5 per course)
- **Course Sections**: 21 records (3-6 per course)
- **Section Contents**: 66 records (2-4 per section)
- **Course Mentors**: 5 records (1 per course)
- **Course Students**: 23 records (3-7 per course)
- **Pricings**: 4 records

## 🔧 Model Updates yang Dilakukan

Menambahkan `HasFactory` trait ke semua model:
- ✅ Category
- ✅ Course
- ✅ CourseBenefit
- ✅ CourseMentor
- ✅ CourseSection
- ✅ CourseStudent
- ✅ Pricing
- ✅ SectionContent
- ✅ Transaction
- ✅ User (sudah ada)

## 🎯 Keunggulan Implementasi

### 1. **Data Realistis**
- Nama kursus, kategori, dan benefit yang sesuai industri
- Harga yang masuk akal untuk pasar Indonesia
- Profil mentor yang profesional

### 2. **Relasi yang Kompleks**
- Foreign key constraints terjaga
- Nested relationships (Course -> Section -> Content)
- Many-to-many relationships (Course-Student)

### 3. **Variasi Data**
- Randomization yang smart untuk testing
- Mix antara data statis dan dinamis
- Status yang beragam (active/inactive, paid/unpaid)

### 4. **Performance Optimized**
- Batch creation untuk efisiensi
- Proper ordering untuk foreign keys
- Minimal database queries

### 5. **Maintainable Code**
- Clean factory definitions
- Reusable components
- Clear documentation

## 🚀 Cara Penggunaan

### Reset dan Seed Database:
```bash
php artisan migrate:fresh --seed
```

### Seed Specific Data:
```bash
php artisan db:seed --class=CategorySeeder
php artisan db:seed --class=CourseSeeder
```

### Generate Additional Data:
```php
// Di Tinker
User::factory(10)->create();
Course::factory(3)->create();
Transaction::factory(5)->paid()->create();
```

## 📋 Testing Commands

```bash
# Cek jumlah data
php artisan tinker --execute="
echo 'Categories: ' . App\Models\Category::count() . PHP_EOL;
echo 'Courses: ' . App\Models\Course::count() . PHP_EOL;
echo 'Users: ' . App\Models\User::count() . PHP_EOL;
"

# Cek relasi
php artisan tinker --execute="
\$course = App\Models\Course::with(['benefits', 'courseSections.sectionContents', 'courseMentors', 'courseStudents'])->first();
echo 'Course: ' . \$course->name . PHP_EOL;
echo 'Benefits: ' . \$course->benefits->count() . PHP_EOL;
echo 'Sections: ' . \$course->courseSections->count() . PHP_EOL;
echo 'Contents: ' . \$course->courseSections->sum(function(\$section) { return \$section->sectionContents->count(); }) . PHP_EOL;
"
```

## ✅ Validasi Berhasil

Semua factory dan seeder berhasil dijalankan tanpa error dengan data yang realistis dan relasi yang lengkap. Database siap untuk development dan testing!