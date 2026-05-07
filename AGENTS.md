# CLAUDE.md — Panduan Alur Kerja Agent

> File ini adalah **aturan wajib** yang harus diikuti oleh agent setiap kali melakukan perubahan atau penambahan pada kode, file, atau sistem apapun. Tidak ada pengecualian.

---

## 🔴 PRINSIP UTAMA

Agent **WAJIB** mengikuti alur ini secara berurutan. Melewati satu langkah pun tidak diperbolehkan, sekecil apapun perubahannya.

---

## 📋 ALUR KERJA WAJIB

### FASE 1 — PAHAMI SEBELUM BERTINDAK

#### 1.1 Baca & Analisis Konteks
- Baca seluruh file yang relevan sebelum menulis satu baris kode pun
- Pahami arsitektur yang sudah ada (struktur folder, naming convention, pola yang digunakan)
- Identifikasi dependensi yang terlibat (library, modul, API, database)
- Catat versi tools/framework yang digunakan

#### 1.2 Identifikasi Scope Perubahan
Tentukan dengan jelas:
- **Apa** yang akan diubah/ditambahkan
- **Mengapa** perubahan ini diperlukan
- **Di mana** perubahan akan berdampak (file mana, fungsi mana, komponen mana)
- **Risiko** apa yang mungkin muncul dari perubahan ini

#### 1.3 Cek Konflik Potensial
- Apakah ada fungsi/variabel dengan nama yang sama?
- Apakah perubahan ini akan merusak bagian lain yang sudah berjalan?
- Apakah ada circular dependency yang mungkin terbentuk?

---

### FASE 2 — RENCANAKAN SEBELUM EKSEKUSI

#### 2.1 Buat Rencana Tertulis
Sebelum mengeksekusi perubahan apapun, agent **harus** menyatakan rencana secara eksplisit:

```
RENCANA PERUBAHAN:
- File yang akan dimodifikasi: [list file]
- File baru yang akan dibuat: [list file]
- File yang akan dihapus: [list file]
- Urutan eksekusi: [langkah 1 → langkah 2 → ...]
- Estimasi dampak: [deskripsi dampak]
```

#### 2.2 Prioritaskan Urutan yang Aman
- Mulai dari perubahan yang paling tidak berisiko
- Buat/modifikasi helper/utility terlebih dahulu sebelum consumer-nya
- Jangan hapus sesuatu sebelum penggantinya siap

---

### FASE 3 — EKSEKUSI SECARA BERTAHAP

#### 3.1 Satu Perubahan, Satu Tujuan
- Setiap langkah eksekusi hanya boleh memiliki **satu tujuan spesifik**
- Jangan menggabungkan refactoring dengan penambahan fitur baru dalam satu langkah
- Jika perubahan besar, pecah menjadi sub-langkah yang lebih kecil

#### 3.2 Tulis Kode yang Konsisten
- Ikuti **naming convention** yang sudah ada di proyek
- Ikuti **code style** yang sudah ada (indentasi, penggunaan quotes, dll)
- Jangan memperkenalkan pola baru tanpa alasan yang jelas
- Tambahkan komentar untuk logika yang kompleks atau tidak intuitif

#### 3.3 Format Wajib untuk Setiap Perubahan File

Setiap kali memodifikasi file, nyatakan:

```
[MODIFIKASI] path/ke/file.ext
Alasan   : [kenapa file ini diubah]
Perubahan: [apa yang diubah secara spesifik]
Dampak   : [bagian lain yang mungkin terpengaruh]
```

Setiap kali membuat file baru, nyatakan:

```
[BARU] path/ke/file.ext
Tujuan   : [fungsi file ini]
Ekspor   : [apa yang diekspor/disediakan file ini]
Dependensi: [file/library yang dibutuhkan]
```

---

### FASE 4 — VERIFIKASI SETELAH EKSEKUSI

#### 4.1 Review Mandiri
Setelah setiap perubahan, agent wajib self-review:
- [ ] Apakah kode sudah sesuai dengan rencana awal?
- [ ] Apakah ada typo atau syntax error yang terlihat?
- [ ] Apakah import/export sudah benar?
- [ ] Apakah environment variable / konfigurasi sudah ditangani?

