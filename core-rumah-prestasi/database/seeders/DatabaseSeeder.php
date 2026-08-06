<?php

namespace Database\Seeders;

use App\Enums\AchievementCategory;
use App\Enums\AchievementLevel;
use App\Models\Competition;
use App\Models\SchoolClass;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Password sementara untuk seluruh akun hasil seeding. Di local, setiap akun
     * dipaksa ganti password saat login pertama. Di testing dibiarkan false agar
     * feature test bisa mengakses route tanpa redirect force-password.
     */
    private const TEMP_PASSWORD = 'smasga2026';

    private function mustChangePassword(): bool
    {
        return ! app()->environment('testing');
    }

    /**
     * Seed data akun nyata sekolah (admin, developer, wali kelas per rombel, dan
     * siswa kelas X A) agar bisa dipakai untuk setup ulang environment
     * local/testing/development. Diblokir di production.
     */
    public function run(): void
    {
        // Local/testing/development only — never auto-seed production.
        if (! app()->environment('local', 'testing', 'development')) {
            return;
        }

        $admin = User::create([
            'name' => 'M. Aziz',
            'username' => 'admin@smasga.sch.id',
            'email' => 'admin@smasga.sch.id',
            'role' => UserRole::Admin,
            'password' => self::TEMP_PASSWORD,
            'must_change_password' => $this->mustChangePassword(),
        ]);

        User::create([
            'name' => 'Developer SMASGA',
            'username' => 'developer@smasga.sch.id',
            'email' => 'developer@smasga.sch.id',
            'role' => UserRole::Developer,
            'password' => self::TEMP_PASSWORD,
            'must_change_password' => $this->mustChangePassword(),
        ]);

        // name => [grade_level, homeroom teacher name, teacher email local-part]
        $classesWithHomeroom = [
            ['X A', 'X', 'Febri Hardiansah, S.Pd', 'febrihardiansah'],
            ['X B', 'X', 'Mohammad Supriyadi, S.Pd.', 'mohammadsupriyadi'],
            ['X C', 'X', 'Lyndha Maulina Dwijayanti, S.Pd, M.Pd', 'lyndhamaulinadwijayanti'],
            ['X D', 'X', 'Noer Akhmad Harry Wijaya, S.Pd', 'noerakhmadharrywijaya'],
            ['X E', 'X', 'Mohammad Abdul Azis, S.Pd', 'mohammadabdulazis'],
            ['X F', 'X', 'Amirah Nuraini Lianti P, S.Pd, M.Pd', 'amirahnurainiliantip'],
            ['X G', 'X', 'Nurdiah Okvitasari, S.Kom', 'nurdiahokvitasari'],
            ['X H', 'X', 'Merry Intan Permatasari, S.Pd', 'merryintanpermatasari'],
            ['X I', 'X', 'Leny Ocktalia, S.Pd', 'lenyocktalia'],
            ['XI A', 'XI', 'Junaida, S.Pd', 'junaida'],
            ['XI B', 'XI', 'Sukendah, S.Pd', 'sukendah'],
            ['XI C', 'XI', 'Wiwik Sudaryanti, S.Pd', 'wiwiksudaryanti'],
            ['XI D', 'XI', 'Muzanni, S.Ag', 'muzanni'],
            ['XI E', 'XI', 'Marseliana, S.Pd', 'marseliana'],
            ['XI F', 'XI', 'Evin Tri Kurniawati, S.Pd., Gr.', 'evintrikurniawati'],
            ['XI G', 'XI', 'Agung Bakti Saputra, S.Pd., Gr.', 'agungbaktisaputra'],
            ['XI H', 'XI', 'Dwi Septian Lesmono, S.Pd., Gr.', 'dwiseptianlesmono'],
            ['XI I', 'XI', 'Hasan Ansori, S.Pd', 'hasanansori'],
            ['XII A', 'XII', 'Fahmi As Shidiqi, S.Pd', 'fahmiasshidiqi'],
            ['XII B', 'XII', 'Khairurrohman, S.Pd.I', 'khairurrohman'],
            ['XII C', 'XII', 'Rudi Susanto, S.Psi.', 'rudisusanto'],
            ['XII D', 'XII', 'Ika Novyati Budi Lestari, S.Pd', 'ikanovyatibudilestari'],
            ['XII E', 'XII', "Zamilul Mas'ad, S.Pd.I", 'zamilulmasad'],
            ['XII F', 'XII', 'Sitti Rofiatul Holifah, S.Pd', 'sittirofiatulholifah'],
            ['XII G', 'XII', 'Oktorica Cindra Suryanti, S.Pd', 'oktoricacindrasuryanti'],
            ['XII H', 'XII', 'Nanang Afandi, S.Kom', 'nanangafandi'],
            ['XII I', 'XII', 'Nico Demus, S.Pd.I', 'nicodemus'],
        ];

        $kelasXA = null;

        foreach ($classesWithHomeroom as [$name, $gradeLevel, $teacherName, $emailLocalPart]) {
            $email = "{$emailLocalPart}@smasga.sch.id";

            $guru = User::create([
                'name' => $teacherName,
                'username' => $email,
                'email' => $email,
                'role' => UserRole::Guru,
                'password' => self::TEMP_PASSWORD,
                'must_change_password' => $this->mustChangePassword(),
            ]);

            $guru->teacherProfile()->create();

            $kelas = SchoolClass::create([
                'name' => $name,
                'grade_level' => $gradeLevel,
                'homeroom_teacher_id' => $guru->id,
            ]);

            $guru->taughtClasses()->attach($kelas->id);

            if ($name === 'X A') {
                $kelasXA = $kelas;
            }
        }

        // Siswa kelas X A.
        $siswaXA = [
            ['12874', 'ACHMAD GIOVANO FANDIANSYAH'],
            ['12885', 'AHNAF PUTRA KOERNIAWAN'],
            ['12890', 'AISYAH RIZKI ZAHIROH'],
            ['12895', 'ALENA NURDIYANTI'],
            ['12896', 'ALFATH NIZAR RAMADHAN'],
            ['12907', 'ANINDYA KAMILA KHAIRUNNISSA'],
            ['12912', 'ARFAINES ULFIYAH CAHYANA'],
            ['12923', 'AURELLIA DWI MARTINE'],
            ['12930', 'BAGOES FAJHRI RAMADHANI'],
            ['12932', 'BARIQ BINTANG SETYANSYAH'],
            ['12960', 'DISTA HAURA SYAFIRA'],
            ['12974', 'FARAH RISQI FELISA'],
            ['12981', 'FELIZIA YOFIKA AUBIL'],
            ['12993', 'GLADYS VIOLETTA JASMINE'],
            ['12998', 'INTAN FEBY MAULIDIA'],
            ['13011', 'KAMILAH NAYLATUL MAGHFIROH'],
            ['13016', 'KEISHA PUTRI DWI AURELIA'],
            ['13029', 'MARETACH NUR MAININGLINDA'],
            ['13036', 'MELY ISTOFAL BASYAROH'],
            ['13039', 'MOCHAMMAD FARIS MADANI'],
            ['13046', 'MOHAMMAD FAKHRI NADHIR AKMAL'],
            ['13066', 'MUHAMMAD HOIRUL FIRDAUS'],
            ['13114', 'NURVINDA FADILAH SISWOYO'],
            ['13124', 'PUTRI MARISA'],
            ['13137', 'RAMDHANI PUTRA'],
            ['13140', 'RENATYA MAULIANA PUTRI'],
            ['13145', 'RIFKI HAKIKI'],
            ['13149', 'RIZKY OKTAVIANTI'],
            ['13155', 'SATRIYA ALEX BALISTRA NOVAL'],
            ['13160', 'SILFIYATUS SAHRO'],
            ['13164', 'SITI MUTMAINNAH'],
            ['13175', 'SYIFAUL QOLBI RISKIAN RAYYAN'],
            ['13178', 'UFAIRAH NAJLA NAURA'],
            ['13179', 'VELIANA SAFITRI'],
            ['13183', 'VINO DAFFA RISQULLAAH'],
            ['13190', 'ZAHROTUL BAIDHO MUTIARA DEWI'],
        ];

        foreach ($siswaXA as [$nis, $name]) {
            $siswa = User::create([
                'name' => $name,
                'username' => $nis,
                'role' => UserRole::Siswa,
                'password' => self::TEMP_PASSWORD,
                'must_change_password' => $this->mustChangePassword(),
            ]);

            $siswa->studentProfile()->create([
                'nis' => $nis,
                'school_class_id' => $kelasXA->id,
            ]);
        }

        Competition::create([
            'title' => 'Olimpiade Sains Nasional (OSN) 2026',
            'category' => AchievementCategory::Akademik,
            'level' => AchievementLevel::Nasional,
            'organizer' => 'Kemendikbud',
            'registration_deadline' => now()->addDays(4),
            'created_by' => $admin->id,
        ]);

        Competition::create([
            'title' => 'Lomba Cerdas Cermat Matematika',
            'category' => AchievementCategory::Akademik,
            'level' => AchievementLevel::Provinsi,
            'organizer' => 'Universitas Jember',
            'registration_deadline' => now()->addDays(18),
            'created_by' => $admin->id,
        ]);

        Competition::create([
            'title' => 'Festival Lomba Seni Siswa Nasional',
            'category' => AchievementCategory::NonAkademik,
            'level' => AchievementLevel::Provinsi,
            'organizer' => 'Disdik Jawa Timur',
            'registration_deadline' => now()->addDays(24),
            'created_by' => $admin->id,
        ]);

        Competition::create([
            'title' => 'Lomba Debat Bahasa Indonesia',
            'category' => AchievementCategory::Organisasi,
            'level' => AchievementLevel::Kabupaten,
            'organizer' => 'Kwarcab Bondowoso',
            'registration_deadline' => now()->addDays(33),
            'created_by' => $admin->id,
        ]);
    }
}
