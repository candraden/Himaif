<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AbsensiPublikController;
use App\Http\Controllers\Mahasiswa\MahasiswaPortalController;
use App\Http\Controllers\Mahasiswa\UnduhSertifikatController;
use App\Http\Controllers\Publik\BerandaController;
use App\Http\Controllers\Publik\KegiatanPublikController;
use App\Http\Controllers\Publik\PengumumanPublikController;
use App\Http\Controllers\Publik\StrukturController;
use App\Http\Controllers\Admin\AbsensiController;
use App\Http\Controllers\Admin\AnggotaController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DivisiController;
use App\Http\Controllers\Admin\KelolaAdminController;
use App\Http\Controllers\Admin\KelolaKaprodiController;
use App\Http\Controllers\Admin\KegiatanController;
use App\Http\Controllers\Admin\MonitoringController;
use App\Http\Controllers\Admin\PengumumanController;
use App\Http\Controllers\Admin\PengurusController;
use App\Http\Controllers\Admin\PeriodeController;
use App\Http\Controllers\Admin\SertifikatController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\MahasiswaAuthController;

// ── Halaman Publik ────────────────────────────────────────────────────────────
Route::get('/', [BerandaController::class, 'index'])->name('home');
Route::get('/kegiatan', [KegiatanPublikController::class, 'index'])->name('publik.kegiatan.index');
Route::get('/kegiatan/{kegiatan}', [KegiatanPublikController::class, 'show'])->name('publik.kegiatan.show');
Route::get('/pengumuman', [PengumumanPublikController::class, 'index'])->name('publik.pengumuman.index');
Route::get('/pengumuman/{pengumuman}', [PengumumanPublikController::class, 'show'])->name('publik.pengumuman.show');
Route::get('/struktur', [StrukturController::class, 'index'])->name('publik.struktur');

// Absensi via link / QR (publik, dilindungi token UUID + time-gate + reCAPTCHA + rate limit)
Route::get('/absen/{token}', [AbsensiPublikController::class, 'form'])->name('absen.form');
Route::post('/absen/{token}', [AbsensiPublikController::class, 'store'])
    ->middleware('throttle:' . config('absensi.rate_limit', 5) . ',1')
    ->name('absen.store');

