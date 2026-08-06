# Wali Kelas Accounts Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Seed 27 akun wali kelas dan assign masing-masing ke kelas X A–I, XI A–I, XII A–I via Artisan command idempotent.

**Architecture:** Satu Artisan command menyimpan mapping hardcode (nama kelas, grade, nama guru, email). Per baris: upsert kelas, upsert user guru + teacher profile `wali_kelas`, set `homeroom_teacher_id` dan pivot `class_teacher`. Password hanya di-set untuk akun baru.

**Tech Stack:** Laravel Artisan Command, Eloquent, Pest/PHPUnit feature tests dengan `RefreshDatabase`.

**Spec:** [`docs/superpowers/specs/2026-08-02-wali-kelas-accounts-design.md`](../specs/2026-08-02-wali-kelas-accounts-design.md)

## Global Constraints

- Email domain: `@smasga.sch.id` (huruf kecil, tanpa spasi/gelar) — pakai email hardcode dari spec, jangan generate ulang
- Password sementara akun **baru** saja: `Smasga2026`, `must_change_password=true`
- Akun yang sudah ada (termasuk Febri): **jangan** ubah password
- Rename kelas misnamed: `XI A` + `grade_level=X` → `X A` jika `X A` belum ada
- Idempotent: re-run tidak duplikat kelas/guru
- Tidak ubah UI admin, migrasi, atau model

---

## File Map

| File | Role |
|------|------|
| [`app/Console/Commands/SeedWaliKelasCommand.php`](../../../app/Console/Commands/SeedWaliKelasCommand.php) | **BARU** — signature `school:seed-wali-kelas`, mapping 27 baris, logic upsert |
| [`tests/Feature/SeedWaliKelasCommandTest.php`](../../../tests/Feature/SeedWaliKelasCommandTest.php) | **BARU** — assert 27 kelas, assign, password baru, idempotent, rename |

---

### Task 1: Feature test (failing first)

**Files:**
- Create: `tests/Feature/SeedWaliKelasCommandTest.php`

**Interfaces:**
- Consumes: `php artisan school:seed-wali-kelas` (belum ada — test harus FAIL dulu)
- Produces: kontrak perilaku yang Task 2 harus penuhi

- [ ] **Step 1: Write the failing test**

```php
<?php

namespace Tests\Feature;

use App\Enums\TeacherPosition;
use App\Enums\UserRole;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SeedWaliKelasCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_creates_27_classes_and_assigns_wali_kelas(): void
    {
        $this->artisan('school:seed-wali-kelas')->assertSuccessful();

        $this->assertSame(27, SchoolClass::count());
        $this->assertSame(27, User::where('role', UserRole::Guru)->count());

        $xa = SchoolClass::where('name', 'X A')->where('grade_level', 'X')->firstOrFail();
        $febri = User::where('email', 'febrihardiansah@smasga.sch.id')->firstOrFail();
        $this->assertSame($febri->id, $xa->homeroom_teacher_id);
        $this->assertTrue($febri->isWaliKelas());
        $this->assertTrue($febri->taughtClasses->contains('id', $xa->id));

        $xiiI = SchoolClass::where('name', 'XII I')->where('grade_level', 'XII')->firstOrFail();
        $nico = User::where('email', 'nicodemus@smasga.sch.id')->firstOrFail();
        $this->assertSame($nico->id, $xiiI->homeroom_teacher_id);
        $this->assertTrue($nico->must_change_password);
        $this->assertTrue(Hash::check('Smasga2026', $nico->password));
    }

    public function test_command_renames_misnamed_xi_a_grade_x_to_x_a(): void
    {
        $misnamed = SchoolClass::create([
            'name' => 'XI A',
            'grade_level' => 'X',
            'major' => '',
        ]);

        $existing = User::create([
            'name' => 'Febri Hardiansah, S.Pd',
            'username' => 'febrihardiansah@smasga.sch.id',
            'email' => 'febrihardiansah@smasga.sch.id',
            'role' => UserRole::Guru,
            'password' => 'password-lama-febri',
            'must_change_password' => false,
        ]);
        $existing->teacherProfile()->create([
            'position' => TeacherPosition::WaliKelas,
        ]);
        $misnamed->update(['homeroom_teacher_id' => $existing->id]);
        $existing->taughtClasses()->attach($misnamed->id);

        $this->artisan('school:seed-wali-kelas')->assertSuccessful();

        $this->assertNull(SchoolClass::where('name', 'XI A')->where('grade_level', 'X')->first());
        $xa = SchoolClass::where('name', 'X A')->where('grade_level', 'X')->firstOrFail();
        $this->assertSame($misnamed->id, $xa->id);
        $this->assertSame($existing->id, $xa->homeroom_teacher_id);

        $febri = $existing->fresh();
        $this->assertTrue(Hash::check('password-lama-febri', $febri->password));
        $this->assertFalse($febri->must_change_password);
    }

    public function test_command_is_idempotent(): void
    {
        $this->artisan('school:seed-wali-kelas')->assertSuccessful();
        $this->artisan('school:seed-wali-kelas')->assertSuccessful();

        $this->assertSame(27, SchoolClass::count());
        $this->assertSame(27, User::where('email', 'like', '%@smasga.sch.id')->where('role', UserRole::Guru)->count());
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=SeedWaliKelasCommandTest`

