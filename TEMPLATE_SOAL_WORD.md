# 📄 Template Format Upload Soal dari Word

## 📋 Format Standar

Buat file Word (.docx) dengan format berikut untuk setiap soal:

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
Pancasila adalah dasar negara Indonesia yang terdiri dari lima sila.
```

## 🎯 Jenis Tipe Soal

### 1. Benar/Salah (`benar_salah`)
```
[KATEGORI] TWK
[TIPE] benar_salah
[SOAL]
Pancasila adalah dasar negara Indonesia.

[A] Benar [1]
[B] Salah [0]

[JAWABAN] A
[PEMBAHASAN]
Pancasila adalah dasar negara Indonesia yang benar.
```

### 2. Pilihan Ganda Satu Jawaban (`pg_satu`)
```
[KATEGORI] TWK
[TIPE] pg_satu
[SOAL]
Siapa yang menciptakan Pancasila?

[A] Soekarno [0]
[B] Hatta [0]
[C] Soekarno dan Hatta [1]
[D] BPUPKI [0]
[E] PPKI [0]

[JAWABAN] C
[PEMBAHASAN]
Pancasila diciptakan oleh Soekarno dan Hatta.
```

### 3. Pilihan Ganda dengan Bobot (`pg_bobot`)
```
[KATEGORI] TWK
[TIPE] pg_bobot
[SOAL]
Pilih jawaban yang benar tentang Pancasila:

[A] Ideologi asing [0]
[B] Lima sila [1]
[C] Sistem partai [0]
[D] Ajaran politik [0.5]
[E] Dasar negara [1]

[JAWABAN] B,E
[PEMBAHASAN]
Pancasila adalah lima sila yang menjadi dasar negara Indonesia.
```

### 4. Pilih 2 Jawaban (`pg_pilih_2`)
```
[KATEGORI] TWK
[TIPE] pg_pilih_2
[SOAL]
Pilih 2 yang benar tentang Pancasila:

[A] Ideologi asing [0]
[B] Lima sila [1]
[C] Sistem partai [0]
[D] Ajaran politik [0]
[E] Dasar negara [1]

[JAWABAN] B,E
[PEMBAHASAN]
Pancasila adalah lima sila yang menjadi dasar negara Indonesia.
```

## ⚠️ Aturan Penting

1. **Format Harus Konsisten**
   - Setiap soal harus dimulai dengan `[KATEGORI]` dan `[TIPE]`
   - Opsi jawaban harus dalam format `[A] teks [bobot]`
   - Bobot harus berupa angka (0, 0.5, 1, dll)

2. **Bobot Jawaban**
   - `[0]` = Salah/tidak relevan
   - `[0.5]` = Setengah benar
   - `[1]` = Benar/sangat relevan

3. **Jawaban Benar**
   - Untuk `benar_salah` dan `pg_satu`: satu huruf (A, B, C, D, E)
   - Untuk `pg_bobot` dan `pg_pilih_2`: bisa multiple (B,E)

4. **Pembahasan**
   - Opsional, tapi sangat direkomendasikan
   - Hanya tersedia untuk paket VIP

## 📝 Contoh File Lengkap

```
[KATEGORI] TWK
[TIPE] pg_satu
[SOAL]
Apa kepanjangan dari NKRI?

[A] Negara Kesatuan Republik Indonesia [1]
[B] Negara Kerajaan Republik Indonesia [0]
[C] Negara Kesatuan Rakyat Indonesia [0]
[D] Negara Kerakyatan Republik Indonesia [0]
[E] Negara Kesatuan Rakyat Islam [0]

[JAWABAN] A
[PEMBAHASAN]
NKRI adalah singkatan dari Negara Kesatuan Republik Indonesia.

[KATEGORI] TIU
[TIPE] pg_bobot
[SOAL]
Pilih yang benar tentang logika:

[A] Logika adalah ilmu berpikir [1]
[B] Logika tidak penting [0]
[C] Logika hanya untuk matematika [0.5]
[D] Logika adalah filsafat [1]
[E] Logika tidak ada gunanya [0]

[JAWABAN] A,D
[PEMBAHASAN]
Logika adalah ilmu berpikir dan bagian dari filsafat.

[KATEGORI] TKP
[TIPE] benar_salah
[SOAL]
Integritas adalah kejujuran dalam bertindak.

[A] Benar [1]
[B] Salah [0]

[JAWABAN] A
[PEMBAHASAN]
Integritas memang merupakan kejujuran dalam bertindak.
```

## 🔧 Tips Upload

1. **Siapkan File Word**
   - Gunakan format .docx
   - Pastikan format konsisten
   - Test dengan 1-2 soal dulu

2. **Pilih Kategori**
   - Upload ke kategori yang sesuai
   - Pastikan kategori sudah dibuat di admin panel

3. **Verifikasi Hasil**
   - Cek soal yang terupload
   - Pastikan opsi dan bobot benar
   - Test dengan membuat tryout

## 🚨 Troubleshooting

### Error: "Format tidak sesuai"
- Pastikan format `[KATEGORI]` dan `[TIPE]` ada
- Cek format opsi `[A] teks [bobot]`
- Pastikan tidak ada karakter khusus

### Error: "Bobot tidak valid"
- Bobot harus berupa angka
- Gunakan format desimal (0.5, 1.0)
- Jangan gunakan huruf atau simbol

### Error: "Jawaban tidak ditemukan"
- Pastikan `[JAWABAN]` ada
- Format jawaban sesuai tipe soal
- Jangan ada spasi berlebih

---

**Catatan**: Template ini dapat disesuaikan dengan kebutuhan spesifik aplikasi CBT POLRI Anda. 