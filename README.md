## Grid Tour Creator

Pembuat kolase grid 2x3 untuk konten tour/bracket yang simpel dan cepat. Anda bisa mengunggah 6 gambar, memberi label tengah (R1–R3), menambahkan nama kiri/kanan, memilih latar, mengatur ukuran kanvas, lalu mengunduh PNG secara langsung (client-side) atau merender via server (PHP GD).

### Fitur
- **Editor visual**: 6 slot gambar (2 kolom × 3 baris) dengan sudut membulat
- **Drag & drop** antar slot untuk menyusun ulang
- **Label tengah**: R1, R2, R3 berada di garis tengah grid
- **Nama di sudut**: kiri dan kanan, tebal dengan outline agar terbaca
- **Warna latar**: pilih bebas via color picker
- **Ukuran kanvas**: 1080×1920, 1440×2160, 2048×3072
- **Unduh PNG (client-side)** atau **Render Server** (GD + TTF)

### Teknologi
- PHP CodeIgniter 3 (tanpa build step)
- TailwindCSS via CDN untuk gaya
- Canvas API untuk preview sisi klien
- PHP GD + FreeType untuk rendering server

### Persyaratan
- PHP 5.6+ (disarankan versi terbaru yang didukung)
- Ekstensi PHP: `gd` (dengan FreeType untuk TTF)
- Web server (Apache/Nginx). Contoh di bawah menggunakan Apache (Laragon)

### Struktur Proyek
- `application/controllers/Collage.php`: Controller utama (editor + render)
- `application/views/collage_editor.php`: Tampilan editor
- `application/config/routes.php`: Routing (`collage`, `collage/generate`)
- `index.php`: Bootstrap CodeIgniter

### Menjalankan Secara Lokal (Laragon/Apache)
1. Clone atau salin proyek ini ke webroot, misalnya `C:\laragon\www\grid-tour-creator`.
2. Buat virtual host (Laragon biasanya otomatis) ke `http://grid-tour-creator.test`.
3. Setel `base_url` di `application/config/config.php`:
   ```php
   $config['base_url'] = 'http://grid-tour-creator.test';
   $config['index_page'] = 'index.php'; // default repo ini menggunakan index.php di URL
   ```
4. Pastikan ekstensi `gd` aktif dan FreeType tersedia.

### Cara Pakai
1. Buka editor:
   - Dengan pengaturan default: `http://grid-tour-creator.test/index.php/collage`
   - Jika sudah menghapus `index.php` dari URL (lihat bagian Opsional): `http://grid-tour-creator.test/collage`
2. Klik setiap tile untuk unggah gambar. Drag & drop untuk tukar slot.
3. Isi nama kiri/kanan, ubah label R1–R3, pilih warna latar dan ukuran.
4. Klik "Unduh PNG" untuk menyimpan hasil dari canvas (client-side), atau klik "Render Server" untuk hasil dari server (GD).

### Endpoint
- `GET /collage` → Editor (HTML)
- `POST /collage/generate` → Mengembalikan `image/png`
  - Form-data yang didukung:
    - `img1`..`img6`: file gambar (opsional, isi yang ada saja)
    - `width`, `height`: ukuran kanvas (misal `1440`, `2160`)
    - `bg`: warna latar hex (misal `#111827`)
    - `leftName`, `rightName`: teks nama
    - `r1`, `r2`, `r3`: label tengah

Contoh cURL (default masih menyertakan `index.php` di URL):
```bash
curl -X POST \
  -F "img1=@C:/path/to/a.jpg" \
  -F "img2=@C:/path/to/b.jpg" \
  -F "width=1440" -F "height=2160" \
  -F "bg=#111827" \
  -F "leftName=Player 1" -F "rightName=Player 2" \
  -F "r1=R1" -F "r2=R2" -F "r3=R3" \
  http://grid-tour-creator.test/index.php/collage/generate \
  --output collage.png
```

### Opsional: Hilangkan `index.php` dari URL
1. Aktifkan `mod_rewrite` (Apache) dan buat `.htaccess` di root publik (direktori yang sama dengan `index.php`):
   ```apache
   <IfModule mod_rewrite.c>
   RewriteEngine On
   RewriteBase /
   RewriteCond %{REQUEST_FILENAME} !-f
   RewriteCond %{REQUEST_FILENAME} !-d
   RewriteRule ^(.*)$ index.php/$1 [L]
   </IfModule>
   ```
2. Ubah `application/config/config.php`:
   ```php
   $config['index_page'] = '';
   ```
Sesudah itu, akses menjadi `http://grid-tour-creator.test/collage` dan `POST` ke `/collage/generate`.

### Keamanan & Catatan
- Header `X-Frame-Options: SAMEORIGIN` disetel di controller.
- CSRF default nonaktif (`$config['csrf_protection'] = FALSE`); jika Anda aktifkan, sesuaikan request klien.
- Hanya tipe gambar umum yang diproses saat render server (JPEG/PNG/GIF).

### Lisensi
Proyek ini mengikutsertakan CodeIgniter 3 berlisensi MIT. Lihat `license.txt`.

### Kredit
- Framework: CodeIgniter 3
- UI: TailwindCSS (CDN)