Expected: FAIL (command `school:seed-wali-kelas` not defined)

- [ ] **Step 3: Commit**

```bash
git add tests/Feature/SeedWaliKelasCommandTest.php
git commit -m "$(cat <<'EOF'
Add failing tests for seed wali kelas command.

EOF
)"
```

---

### Task 2: Implement `SeedWaliKelasCommand`

**Files:**
- Create: `app/Console/Commands/SeedWaliKelasCommand.php`
- Test: `tests/Feature/SeedWaliKelasCommandTest.php`

**Interfaces:**
- Consumes: `User`, `SchoolClass`, `TeacherPosition`, `UserRole`, mapping dari spec
- Produces: exit code `self::SUCCESS`; side effects di DB sesuai kriteria sukses spec

- [ ] **Step 1: Create the command file**

```php
<?php

namespace App\Console\Commands;

use App\Enums\TeacherPosition;
use App\Enums\UserRole;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SeedWaliKelasCommand extends Command
{
    protected $signature = 'school:seed-wali-kelas';

    protected $description = 'Buat/perbarui 27 akun wali kelas dan assign ke kelas X–XII A–I';

    /** @return list<array{class: string, grade: string, name: string, email: string}> */
    private function mappings(): array
    {
        return [
            ['class' => 'X A', 'grade' => 'X', 'name' => 'Febri Hardiansah, S.Pd', 'email' => 'febrihardiansah@smasga.sch.id'],
            ['class' => 'X B', 'grade' => 'X', 'name' => 'Mohammad Supriyadi, S.Pd.', 'email' => 'mohammadsupriyadi@smasga.sch.id'],
            ['class' => 'X C', 'grade' => 'X', 'name' => 'Lyndha Maulina Dwijayanti, S.Pd, M.Pd', 'email' => 'lyndhamaulinadwijayanti@smasga.sch.id'],
            ['class' => 'X D', 'grade' => 'X', 'name' => 'Noer Akhmad Harry Wijaya, S.Pd', 'email' => 'noerakhmadharrywijaya@smasga.sch.id'],
            ['class' => 'X E', 'grade' => 'X', 'name' => 'Mohammad Abdul Azis, S.Pd', 'email' => 'mohammadabdulazis@smasga.sch.id'],
            ['class' => 'X F', 'grade' => 'X', 'name' => 'Amirah Nuraini Lianti P, S.Pd, M.Pd', 'email' => 'amirahnurainiliantip@smasga.sch.id'],
            ['class' => 'X G', 'grade' => 'X', 'name' => 'Nurdiah Okvitasari, S.Kom', 'email' => 'nurdiahokvitasari@smasga.sch.id'],
            ['class' => 'X H', 'grade' => 'X', 'name' => 'Merry Intan Permatasari, S.Pd', 'email' => 'merryintanpermatasari@smasga.sch.id'],
            ['class' => 'X I', 'grade' => 'X', 'name' => 'Leny Ocktalia, S.Pd', 'email' => 'lenyocktalia@smasga.sch.id'],
            ['class' => 'XI A', 'grade' => 'XI', 'name' => 'Junaida, S.Pd', 'email' => 'junaida@smasga.sch.id'],
            ['class' => 'XI B', 'grade' => 'XI', 'name' => 'Sukendah, S.Pd', 'email' => 'sukendah@smasga.sch.id'],
            ['class' => 'XI C', 'grade' => 'XI', 'name' => 'Wiwik Sudaryanti, S.Pd', 'email' => 'wiwiksudaryanti@smasga.sch.id'],
            ['class' => 'XI D', 'grade' => 'XI', 'name' => 'Muzanni, S.Ag', 'email' => 'muzanni@smasga.sch.id'],
            ['class' => 'XI E', 'grade' => 'XI', 'name' => 'Marseliana, S.Pd', 'email' => 'marseliana@smasga.sch.id'],
            ['class' => 'XI F', 'grade' => 'XI', 'name' => 'Evin Tri Kurniawati, S.Pd., Gr.', 'email' => 'evintrikurniawati@smasga.sch.id'],
            ['class' => 'XI G', 'grade' => 'XI', 'name' => 'Agung Bakti Saputra, S.Pd., Gr.', 'email' => 'agungbaktisaputra@smasga.sch.id'],
            ['class' => 'XI H', 'grade' => 'XI', 'name' => 'Dwi Septian Lesmono, S.Pd., Gr.', 'email' => 'dwiseptianlesmono@smasga.sch.id'],
            ['class' => 'XI I', 'grade' => 'XI', 'name' => 'Hasan Ansori, S.Pd', 'email' => 'hasanansori@smasga.sch.id'],
            ['class' => 'XII A', 'grade' => 'XII', 'name' => 'Fahmi As Shidiqi, S.Pd', 'email' => 'fahmiasshidiqi@smasga.sch.id'],
            ['class' => 'XII B', 'grade' => 'XII', 'name' => 'Khairurrohman, S.Pd.I', 'email' => 'khairurrohman@smasga.sch.id'],
            ['class' => 'XII C', 'grade' => 'XII', 'name' => 'Rudi Susanto, S.Psi.', 'email' => 'rudisusanto@smasga.sch.id'],
            ['class' => 'XII D', 'grade' => 'XII', 'name' => 'Ika Novyati Budi Lestari, S.Pd', 'email' => 'ikanovyatibudilestari@smasga.sch.id'],
            ['class' => 'XII E', 'grade' => 'XII', 'name' => "Zamilul Mas'ad, S.Pd.I", 'email' => 'zamilulmasad@smasga.sch.id'],
            ['class' => 'XII F', 'grade' => 'XII', 'name' => 'Sitti Rofiatul Holifah, S.Pd', 'email' => 'sittirofiatulholifah@smasga.sch.id'],
            ['class' => 'XII G', 'grade' => 'XII', 'name' => 'Oktorica Cindra Suryanti, S.Pd', 'email' => 'oktoricacindrasuryanti@smasga.sch.id'],
            ['class' => 'XII H', 'grade' => 'XII', 'name' => 'Nanang Afandi, S.Kom', 'email' => 'nanangafandi@smasga.sch.id'],
            ['class' => 'XII I', 'grade' => 'XII', 'name' => 'Nico Demus, S.Pd.I', 'email' => 'nicodemus@smasga.sch.id'],
        ];
    }

    public function handle(): int
    {
        $this->fixMisnamedXa();

        $rows = [];

        DB::transaction(function () use (&$rows) {
            foreach ($this->mappings() as $map) {
                $rows[] = $this->upsertPair($map);
            }
        });

        $this->table(['Kelas', 'Guru', 'Email', 'Status'], $rows);

        return self::SUCCESS;
    }

    private function fixMisnamedXa(): void
    {
        $existsXa = SchoolClass::query()
            ->where('name', 'X A')
            ->where('grade_level', 'X')
            ->exists();

        if ($existsXa) {
            return;
        }

        $misnamed = SchoolClass::query()
            ->where('name', 'XI A')
            ->where('grade_level', 'X')
            ->first();

        if ($misnamed) {
            $misnamed->update(['name' => 'X A']);
            $this->info("Renamed kelas #{$misnamed->id} XI A (grade X) → X A");
        }
    }

    /**
     * @param  array{class: string, grade: string, name: string, email: string}  $map
     * @return list<string>
     */
    private function upsertPair(array $map): array
    {
        $class = SchoolClass::query()->firstOrCreate(
            ['name' => $map['class'], 'grade_level' => $map['grade']],
            ['major' => '']
        );

        $teacher = User::query()->where('email', $map['email'])->first();
        $status = 'updated';

        if (! $teacher) {
            $teacher = User::create([
                'name' => $map['name'],
                'username' => $map['email'],
                'email' => $map['email'],
                'role' => UserRole::Guru,
                'password' => 'Smasga2026',
                'must_change_password' => true,
                'is_active' => true,
            ]);
            $status = 'created';
        } else {
            $teacher->update([
                'name' => $map['name'],
                'username' => $map['email'],
                'role' => UserRole::Guru,
                'is_active' => true,
            ]);
        }

        $teacher->teacherProfile()->updateOrCreate(
            ['user_id' => $teacher->id],
            ['position' => TeacherPosition::WaliKelas]
        );

        $class->update(['homeroom_teacher_id' => $teacher->id]);
        $class->teachers()->syncWithoutDetaching([$teacher->id]);

        return [$map['class'], $map['name'], $map['email'], $status];
    }
}
```

