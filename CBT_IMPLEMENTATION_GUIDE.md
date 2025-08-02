# 🚀 Panduan Implementasi Sistem CBT POLRI

## 📋 Langkah-langkah Implementasi

### 1. Install Dependencies
```bash
composer install
```

### 2. Jalankan Migration
```bash
php artisan migrate
```

### 3. Jalankan Seeder
```bash
php artisan db:seed
```

### 4. Akses Admin Panel
- Login sebagai admin
- Akses menu: `/admin/kategori` untuk mengelola kategori soal
- Akses menu: `/admin/soal` untuk mengelola soal
- Akses menu: `/admin/tryout` untuk mengelola tryout

## 🗂️ Struktur Database yang Dibuat

### Tabel `kategori_soal`
- `id` - Primary key
- `nama` - Nama kategori (TWK, TIU, TKP, dll)
- `kode` - Kode singkat kategori
- `deskripsi` - Deskripsi kategori
- `is_active` - Status aktif/nonaktif

### Tabel `soals`
- `id` - Primary key
- `pertanyaan` - Teks pertanyaan
- `tipe` - Jenis soal (benar_salah, pg_satu, pg_bobot, pg_pilih_2)
- `kategori_id` - Foreign key ke kategori_soal
- `pembahasan` - Pembahasan soal (untuk VIP)
- `jawaban_benar` - Jawaban benar (untuk tipe benar_salah dan pg_satu)
- `is_active` - Status aktif/nonaktif

### Tabel `opsi_soal`
- `id` - Primary key
- `soal_id` - Foreign key ke soals
- `opsi` - Opsi jawaban (A, B, C, D, E)
- `teks` - Teks opsi
- `bobot` - Nilai bobot (0.00 - 1.00)

### Tabel `tryouts`
- `id` - Primary key
- `judul` - Judul tryout
- `deskripsi` - Deskripsi tryout
- `struktur` - JSON struktur soal per kategori
- `durasi_menit` - Durasi pengerjaan
- `akses_paket` - Paket yang boleh akses (free, premium, vip)
- `is_active` - Status aktif/nonaktif

### Tabel `user_tryout_soal`
- `id` - Primary key
- `user_id` - Foreign key ke users
- `tryout_id` - Foreign key ke tryouts
- `soal_id` - Foreign key ke soals
- `urutan` - Urutan soal
- `jawaban_user` - JSON jawaban user
- `skor` - Skor untuk soal ini
- `waktu_jawab` - Waktu pengerjaan (detik)
- `sudah_dijawab` - Status sudah dijawab

## 🎯 Fitur yang Tersedia

### Untuk Admin:
1. **Manajemen Kategori Soal**
   - Tambah, edit, hapus kategori
   - Toggle status aktif/nonaktif

2. **Manajemen Soal**
   - Tambah soal manual
   - Upload soal dari file Word (.docx)
   - Edit dan hapus soal
   - Lihat opsi jawaban

3. **Manajemen Tryout**
   - Buat tryout dengan struktur soal
   - Set durasi dan akses paket
   - Lihat detail tryout

### Untuk User:
1. **Akses Tryout**
   - Lihat daftar tryout sesuai paket
   - Mulai pengerjaan tryout
   - Timer otomatis

2. **Pengerjaan Soal**
   - Interface pengerjaan soal
   - Submit jawaban per soal
   - Skor otomatis

3. **Hasil Tryout**
   - Lihat skor total
   - Lihat pembahasan (VIP only)
   - Riwayat pengerjaan

## 📝 Format Upload Word

Buat file Word (.docx) dengan format:

```
[KATEGORI] TWK
[TIPE] pg_bobot
[SOAL]
Apa makna Pancasila?

[A] Ideologi asing [0]
[B] Lima sila [1]
[C] Sistem partai [0]
[D] Ajaran politik [0.5]
[E] Dasar negara [1]

[JAWABAN] B
[PEMBAHASAN]
Pancasila adalah dasar negara Indonesia.
```

## 🔧 Konfigurasi Paket

### Logika Paket Akses:
- **Free**: Akses tryout dengan `akses_paket = 'free'`
- **Premium**: Akses tryout dengan `akses_paket = 'free'` atau `'premium'`
- **VIP**: Akses semua tryout + pembahasan soal

### Update Model User:
```php
public function getPaketAksesAttribute()
{
    if (!$this->hasActiveSubscription()) {
        return 'free';
    }
    
    // Sesuaikan dengan logika bisnis Anda
    return 'premium'; // atau 'vip'
}
```

## 🎨 Customization

### 1. Tampilan
- Edit file view di `resources/views/admin/` dan `resources/views/user/`
- Gunakan Bootstrap dan Font Awesome yang sudah tersedia

### 2. Logika Scoring
- Edit method `calculateScore()` di `TryoutController`
- Sesuaikan dengan kebutuhan penilaian

### 3. Timer
- Implementasi timer JavaScript di view `user/tryout/work.blade.php`
- Auto-submit saat waktu habis

## 🚨 Troubleshooting

### 1. Migration Error
```bash
php artisan migrate:rollback
php artisan migrate
```

### 2. Composer Error
```bash
composer dump-autoload
```

### 3. Cache Error
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

## 📞 Support

Jika ada masalah atau pertanyaan, silakan hubungi tim development.

---

**Status Implementasi**: ✅ Selesai
**Versi**: 1.0.0
**Tanggal**: {{ date('Y-m-d') }} 