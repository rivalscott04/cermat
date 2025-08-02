
# 📚 Sistem Computer Based Test (CBT) – Ujian Polisi

Aplikasi CBT ini dirancang untuk menyelenggarakan ujian berbasis komputer bagi peserta seleksi POLRI. Sistem mendukung berbagai jenis soal, pembobotan dinamis, dan pengelolaan tryout berdasarkan paket langganan.

---

## 🎯 Tujuan

- Memfasilitasi peserta dalam mengerjakan tryout sesuai dengan paket: Free, Premium, atau VIP.
- Memungkinkan admin mengelola soal dalam jumlah besar (500+) dari file Word secara efisien.
- Menyediakan jenis soal fleksibel: benar/salah, pilihan ganda, soal berbobot, dan multi-pilihan.

---

## 🧩 Fitur Utama

### 🧑‍🎓 Untuk Peserta
- Login & akses tryout
- Mengerjakan soal dengan timer
- Skor otomatis
- Akses pembahasan (hanya untuk VIP)

### 🧑‍💼 Untuk Admin
- Upload soal dari file Word (.docx)
- Klasifikasi soal berdasarkan kategori (TWK, TPA, Psikotes, dll)
- Menyusun tryout dari soal acak
- Monitoring hasil peserta

---

## 🧱 Struktur Sistem

### Paket Pengguna
| Paket   | Akses Tryout | Pembahasan |
|---------|---------------|------------|
| Free    | 2 tryout      | ❌ Tidak Ada |
| Premium | 20 tryout     | ❌ Tidak Ada |
| VIP     | Semua         | ✅ Tersedia |

### Tipe Soal
- `benar_salah` → 2 opsi
- `pg_satu` → A-E, 1 benar
- `pg_bobot` → A-E, semua bisa benar dengan bobot
- `pg_pilih_2` → pilih 2 dari 5 opsi

---

## 🧾 Format Upload Soal dari Word

**Contoh Format Soal Terstruktur di Word (.docx):**
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

> ⚠️ Format ini harus konsisten agar parser dapat bekerja otomatis.

---

## 📦 Struktur Tabel Utama (Database)

### `soals`
| Field         | Tipe       | Keterangan               |
|---------------|------------|--------------------------|
| `id`          | int        | ID Soal                  |
| `pertanyaan`  | text       | Isi pertanyaan           |
| `tipe`        | enum       | Jenis soal (`pg_satu`, `pg_bobot`, dll) |
| `kategori_id` | foreign key| Kategori soal            |

### `opsi_soal`
| Field       | Tipe     | Keterangan                        |
|-------------|----------|-----------------------------------|
| `soal_id`   | FK       | ID soal induk                    |
| `opsi`      | char(1)  | A, B, C, D, E                    |
| `teks`      | text     | Teks pilihan                     |
| `bobot`     | float    | Nilai bobot (default 0 atau 1)   |

### `tryouts`
| Field         | Tipe   | Keterangan                    |
|---------------|--------|-------------------------------|
| `judul`       | string | Nama tryout                  |
| `struktur`    | JSON   | Jumlah soal per kategori     |
| `durasi_menit`| int    | Waktu pengerjaan             |
| `akses_paket` | string | Paket yang boleh akses       |

---

## 🔄 Alur Pengerjaan Tryout

1. Peserta login dan memilih tryout
2. Sistem mengambil soal acak berdasarkan struktur tryout:
   ```json
   {
     "TWK": 10,
     "TPA": 10,
     "Psikotes": 5
   }
   ```
3. Soal disimpan ke `user_tryout_soal` agar konsisten
4. Peserta menjawab → sistem nilai otomatis
5. Jika VIP, peserta melihat pembahasan

---

## 🔧 Tools & Teknologi

- **Laravel** untuk backend
- **Blade / Vue** untuk frontend
- **phpoffice/phpword** untuk parsing Word
- **MySQL** sebagai basis data

---

## 🚀 Langkah Pengembangan Selanjutnya

1. 🔲 Buat template Word standar (untuk input massal soal)
2. 🔲 Buat parser Word → array soal di Laravel
3. 🔲 Simpan soal + opsi + bobot + pembahasan ke DB
4. 🔲 Buat fitur builder tryout dari soal acak berdasarkan kategori
5. 🔲 Buat antarmuka peserta + admin panel

---

## 📁 Direktori Penting (Laravel)

```
app/
├── Http/Controllers/
│   ├── TryoutController.php
│   ├── SoalController.php
│   └── UploadController.php
├── Models/
│   ├── Soal.php
│   ├── OpsiSoal.php
│   ├── Tryout.php
│   ├── KategoriSoal.php
```

---

## 🤝 Kontribusi & Lisensi

Proyek ini bersifat tertutup (private) dan digunakan untuk internal keperluan ujian berbasis CBT POLRI. Semua kode, template, dan struktur dapat dikembangkan sesuai kebutuhan dinas atau lembaga yang ditunjuk.
