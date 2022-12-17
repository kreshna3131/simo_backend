<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-15">
    <title>E-Resep - {{ $recipe->unique_id }}</title>
    <link rel="stylesheet" href="{{ public_path('assets/vendor/bootstrap-3.4.1.min.css') }}">
    <style>
        @page {
            size: a4;
            margin: 0mm 0mm 0mm 0mm;
        }
        @font-face {
            font-family: "Calibri Regular";
            src: url("{{ public_path('font/calibri/calibri-regular.ttf') }}");
            font-weight: normal;
            font-style: normal;
        }
        @font-face {
            font-family: "Calibri Bold";
            src: url("{{ public_path('font/calibri/calibri-bold.ttf') }}");
            font-weight: normal;
            font-style: normal;
        }
        body {
            font-family: 'Calibri Regular', sans-serif;
            padding: 40px 50px;
            font-size: 15px;
            line-height: 17px;
            letter-spacing: .3px;
        }
        .tbr_font-regular {
            font-family: 'Calibri Regular', sans-serif;
        }
        .tbr_font-bold {
            font-family: 'Calibri Bold', sans-serif;
        }
        .p-0 { padding: 0 !important; }
        .px-2 { padding: 0 20px !important; }
        .m-0 { margin: 0 !important; }
        .mt-1 { margin-top: 10px !important; }
        .mt-2 { margin-top: 20px !important; }
        .mt-3 { margin-top: 30px !important; }
        .mt-4 { margin-top: 40px !important; }
        .mt-5 { margin-top: 50px !important; }
        .mb-1 { margin-bottom: 10px !important; }
        .mb-2 { margin-bottom: 20px !important; }
        .mb-3 { margin-bottom: 30px !important; }
        .mb-4 { margin-bottom: 40px !important; }
        .mb-5 { margin-bottom: 50px !important; }
        .tbr_table--profile tr td.middle-table {
            padding-right: 20px;
            padding-left: 0;
        }
        .tbr_table--profile tr td.first-table {
            padding-right: 30px;
            padding-left: 0;
        }
        .tbr_table--striped tr td.middle-table {
            padding-right: 10px;
            padding-left: 0;
        }
        .tbr_table--striped tr td.first-table {
            padding-right: 0;
            padding-left: 0;
        }
        .table-striped>tbody>tr:nth-child(odd)>td {
            background-color: rgba(13, 154, 137, 0.08); // Choose your own color here
        }
        .table-striped>tbody>tr>th{
            font-size: 12px !important;
            font-family: 'Calibri Bold', sans-serif;
        }
        .line-header {
            border: 0;
            border-style: inset;
            border-top: 2px solid #000000;
        }
        .line-recipe {
            border: 1px solid;
            border-style: dashed;
            color: rgba(12, 150, 189, 0.3);
            margin-top: 10px;
        }
        .page-break {
            page-break-after: always; 
            border: 0;
        }
        .patient-detail { 
            border-radius: 10px;
            padding: 10px;
            background-color: rgba(12, 150, 189, 0.08);
        }
        .dashed {
            border: 2px rgba(12, 150, 189, 0.3) dashed;
        }
    </style>
