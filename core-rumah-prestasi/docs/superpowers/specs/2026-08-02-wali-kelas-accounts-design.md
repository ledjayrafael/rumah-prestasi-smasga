# Desain: Akun Wali Kelas per Kelas

**Tanggal:** 2026-08-02  
**Status:** Disetujui (siap rencana implementasi)

## Tujuan

Menyediakan 27 akun guru (posisi wali kelas), masing-masing di-assign ke satu kelas X A–I, XI A–I, XII A–I, agar setiap wali kelas bisa mengelola siswa kelas binaannya.

## Keputusan yang sudah disepakati

| Topik | Keputusan |
|-------|-----------|
| Email login | `namatanpaspasi@smasga.sch.id` (huruf kecil, tanpa spasi/titik gelar) |
| Password sementara | `Smasga2026` untuk akun **baru**; `must_change_password=true` |
| Akun Febri yang sudah ada | Dipakai ulang untuk X A; password **tidak** di-reset |
| Kelas existing | Rename `XI A` (id=2, grade X) → `X A` |
| Cara eksekusi | Artisan command idempotent |

## Pendekatan

**Artisan command** `php artisan school:seed-wali-kelas`

- Bisa dijalankan di local dan Hostinger
- Idempotent: aman di-re-run (match by email / nama+tingkat kelas)
- Tidak mengubah password akun guru yang sudah ada

Alternatif yang ditolak: seeder-only (terbatas env local/testing), input manual admin UI (lambat, rawan salah).

## Data mapping (27 pasangan)

### Kelas X

| Kelas | Wali Kelas | Email |
|-------|------------|-------|
| X A | Febri Hardiansah, S.Pd | febrihardiansah@smasga.sch.id |
| X B | Mohammad Supriyadi, S.Pd. | mohammadsupriyadi@smasga.sch.id |
| X C | Lyndha Maulina Dwijayanti, S.Pd, M.Pd | lyndhamaulinadwijayanti@smasga.sch.id |
| X D | Noer Akhmad Harry Wijaya, S.Pd | noerakhmadharrywijaya@smasga.sch.id |
| X E | Mohammad Abdul Azis, S.Pd | mohammadabdulazis@smasga.sch.id |
| X F | Amirah Nuraini Lianti P, S.Pd, M.Pd | amirahnurainiliantip@smasga.sch.id |
| X G | Nurdiah Okvitasari, S.Kom | nurdiahokvitasari@smasga.sch.id |
| X H | Merry Intan Permatasari, S.Pd | merryintanpermatasari@smasga.sch.id |
| X I | Leny Ocktalia, S.Pd | lenyocktalia@smasga.sch.id |

### Kelas XI

| Kelas | Wali Kelas | Email |
|-------|------------|-------|
| XI A | Junaida, S.Pd | junaida@smasga.sch.id |
| XI B | Sukendah, S.Pd | sukendah@smasga.sch.id |
| XI C | Wiwik Sudaryanti, S.Pd | wiwiksudaryanti@smasga.sch.id |
| XI D | Muzanni, S.Ag | muzanni@smasga.sch.id |
| XI E | Marseliana, S.Pd | marseliana@smasga.sch.id |
| XI F | Evin Tri Kurniawati, S.Pd., Gr. | evintrikurniawati@smasga.sch.id |
| XI G | Agung Bakti Saputra, S.Pd., Gr. | agungbaktisaputra@smasga.sch.id |
| XI H | Dwi Septian Lesmono, S.Pd., Gr. | dwiseptianlesmono@smasga.sch.id |
| XI I | Hasan Ansori, S.Pd | hasanansori@smasga.sch.id |

### Kelas XII

| Kelas | Wali Kelas | Email |
|-------|------------|-------|
| XII A | Fahmi As Shidiqi, S.Pd | fahmiasshidiqi@smasga.sch.id |
| XII B | Khairurrohman, S.Pd.I | khairurrohman@smasga.sch.id |
| XII C | Rudi Susanto, S.Psi. | rudisusanto@smasga.sch.id |
| XII D | Ika Novyati Budi Lestari, S.Pd | ikanovyatibudilestari@smasga.sch.id |
| XII E | Zamilul Mas'ad, S.Pd.I | zamilulmasad@smasga.sch.id |
| XII F | Sitti Rofiatul Holifah, S.Pd | sittirofiatulholifah@smasga.sch.id |
| XII G | Oktorica Cindra Suryanti, S.Pd | oktoricacindrasuryanti@smasga.sch.id |
| XII H | Nanang Afandi, S.Kom | nanangafandi@smasga.sch.id |
| XII I | Nico Demus, S.Pd.I | nicodemus@smasga.sch.id |

## Aturan pembuatan email

Dari nama lengkap (tanpa gelar):

1. Buang gelar: `S.Pd`, `S.Pd.`, `S.Pd.,`, `M.Pd`, `S.Kom`, `S.Ag`, `S.Psi.`, `S.Pd.I`, `Gr.`, koma, titik
2. Buang apostrof (`Mas'ad` → `masad`)
3. Lowercase, hapus spasi
4. Suffix `@smasga.sch.id`

Contoh: `Amirah Nuraini Lianti P, S.Pd, M.Pd` → `amirahnurainiliantip@smasga.sch.id`

## Perilaku command

Untuk setiap baris mapping, dalam satu DB transaction per pasangan (atau satu transaction untuk semua):

1. **Kelas:** `firstOrCreate` by `name` + `grade_level`; set `major` kosong. Khusus kasus existing: jika temukan kelas bernama `XI A` dengan `grade_level=X` dan belum ada `X A`, rename ke `X A`.
2. **Guru:** `firstOrCreate` by `email`/`username`:
   - **Baru:** set `name`, `role=guru`, `password=Smasga2026`, `must_change_password=true`, `is_active=true`
   - **Sudah ada:** update `name` jika perlu; **jangan** ubah password
3. **TeacherProfile:** pastikan `position=wali_kelas` (`subject` boleh null)
4. **Assign:**
   - `school_classes.homeroom_teacher_id` = guru.id
   - `class_teacher` pivot: `syncWithoutDetaching([guru.id])`
5. **Output:** tabel console: kelas | nama guru | email | status (created/updated/skipped)

## File yang akan disentuh

| File | Peran |
|------|-------|
| `app/Console/Commands/SeedWaliKelasCommand.php` | **BARU** — command + data mapping |
| (opsional) test feature singkat | Pastikan 27 kelas + assign homeroom benar setelah command |

Tidak mengubah UI admin, model, atau migrasi.

## Out of scope

- Reset password akun yang sudah ada
- Import siswa ke kelas
- Akun guru mapel (bukan wali kelas)
- Perubahan email domain selain `@smasga.sch.id`

## Kriteria sukses

- 27 kelas X A–I, XI A–I, XII A–I ada di DB dengan `grade_level` benar
- 27 akun guru aktif, posisi `wali_kelas`, masing-masing `homeroom_teacher_id` di kelasnya
- Febri tetap bisa login dengan password yang sudah dia punya
- Guru baru login dengan `Smasga2026` dan dipaksa ganti password
- Command idempotent: jalan kedua tidak duplikat kelas/guru