// ── Auth & Panel Admin ────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {

   Route::middleware('guest')->group(function () {
    Route::get('/login',  [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.post');
});

    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::middleware('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Anggota
        Route::get('anggota/import',          [AnggotaController::class, 'importForm'])->name('anggota.import');
        Route::post('anggota/import',         [AnggotaController::class, 'import'])->name('anggota.import.store');
        Route::get('anggota/import/template', [AnggotaController::class, 'template'])->name('anggota.import.template');
        Route::post('anggota/{anggota}/reset-password', [AnggotaController::class, 'resetPassword'])->name('anggota.reset-password');
        Route::resource('anggota', AnggotaController::class)->except(['show'])->parameters(['anggota' => 'anggota']);

        // Kegiatan
        Route::get('kegiatan/{kegiatan}/qr',       [KegiatanController::class, 'qr'])->name('kegiatan.qr');
        Route::post('kegiatan/{kegiatan}/token',   [KegiatanController::class, 'buatTokenBaru'])->name('kegiatan.token');
        Route::patch('kegiatan/{kegiatan}/status', [KegiatanController::class, 'ubahStatus'])->name('kegiatan.status');
        Route::resource('kegiatan', KegiatanController::class);

        // Absensi
        Route::get('absensi', [AbsensiController::class, 'index'])->name('absensi.index');
        Route::get('kegiatan/{kegiatan}/absensi',  [AbsensiController::class, 'kegiatan'])->name('absensi.kegiatan');
        Route::post('kegiatan/{kegiatan}/absensi', [AbsensiController::class, 'tambah'])->name('absensi.tambah');
        Route::post('kegiatan/{kegiatan}/absensi/isi-alpa', [AbsensiController::class, 'isiAlpa'])->name('absensi.isi-alpa');
        Route::get('kegiatan/{kegiatan}/absensi/export/excel', [AbsensiController::class, 'exportExcel'])->name('absensi.export.excel');
        Route::get('kegiatan/{kegiatan}/absensi/export/pdf',   [AbsensiController::class, 'exportPdf'])->name('absensi.export.pdf');
        Route::get('kegiatan/{kegiatan}/absensi/peserta/{nim}',    [AbsensiController::class, 'peserta'])->where('nim', '[0-9]+')->name('absensi.peserta');
        Route::put('kegiatan/{kegiatan}/absensi/peserta/{nim}',    [AbsensiController::class, 'perbaruiPeserta'])->where('nim', '[0-9]+')->name('absensi.peserta.update');
        Route::delete('kegiatan/{kegiatan}/absensi/peserta/{nim}', [AbsensiController::class, 'hapusPeserta'])->where('nim', '[0-9]+')->name('absensi.peserta.hapus');

        // Monitoring
        Route::get('monitoring', [MonitoringController::class, 'index'])->name('monitoring.index');
        Route::get('monitoring/anggota/{anggota}', [MonitoringController::class, 'anggota'])->name('monitoring.anggota');

        // Periode
        Route::get('periode', [PeriodeController::class, 'index'])->name('periode.index');
        Route::post('periode', [PeriodeController::class, 'store'])->name('periode.store');
        Route::get('periode/{periode}/edit', [PeriodeController::class, 'edit'])->name('periode.edit');
        Route::put('periode/{periode}', [PeriodeController::class, 'update'])->name('periode.update');
        Route::post('periode/{periode}/aktifkan', [PeriodeController::class, 'aktifkan'])->name('periode.aktifkan');
        Route::delete('periode/{periode}', [PeriodeController::class, 'destroy'])->name('periode.destroy');

        // Divisi & Pengurus
        Route::resource('divisi', DivisiController::class)->except(['index', 'show'])->parameters(['divisi' => 'divisi']);
        Route::resource('pengurus', PengurusController::class)->except(['show'])->parameters(['pengurus' => 'pengurus']);

        // Pengumuman & Sertifikat
        Route::resource('pengumuman', PengumumanController::class)->except(['show'])->parameters(['pengumuman' => 'pengumuman']);
        Route::put('kegiatan/{kegiatan}/sertifikat', [SertifikatController::class, 'update'])->name('kegiatan.sertifikat');

        // ── Super Admin only ──────────────────────────────────────────────────
        Route::middleware('superadmin')->prefix('super')->name('super.')->group(function () {
            // Kelola Admin
            Route::get('/akun',                                 [KelolaAdminController::class, 'index'])->name('akun');
            Route::get('/akun/tambah',                          [KelolaAdminController::class, 'create'])->name('akun.create');
            Route::post('/akun',                                [KelolaAdminController::class, 'store'])->name('akun.store');
            Route::get('/akun/{kelola_admin}/edit',             [KelolaAdminController::class, 'edit'])->name('akun.edit');
            Route::put('/akun/{kelola_admin}',                  [KelolaAdminController::class, 'update'])->name('akun.update');
            Route::patch('/akun/{kelola_admin}/reset-password', [KelolaAdminController::class, 'resetPassword'])->name('akun.reset-password');
            Route::delete('/akun/{kelola_admin}',               [KelolaAdminController::class, 'destroy'])->name('akun.destroy');
            Route::patch('/akun/{id}/restore',                  [KelolaAdminController::class, 'restore'])->name('akun.restore');

            // Kelola Kaprodi
            Route::get('/kaprodi',                                      [KelolaKaprodiController::class, 'index'])->name('kaprodi');
            Route::get('/kaprodi/tambah',                               [KelolaKaprodiController::class, 'create'])->name('kaprodi.create');
            Route::post('/kaprodi',                                     [KelolaKaprodiController::class, 'store'])->name('kaprodi.store');
            Route::get('/kaprodi/{kelola_kaprodi}/edit',                [KelolaKaprodiController::class, 'edit'])->name('kaprodi.edit');
            Route::put('/kaprodi/{kelola_kaprodi}',                     [KelolaKaprodiController::class, 'update'])->name('kaprodi.update');
            Route::patch('/kaprodi/{kelola_kaprodi}/reset-password',    [KelolaKaprodiController::class, 'resetPassword'])->name('kaprodi.reset-password');
            Route::delete('/kaprodi/{kelola_kaprodi}',                  [KelolaKaprodiController::class, 'destroy'])->name('kaprodi.destroy');
            Route::patch('/kaprodi/{id}/restore',                       [KelolaKaprodiController::class, 'restore'])->name('kaprodi.restore');
        });
    });
});

// ── Auth Mahasiswa ────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',  [MahasiswaAuthController::class, 'showLoginForm'])->name('mahasiswa.login');
    Route::post('/login', [MahasiswaAuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('mahasiswa.login.post');
});
Route::post('/logout', [MahasiswaAuthController::class, 'logout'])->name('mahasiswa.logout');

// ── Portal Mahasiswa ──────────────────────────────────────────────────────────
Route::middleware('mahasiswa_auth')->prefix('portal')->name('mahasiswa.')->group(function () {

    // Ganti password (first login)
    Route::get('/ganti-password',  [MahasiswaAuthController::class, 'showChangePasswordForm'])->name('password.change');
    Route::post('/ganti-password', [MahasiswaAuthController::class, 'changePassword'])->name('password.update');

    // Dashboard
    Route::get('/dashboard', [MahasiswaPortalController::class, 'dashboard'])->name('dashboard');

    // Absensi
    Route::get('/absensi', [MahasiswaPortalController::class, 'absensi'])->name('absensi');

    // Monitoring
    Route::get('/monitoring', [MahasiswaPortalController::class, 'monitoring'])->name('monitoring');

    // KTA Digital
    Route::get('/kta', [MahasiswaPortalController::class, 'kta'])->name('kta');

    // Profil
    Route::get('/profil',           [MahasiswaPortalController::class, 'profil'])->name('profil');
    Route::post('/profil/foto',     [MahasiswaPortalController::class, 'updateFoto'])->name('profil.foto');
    Route::put('/profil/password',  [MahasiswaPortalController::class, 'updatePassword'])->name('profil.password');

    // Sertifikat (unduh via redirect)
    Route::get('/sertifikat/{kegiatan}', [UnduhSertifikatController::class, 'unduh'])->name('sertifikat');
});