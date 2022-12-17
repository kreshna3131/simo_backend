<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>HASIL PEMERIKSAAN RAWAT JALAN TERINTEGRASI</title>
    <link rel="stylesheet" href="{{ public_path('assets/vendor/bootstrap-3.4.1.min.css') }}">
    <style>
        @page {
            size: a4;
            margin: 0mm 0mm 0mm 0mm;
        }
        @font-face {
            font-family: "Calibri Regular";
            src: url("{{ public_path('assets/font/calibri/calibri-regular.ttf') }}");
            font-weight: normal;
            font-style: normal;
        }
        @font-face {
            font-family: "Calibri Bold";
            src: url("{{ public_path('assets/font/calibri/calibri-bold.ttf') }}");
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
            padding-right: 20px;
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
        .table-striped>tbody>tr:nth-child(odd)>td, 
        .table-striped>tbody>tr:nth-child(odd)>th {
            background-color: rgba(13, 154, 137, 0.08); // Choose your own color here
        }
        .line-header {
            border: 0;
            border-style: inset;
            border-top: 2px solid #000000;
        }
        .page-break {
            page-break-before: always; 
        }
    </style>
</head>
<body style="background-image: url('{{ public_path('assets/images/background/Bg-Assesment.png') }}'); background-repeat: no-repeat; background-position: center;">
    <div class="tbr_header">
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
        <hr class="line-header">
    </div>
    <div class="tbr_profile">
        <table style="width: 100%;">
            <tr>
                <td style="width: 80%;">
                    <table class="tbr_table--profile" style="width: 100%;">
                        <tr>
                            <td class="first-table">No. RM</td>
                            <td class="middle-table">:</td>
                            <td>{{ $visits ? $visits['norm'] : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="first-table">Tanggal dan jam kunjungan</td>
                            <td class="middle-table">:</td>
                            <td>{{ $visits ? formatFromTo($visits['tglawal'], 'Y-m-d H:i:s', 'd F Y \j\a\m H:i') : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="first-table">Nama</td>
                            <td class="middle-table">:</td>
                            <td>{{ $visits ? $visits['nama'] : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="first-table">Tgl Lahir</td>
                            <td class="middle-table">:</td>
                            <td>{{ $visits ? formatFromTo($visits['tgllahir'], 'Y-m-d', 'd F Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="first-table" style="vertical-align: top;">Alamat</td>
                            <td>:</td>
                            <td>{{ $visits ? $visits['alamat'] : '-' }}</td>
                        </tr>
                    </table>
                </td>
                <td style="width: 20%; vertical-align: top;">
                    <table style="width: 100%; text-align: right;">
                        <tr>
                            <td>{{ formatDate($visits['tglawal'], 'd F Y'); }}</td>
                        </tr>
                        <tr>
                            <td>{{ $integration->sub_assesment_id ? 'Kunjungan '. $soap->visit_number : "Non Integrasi" }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <div class="tbr_body mt-2">
        <table style="width: 100%">
            <tr>
                <th>
                    <td class="tbr_font-bold" align="center" style="background-color: #0D9A89; color: #FFFFFF; height: 30px;">HASIL PEMERIKSAAN RAWAT JALAN TERINTEGRASI</td>
                </th>
            </tr>
        </table>
        <table style="width: 100%; margin-top: 20px">
            <tr>
                <th>
                    <td class="tbr_font-bold" align="left" style="background-color: #0D9A89; color: #FFFFFF; padding: 0 20px; height: 30px;">{{ $integration->sub_assesment_id ? $integration->subAssesment->template->name : "Non Integrasi" }}</td>
                    <td class="tbr_font-bold" align="right" style="background-color: #0D9A89; color: #FFFFFF; padding: 0 20px; height: 30px;">{{ $integration->sub_assesment_id ? 'SOAP #'. $soap->soap_number : "Non Integrasi" }}</td>
                </th>
            </tr>
        </table>
        <table style="width: 100%">
            <tr>
                <th>
                    <td class="tbr_font-bold" align="center" style="height: 30px; border-bottom: 1px solid #000000;">Detail Pemeriksa</td>
                </th>
            </tr>
        </table>
        <table class="tbr_table--striped table-striped" style="width: 100%">
            <tr>
                <td class="first-table" style="width: 50%; padding: 0 20px;">Tanggal pemeriksaan</td>
                <td class="middle-table" style="width: 5%">:</td>
                <td style="width: 45%">{{ $integration->created_at }}</td>
            </tr>
            <tr>
                <td class="first-table" style="width: 50%; padding: 0 20px;">Oleh</td>
                <td class="middle-table" style="width: 5%">:</td>
                <td style="width: 45%">{{ $integration->created_by }}</td>
            </tr>
            <tr>
                <td class="first-table" style="width: 50%; padding: 0 20px;">Sebagai</td>
                <td class="middle-table" style="width: 5%">:</td>
                <td style="width: 45%">{{ $integration->created_role }}</td>
            </tr>
        </table>
        <table style="width: 100%">
            <tr>
                <th>
                    <td class="tbr_font-bold" align="center" style="height: 30px; border-bottom: 1px solid #000000;">Subjective (S)</td>
                </th>
            </tr>
        </table>
        <table class="tbr_table--striped table-striped" style="width: 100%">
            <tr>
                <td style="width: 100%; padding: 0 20px;">
                    {{ $integration->keluhan }}
                </td>
            </tr>
        </table>
        <table style="width: 100%">
            <tr>
                <th>
                    <td class="tbr_font-bold" align="center" style="height: 30px; border-bottom: 1px solid #000000;">Objective (O)</td>
                </th>
            </tr>
        </table>
        <table class="tbr_table--striped table-striped" style="width: 100%">
            <tr>
                <td class="first-table" style="width: 50%; padding: 0 20px;">Tekanan darah</td>
                <td class="middle-table" style="width: 5%">:</td>
                <td style="width: 45%">{{ $integration->tekanan_darah }}</td>
            </tr>
            <tr>
                <td class="first-table" style="width: 50%; padding: 0 20px;">Frekuensi nadi</td>
                <td class="middle-table" style="width: 5%">:</td>
                <td style="width: 45%">{{ $integration->frekuensi_nadi }}</td>
            </tr>
            <tr>
                <td class="first-table" style="width: 50%; padding: 0 20px;">Frekuensi napas</td>
                <td class="middle-table" style="width: 5%">:</td>
                <td style="width: 45%">{{ $integration->frekuensi_napas }}</td>
            </tr>
            <tr>
                <td class="first-table" style="width: 50%; padding: 0 20px;">Suhu</td>
                <td class="middle-table" style="width: 5%">:</td>
                <td style="width: 45%">{{ $integration->suhu_badan }}</td>
            </tr>
            <tr>
                <td class="first-table" style="width: 50%; padding: 0 20px;">Berat badan</td>
                <td class="middle-table" style="width: 5%">:</td>
                <td style="width: 45%">{{ $integration->berat_badan }}</td>
            </tr>
            <tr>
                <td class="first-table" style="width: 50%; padding: 0 20px;">Tinggi badan</td>
                <td class="middle-table" style="width: 5%">:</td>
                <td style="width: 45%">{{ $integration->tinggi_badan }}</td>
            </tr>
            <tr>
                <td class="first-table" style="width: 50%; padding: 0 20px;">GDS</td>
                <td class="middle-table" style="width: 5%">:</td>
                <td style="width: 45%">{{ $integration->gds }}</td>
            </tr>
            <tr>
                <td class="first-table" style="width: 50%; padding: 0 20px;">Keadaan umum</td>
                <td class="middle-table" style="width: 5%">:</td>
                <td style="width: 45%">{{ $integration->keadaan_umum }}</td>
            </tr>
            <tr>
                <td class="first-table" style="width: 50%; padding: 0 20px;">Tindakan resusitasi</td>
                <td class="middle-table" style="width: 5%">:</td>
                <td style="width: 45%">{{ $integration->tindakan_resusitasi }}</td>
            </tr>
        </table>
        <table style="width: 100%">
            <tr>
                <th>
                    <td class="tbr_font-bold" align="center" style="height: 30px; border-bottom: 1px solid #000000;">Assesment (A)</td>
                </th>
            </tr>
        </table>
        <table class="tbr_table--striped table-striped" style="width: 100%">
            <tr>
                <td style="width: 100%; padding: 0 20px;">
                    {{ $integration->integration == 'medis' ? $integration->diagnosis_kerja : $integration->diagnosis_keperawatan }}
                </td>
            </tr>
        </table>
        <table style="width: 100%">
            <tr>
                <th>
                    <td class="tbr_font-bold" align="center" style="height: 30px; border-bottom: 1px solid #000000;">Plan (P)</td>
                </th>
            </tr>
        </table>
        <table class="tbr_table--striped table-striped" style="width: 100%">
            @if ($integration->integration == 'medis')
                <tr>
                    <td class="first-table" style="width: 50%; padding: 0 20px;">Rencana Terapi</td>
                    <td class="middle-table" style="width: 5%">:</td>
                    <td style="width: 45%">{{ $integration->rencana_terapi }}</td>
                </tr>
                <tr>
                    <td class="first-table" style="width: 50%; padding: 0 20px;">Rencana tindak lanjut</td>
                    <td class="middle-table" style="width: 5%">:</td>
                    <td style="width: 45%">{{ $integration['rencana_tindak_lanjut'] ? collect($integration['rencana_tindak_lanjut'])['value'] : '-' }}</td>
                </tr>
                @if ($integration['rencana_tindak_lanjut'])
                    {{ info(collect(collect($integration['rencana_tindak_lanjut'])['childValue'])) }}
                    @if (collect($integration['rencana_tindak_lanjut'])['value'] == 'Rawat Inap')
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">Ruang</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ collect(collect($integration['rencana_tindak_lanjut'])['childValue'])['rencana_tindak_lanjut_rawat_inap_ruang'] ? collect(collect($integration['rencana_tindak_lanjut'])['childValue'])['rencana_tindak_lanjut_rawat_inap_ruang'] : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">Indikasi</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ collect(collect($integration['rencana_tindak_lanjut'])['childValue'])['rencana_tindak_lanjut_rawat_inap_indikasi'] ? collect(collect($integration['rencana_tindak_lanjut'])['childValue'])['rencana_tindak_lanjut_rawat_inap_indikasi'] : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">DPJP</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ collect(collect($integration['rencana_tindak_lanjut'])['childValue'])['rencana_tindak_lanjut_rawat_inap_dpjp'] ? collect(collect($integration['rencana_tindak_lanjut'])['childValue'])['rencana_tindak_lanjut_rawat_inap_dpjp'] : '-' }}</td>
                        </tr>
                    @endif
                    @if (collect($integration['rencana_tindak_lanjut'])['value'] == 'Rujuk ke')
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">RS</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ collect(collect($integration['rencana_tindak_lanjut'])['childValue'])['rencana_tindak_lanjut_rujuk_ke_rs'] ? collect(collect($integration['rencana_tindak_lanjut'])['childValue'])['rencana_tindak_lanjut_rujuk_ke_rs'] : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">dr Spesialis</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ collect(collect($integration['rencana_tindak_lanjut'])['childValue'])['rencana_tindak_lanjut_rujuk_ke_dokter_spesialis'] ? collect(collect($integration['rencana_tindak_lanjut'])['childValue'])['rencana_tindak_lanjut_rujuk_ke_dokter_spesialis'] : '-' }}</td>
                        </tr>
                    @endif
                    @if (collect($integration['rencana_tindak_lanjut'])['value'] == 'Konsul ke')
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">dr Spesialis</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ collect(collect($integration['rencana_tindak_lanjut'])['childValue'])['rencana_tindak_lanjut_konsul_ke_dokter_spesialis'] ? collect(collect($integration['rencana_tindak_lanjut'])['childValue'])['rencana_tindak_lanjut_konsul_ke_dokter_spesialis'] : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">Gizi</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ collect(collect($integration['rencana_tindak_lanjut'])['childValue'])['rencana_tindak_lanjut_konsul_ke_gizi'] ? collect(collect($integration['rencana_tindak_lanjut'])['childValue'])['rencana_tindak_lanjut_konsul_ke_gizi'] : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">Lain-lain</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ collect(collect($integration['rencana_tindak_lanjut'])['childValue'])['rencana_tindak_lanjut_konsul_ke_lain_lain'] ? collect(collect($integration['rencana_tindak_lanjut'])['childValue'])['rencana_tindak_lanjut_konsul_ke_lain_lain'] : '-' }}</td>
                        </tr>
                    @endif
                @endif
            @else 
                <tr>
                    <td style="width: 100%; padding: 0 20px;">
                        {{ $integration->implementasi}}
                    </td>
                </tr>
            @endif
        </table>
    </div>
</body>
</html>