<?php

use App\Models\RequestLab;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

if (! function_exists('formatDate')) {
    /**
     * Custom format date
     *
     * @param string $date
     * @param string $format
     *
     * @return string
     */
    function formatDate($date, $format)
    {
        if (!$date) {
            return "";
        }

        return Carbon::parse($date)->translatedFormat($format);
    }
}

if (! function_exists('formatFromTo')) {
    /**
     * Format date to time stamp
     *
     * @param string $date
     * @param string $format_from
     * @param string $format_to
     *
     * @return string
     */
    function formatFromTo($date, $format_from, $format_to)
    {
        if (!$date) {
            return "";
        }

        try {
            $date = Carbon::createFromLocaleFormat($format_from, config('app.locale'), $date)->translatedFormat($format_to);

            return $date;
        } catch (\Throwable $th) {
            return "";
        }
    }
}

if (!function_exists('rupiah')) {
    /**
     * @param  float $angka
     * @param  bool $prefix
     * @return string
     */
    function rupiah($angka, $prefix = true)
    {
        if (is_null($angka)) {
            return null;
        }

        if ($prefix) {
            $hasil_rupiah = "Rp " . number_format($angka, 0, ',', '.');
            return $hasil_rupiah;
        }

        $hasil_rupiah = number_format($angka, 0, ',', '.');
        return $hasil_rupiah;
    }
}

if (!function_exists('calculatePercentageProgressBar')) {
    function calculatePercentageProgressBar($value)
    {
        $minPercent = 0.5;
        $maxPercent = 100;

        if ($value == 0) {
            return $minPercent;
        }

        $percentage = 100 - 100 / (30 / $value);

        if ($percentage < $minPercent) {
            return $minPercent;
        }

        if ($percentage > $maxPercent) {
            return $maxPercent;
        }

        return $percentage;
    }
}

if (!function_exists('searchDate')) {
    function searchDate($value, $formatFrom, $formatTo)
    {
        try {
            if($formatFrom == 'd F Y') {
                if(Carbon::createFromLocaleFormat($formatFrom, config('app.locale'), $value)) {
                    $format = Carbon::createFromLocaleFormat($formatFrom, config('app.locale'), $value)->translatedFormat($formatTo);
                    return Carbon::parse($format)->format($formatTo);
                }
            } else {
                if(Carbon::createFromFormat($formatFrom, $value)) {
                    $format = Carbon::createFromFormat($formatFrom, $value, 'Asia/Jakarta')->format('Y-m-d');
                    return Carbon::parse($format)->format($formatTo);
                }
            }
        } catch (\Throwable $th) {
            return 'date invalid';
        }
    }
}

if (!function_exists('numberToRoman')) {    
    /**
     * Mengubah angka menjadi angka romawi
     *
     * @param  mixed $number
     * @return void
     */
    function numberToRoman($number) {
        $map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
        $returnValue = '';
        while ($number > 0) {
            foreach ($map as $roman => $int) {
                if($number >= $int) {
                    $number -= $int;
                    $returnValue .= $roman;
                    break;
                }
            }
        }
        return $returnValue;
    }
}

if (!function_exists('getRoleFromPermission')) {
    function getRoleFromPermission($permission)
    {
        try {
            $permission_name = null;
            if($permission == 'Covid') {
                $permission_name = 'ubah deteksi dini kewaspadaan terhadap COVID 19';
            }
            if($permission == 'Umum Dewasa') {
                $permission_name = 'ubah assesmen awal keperawatan rawat jalan';
            }
            if($permission == 'Inspection') {
                $permission_name = 'tambah item hasil pemeriksaan rawat jalan terintegrasi';
            }
            if($permission == 'Resume') {
                $permission_name = 'tambah item resume rawat jalan';
            }
            if($permission == 'Umum Anak') {
                $permission_name = 'ubah assesmen awal keperawatan pasien anak rawat jalan';
            }
            if($permission == 'Spesialis Anak') {
                $permission_name = 'ubah assesmen awal medis anak';
            }
            if($permission == 'Spesialis Penyakit Dalam') {
                $permission_name = 'ubah assesmen awal medis penyakit dalam';
            }
            if($permission == 'Spesialis Syaraf') {
                $permission_name = 'ubah assesmen awal medis syaraf';
            }
            if($permission == 'Spesialis Paru') {
                $permission_name = 'ubah assesmen awal medis paru';
            }
            info($permission_name);
            $rolesWithPerm = Role::where('id', '!=', '1')
            ->where('id', '!=', '2')
            ->whereHas('permissions', function($query) use($permission_name) {
                $query->where('name', $permission_name);
             })->get();

            $roles = [];
            foreach ($rolesWithPerm as $key => $role) {
                $roles[] = $role->name;
            }

            $role = implode(', ', $roles);
            return $role;
        } catch (\Throwable $th) {
            info($th);
            return 'gagal';
        }
    }
}

