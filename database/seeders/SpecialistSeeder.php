<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SpecialistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('specialists')->insert([
            [
                'name' => 'LAINNYA',
                'code' => '000'
            ],
            [
                'name' => 'ALERGI-IMMUNOLOGI KLINIK',
                'code' => '004'
            ],
            [
                'name' => 'GASTROENTEROLOGI-HEPATOLOGI',
                'code' => '005'
            ],
            [
                'name' => 'GERIATRI',
                'code' => '006'
            ],
            [
                'name' => 'GINJAL-HIPERTENSI',
                'code' => '007'
            ],
            [
                'name' => 'HEMATOLOGI - ONKOLOGI MEDIK',
                'code' => '008'
            ],
            [
                'name' => 'HEPATOLOGI',
                'code' => '009'
            ],
            [
                'name' => 'ENDOKRIN-METABOLIK-DIABETES',
                'code' => '010'
            ],
            [
                'name' => 'PSIKOSOMATIK',
                'code' => '011'
            ],
            [
                'name' => 'PULMONOLOGI',
                'code' => '012'
            ],
            [
                'name' => 'REUMATOLOGI',
                'code' => '013'
            ],
            [
                'name' => 'PENYAKIT TROPIK-INFEKSI',
                'code' => '014'
            ],
            [
                'name' => 'KARDIOVASKULAR',
                'code' => '015'
            ],
            [
                'name' => 'BEDAH ONKOLOGI',
                'code' => '017'
            ],
            [
                'name' => 'BEDAH DIGESTIF',
                'code' => '018'
            ],
            [
                'name' => 'FETOMATERNAL',
                'code' => '020'
            ],
            [
                'name' => 'ONKOLOGI GINEKOLOGI',
                'code' => '021'
            ],
            [
                'name' => 'UROGINEKOLOGI REKONTRUSKI',
                'code' => '022'
            ],
            [
                'name' => 'OBSTETRI GINEKOLOGI SOSIAL',
                'code' => '023'
            ],
            [
                'name' => 'ENDOKRINOLOGI',
                'code' => '024'
            ],
            [
                'name' => 'FERTILITAS',
                'code' => '025'
            ],
            [
                'name' => 'ANAK ALERGI IMUNOLOGI',
                'code' => '027'
            ],
            [
                'name' => 'ANAK ENDOKRINOLOGI',
                'code' => '028'
            ],
            [
                'name' => 'ANAK GASTRO-HEPATOLOGI',
                'code' => '029'
            ],
            [
                'name' => 'ANAK HEMATOLOGI ONKOLOGI',
                'code' => '030'
            ],
            [
                'name' => 'ANAK INFEKSI & PEDIATRI TROPIS',
                'code' => '031'
            ],
            [
                'name' => 'ANAK KARDIOLOGI',
                'code' => '032'
            ],
            [
                'name' => 'ANAK NEFROLOGI',
                'code' => '033'
            ],
            [
                'name' => 'ANAK NEUROLOGI',
                'code' => '034'
            ],
            [
                'name' => 'PEDIATRI GAWAT DARURAT',
                'code' => '036'
            ],
            [
                'name' => 'PENCITRAAN ANAK',
                'code' => '037'
            ],
            [
                'name' => 'PERINATOLOGI',
                'code' => '038'
            ],
            [
                'name' => 'RESPIROLOGI ANAK',
                'code' => '039'
            ],
            [
                'name' => 'TUMBUH KEMBANG PED. SOSIAL',
                'code' => '040'
            ],
            [
                'name' => 'KESEHATAN REMAJA',
                'code' => '041'
            ],
            [
                'name' => 'INTENSIVE CARE/ICU',
                'code' => '043'
            ],
            [
                'name' => 'ANESTESI KARDIOVASKULER',
                'code' => '044'
            ],
            [
                'name' => 'MANAJEMEN NYERI',
                'code' => '045'
            ],
            [
                'name' => 'NEUROANESTESI',
                'code' => '047'
            ],
            [
                'name' => 'ANESTESI PEDIATRI',
                'code' => '048'
            ],
            [
                'name' => 'ANESTESI OBSTETRI',
                'code' => '049'
            ],
            [
                'name' => 'Radiologi Thoraks',
                'code' => '051'
            ],
            [
                'name' => 'Radiologi Muskuloskeletal',
                'code' => '052'
            ],
            [
                'name' => 'Radiologi Tr Urinariusgenitalia',
                'code' => '053'
            ],
            [
                'name' => 'Radiologi Tr Digestivus',
                'code' => '054'
            ],
            [
                'name' => 'Neuroradiologi',
                'code' => '055'
            ],
            [
                'name' => 'Pencitraan Payudara/womans imaging',
                'code' => '056'
            ],
            [
                'name' => 'Radiologi intervensional kardiovaskular',
                'code' => '057'
            ],
            [
                'name' => 'Pencitraan kepala leher',
                'code' => '058'
            ],
            [
                'name' => 'Radiologi pediatrik',
                'code' => '059'
            ],
            [
                'name' => 'Kedokteran nuklir',
                'code' => '060'
            ],
            [
                'name' => 'OTOLOGI',
                'code' => '067'
            ],
            [
                'name' => 'NEUROTOLOGI',
                'code' => '068'
            ],
            [
                'name' => 'RINOLOGI',
                'code' => '069'
            ],
            [
                'name' => 'LARINGO-FARINGOLOGI',
                'code' => '070'
            ],
            [
                'name' => 'ONKOLOGI KEPALA LEHER',
                'code' => '071'
            ],
            [
                'name' => 'PLASTIK REKONSTRUKSI',
                'code' => '072'
            ],
            [
                'name' => 'BRONKOESOFAGOLOGI',
                'code' => '073'
            ],
            [
                'name' => 'ALERGI IMUNOLOGI',
                'code' => '074'
            ],
            [
                'name' => 'THT KOMUNITAS',
                'code' => '075'
            ],
            [
                'name' => 'NEUROTRAUMA',
                'code' => '078'
            ],
            [
                'name' => 'NEUROINFEKSI',
                'code' => '079'
            ],
            [
                'name' => 'NEUROINFEKSI DAN IMUNOLOGI',
                'code' => '080'
            ],
            [
                'name' => 'EPILEPSI',
                'code' => '081'
            ],
            [
                'name' => 'NEUROFISIOLOGI KLINIS',
                'code' => '082'
            ],
            [
                'name' => 'NEUROMUSKULAR',
                'code' => '083'
            ],
            [
                'name' => 'NEURO-INTENSIF',
                'code' => '086'
            ],
            [
                'name' => 'INFEKSI',
                'code' => '095'
            ],
            [
                'name' => 'ONKOLOGI TORAKS',
                'code' => '096'
            ],
            [
                'name' => 'ASMA DAN PPOK',
                'code' => '097'
            ],
            [
                'name' => 'FAAL PARU KLINIK',
                'code' => '099'
            ],
            [
                'name' => 'PARU KERJA DAN LINGKUNGAN',
                'code' => '100'
            ],
            [
                'name' => 'IMUNOLOGIK KLINIK',
                'code' => '101'
            ],
            [
                'name' => 'BURN (LUKA BAKAR)',
                'code' => '104'
            ],
            [
                'name' => 'MICRO SURGERY',
                'code' => '105'
            ],
            [
                'name' => 'KRANIOFASIAL (KKF)',
                'code' => '106'
            ],
            [
                'name' => 'HAND (BEDAH TANGAN)',
                'code' => '107'
            ],
            [
                'name' => 'GENITALIA EKSTERNA',
                'code' => '108'
            ],
            [
                'name' => 'REKONTRUKSI DAN ESTETIK',
                'code' => '109'
            ],
            [
                'name' => 'BEDAH VASKULER',
                'code' => '132'
            ],
            [
                'name' => 'KORNEA DAN BEDAH REFRAKTIF',
                'code' => '133'
            ],
            [
                'name' => 'INFEKSI DAN IMMUNOLOGI',
                'code' => '134'
            ],
            [
                'name' => 'VITREO - RETINA',
                'code' => '135'
            ],
            [
                'name' => 'STRABISMUS',
                'code' => '136'
            ],
            [
                'name' => 'NEURO OFTALMOLOGI',
                'code' => '137'
            ],
            [
                'name' => 'GLAUKOMA',
                'code' => '138'
            ],
            [
                'name' => 'PEDRIATIK OFTALMOLOGI',
                'code' => '139'
            ],
            [
                'name' => 'REFRAKSI',
                'code' => '140'
            ],
            [
                'name' => 'REKONSTRUKSI',
                'code' => '141'
            ],
            [
                'name' => 'ONKOLOGI MATA',
                'code' => '142'
            ],
            [
                'name' => 'DERMATOLOGI INFEKSI TROPIK',
                'code' => '143'
            ],
            [
                'name' => 'DERMATOLOGI PEDIATRIK',
                'code' => '144'
            ],
            [
                'name' => 'INFEKSI MENULAR SEKSUAL',
                'code' => '146'
            ],
            [
                'name' => 'DERMATO - ALERGO - IMUNOLOGI',
                'code' => '147'
            ],
            [
                'name' => 'DERMATOLOGI GERIATRIK',
                'code' => '148'
            ],
            [
                'name' => 'TUMOR DAN BEDAH KUIT',
                'code' => '149'
            ],
            [
                'name' => 'DERMATOPATOLOGI',
                'code' => '150'
            ],
            [
                'name' => 'TRAUMA DAN REKONSTRUKSI',
                'code' => '151'
            ],
            [
                'name' => 'TULANG BELAKANG',
                'code' => '152'
            ],
            [
                'name' => 'TUMOR TULANG',
                'code' => '153'
            ],
            [
                'name' => 'PEDIATRIK',
                'code' => '154'
            ],
            [
                'name' => 'HAND AND MICROSURGERY',
                'code' => '156'
            ],
            [
                'name' => 'REKONSTRUKSI DEWASA/HIP AND KNEE',
                'code' => '157'
            ],
            [
                'name' => 'BIO ORTHOPEDIC',
                'code' => '158'
            ],
            [
                'name' => 'NEUROPSIKIATRI DAN PSIKOMETRI',
                'code' => '160'
            ],
            [
                'name' => 'PSIKIATRI ANAK DAN REMAJA',
                'code' => '162'
            ],
            [
                'name' => 'PSIKIATRI GERIARTRI',
                'code' => '163'
            ],
            [
                'name' => 'CONSULTATION-LIAISON PSYCHIATRI',
                'code' => '165'
            ],
            [
                'name' => 'RADIOTERAPI',
                'code' => '168'
            ],
            [
                'name' => 'RADIOLOGI ONKOLOGI',
                'code' => '169'
            ],
            [
                'name' => 'BEDAH KEPALA LEHER',
                'code' => '170'
            ],
            [
                'name' => 'ANAK',
                'code' => 'ANA'
            ],
            [
                'name' => 'ANDROLOGI',
                'code' => 'AND'
            ],
            [
                'name' => 'ANASTESI',
                'code' => 'ANT'
            ],
            [
                'name' => 'BEDAH ANAK',
                'code' => 'BDA'
            ],
            [
                'name' => 'GIGI BEDAH MULUT',
                'code' => 'BDM'
            ],
            [
                'name' => 'BEDAH PLASTIK',
                'code' => 'BDP'
            ],
            [
                'name' => 'BEDAH',
                'code' => 'BED'
            ],
            [
                'name' => 'BEDAH SARAF',
                'code' => 'BSY'
            ],
            [
                'name' => 'BTKV (BEDAH THORAX KARDIOVASKU)',
                'code' => 'BTK'
            ],
            [
                'name' => 'FISIOTERAPI',
                'code' => 'FIS'
            ],
            [
                'name' => 'FARMAKOLOGI KLINIK',
                'code' => 'FMK'
            ],
            [
                'name' => 'GIGI',
                'code' => 'GIG'
            ],
            [
                'name' => 'GIZI KLINIK',
                'code' => 'GIZ'
            ],
            [
                'name' => 'GIGI ENDODONSI',
                'code' => 'GND'
            ],
            [
                'name' => 'GIGI ORTHODONTI',
                'code' => 'GOR'
            ],
            [
                'name' => 'GIGI PERIODONTI',
                'code' => 'GPR'
            ],
            [
                'name' => 'GIGI RADIOLOGI',
                'code' => 'GRD'
            ],
            [
                'name' => 'HEMODIALISA',
                'code' => 'HDL'
            ],
            [
                'name' => 'B20',
                'code' => 'HIV'
            ],
            [
                'name' => 'INSTALASI GAWAT DARURAT',
                'code' => 'IGD'
            ],
            [
                'name' => 'PENYAKIT DALAM',
                'code' => 'INT'
            ],
            [
                'name' => 'REHABILITASI MEDIK',
                'code' => 'IRM'
            ],
            [
                'name' => 'JANTUNG DAN PEMBULUH DARAH',
                'code' => 'JAN'
            ],
            [
                'name' => 'JIWA',
                'code' => 'JIW'
            ],
            [
                'name' => 'KEDOKTERAN KELAUTAN',
                'code' => 'KDK'
            ],
            [
                'name' => 'KEDOKTERAN NUKLIR',
                'code' => 'KDN'
            ],
            [
                'name' => 'KEDOKTERAN OKUPASI',
                'code' => 'KDO'
            ],
            [
                'name' => 'KEDOKTERAN PENERBANGAN',
                'code' => 'KDP'
            ],
            [
                'name' => 'SARANA KEMOTERAPI',
                'code' => 'KEM'
            ],
            [
                'name' => 'KULIT KELAMIN',
                'code' => 'KLT'
            ],
            [
                'name' => 'GIGI PEDODONTIS',
                'code' => 'KON'
            ],
            [
                'name' => 'KEDOKTERAAN OLAHRAGA',
                'code' => 'KOR'
            ],
            [
                'name' => 'MATA',
                'code' => 'MAT'
            ],
            [
                'name' => 'MIKROBIOLOGI KLINIK',
                'code' => 'MKB'
            ],
            [
                'name' => 'OBGYN',
                'code' => 'OBG'
            ],
            [
                'name' => 'ORTHOPEDI',
                'code' => 'ORT'
            ],
            [
                'name' => 'PATOLOGI ANATOMI',
                'code' => 'PAA'
            ],
            [
                'name' => 'PATOLOGI KLINIK',
                'code' => 'PAK'
            ],
            [
                'name' => 'PARU',
                'code' => 'PAR'
            ],
            [
                'name' => 'GIGI PENYAKIT MULUT',
                'code' => 'PNM'
            ],
            [
                'name' => 'PARASITOLOGI UMUM',
                'code' => 'PRM'
            ],
            [
                'name' => 'PSIKOLOGI',
                'code' => 'PSI'
            ],
            [
                'name' => 'GIGI PROSTHODONTI',
                'code' => 'PTD'
            ],
            [
                'name' => 'SARANA RADIOTERAPI',
                'code' => 'RAT'
            ],
            [
                'name' => 'RADIOLOGI ONKOLOGI',
                'code' => 'RDN'
            ],
            [
                'name' => 'RADIOLOGI',
                'code' => 'RDO'
            ],
            [
                'name' => 'RADIOTERAPI',
                'code' => 'RDT'
            ],
            [
                'name' => 'SARAF',
                'code' => 'SAR'
            ],
            [
                'name' => 'THT-KL',
                'code' => 'THT'
            ],
            [
                'name' => 'UMUM',
                'code' => 'UMU'
            ],
            [
                'name' => 'UROLOGI',
                'code' => 'URU'
            ],
        ]);
    }
}