</head>
<body style="background-image: url('{{ public_path('assets/images/background/Bg-Assesment.png') }}'); background-repeat: no-repeat; background-position: center;">
    <div class="tbr_header" style="position: fixed; width: 87.5%; top: 40px; overflow:hidden">
        <table style="width: 100%">
            <tr>
                <td>
                    <img src="{{ public_path('assets/images/logo/Logo-Kab.png') }}" alt="" style="width: 60px; height: auto;">
                </td>
                <td align="center">
                    <div class="tbr_font-bold">
                        PEMERINTAH KABUPATEN BOYOLALI <br>
                        RUMAH SAKIT UMUM DAERAH SIMO <br>
                    </div>
                    <div class="tbr_font-regular">Jl. Simo-Bangak Km. 1, Ds. Palem, Kec. Simo, Kab. Boyolali 57377 <br>
                        No. Telp/Faks ( 0276 ) 3294719 Email : rsusimo@yahoo.com
                    </div>
                </td>
                <td align="right">
                    <img src="{{ public_path('assets/images/logo/Logo-RSUD.png') }}" alt="" style="width: 60px; height: auto;">
                </td>
            </tr>
        </table>
    </div>
    <div style="position: fixed; width: 87.5%; top: 140px; overflow:hidden">
        <table style="width: 100%">
            <tr>
                <th>
                    <td align="left" style="background-color: #0D9A89; width: 50%; color: #FFFFFF; padding: 6px 20px;">
                        <table style="width: 100%">
                            <tr>
                                <td class="first-table" style="width: 30%;">Nama Dokter</td>
                                <td class="middle-table" style="width: 5%">:</td>
                                <td style="width: 65%">{{ $user->name }}</td>
                            </tr>
                            <tr>
                                <td class="first-table" style="width: 30%;">Nomor SIP</td>
                                <td class="middle-table" style="width: 5%">:</td>
                                <td style="width: 65%">{{ $user->sip_number }}</td>
                            </tr>
                        </table>
                    </td>
                    <td align="right" style="background-color: rgba(12, 150, 189, 0.08); width: 50%; color: #000000; padding: 6px 20px;">
                        <table style="width: 100%">
                            <tr>
                                <td align="left" class="first-table" style="width: 50%;">U/JKN/Jamkesda</td>
                                <td align="right" style="width: 50%">{{ now()->format('d F Y') }} <br> Kunjungan ke {{ $recipe->visit_number }}</td>
                            </tr>
                        </table>
                    </td>
                </th>
            </tr>
        </table>
    </div>
    <div style="position: fixed; width: 40%; top: 212px; right: 50px; overflow:hidden">
        I. TELAAH RESEP
        <table style="width: 100%; border: 1px solid">
            <tbody>
                <tr style="border: 1px solid">
                    <td align="center" style="border: 1px solid; width: 10%; padding: 1px 6px;">No</td>
                    <td align="center" style="border: 1px solid; width: 45%; padding: 1px 6px;">Uraian</td>
                    <td align="center" style="border: 1px solid; width: 15%; padding: 1px 6px;">Ya</td>
                    <td align="center" style="border: 1px solid; width: 15%; padding: 1px 6px;">Tidak</td>
                    <td align="center" style="border: 1px solid; width: 15%; padding: 1px 6px;">Ket</td>
                </tr>
                <tr style="border: 1px solid">
                    <td align="center" style="border: 1px solid; padding: 1px 6px;">1</td>
                    <td style="border: 1px solid; padding: 1px 6px;">Administrasi</td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                </tr>
                <tr style="border: 1px solid">
                    <td align="center" style="border: 1px solid; padding: 1px 6px;">2</td>
                    <td style="border: 1px solid; padding: 1px 6px;">Farmeatis</td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                </tr>
                <tr style="border: 1px solid">
                    <td align="center" style="border: 1px solid; padding: 1px 6px;">3</td>
                    <td style="border: 1px solid; padding: 1px 6px;">Klinis</td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                </tr>
                <tr style="border: 1px solid">
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                    <td style="border: 1px solid; padding: 1px 6px;">a. Tepat dosis</td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                </tr>
                <tr style="border: 1px solid">
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                    <td style="border: 1px solid; padding: 1px 6px;">b. Tepat rule</td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                </tr>
                <tr style="border: 1px solid">
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                    <td style="border: 1px solid; padding: 1px 6px;">c. Tepat waktu</td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                </tr>
                <tr style="border: 1px solid">
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                    <td style="border: 1px solid; padding: 1px 6px;">d. Duplikasi</td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                </tr>
                <tr style="border: 1px solid">
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                    <td style="border: 1px solid; padding: 1px 6px;">e. Kontra Indikasi</td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                </tr>
                <tr style="border: 1px solid">
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                    <td style="border: 1px solid; padding: 1px 6px;">f. Interaksi</td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                </tr>
                <tr style="border: 1px solid">
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                    <td style="border: 1px solid; padding: 1px 6px;">g. Alergi</td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                </tr>
            </tbody>
        </table>
        II. TELAAH OBAT
        <table style="width: 100%; margin-top: 10px; border: 1px solid">
            <tbody>
                <tr style="border: 1px solid">
                    <td align="center" style="border: 1px solid; width: 10%; padding: 1px 6px;">No</td>
                    <td align="center" style="border: 1px solid; width: 45%; padding: 1px 6px;">Uraian</td>
                    <td align="center" style="border: 1px solid; width: 15%; padding: 1px 6px;">Ya</td>
                    <td align="center" style="border: 1px solid; width: 15%; padding: 1px 6px;">Tidak</td>
                    <td align="center" style="border: 1px solid; width: 15%; padding: 1px 6px;">Ket</td>
                </tr>
                <tr style="border: 1px solid">
                    <td align="center" style="border: 1px solid; padding: 1px 6px;">1</td>
                    <td style="border: 1px solid; padding: 1px 6px;">Obat sesuai</td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                </tr>
                <tr style="border: 1px solid">
                    <td align="center" style="border: 1px solid; padding: 1px 6px;">2</td>
                    <td style="border: 1px solid; padding: 1px 6px;">Dosis</td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                </tr>
                <tr style="border: 1px solid">
                    <td align="center" style="border: 1px solid; padding: 1px 6px;">3</td>
                    <td style="border: 1px solid; padding: 1px 6px;">Jumlah</td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                </tr>
                <tr style="border: 1px solid">
                    <td align="center" style="border: 1px solid; padding: 1px 6px;">4</td>
                    <td style="border: 1px solid; padding: 1px 6px;">Frekuensi</td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                </tr>
                <tr style="border: 1px solid">
                    <td align="center" style="border: 1px solid; padding: 1px 6px;">5</td>
                    <td style="border: 1px solid; padding: 1px 6px;">Rute</td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                </tr>
            </tbody>
        </table>
        III. STANDAR PELAYANAN MINIMAL (SPM)
        <table style="width: 100%; margin-top: 10px; border: 1px solid">
            <tbody>
                <tr style="border: 1px solid">
                    <td align="center" style="border: 1px solid; padding: 1px 6px;">Keterangan</td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;">Paraf</td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;">Jam</td>
                </tr>
                <tr style="border: 1px solid">
                    <td style="border: 1px solid; padding: 1px 6px;">PENERIMA RESEP</td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                </tr>
                <tr style="border: 1px solid">
                    <td style="border: 1px solid; padding: 1px 6px;">TELAAH</td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                </tr>
                <tr style="border: 1px solid">
                    <td style="border: 1px solid; padding: 1px 6px;">RESEP / OBAT</td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                </tr>
                <tr style="border: 1px solid">
                    <td style="border: 1px solid; padding: 1px 6px;">KIE</td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                </tr>
                <tr style="border: 1px solid">
                    <td style="border: 1px solid; padding: 1px 6px;">PENERIMA OBAT</td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;"></td>
                </tr>
            </tbody>
        </table>
        III. KESESUAIAN FORNAS & FORMULARIUM RS
        <table style="width: 100%; margin-top: 10px; border: 1px solid">
            <tbody>
                <tr style="border: 1px solid">
                    <td align="center" colspan="2" style="border: 1px solid; padding: 1px 6px;">FORMULARIUM NASIONAL</td>
                    <td align="center" colspan="2" style="border: 1px solid; padding: 1px 6px;">FORMULARIUM RS</td>
                </tr>
                <tr style="border: 1px solid">
                    <td align="center" style="border: 1px solid; padding: 1px 6px;">Jumlah Sesuai</td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;">Jml. Tidak Sesuai</td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;">Jumlah Sesuai</td>
                    <td align="center" style="border: 1px solid; padding: 1px 6px;">Jml. Tidak Sesuai</td>
                </tr>
                <tr style="border: 1px solid">
                    <td align="center" style="border: 1px solid; height: 30px"></td>
                    <td align="center" style="border: 1px solid; height: 30px"></td>
                    <td align="center" style="border: 1px solid; height: 30px"></td>
                    <td align="center" style="border: 1px solid; height: 30px"></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="row" style="margin-top: 170px; padding-right: 60px;">
        <div class="col-xs-6">
            @foreach ($nonconcoctions as $key => $nonconcoction)
                <table style="width: 100%; {{ $key > 1 && ($key + 1) % 5 == 1 ? 'padding-top: 170px;' : '' }}">
                    <tbody>
                        <tr>
                            <td>
                                R/
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-left: 20px;">
                                {{ $nonconcoction->medicine_name }} {{ $nonconcoction->medicine_unit }} No. {{ numberToRoman($nonconcoction->medicine_quantity) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-left: 20px;">
                                ∫ {{ count(explode(', ', $nonconcoction->medicine_use_time)) }} d.d {{ $nonconcoction->medicine_suggestion_use }} 
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-left: 20px;">
                                {{ $nonconcoction->medicine_use_time }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="line-recipe"></div>                        
                @if (($key + 1) % 5 == 0 || $nonconcoctions->count() == ($key + 1) && $concoctions->count() == 0)
                    <table style="width: 100%; margin-top: 40px;">
                        <tr>
                            <th>
                                <tr>
                                    <td style="width: 30%">
                                        Nomor CM
                                    </td>
                                    <td style="width: 70%">
                                        <div style="background-color: rgba(12, 150, 189, 0.08); border-radius: 10px 10px 0 0; border: 2px rgba(12, 150, 189, 0.3) dashed; border-bottom: none; padding: 0 10px 0 10px;">
                                            {{ $visits['norm'] }}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 30%">
                                        Nama Pasien
                                    </td>
                                    <td style="width: 70%">
                                        <div style="background-color: rgba(12, 150, 189, 0.08); border: 2px rgba(12, 150, 189, 0.3) dashed; border-bottom: none; border-top: none; padding: 0 10px 0 10px;">
                                            {{ $visits['nama'] }}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 30%">
                                        Umur
                                    </td>
                                    <td style="width: 70%">
                                        <div style="background-color: rgba(12, 150, 189, 0.08); border: 2px rgba(12, 150, 189, 0.3) dashed; border-bottom: none; border-top: none; padding: 0 10px 0 10px;">
                                            {{ Carbon\Carbon::parse($visits['tgllahir'])->diff(Carbon\Carbon::now())->format('%y tahun %m bulan %d hari') }}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 30%">
                                        Berat Badan
                                    </td>
                                    <td style="width: 70%">
                                        <div style="background-color: rgba(12, 150, 189, 0.08); border: 2px rgba(12, 150, 189, 0.3) dashed; border-bottom: none; border-top: none; padding: 0 10px 0 10px;">
                                            {{ $weight ? $weight->berat_badan : 0 }} Kg
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 30%">
                                        Alamat
                                    </td>
                                    <td style="width: 70%">
                                        <div style="background-color: rgba(12, 150, 189, 0.08); border-radius: 0 0 10px 10px; border: 2px rgba(12, 150, 189, 0.3) dashed; border-top: none; padding: 0 10px 0 10px;">
                                            {{ $visits['alamat'] }}
                                        </div>
                                    </td>
                                </tr>
                            </th>
                        </tr>
                    </table>
                    @if (($key + 1) % 5 == 0 && $nonconcoctions->count() != ($key + 1))
                        <div class="page-break"></div>
                    @endif
                @endif
            @endforeach
            @foreach ($concoctions as $key => $concoction)
                <table style="width: 100%; {{ $nonconcoctions->count() != 0 && ($nonconcoctions->count() + ($key + 1)) % 5 == 1 ? 'padding-top: 170px;' : '' }}">
                    <tbody>
                        <tr>
                            <td>
                                R/
                            </td>
                        </tr>
                        @foreach ($concoction->concoctionMedicines as $medicine)
                            <tr>
                                <td style="padding-left: 20px;">
                                    {{ $medicine->name }} {{ $medicine->unit }} {{ $medicine->strength }} {{ $medicine->dose }}
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td style="padding-left: 20px;">
                                {{ $concoction->name }} No. {{ numberToRoman($concoction->total) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-left: 20px;">
                                ∫ {{ count(explode(', ', $concoction->use_time)) }} d.d {{ $concoction->suggestion_use }} 
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-left: 20px;">
                                {{ $concoction->use_time }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="line-recipe"></div>
                @if (($nonconcoctions->count() + ($key + 1)) % 5 == 0 || $concoctions->count() == ($key + 1))
                    <table style="width: 100%; margin-top: 40px;">
                        <tr>
                            <th>
                                <tr>
                                    <td style="width: 30%">
                                        Nomor CM
                                    </td>
                                    <td style="width: 70%">
                                        <div style="background-color: rgba(12, 150, 189, 0.08); border-radius: 10px 10px 0 0; border: 2px rgba(12, 150, 189, 0.3) dashed; border-bottom: none; padding: 0 10px 0 10px;">
                                            {{ $visits['norm'] }}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 30%">
                                        Nama Pasien
                                    </td>
                                    <td style="width: 70%">
                                        <div style="background-color: rgba(12, 150, 189, 0.08); border: 2px rgba(12, 150, 189, 0.3) dashed; border-bottom: none; border-top: none; padding: 0 10px 0 10px;">
                                            {{ $visits['nama'] }}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 30%">
                                        Umur
                                    </td>
                                    <td style="width: 70%">
                                        <div style="background-color: rgba(12, 150, 189, 0.08); border: 2px rgba(12, 150, 189, 0.3) dashed; border-bottom: none; border-top: none; padding: 0 10px 0 10px;">
                                            {{ Carbon\Carbon::parse($visits['tgllahir'])->diff(Carbon\Carbon::now())->format('%y tahun %m bulan %d hari') }}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 30%">
                                        Berat Badan
                                    </td>
                                    <td style="width: 70%">
                                        <div style="background-color: rgba(12, 150, 189, 0.08); border: 2px rgba(12, 150, 189, 0.3) dashed; border-bottom: none; border-top: none; padding: 0 10px 0 10px;">
                                            {{ $weight ? $weight->berat_badan : 0 }} Kg
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 30%">
                                        Alamat
                                    </td>
                                    <td style="width: 70%">
                                        <div style="background-color: rgba(12, 150, 189, 0.08); border-radius: 0 0 10px 10px; border: 2px rgba(12, 150, 189, 0.3) dashed; border-top: none; padding: 0 10px 0 10px;">
                                            {{ $visits['alamat'] }}
                                        </div>
                                    </td>
                                </tr>
                            </th>
                        </tr>
                    </table>
                    @if (($nonconcoctions->count() + ($key + 1)) % 5 == 0)
                        <div class="page-break"></div>
                    @endif
                @endif
            @endforeach
        </div>
    </div>
    <script type="text/php">
        if ( isset($pdf) ) {
        
          $size = 6;
          $color = array(0,0,0);
          if (class_exists('Font_Metrics')) {
            $font = Font_Metrics::get_font("helvetica");
            $text_height = Font_Metrics::get_font_height($font, $size);
            $width = Font_Metrics::get_text_width("Page 1 of 2", $font, $size);
          } elseif (class_exists('Dompdf\\FontMetrics')) {
            $font = $fontMetrics->getFont("helvetica");
            $text_height = $fontMetrics->getFontHeight($font, $size);
            $width = $fontMetrics->getTextWidth("Page 1 of 2", $font, $size);
          }
        
          $foot = $pdf->open_object();
          
          $w = $pdf->get_width();
          $h = $pdf->get_height();
        
          // Draw a line along the bottom
          $y = $h - $text_height - 24;
        
          $pdf->close_object();
          $pdf->add_object($foot, "all");
        
          $text = "Page {PAGE_NUM} of {PAGE_COUNT}";  
        
          // Center the text
          $pdf->page_text($w / 2 - $width / 2, $y, $text, $font, $size, $color);    
        }
        </script>
</body>
</html>