if (!function_exists('saveOrderLis')) {       
    /**
     * Save Order ke API LIS
     *
     * @param  mixed $visit
     * @param  mixed $unique_id
     * @param  mixed $patient_type
     * @param  mixed $doctor_name
     * @param  mixed $doctor_id
     * @param  array $action_id
     * @param  array $action_name
     * @return void
     */
    function saveOrderLis($visit, $unique_id, $patient_type, $doctor_name, $doctor_id, $action_id, $action_name) {
        $data = [];
        foreach ($action_id as $key => $action) {
            $row['id_test'] = strval($action);
            $row['nama_test'] = $action_name[$key];
            $row['is_add'] = 1;
            $data[] = $row;
        }

        $doctor['id_dokter'] = $visit['kodeprovider'];
        $doctor['nama_dokter'] = $visit['dokter'];
        $doctor['id_instalasi'] = $visit['kodeunit'];
        $doctor['id_ward'] = $visit['koderuang'];
        $doctor['ward'] = $visit['ruang'];
        $detail_rujukan[] = $doctor;

        $post = [
            "title" => "",
            "no_pendaftaran" => $visit['kode'],  
            "registration_date" => now(),
            "no_rm" => $visit['norm'],  
            "no_order" => $unique_id,  
            "nama_pasien" => $visit['nama'],  
            "tempat_lahir" => "-",  
            "tgl_lahir" => $visit['tgllahir'],  
            "jk" => $visit['jkl'] == 'Perempuan' ? 'P' : 'L',  
            "alamat" => $visit['alamat'],  
            "phone" => "", 
            "nik" => "", 
            "id_jenis_pasien" => $patient_type,  
            "jenis_pasien" => $patient_type == 1 ? "UMUM" : "ASURANSI",  
            "id_penjamin" => $visit['kodejamin'],  
            "penjamin" => $visit['penjamin'],  
            "rujukan_asal" => 1, 
            "detail_rujukan" => $detail_rujukan, 
            "cito" => "false",  
            "diagnose" => "-",  
            "icd10" => [],
            "order" => $data  
        ];

        //X-time
        date_default_timezone_set('UTC'); 
        $tStamp = strval(time()-strtotime('1970-01-01 00:00:00')); 

        //X-sign
        $xcons = "testtesttest";
        $xkey = "secretkey";
        $signature = hash_hmac('sha256', $xcons, $xkey, true);
        $encodedSignature = base64_encode($signature);

        $saveOrder = Http::withoutVerifying()->withHeaders(['X-cons' => '330913001', 'X-time' => $tStamp, 'X-sign' => $encodedSignature, 'Accept' => 'application/json'])->post(''.env('LIS_URL').'/api/v1/saveOrder', $post);

        return $saveOrder;
    }
}

if (!function_exists('addEditOrderLis')) {   
    /**
     * Save Order ke API LIS
     *
     * @param  mixed $number
     * @return void
     */
    function addEditOrderLis($visit, RequestLab $requestLab, $action_id, $action_name) {
        $requestLabAction = [];
        foreach ($requestLab->actionLabs as $key => $action) {
            $requestLabAction[] = $action->action_id;
        }

        $data = [];
        foreach ($action_id as $key => $action) {
            if(!in_array($action, $requestLabAction)) {
                $row['id_test'] = strval($action);
                $row['nama_test'] = $action_name[$key];
                $row['is_add'] = "true";
                $data[] = $row;
            }
        }

        $post = [
            "no_laboratorium" => strval($requestLab->laboratorium_id),  
            "no_rm" => strval($visit['norm']),  
            "no_order" => strval($requestLab->unique_id), 
            "keterangan" => "",
            "detail" => $data 
        ];

        //X-time
        date_default_timezone_set('UTC'); 
        $tStamp = strval(time()-strtotime('1970-01-01 00:00:00')); 

        //X-sign
        $xcons = "testtesttest";
        $xkey = "secretkey";
        $signature = hash_hmac('sha256', $xcons, $xkey, true);
        $encodedSignature = base64_encode($signature);

        $addEditOrder = Http::withoutVerifying()->withHeaders(['X-cons' => '330913001', 'X-time' => $tStamp, 'X-sign' => $encodedSignature, 'Accept' => 'application/json'])->post(''.env('LIS_URL').'/api/v1/saveEditOrder', $post);

        info($addEditOrder);

        return $addEditOrder;
    }
}

if (!function_exists('subEditOrderLis')) {   
    /**
     * Save Order ke API LIS
     *
     * @param  mixed $number
     * @return void
     */
    function subEditOrderLis($visit, RequestLab $requestLab, $action_id) {
        $data = [];

        $row['id_test'] = strval($action_id->action_id);
        $row['nama_test'] = $action_id->name;
        $row['is_add'] = "false" ;
        $data[] = $row;

        $post = [
            "no_laboratorium" => strval($requestLab->laboratorium_id),  
            "no_rm" => strval($visit['norm']),  
            "no_order" => strval($requestLab->unique_id), 
            "keterangan" => "",
            "detail" => $data 
        ];

        info([$visit, strval($requestLab->laboratorium_id)]);

        //X-time
        date_default_timezone_set('UTC'); 
        $tStamp = strval(time()-strtotime('1970-01-01 00:00:00')); 

        //X-sign
        $xcons = "testtesttest";
        $xkey = "secretkey";
        $signature = hash_hmac('sha256', $xcons, $xkey, true);
        $encodedSignature = base64_encode($signature);

        $subEditOrder = Http::withoutVerifying()->withHeaders(['X-cons' => '330913001', 'X-time' => $tStamp, 'X-sign' => $encodedSignature, 'Accept' => 'application/json'])->post(''.env('LIS_URL').'/api/v1/saveEditOrder', $post);

        info($subEditOrder);

        return $subEditOrder;
    }
}