#### 4.2 Cek Konsistensi
- Pastikan tidak ada file yang tertinggal setengah jalan (half-modified)
- Pastikan tidak ada referensi ke fungsi/variabel yang sudah dihapus atau belum dibuat
- Pastikan tidak ada duplikasi logika yang tidak perlu

#### 4.3 Dokumentasikan Hasil
Di akhir setiap sesi perubahan, buat ringkasan:

```
RINGKASAN PERUBAHAN:
✅ Selesai : [daftar yang berhasil]
⚠️  Catatan : [hal yang perlu diperhatikan atau follow-up]
❌ Belum   : [jika ada yang belum selesai dan alasannya]
```

---

## 🚫 LARANGAN KERAS

Agent **DILARANG**:

1. **Menebak** — Jika tidak yakin, tanyakan atau baca dokumentasi terlebih dahulu
2. **Menghapus kode** tanpa benar-benar memahami fungsinya
3. **Mengubah lebih dari yang diminta** — Jangan "sekalian benerin" hal lain tanpa izin eksplisit
4. **Melewati verifikasi** dengan alasan "pasti sudah benar"
5. **Membuat file baru** tanpa menjelaskan mengapa file yang ada tidak bisa digunakan
6. **Copy-paste kode** tanpa memahami dan menyesuaikan konteksnya
7. **Mengabaikan error** dan melanjutkan ke langkah berikutnya

---

## ⚡ ATURAN KHUSUS PER JENIS PERUBAHAN

### Menambahkan Fitur Baru
1. Pahami bagaimana fitur serupa sudah diimplementasi di proyek
2. Tentukan di mana fitur baru ini "hidup" dalam arsitektur
3. Buat/modifikasi data model terlebih dahulu (jika ada)
4. Buat logika/service layer
5. Baru kemudian buat UI/endpoint
6. Update dokumentasi atau README jika diperlukan

### Memperbaiki Bug
1. **Reproduksi bug terlebih dahulu** — pastikan bug benar-benar ada dan pahami kondisinya
2. Identifikasi root cause, bukan hanya symptom-nya
3. Perbaiki di sumber masalah, bukan patch di permukaan
4. Pastikan perbaikan tidak membuat bug baru (regression)
5. Jelaskan mengapa solusinya benar

### Refactoring
1. Pastikan ada tes atau cara verifikasi bahwa perilaku tidak berubah
2. Refactor satu hal dalam satu waktu (satu fungsi, satu modul)
3. Jangan ubah fungsionalitas saat refactoring — hanya ubah struktur
4. Dokumentasikan alasan refactoring (bukan hanya apa yang diubah)

### Menghapus Kode / File
1. Cari semua referensi ke kode/file tersebut di seluruh proyek
2. Pastikan tidak ada yang menggunakannya (atau sudah dipindahkan)
3. Hapus referensi terlebih dahulu, baru hapus sumber
4. Konfirmasi sebelum menghapus jika ada keraguan

---

## 🗂️ STRUKTUR LAPORAN PROGRESS

Untuk perubahan yang membutuhkan beberapa langkah, gunakan format ini:

```
═══════════════════════════════════════
LANGKAH [N] dari [TOTAL]: [Judul Langkah]
═══════════════════════════════════════
STATUS  : 🔄 Sedang dikerjakan / ✅ Selesai / ❌ Gagal
TARGET  : [apa yang ingin dicapai langkah ini]
TINDAKAN: [apa yang dilakukan]
HASIL   : [apa hasilnya]
BERIKUT : [langkah apa yang akan dilakukan selanjutnya]
```

---

## 💬 KOMUNIKASI DENGAN USER

- **Selalu** beritahu user sebelum melakukan perubahan yang bersifat destruktif
- **Selalu** jelaskan trade-off jika ada lebih dari satu pendekatan
- **Selalu** minta konfirmasi jika scope perubahan lebih luas dari yang diminta
- **Jangan** buat asumsi diam-diam — nyatakan asumsi secara eksplisit
- **Jangan** gunakan jargon teknis tanpa penjelasan jika user bukan developer

---

> **Ingat**: Kecepatan bukan prioritas utama. Perubahan yang benar dan dapat dipahami jauh lebih berharga daripada perubahan yang cepat tapi rapuh.