Laravel auto-discovers commands di `app/Console/Commands` — tidak perlu daftar manual.

- [ ] **Step 2: Run tests to verify they pass**

Run: `php artisan test --filter=SeedWaliKelasCommandTest`

Expected: PASS (3 tests)

- [ ] **Step 3: Commit**

```bash
git add app/Console/Commands/SeedWaliKelasCommand.php tests/Feature/SeedWaliKelasCommandTest.php
git commit -m "$(cat <<'EOF'
Add artisan command to seed 27 wali kelas accounts.

EOF
)"
```

---

### Task 3: Jalankan di database local

**Files:**
- Tidak ada file baru — eksekusi terhadap DB `portal_prestasi_sman1`

**Interfaces:**
- Consumes: command dari Task 2
- Produces: 27 kelas + assign di DB local siap dipakai

- [ ] **Step 1: Run the command**

```bash
php artisan school:seed-wali-kelas
```

Expected: tabel 27 baris; pesan rename untuk kelas id=2; exit 0

- [ ] **Step 2: Verify counts**

```bash
php artisan tinker --execute="
echo 'kelas='.App\Models\SchoolClass::count().PHP_EOL;
echo 'guru_smasga='.App\Models\User::where('role','guru')->where('email','like','%@smasga.sch.id')->count().PHP_EOL;
App\Models\SchoolClass::with('homeroomTeacher')->orderBy('grade_level')->orderBy('name')->get()->each(fn(\$c)=>print(\$c->name.' | '.(\$c->homeroomTeacher?->name??'-').PHP_EOL));
"
```

Expected: `kelas=27`, `guru_smasga=27`, setiap kelas punya wali

- [ ] **Step 3: Confirm Febri password untouched**

```bash
php artisan tinker --execute="
\$u=App\Models\User::where('email','febrihardiansah@smasga.sch.id')->first();
echo 'must_change='.(\$u->must_change_password?'yes':'no').PHP_EOL;
echo 'hash_smasga2026='.(Illuminate\Support\Facades\Hash::check('Smasga2026', \$u->password)?'yes':'no').PHP_EOL;
"
```

Expected: `must_change` mengikuti nilai sebelum seed (biasanya `no` jika sudah ganti); `hash_smasga2026=no` jika password lama bukan `Smasga2026`

- [ ] **Step 4: No extra commit** (data-only; command sudah di-commit di Task 2)

---

## Deployment note (Hostinger)

Setelah deploy file command:

```bash
php artisan school:seed-wali-kelas
```

**File yang perlu di-upload:** `app/Console/Commands/SeedWaliKelasCommand.php` (plus test jika ingin, tidak wajib di production).
