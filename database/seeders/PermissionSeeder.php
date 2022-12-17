<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create role permissions
        Permission::create(['name' => 'lihat role', 'group_permission' => 'general', 'group' => 'role', 'created_at' => now()->subMinutes(1)]);
        Permission::create(['name' => 'tambah role', 'group_permission' => 'general', 'group' => 'role', 'created_at' => now()->subMinutes(2)]);
        Permission::create(['name' => 'ubah role', 'group_permission' => 'general', 'group' => 'role', 'created_at' => now()->subMinutes(3), 'associations' => json_encode(['lihat role'])]);
        Permission::create(['name' => 'hapus role', 'group_permission' => 'general', 'group' => 'role', 'created_at' => now()->subMinutes(4), 'associations' => json_encode(['lihat role'])]);

        // create user permissions
        Permission::create(['name' => 'lihat pengguna', 'group_permission' => 'general', 'group' => 'pengguna', 'created_at' => now()->subMinutes(5)]);
        Permission::create(['name' => 'tambah pengguna', 'group_permission' => 'general', 'group' => 'pengguna', 'created_at' => now()->subMinutes(6), 'associations' => json_encode(['lihat pengguna'])]);
        Permission::create(['name' => 'ubah pengguna', 'group_permission' => 'general', 'group' => 'pengguna', 'created_at' => now()->subMinutes(7), 'associations' => json_encode(['lihat pengguna'])]);

        // create specialist
        Permission::create(['name' => 'lihat spesialis', 'group_permission' => 'general', 'group' => 'spesialis', 'created_at' => now()->subMinutes(9)]);
        Permission::create(['name' => 'tambah spesialis', 'group_permission' => 'general', 'group' => 'spesialis', 'created_at' => now()->subMinutes(10), 'associations' => json_encode(['lihat spesialis'])]);
        Permission::create(['name' => 'ubah spesialis', 'group_permission' => 'general', 'group' => 'spesialis', 'created_at' => now()->subMinutes(11), 'associations' => json_encode(['lihat spesialis'])]);
        Permission::create(['name' => 'hapus spesialis', 'group_permission' => 'general', 'group' => 'spesialis', 'created_at' => now()->subMinutes(12), 'associations' => json_encode(['lihat spesialis'])]);

        // view kunjungan pasien
        Permission::create(['name' => 'lihat kunjungan', 'group_permission' => 'general', 'group' => 'kunjungan', 'created_at' => now()->subMinutes(13)]);
        Permission::create(['name' => 'ubah status kunjungan', 'group_permission' => 'general', 'group' => 'kunjungan', 'created_at' => now()->subMinutes(14), 'associations' => json_encode(['lihat kunjungan'])]);

        // view pasien
        Permission::create(['name' => 'lihat histori rekam medis', 'group_permission' => 'general', 'group' => 'histori rekam medis', 'created_at' => now()->subMinutes(15)]);

        // create profile permissions
        Permission::create(['name' => 'lihat profile', 'group_permission' => 'general', 'group' => 'pengaturan', 'created_at' => now()->subMinutes(16)]);
        Permission::create(['name' => 'ubah profile', 'group_permission' => 'general', 'group' => 'pengaturan', 'created_at' => now()->subMinutes(17), 'associations' => json_encode(['lihat profile'])]);
        Permission::create(['name' => 'lihat pengaturan assesmen', 'group_permission' => 'general', 'group' => 'pengaturan', 'created_at' => now()->subMinutes(18)]);
        Permission::create(['name' => 'ubah pengaturan assesmen', 'group_permission' => 'general', 'group' => 'pengaturan', 'created_at' => now()->subMinutes(19), 'associations' => json_encode(['lihat pengaturan assesmen'])]);

        // // create soap
        // Permission::create(['name' => 'view list soap', 'group' => 'soap']);
        // Permission::create(['name' => 'create soap', 'group' => 'soap']);
        // Permission::create(['name' => 'update soap', 'group' => 'soap', 'associations' => json_encode(['view list soap'])]);

        // assesment covid
        Permission::create(['name' => 'tambah assesment covid', 'group_permission' => 'assesment', 'group' => 'assesment covid', 'created_at' => now()->subMinutes(20)]);
        Permission::create(['name' => 'lihat deteksi dini kewaspadaan terhadap COVID 19', 'italic' => 1, 'group_permission' => 'assesment', 'group' => 'assesment covid', 'created_at' => now()->subMinutes(22)]);
        Permission::create(['name' => 'ubah deteksi dini kewaspadaan terhadap COVID 19', 'italic' => 1, 'group_permission' => 'assesment', 'group' => 'assesment covid', 'created_at' => now()->subMinutes(23), 'associations' => json_encode(['lihat deteksi dini kewaspadaan terhadap COVID 19'])]);

        // assesment dewasa
        Permission::create(['name' => 'tambah assesment dewasa', 'group_permission' => 'assesment', 'group' => 'assesment dewasa', 'created_at' => now()->subMinutes(24)]);
        Permission::create(['name' => 'ubah status assesment dewasa', 'separator' => 1,  'group_permission' => 'assesment', 'group' => 'assesment dewasa', 'created_at' => now()->subMinutes(25), 'associations' => json_encode(['lihat assesmen awal keperawatan rawat jalan'])]);
        Permission::create(['name' => 'lihat assesmen awal keperawatan rawat jalan', 'italic' => 1, 'group_permission' => 'assesment', 'group' => 'assesment dewasa', 'created_at' => now()->subMinutes(26)]);
        Permission::create(['name' => 'ubah assesmen awal keperawatan rawat jalan', 'italic' => 1, 'separator' => 1,  'group_permission' => 'assesment', 'group' => 'assesment dewasa', 'created_at' => now()->subMinutes(27), 'associations' => json_encode(['lihat assesmen awal keperawatan rawat jalan'])]);

        // assesment anak
        Permission::create(['name' => 'tambah assesment anak', 'group_permission' => 'assesment', 'group' => 'assesment anak', 'created_at' => now()->subMinutes(36)]);
        Permission::create(['name' => 'ubah status assesment anak', 'separator' => 1, 'group_permission' => 'assesment', 'group' => 'assesment anak', 'created_at' => now()->subMinutes(37), 'associations' => json_encode(['lihat assesmen awal keperawatan pasien anak rawat jalan'])]);
        Permission::create(['name' => 'lihat assesmen awal keperawatan pasien anak rawat jalan', 'italic' => 1, 'group_permission' => 'assesment', 'group' => 'assesment anak', 'created_at' => now()->subMinutes(38)]);
        Permission::create(['name' => 'ubah assesmen awal keperawatan pasien anak rawat jalan', 'italic' => 1, 'separator' => 1, 'group_permission' => 'assesment', 'group' => 'assesment anak', 'created_at' => now()->subMinutes(39), 'associations' => json_encode(['lihat assesmen awal keperawatan pasien anak rawat jalan'])]);

        // assesment medis
        Permission::create(['name' => 'tambah assesment spesialis penyakit dalam', 'group_permission' => 'assesment', 'group' => 'assesment spesialis penyakit dalam', 'created_at' => now()->subMinutes(42)]);
        Permission::create(['name' => 'ubah status assesment spesialis penyakit dalam', 'separator' => 1, 'group_permission' => 'assesment', 'group' => 'assesment spesialis penyakit dalam', 'created_at' => now()->subMinutes(43), 'associations' => json_encode(['lihat assesmen awal medis penyakit dalam'])]);
        Permission::create(['name' => 'lihat assesmen awal medis penyakit dalam', 'italic' => 1, 'group_permission' => 'assesment', 'group' => 'assesment spesialis penyakit dalam', 'created_at' => now()->subMinutes(44)]);
        Permission::create(['name' => 'ubah assesmen awal medis penyakit dalam', 'italic' => 1, 'group_permission' => 'assesment', 'group' => 'assesment spesialis penyakit dalam', 'created_at' => now()->subMinutes(45), 'associations' => json_encode(['lihat assesmen awal medis penyakit dalam'])]);

        //Laboratorium
        Permission::create(['name' => 'melihat permintaan laboratorium di kunjungan', 'group_permission' => 'request', 'group' => 'request laboratorium', 'created_at' => now()->subMinutes(46)]);
        Permission::create(['name' => 'tambah permintaan laboratorium di kunjungan', 'group_permission' => 'request', 'group' => 'request laboratorium', 'associations' => json_encode(['melihat permintaan laboratorium di kunjungan']), 'created_at' => now()->subMinutes(47)]);
        Permission::create(['name' => 'mengelola permintaan di laboratorium', 'group_permission' => 'request', 'group' => 'request laboratorium', 'created_at' => now()->subMinutes(48)]);
        Permission::create(['name' => 'melihat hasil permintaan di laboratorium', 'group_permission' => 'request', 'group' => 'request laboratorium', 'created_at' => now()->subMinutes(49)]);

        //Radiologi
        Permission::create(['name' => 'melihat permintaan radiologi di kunjungan', 'group_permission' => 'request', 'group' => 'request radiologi', 'created_at' => now()->subMinutes(50)]);
        Permission::create(['name' => 'tambah permintaan radiologi di kunjungan', 'group_permission' => 'request', 'group' => 'request radiologi', 'associations' => json_encode(['melihat permintaan radiologi di kunjungan']), 'created_at' => now()->subMinutes(51)]);
        Permission::create(['name' => 'mengelola permintaan di radiologi', 'group_permission' => 'request', 'group' => 'request radiologi', 'created_at' => now()->subMinutes(52)]);

        //ActivityLog
        Permission::create(['name' => 'lihat activity log', 'group_permission' => 'general', 'group' => 'activity log', 'created_at' => now()->subMinutes(54)]);

        //Master Group Tindakan
        Permission::create(['name' => 'lihat group tindakan', 'group_permission' => 'general', 'group' => 'master tindakan', 'created_at' => now()->subMinutes(55)]);
        Permission::create(['name' => 'tambah group tindakan', 'group_permission' => 'general', 'group' => 'master tindakan', 'created_at' => now()->subMinutes(56), 'associations' => json_encode(['lihat group tindakan'])]);
        Permission::create(['name' => 'ubah group tindakan', 'group_permission' => 'general', 'group' => 'master tindakan', 'created_at' => now()->subMinutes(57), 'associations' => json_encode(['lihat group tindakan'])]);
        Permission::create(['name' => 'hapus group tindakan', 'group_permission' => 'general', 'group' => 'master tindakan', 'created_at' => now()->subMinutes(58), 'associations' => json_encode(['lihat group tindakan'])]);

        //Master SubGroup Tindakan
        Permission::create(['name' => 'lihat sub group tindakan', 'group_permission' => 'general', 'group' => 'master tindakan', 'created_at' => now()->subMinutes(59), 'associations' => json_encode(['lihat group tindakan'])]);
        Permission::create(['name' => 'tambah sub group tindakan', 'group_permission' => 'general', 'group' => 'master tindakan', 'created_at' => now()->subMinutes(60), 'associations' => json_encode(['lihat sub group tindakan'])]);
        Permission::create(['name' => 'ubah sub group tindakan', 'group_permission' => 'general', 'group' => 'master tindakan', 'created_at' => now()->subMinutes(61), 'associations' => json_encode(['lihat sub group tindakan', 'lihat group tindakan'])]);
        Permission::create(['name' => 'hapus sub group tindakan', 'group_permission' => 'general', 'group' => 'master tindakan', 'created_at' => now()->subMinutes(62), 'associations' => json_encode(['lihat sub group tindakan'])]);

        //Resep
        Permission::create(['name' => 'melihat permintaan resep di kunjungan', 'group_permission' => 'request', 'group' => 'resep', 'created_at' => now()->subMinutes(63)]);
        Permission::create(['name' => 'tambah permintaan resep di kunjungan', 'group_permission' => 'request', 'group' => 'resep', 'associations' => json_encode(['melihat permintaan resep di kunjungan']), 'created_at' => now()->subMinutes(64)]);
        Permission::create(['name' => 'mengelola permintaan di farmasi', 'group_permission' => 'request', 'group' => 'resep', 'created_at' => now()->subMinutes(65)]);

        // assesment hasil terintegrasi
        Permission::create(['name' => 'lihat assesment hasil terintegrasi', 'group_permission' => 'assesment', 'group' => 'hasil terintegrasi', 'associations' => json_encode(['lihat assesment hasil terintegrasi', 'lihat kunjungan']), 'created_at' => now()->subMinutes(67)]);
        Permission::create(['name' => 'tambah assesment hasil terintegrasi', 'group_permission' => 'assesment', 'group' => 'hasil terintegrasi', 'associations' => json_encode(['lihat assesment hasil terintegrasi', 'lihat kunjungan']), 'created_at' => now()->subMinutes(68)]);
        Permission::create(['name' => 'ubah assesment hasil terintegrasi', 'group_permission' => 'assesment', 'group' => 'hasil terintegrasi', 'associations' => json_encode(['lihat assesment hasil terintegrasi', 'lihat kunjungan']), 'created_at' => now()->subMinutes(69)]);

        // assesment hasil resume rawat jalan
        Permission::create(['name' => 'lihat assesment hasil resume rawat jalan', 'group_permission' => 'assesment', 'group' => 'hasil resume rawat jalan', 'associations' => json_encode(['lihat assesment hasil resume rawat jalan', 'lihat kunjungan']), 'created_at' => now()->subMinutes(70)]);
        Permission::create(['name' => 'ubah assesment hasil resume rawat jalan', 'group_permission' => 'assesment', 'group' => 'hasil resume rawat jalan', 'associations' => json_encode(['lihat assesment hasil resume rawat jalan', 'lihat kunjungan']), 'created_at' => now()->subMinutes(71)]);

        // Request Rehab Medik
        Permission::create(['name' => 'melihat permintaan rehab medik di kunjungan', 'group_permission' => 'request', 'group' => 'request rehab medik', 'created_at' => now()->subMinutes(72)]);
        Permission::create(['name' => 'tambah permintaan rehab medik di kunjungan', 'group_permission' => 'request', 'group' => 'request rehab medik', 'associations' => json_encode(['melihat permintaan rehab medik di kunjungan']), 'created_at' => now()->subMinutes(73)]);
        Permission::create(['name' => 'mengelola permintaan di rehab medik', 'group_permission' => 'request', 'group' => 'request rehab medik', 'created_at' => now()->subMinutes(74)]);

        // ICD 9 
        Permission::create(['name' => 'lihat icd 9', 'group_permission' => 'icd', 'group' => 'icd 9', 'created_at' => now()->subMinutes(75)]);
        Permission::create(['name' => 'tambah icd 9', 'group_permission' => 'icd', 'group' => 'icd 9', 'created_at' => now()->subMinutes(76), 'associations' => json_encode(['lihat icd 9'])]);
        Permission::create(['name' => 'ubah icd 9', 'group_permission' => 'icd', 'group' => 'icd 9', 'created_at' => now()->subMinutes(77), 'associations' => json_encode(['lihat icd 9'])]);
        Permission::create(['name' => 'ubah status icd 9', 'group_permission' => 'icd', 'group' => 'icd 9', 'created_at' => now()->subMinutes(78), 'associations' => json_encode(['lihat icd 9'])]);

        // ICD 10 
        Permission::create(['name' => 'lihat icd 10', 'group_permission' => 'icd', 'group' => 'icd 10', 'created_at' => now()->subMinutes(75), 'associations' => json_encode(['lihat icd 9'])]);
        Permission::create(['name' => 'tambah icd 10', 'group_permission' => 'icd', 'group' => 'icd 10', 'created_at' => now()->subMinutes(76), 'associations' => json_encode(['lihat icd 10'])]);
        Permission::create(['name' => 'ubah icd 10', 'group_permission' => 'icd', 'group' => 'icd 10', 'created_at' => now()->subMinutes(77), 'associations' => json_encode(['lihat icd 10'])]);
        Permission::create(['name' => 'ubah status icd 10', 'group_permission' => 'icd', 'group' => 'icd 10', 'created_at' => now()->subMinutes(78), 'associations' => json_encode(['lihat icd 10'])]);

        // assesment awal anak
        Permission::create(['name' => 'tambah assesment spesialis anak', 'group_permission' => 'assesment', 'group' => 'assesment spesialis anak', 'created_at' => now()->subMinutes(79)]);
        Permission::create(['name' => 'ubah status assesment spesialis anak', 'separator' => 1, 'group_permission' => 'assesment', 'group' => 'assesment spesialis anak', 'created_at' => now()->subMinutes(80), 'associations' => json_encode(['lihat assesmen awal medis anak'])]);
        Permission::create(['name' => 'lihat assesmen awal medis anak', 'italic' => 1, 'group_permission' => 'assesment', 'group' => 'assesment spesialis anak', 'created_at' => now()->subMinutes(81)]);
        Permission::create(['name' => 'ubah assesmen awal medis anak', 'italic' => 1, 'group_permission' => 'assesment', 'group' => 'assesment spesialis anak', 'created_at' => now()->subMinutes(82), 'associations' => json_encode(['lihat assesmen awal medis anak'])]);

        // assesment awal medis syaraf
        Permission::create(['name' => 'tambah assesment spesialis syaraf', 'group_permission' => 'assesment', 'group' => 'assesment spesialis syaraf', 'created_at' => now()->subMinutes(83)]);
        Permission::create(['name' => 'ubah status assesment spesialis syaraf', 'separator' => 1, 'group_permission' => 'assesment', 'group' => 'assesment spesialis syaraf', 'created_at' => now()->subMinutes(84), 'created_at' => now()->subMinutes(85), 'associations' => json_encode(['lihat assesmen awal medis syaraf'])]);
        Permission::create(['name' => 'lihat assesmen awal medis syaraf', 'italic' => 1, 'group_permission' => 'assesment', 'group' => 'assesment spesialis syaraf']);
        Permission::create(['name' => 'ubah assesmen awal medis syaraf', 'italic' => 1, 'group_permission' => 'assesment', 'group' => 'assesment spesialis syaraf', 'created_at' => now()->subMinutes(86), 'associations' => json_encode(['lihat assesmen awal medis syaraf'])]);

        // assesment awal medis Paru
        Permission::create(['name' => 'tambah assesment spesialis paru', 'group_permission' => 'assesment', 'group' => 'assesment spesialis paru', 'created_at' => now()->subMinutes(86)]);
        Permission::create(['name' => 'ubah status assesment spesialis paru', 'separator' => 1, 'group_permission' => 'assesment', 'group' => 'assesment spesialis paru', 'created_at' => now()->subMinutes(87), 'associations' => json_encode(['lihat assesmen awal medis paru'])]);
        Permission::create(['name' => 'lihat assesmen awal medis paru', 'italic' => 1, 'group_permission' => 'assesment', 'group' => 'assesment spesialis paru', 'created_at' => now()->subMinutes(88)]);
        Permission::create(['name' => 'ubah assesmen awal medis paru', 'italic' => 1, 'group_permission' => 'assesment', 'group' => 'assesment spesialis paru', 'created_at' => now()->subMinutes(89), 'associations' => json_encode(['lihat assesmen awal medis paru'])]);

        // assesment global
        Permission::create(['name' => 'tambah assesment global', 'group_permission' => 'assesment', 'group' => 'assesment global', 'created_at' => now()->subMinutes(90)]);
        Permission::create(['name' => 'lihat assesmen global', 'italic' => 1, 'group_permission' => 'assesment', 'group' => 'assesment global', 'created_at' => now()->subMinutes(92)]);
        Permission::create(['name' => 'ubah assesmen global', 'italic' => 1, 'group_permission' => 'assesment', 'group' => 'assesment global', 'created_at' => now()->subMinutes(93), 'associations' => json_encode(['lihat assesmen global'])]);

        // // create system permissions
        // Permission::create(['name' => 'access database', 'group' => 'system']);
        // Permission::create(['name' => 'update email smtp', 'group' => 'system']);
        // Permission::create(['name' => 'update two factor authentication', 'group' => 'system']);
        // Permission::create(['name' => 'update member register', 'group' => 'system']);

        // // create error permissions
        // Permission::create(['name' => 'view list error', 'group' => 'error']);
        // Permission::create(['name' => 'view error', 'group' => 'error', 'associations' => json_encode(['view list error'])]);
        // Permission::create(['name' => 'update error', 'group' => 'error', 'associations' => json_encode(['view list error'])]);

        // // Update privacy policy
        // Permission::create(['name' => 'update privacy policy', 'group' => 'privacy policy']);

        // // Update term condition
        // Permission::create(['name' => 'update term condition', 'group' => 'term condition']);

        // // Update about us
        // Permission::create(['name' => 'update about us', 'group' => 'about us']);

        // // create support permissions
        // Permission::create(['name' => 'view list support', 'group' => 'support']);
        // Permission::create(['name' => 'create support', 'group' => 'support']);
        // Permission::create(['name' => 'update support', 'group' => 'support', 'associations' => json_encode(['view list support'])]);
        // Permission::create(['name' => 'delete support', 'group' => 'support', 'associations' => json_encode(['view list support'])]);

        // // create distribution permissions
        // Permission::create(['name' => 'view list distribution', 'group' => 'distribution']);
        // Permission::create(['name' => 'create distribution', 'group' => 'distribution', 'associations' => json_encode(['view list distribution'])]);
        // Permission::create(['name' => 'update distribution', 'group' => 'distribution', 'associations' => json_encode(['view list distribution'])]);
        // Permission::create(['name' => 'delete distribution', 'group' => 'distribution', 'associations' => json_encode(['view list distribution'])]);

        // // create ticket permissions
        // Permission::create(['name' => 'view list ticket', 'group' => 'ticket']);
        // Permission::create(['name' => 'update ticket', 'group' => 'ticket', 'associations' => json_encode(['view list ticket'])]);
        // Permission::create(['name' => 'reply ticket', 'group' => 'ticket', 'associations' => json_encode(['view list ticket', 'update ticket'])]);

        $permissions = Permission::all();

        $no_role = Role::insert(['id' => 1, 'name' => 'Tidak punya role', 'guard_name' => 'web', 'note' => 'Role untuk delegasi', 'created_at' => now()->subYears(10)]);

        $role = Role::create(['name' => 'Superuser', 'note' => 'Superuser memiliki semua akses']);
        $role->givePermissionTo($permissions->pluck('name')->toArray());

        $user = User::find(1);
        $user->assignRole($role->name);
    }
}
