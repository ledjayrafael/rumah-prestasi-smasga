# Redesain Popup Preview Berkas Bukti Prestasi (Siswa)

**Tanggal:** 2026-08-07
**Lokasi:** `resources/views/siswa/achievements/show.blade.php` (halaman `siswa/prestasi/{id}`)
**Status:** Disetujui, menunggu implementasi

## Konteks

Halaman detail prestasi siswa menampilkan daftar "Berkas Bukti". Saat ini, klik pada
salah satu berkas (gambar atau PDF) membuka popup preview: modal `fixed inset-0`
di tengah layar (`bg-navy-950/80 backdrop-blur-sm`), berisi `<img>` untuk gambar atau
`<iframe>` untuk PDF, plus tombol download dan close. Fungsinya sudah benar, tapi
tampilannya generik (kotak statis di tengah, tanpa animasi) dan kurang selaras dengan
gaya mobile-app halaman siswa (bottom nav + FAB).

Redesain ini mengganti popup tersebut menjadi **bottom sheet** — pola yang lebih
natural untuk layout mobile-first — tanpa mengubah fungsi inti: klik berkas bukti →
muncul pop-up → ada tombol download dan tombol close.

## Keputusan Desain

| Aspek | Keputusan |
|---|---|
| Layout | Bottom sheet (meluncur dari bawah), bukan dialog di tengah atau fullscreen lightbox |
| Tone warna | Gelap penuh (`bg-navy-950`), teks putih — media jadi fokus, kontras tinggi |
| Cara tutup | Tap backdrop, tombol close, atau Escape. **Tidak ada** swipe/drag-to-dismiss |
| Animasi | Slide-up + fade saat buka; kebalikannya saat tutup |
| Navigasi antar-berkas | Tidak ada (di luar cakupan) |
| Zoom/pinch pada gambar | Tidak ada (di luar cakupan) |

Alasan menolak alternatif:
- **Centered dialog (dipercantik saja)**: minim risiko tapi tidak menyelesaikan
  ketidaksesuaian dengan pola mobile-app yang sudah dipakai di halaman ini.
- **Fullscreen lightbox**: terlalu "berat" untuk PDF (yang sudah punya scroll/zoom
  sendiri di dalam iframe), dan tidak konsisten dengan pola modal lain di app
  (`logout-confirm-modal.blade.php` memakai kotak, bukan fullscreen).

## Struktur Visual & Interaksi

- **Trigger**: daftar "Berkas Bukti" (baris putih dengan thumbnail/ikon) **tidak diubah**.
- **Sheet**: lebar penuh, menempel di bawah viewport, sudut atas membulat
  (`rounded-t-3xl`), background `bg-navy-950`.
- **Backdrop**: `bg-navy-950/70 backdrop-blur-sm`, menutupi seluruh layar di belakang sheet.
- **Header dalam sheet**: baris atas — nama file (truncate) di kiri; dua tombol bundar
  translucent (`bg-white/10`) di kanan — download lalu close. Konsisten dengan tombol
  yang sudah ada di implementasi sekarang.
- **Konten media**:
  - Gambar: `object-contain`, tinggi maksimum ~`75vh`, dipusatkan secara horizontal.
  - PDF/file lain: `<iframe>` mengisi sisa tinggi sheet (~`80vh`) untuk kenyamanan
    scroll dokumen.
- **Animasi**:
  - Buka: backdrop fade-in (~200ms); panel slide-up dari `translateY(100%)` ke `0`
    (~250ms, ease-out).
  - Tutup: kebalikan animasi buka, baru kemudian elemen di-`hidden`-kan (via
    `transitionend` atau fallback timeout) — supaya tidak "kedip" karena disembunyikan
    sebelum animasi tuntas.
- **Tutup**: klik backdrop, klik tombol close, atau tombol Escape (desktop). Tidak ada
  gestur swipe.

## Implementasi Teknis

- Tanpa dependency baru — tetap vanilla JS + Tailwind, tidak menambah library animasi.
- Toggle sheet 2 tahap agar transisi CSS berjalan mulus:
  1. **Buka**: hapus `hidden`, tambah `flex` pada wrapper backdrop → pada frame
     berikutnya (`requestAnimationFrame`) tambahkan class "in" (backdrop → opacity
     penuh, panel → `translate-y-0`). State awal: panel `translate-y-full`, backdrop
     `opacity-0`.
  2. **Tutup**: hapus class "in" (panel balik ke `translate-y-full`, backdrop kembali
     `opacity-0`), lalu setelah `transitionend` (atau fallback `setTimeout` ~250ms)
     tambahkan `hidden` dan reset `src` gambar/iframe.
- Struktur blade: markup modal lama diganti markup sheet baru, tapi `id` elemen kunci
  dipertahankan (`image-preview-panel`, `image-preview-name`, `image-preview-download`,
  `image-preview-close`, `image-preview-img`, `image-preview-frame`) — logic pemilihan
  gambar-vs-file lewat `data-preview-type` disesuaikan, tidak ditulis ulang dari nol.
- **Tidak ada perubahan** pada `AchievementFileController` — `Content-Disposition: inline`
  yang sudah dibenahi sebelumnya tetap dipakai apa adanya.
- **Rebuild aset wajib**: class Tailwind baru (`translate-y-full`, `rounded-t-3xl`,
  `duration-200`/`duration-250`, dll.) belum ada di `public_html/rumah-prestasi/build/`.
  Setelah implementasi, jalankan `npm run build` dan commit hasil build sebelum push,
  atau frame tetap tidak akan tampak benar di production (masalah yang sama seperti
  sebelumnya).

## Testing

Tidak ada test otomatis untuk Blade view ini. Verifikasi tetap manual via Claude in
Chrome: login sebagai siswa (production, karena `APP_URL` mengarah ke domain
production sehingga login lokal ikut redirect ke sana), klik berkas gambar dan
berkas PDF, cek:
- Animasi buka (slide-up + fade) dan tutup berjalan mulus, tidak "kedip".
- Tombol download mengarah ke file yang benar dan memicu unduhan (bukan pratinjau).
- Tombol close, tap-backdrop, dan Escape semuanya menutup sheet.
- Tampilan pada iPhone-width viewport (halaman ini mobile-first).
