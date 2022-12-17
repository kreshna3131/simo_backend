<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $assesments->template->name }}</title>
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
                            <td class="tbr_font-bold" style="color: #0D9A89; font-size: 20px;">SOAP #{{ $soap->soap_number }} </td>
                        </tr>
                        <tr>
                            <td>{{ formatDate($visits['tglawal'], 'd F Y'); }}</td>
                        </tr>
                        <tr>
                            <td>Kunjungan ke {{ $soap->visit_number }}</td>
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
                    <td class="tbr_font-bold" align="center" style="background-color: #0D9A89; color: #FFFFFF; height: 30px;">{{ strtoupper($assesments->template->name) }}</td>
                </th>
            </tr>
        </table>
        @foreach ($attributes as $key => $attribute)
            <table style="width: 100%">
                <tr>
                    <th>
                        <td class="tbr_font-bold" align="center" style="height: 30px; border-bottom: 1px solid #000000;">{{ strtoupper($key) }}</td>
                    </th>
                </tr>
            </table>
            <table class="tbr_table--striped table-striped" style="width: 100%">
                @foreach ($attribute as $attr)
                    @if ($attr->type == 'conditional_radio_prepend_number' || $attr->type == 'conditional_radio_prepend_string')
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">{{ $attr->label }}</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ ($assesments[$attr->name] === 1) ? 'Ya' : (($assesments[$attr->name] === 0) ? 'Tidak' : '-')}} {{ $assesments[$attr->name] ? ', '. $assesments[$attr->name.'_text'] : '' }}</td>
                        </tr>
                    @elseif ($attr->type == 'radio')
                        @if ($assesments[$attr->name] === 1 || $assesments[$attr->name] === 0 || $assesments[$attr->name] == null)
                            <tr>
                                <td class="first-table" style="width: 50%; padding: 0 20px;">{{ $attr->label }}</td>
                                <td class="middle-table" style="width: 5%">:</td>
                                <td style="width: 45%">{{ ($assesments[$attr->name] === 0) ? 'Tidak' : ($assesments[$attr->name] === 1 ? 'Ya' : '-') }}</td>
                            </tr>
                        @else 
                            <tr>
                                <td class="first-table" style="width: 50%; padding: 0 20px;">{{ $attr->label }}</td>
                                <td class="middle-table" style="width: 5%">:</td>
                                <td style="width: 45%">{{ ucfirst($assesments[$attr->name]) }}</td>
                            </tr>
                        @endif
                    @elseif ($attr->type == 'pernah_mondok')
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">Pernah Mondok di RSUD Simo?</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ ($assesments['pernah_dirawat_simo'] === 1) ? 'Ya' : ($assesments['pernah_dirawat_simo'] === 0 ? 'Tidak' : '-') }}</td>
                        </tr>
                        @if ($assesments['pernah_dirawat_simo'] == 1)
                            <tr>
                                <td class="first-table" style="width: 50%; padding: 0 20px;">Mondok di RSUD Simo yang ke</td>
                                <td class="middle-table" style="width: 5%">:</td>
                                <td style="width: 45%">{{ $assesments['inap_ke_simo'] }}</td>
                            </tr>
                            <tr>
                                <td class="first-table" style="width: 50%; padding: 0 20px;">Dirawat terakhir tanggal</td>
                                <td class="middle-table" style="width: 5%">:</td>
                                <td style="width: 45%">{{ formatFromTo($assesments['terakhir_dirawat_simo'], 'Y-m-d H:i:s', 'd F Y') }}</td>
                            </tr>
                            <tr>
                                <td class="first-table" style="width: 50%; padding: 0 20px;">Dirawat terakhir di ruang</td>
                                <td class="middle-table" style="width: 5%">:</td>
                                <td style="width: 45%">{{ $assesments['terakhir_dirawat_diruang_simo'] }}</td>
                            </tr>
                        @endif
                    @elseif ($attr->type == 'operasi_yang_pernah_dialami')
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">Pernah Operasi</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ ($assesments['operasi_yang_pernah_dialami'] === 1) ? 'Pernah' : ($assesments['operasi_yang_pernah_dialami'] === 0 ? 'Belum' : '-') }}</td>
                        </tr>
                        @if ($assesments['operasi_yang_pernah_dialami'] == 1)
                            <tr>
                                <td class="first-table" style="width: 50%; padding: 0 20px;">Jenis</td>
                                <td class="middle-table" style="width: 5%">:</td>
                                <td style="width: 45%">{{ $assesments['operasi_yang_pernah_dialami_jenis'] }}</td>
                            </tr>
                            <tr>
                                <td class="first-table" style="width: 50%; padding: 0 20px;">Kapan</td>
                                <td class="middle-table" style="width: 5%">:</td>
                                <td style="width: 45%">{{ $assesments['operasi_yang_pernah_dialami_kapan'] }}</td>
                            </tr>
                            <tr>
                                <td class="first-table" style="width: 50%; padding: 0 20px;">Komplikasi</td>
                                <td class="middle-table" style="width: 5%">:</td>
                                <td style="width: 45%">{{ $assesments['operasi_yang_pernah_dialami_komplikasi'] }}</td>
                            </tr>
                        @endif
                    @elseif ($attr->type == 'date')
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">{{ $attr->label }}</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ $assesments[$attr->name] ? formatFromTo($assesments[$attr->name], 'Y-m-d H:i:s', 'd F Y') : '-' }}</td>
                        </tr>
                    @elseif ($attr->type == 'rencana_tindak_lanjut')
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">{{ $attr->label }}</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ $assesments[$attr->name] ? collect($assesments[$attr->name])['value'] : '-' }}</td>
                        </tr>
                        @if ($assesments[$attr->name])
                            {{ info(collect(collect($assesments[$attr->name])['childValue'])) }}
                            @if (collect($assesments[$attr->name])['value'] == 'Rawat inap')
                                <tr>
                                    <td class="first-table" style="width: 50%; padding: 0 20px;">Rencana Tindak Lanjut (Ruang)</td>
                                    <td class="middle-table" style="width: 5%">:</td>
                                    <td style="width: 45%">{{ collect(collect($assesments[$attr->name])['childValue'])['rencana_tindak_lanjut_rawat_inap_ruang'] ? collect(collect($assesments[$attr->name])['childValue'])['rencana_tindak_lanjut_rawat_inap_ruang'] : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="first-table" style="width: 50%; padding: 0 20px;">Rencana Tindak Lanjut (Indikasi)</td>
                                    <td class="middle-table" style="width: 5%">:</td>
                                    <td style="width: 45%">{{ collect(collect($assesments[$attr->name])['childValue'])['rencana_tindak_lanjut_rawat_inap_indikasi'] ? collect(collect($assesments[$attr->name])['childValue'])['rencana_tindak_lanjut_rawat_inap_indikasi'] : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="first-table" style="width: 50%; padding: 0 20px;">Rencana Tindak Lanjut (DPJP)</td>
                                    <td class="middle-table" style="width: 5%">:</td>
                                    <td style="width: 45%">{{ collect(collect($assesments[$attr->name])['childValue'])['rencana_tindak_lanjut_rawat_inap_dpjp'] ? collect(collect($assesments[$attr->name])['childValue'])['rencana_tindak_lanjut_rawat_inap_dpjp'] : '-' }}</td>
                                </tr>
                            @endif
                            @if (collect($assesments[$attr->name])['value'] == 'Rujuk ke')
                                <tr>
                                    <td class="first-table" style="width: 50%; padding: 0 20px;">Rencana Tindak Lanjut (RS)</td>
                                    <td class="middle-table" style="width: 5%">:</td>
                                    <td style="width: 45%">{{ collect(collect($assesments[$attr->name])['childValue'])['rencana_tindak_lanjut_rujuk_ke_rs'] ? collect(collect($assesments[$attr->name])['childValue'])['rencana_tindak_lanjut_rujuk_ke_rs'] : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="first-table" style="width: 50%; padding: 0 20px;">Rencana Tindak Lanjut (dr Spesialis)</td>
                                    <td class="middle-table" style="width: 5%">:</td>
                                    <td style="width: 45%">{{ collect(collect($assesments[$attr->name])['childValue'])['rencana_tindak_lanjut_rujuk_ke_dokter_spesialis'] ? collect(collect($assesments[$attr->name])['childValue'])['rencana_tindak_lanjut_rujuk_ke_dokter_spesialis'] : '-' }}</td>
                                </tr>
                            @endif
                            @if (collect($assesments[$attr->name])['value'] == 'Konsul ke')
                                <tr>
                                    <td class="first-table" style="width: 50%; padding: 0 20px;">Rencana Tindak Lanjut (dr Spesialis)</td>
                                    <td class="middle-table" style="width: 5%">:</td>
                                    <td style="width: 45%">{{ collect(collect($assesments[$attr->name])['childValue'])['rencana_tindak_lanjut_konsul_ke_dokter_spesialis'] ? collect(collect($assesments[$attr->name])['childValue'])['rencana_tindak_lanjut_konsul_ke_dokter_spesialis'] : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="first-table" style="width: 50%; padding: 0 20px;">Rencana Tindak Lanjut (Gizi)</td>
                                    <td class="middle-table" style="width: 5%">:</td>
                                    <td style="width: 45%">{{ collect(collect($assesments[$attr->name])['childValue'])['rencana_tindak_lanjut_konsul_ke_dokter_gizi'] ? collect(collect($assesments[$attr->name])['childValue'])['rencana_tindak_lanjut_konsul_ke_dokter_gizi'] : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="first-table" style="width: 50%; padding: 0 20px;">Rencana Tindak Lanjut (Lain-lain)</td>
                                    <td class="middle-table" style="width: 5%">:</td>
                                    <td style="width: 45%">{{ collect(collect($assesments[$attr->name])['childValue'])['rencana_tindak_lanjut_konsul_ke_dokter_lain_lain'] ? collect(collect($assesments[$attr->name])['childValue'])['rencana_tindak_lanjut_konsul_ke_dokter_lain_lain'] : '-' }}</td>
                                </tr>
                            @endif
                        @endif
                    @elseif ($attr->type == 'thoraks')
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">{{ $attr->label . ' Cor' }}</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ $assesments[$attr->name.'_cor'] ? $assesments[$attr->name.'_cor'] : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">{{ $attr->label . ' Pulmo' }}</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ $assesments[$attr->name.'_pulmo'] ? $assesments[$attr->name.'_pulmo'] : '-' }}</td>
                        </tr>
                    @elseif ($attr->type == 'nutrisional_dewasa_penurunan_bb')
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">{{ $attr->label }}</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ ucfirst($assesments[$attr->name]) }} {{ $assesments[$attr->name] == 'ya' ? ', '. $assesments[$attr->name.'_pilihan'] : '' }}</td>
                        </tr>
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">{{ $attr->label. ' (Total Score)' }}</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ $assesments[$attr->name.'_total_skor'] }}</td>
                        </tr>
                    @elseif ($attr->type == 'analisa_keperawatan')
                        @if ($assesments[$attr->name])
                            @foreach (json_decode($assesments[$attr->name]) as $analisa)
                                <tr>
                                    <td style="width: 100%; padding: 0 20px;">
                                        <img src="{{ public_path('assets/images/icon/Check-Icon.png') }}" alt="{{ $analisa->value }}" style="margin-top: 10px;">
                                        {{ $analisa->value }} {{ isset($analisa->text) ? ': '. $analisa->text : '' }}
                                    </td>
                                </tr>
                            @endforeach
                        @else 
                            <tr>
                                <td style="width: 100%; padding: 0 20px;">
                                    -
                                </td>
                            </tr>
                        @endif
                    @elseif ($attr->type == 'riwayat_imunisasi')
                        @if ($assesments[$attr->name])
                            @foreach (json_decode($assesments[$attr->name]) as $imunisasi)
                                <tr>
                                    <td style="width: 100%; padding: 0 20px;">
                                        <img src="{{ public_path('assets/images/icon/Check-Icon.png') }}" alt="{{ $imunisasi }}" style="margin-top: 10px;">
                                        {{ $imunisasi }}
                                    </td>
                                </tr>
                            @endforeach
                        @else 
                            <tr>
                                <td style="width: 100%; padding: 0 20px;">
                                    -
                                </td>
                            </tr>
                        @endif
                    @elseif ($attr->name == 'neurologis_extrimitas')
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">Extremitas syaraf gerak (I)</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ $assesments->neurologis_extrimitas ? ucfirst(collect($assesments->neurologis_extrimitas)['extrimitas_syaraf_gerak_I_tanda']) : '-' }}, {{ $assesments->neurologis_extrimitas ? collect($assesments->neurologis_extrimitas)['extrimitas_syaraf_gerak_I_'.collect($assesments->neurologis_extrimitas)['extrimitas_syaraf_gerak_I_tanda'].''] : '-' }}</td>
                        </tr> 
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">Extremitas syaraf gerak (II)</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ $assesments->neurologis_extrimitas ? ucfirst(collect($assesments->neurologis_extrimitas)['extrimitas_syaraf_gerak_II_tanda']) : '-' }}, {{ $assesments->neurologis_extrimitas ? collect($assesments->neurologis_extrimitas)['extrimitas_syaraf_gerak_II_'.collect($assesments->neurologis_extrimitas)['extrimitas_syaraf_gerak_II_tanda'].''] : '-' }}</td>
                        </tr> 
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">Extremitas syaraf gerak (III)</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ $assesments->neurologis_extrimitas ? ucfirst(collect($assesments->neurologis_extrimitas)['extrimitas_syaraf_gerak_III_tanda']) : '-' }}, {{ $assesments->neurologis_extrimitas ? collect($assesments->neurologis_extrimitas)['extrimitas_syaraf_gerak_III_'.collect($assesments->neurologis_extrimitas)['extrimitas_syaraf_gerak_III_tanda'].''] : '-' }}</td>
                        </tr> 
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">Extremitas syaraf gerak (IV)</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ $assesments->neurologis_extrimitas ? ucfirst(collect($assesments->neurologis_extrimitas)['extrimitas_syaraf_gerak_IV_tanda']) : '-' }}, {{ $assesments->neurologis_extrimitas ? collect($assesments->neurologis_extrimitas)['extrimitas_syaraf_gerak_IV_'.collect($assesments->neurologis_extrimitas)['extrimitas_syaraf_gerak_IV_tanda'].''] : '-' }}</td>
                        </tr> 
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">Extremitas Refleks Fisiologi (I)</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ $assesments->neurologis_extrimitas ? ucfirst(collect($assesments->neurologis_extrimitas)['extrimitas_refleks_fisiologi_I']) : '-' }}</td>
                        </tr> 
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">Extremitas Refleks Fisiologi (II)</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ $assesments->neurologis_extrimitas ? ucfirst(collect($assesments->neurologis_extrimitas)['extrimitas_refleks_fisiologi_II']) : '-' }}</td>
                        </tr> 
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">Extremitas Refleks Fisiologi (III)</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ $assesments->neurologis_extrimitas ? ucfirst(collect($assesments->neurologis_extrimitas)['extrimitas_refleks_fisiologi_III']) : '-' }}</td>
                        </tr> 
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">Extremitas Refleks Fisiologi (IV)</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ $assesments->neurologis_extrimitas ? ucfirst(collect($assesments->neurologis_extrimitas)['extrimitas_refleks_fisiologi_IV']) : '-' }}</td>
                        </tr> 
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">Extremitas Kekuatan Motorik (I)</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ $assesments->neurologis_extrimitas ? ucfirst(collect($assesments->neurologis_extrimitas)['extrimitas_kekuatan_motorik_I']) : '-' }}</td>
                        </tr> 
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">Extremitas Kekuatan Motorik (II)</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ $assesments->neurologis_extrimitas ? ucfirst(collect($assesments->neurologis_extrimitas)['extrimitas_kekuatan_motorik_II']) : '-' }}</td>
                        </tr> 
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">Extremitas Kekuatan Motorik (III)</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ $assesments->neurologis_extrimitas ? ucfirst(collect($assesments->neurologis_extrimitas)['extrimitas_kekuatan_motorik_III']) : '-' }}</td>
                        </tr> 
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">Extremitas Kekuatan Motorik (IV)</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ $assesments->neurologis_extrimitas ? ucfirst(collect($assesments->neurologis_extrimitas)['extrimitas_kekuatan_motorik_IV']) : '-' }}</td>
                        </tr> 
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">Extremitas Refleks Patologi (I)</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ $assesments->neurologis_extrimitas ? ucfirst(collect($assesments->neurologis_extrimitas)['extrimitas_refleks_patologi_I']) : '-' }}</td>
                        </tr> 
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">Extremitas Refleks Patologi (II)</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ $assesments->neurologis_extrimitas ? ucfirst(collect($assesments->neurologis_extrimitas)['extrimitas_refleks_patologi_II']) : '-' }}</td>
                        </tr> 
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">Extremitas Refleks Patologi (III)</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ $assesments->neurologis_extrimitas ? ucfirst(collect($assesments->neurologis_extrimitas)['extrimitas_refleks_patologi_III']) : '-' }}</td>
                        </tr> 
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">Extremitas Refleks Patologi (IV)</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ $assesments->neurologis_extrimitas ? ucfirst(collect($assesments->neurologis_extrimitas)['extrimitas_refleks_patologi_IV']) : '-' }}</td>
                        </tr> 
                    @elseif ($attr->name == 'neurologis_kepala')
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">Kepala (Pupil)</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">Diameter {{ $assesments->neurologis_kepala ? collect($assesments->neurologis_kepala)['neurologis_kepala_diameter_pupil_kiri'] : '-' }}  / {{ $assesments->neurologis_kepala ? collect($assesments->neurologis_kepala)['neurologis_kepala_diameter_pupil_kanan'] : '-' }} {{ $assesments->neurologis_kepala ? ucfirst(collect($assesments->neurologis_kepala)['neurologis_kepala_ukuran_pupil']) : '-' }}</td>
                        </tr> 
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">Kepala (Refleks Cahaya)</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ $assesments->neurologis_kepala ? ucfirst(collect($assesments->neurologis_kepala)['neurologis_kepala_refleks_cahaya']) : '-' }}</td>
                        </tr> 
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">Kepala (Refleks Kornea)</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ $assesments->neurologis_kepala ? ucfirst(collect($assesments->neurologis_kepala)['neurologis_kepala_refleks_kornea']) : '-' }}</td>
                        </tr> 
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">Kepala (Nervi Cranialis (I-XII))</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ $assesments->neurologis_kepala ? ucfirst(collect($assesments->neurologis_kepala)['neurologis_kepala_nervi_kranialis']) : '-' }}</td>
                        </tr> 
                    @elseif ($attr->name == 'neurologis_leher')
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">Leher (Kaku Kuduk)</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ $assesments->neurologis_leher ? ucfirst(collect($assesments->neurologis_leher)['neurologis_leher_kaku_kuduk']) : '-' }}</td>
                        </tr> 
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">Leher (Meningeal Sign)</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ $assesments->neurologis_leher ? ucfirst(collect($assesments->neurologis_leher)['neurologis_leher_meningeal_sign']) : '-' }}</td>
                        </tr> 
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">Leher (Brudzinski)</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ $assesments->neurologis_leher ? ucfirst(collect($assesments->neurologis_leher)['neurologis_leher_brudzinski']) : '-' }}</td>
                        </tr> 
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">Leher (Dolls Eye Phenomen)</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ $assesments->neurologis_leher ? ucfirst(collect($assesments->neurologis_leher)['neurologis_leher_dolls_eye_phenomen']) : '-' }}</td>
                        </tr> 
                    @elseif ($attr->name == 'implementasi')
                        <tr>
                            <td style="width: 100%; padding: 0 20px;">
                                {{ $assesments[$attr->name] }}
                            </td>
                        </tr>
                    @elseif ($attr->type == 'nyeri')
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">{{ $attr->label }}</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ $assesments[$attr->name] }}, {{ $assesments[$attr->name.'_pilihan'] }}</td>
                        </tr>
                    @elseif ($attr->type == 'laboratorium')
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">{{ $attr->label }}</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ $laboratorium->count() > 0 ? $laboratorium->unique_id : '-' }}</td>
                        </tr>
                    @elseif ($attr->name == 'pernah_dirawat')
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">{{ $attr->label }}</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ $assesments[$attr->name] ? 'Ya' : 'Tidak' }}</td>
                        </tr>
                    @elseif ($attr->type == 'fungsional')
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">Makan (feeding)</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ $assesments->fungsional_makan }}</td>
                        </tr>
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">Mandi (bathing)</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ $assesments->fungsional_mandi }}</td>
                        </tr>
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">Perawatan diri (grooming)</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ $assesments->fungsional_grooming }}</td>
                        </tr>
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">Berpakaian (dressing)</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ $assesments->fungsional_dressing }}</td>
                        </tr>
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">Buang air kecil (bowel)</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ $assesments->fungsional_bowel }}</td>
                        </tr>
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">Buang air besar (bladder)</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ $assesments->fungsional_bladder }}</td>
                        </tr>
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">Penggunaan toilet</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ $assesments->fungsional_penggunaan_toilet }}</td>
                        </tr>
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">Transfer</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ $assesments->fungsional_transfer }}</td>
                        </tr>
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">Mobilitas</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ $assesments->fungsional_mobilitas }}</td>
                        </tr>
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">Naik turun tangga</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ $assesments->fungsional_naik_turun_tangga }}</td>
                        </tr>
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">Implementasi hasil</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ $assesments->fungsional_hasil }}</td>
                        </tr>
                    @else
                        <tr>
                            <td class="first-table" style="width: 50%; padding: 0 20px;">{{ $attr->label }}</td>
                            <td class="middle-table" style="width: 5%">:</td>
                            <td style="width: 45%">{{ ucfirst($assesments[$attr->name] ? $assesments[$attr->name] : '-' ) }} {{ $attr->info ? $attr->info : '' }}</td>
                        </tr>
                    @endif
                @endforeach
            </table>
        @endforeach
        
        @if ($assesments->template->name == 'Assesment Awal Medis Penyakit Dalam' || $assesments->template->name == 'Assesment Awal Medis Anak' || $assesments->template->name == 'Assesment Awal Medis Syaraf' || $assesments->template->name == 'Assesment Awal Medis Paru')
            @if ($laboratorium->count() > 0)
                <div class="page-break"></div>
                <table style="width: 100%;">
                    <tr>
                        <th>
                            <td class="tbr_font-bold" align="left" style="background-color: #0C96C5; color: #FFFFFF; padding: 6px 20px;">LABORATORIUM</td>
                            <td class="tbr_font-bold" align="right" style="background-color: #0C96C5; color: #FFFFFF; padding: 6px 20px;">{{ $laboratorium ? $laboratorium->unique_id : '-' }}</td>
                        </th>
                    </tr>
                </table>
                <table style="width: 100%; margin-bottom: 20px;">
                    <tr>
                        <td align="left" style="background-color: rgba(12, 150, 189, 0.08); color: #000000; padding: 6px 20px;">Nama Pasien : {{ $visits ? $visits['nama'] : '-' }}</td>
                    </tr>
                </table>
                @if ($resultLab['metaData']['code'] == 200)
                    @foreach ($resultLab['response']['data']['pemeriksaan'] as $key => $result1)
                            <p class="tbr_font-bold">{{ $key }}</p>
                            @foreach ($result1 as $result2)
                                <p class="tbr_font-bold" style="margin-left: 20px;">{{ $result2['name'] }}</p>
                                @if ($result2['childs'] == null)
                                    <table>
                                        <tr>
                                            <td class="first-table" style="width: 50%; padding: 0 20px;">Unit</td>
                                            <td class="middle-table" style="width: 5%">:</td>
                                            <td style="width: 45%">{{ $result2['unit'] }}</td>
                                        </tr>
                                    </table>
                                @endif
                                @if ($result2['childs'])
                                    @foreach ($result2['childs'] as $result3)
                                        <p class="tbr_font-bold" style="margin-left: 40px;">{{ $result3['name'] }}</p>
                                        {{ info($result3) }}
                                        @if ($result3['childs'] == null)
                                            <table style="width: 50% ;margin-left: 60px;">
                                                <tr>
                                                    <td class="first-table" style="width: 50%; padding: 0 20px;">Unit</td>
                                                    <td class="middle-table" style="width: 5%">:</td>
                                                    <td style="width: 45%">{{ $result3['unit'] }}</td>
                                                </tr>
                                            </table>
                                            <table style="width: 50% ;margin-left: 60px;">
                                                <tr>
                                                    <td class="first-table" style="width: 50%; padding: 0 20px;">Metode</td>
                                                    <td class="middle-table" style="width: 5%">:</td>
                                                    <td style="width: 45%">{{ $result3['method'] }}</td>
                                                </tr>
                                            </table>
                                            <table style="width: 50% ;margin-left: 60px;">
                                                <tr>
                                                    <td class="first-table" style="width: 50%; padding: 0 20px;">Tanda</td>
                                                    <td class="middle-table" style="width: 5%">:</td>
                                                    <td style="width: 45%">{{ $result3['flag'] }}</td>
                                                </tr>
                                            </table>
                                            <table style="width: 50% ;margin-left: 60px;">
                                                <tr>
                                                    <td class="first-table" style="width: 50%; padding: 0 20px;">Hasil Angka</td>
                                                    <td class="middle-table" style="width: 5%">:</td>
                                                    <td style="width: 45%">{{ $result3['value'] }}</td>
                                                </tr>
                                            </table>
                                            <table style="width: 50% ;margin-left: 60px;">
                                                <tr>
                                                    <td class="first-table" style="width: 50%; padding: 0 20px;">Hasil Karakter</td>
                                                    <td class="middle-table" style="width: 5%">:</td>
                                                    <td style="width: 45%">{{ $result3['value_string'] }}</td>
                                                </tr>
                                            </table>
                                            <table style="width: 50% ;margin-left: 60px;">
                                                <tr>
                                                    <td class="first-table" style="width: 50%; padding: 0 20px;">Hasil Memo</td>
                                                    <td class="middle-table" style="width: 5%">:</td>
                                                    <td style="width: 45%">{{ $result3['value_memo'] }}</td>
                                                </tr>
                                            </table>
                                        @endif
                                        @if ($result3['childs'])
                                            @foreach ($result3['childs'] as $result4)
                                                <p class="tbr_font-bold" style="margin-left: 60px;">{{ $result4['name'] }}</p>
                                                <table style="width: 50% ;margin-left: 80px;">
                                                    <tr>
                                                        <td class="first-table" style="width: 50%; padding: 0 20px;">Unit</td>
                                                        <td class="middle-table" style="width: 5%">:</td>
                                                        <td style="width: 45%">{{ $result4['unit'] }}</td>
                                                    </tr>
                                                </table>
                                                <table style="width: 50% ;margin-left: 80px;">
                                                    <tr>
                                                        <td class="first-table" style="width: 50%; padding: 0 20px;">Metode</td>
                                                        <td class="middle-table" style="width: 5%">:</td>
                                                        <td style="width: 45%">{{ $result4['method'] }}</td>
                                                    </tr>
                                                </table>
                                                <table style="width: 50% ;margin-left: 80px;">
                                                    <tr>
                                                        <td class="first-table" style="width: 50%; padding: 0 20px;">Tanda</td>
                                                        <td class="middle-table" style="width: 5%">:</td>
                                                        <td style="width: 45%">{{ $result4['flag'] }}</td>
                                                    </tr>
                                                </table>
                                                <table style="width: 50% ;margin-left: 80px;">
                                                    <tr>
                                                        <td class="first-table" style="width: 50%; padding: 0 20px;">Hasil Angka</td>
                                                        <td class="middle-table" style="width: 5%">:</td>
                                                        <td style="width: 45%">{{ $result4['value'] }}</td>
                                                    </tr>
                                                </table>
                                                <table style="width: 50% ;margin-left: 80px;">
                                                    <tr>
                                                        <td class="first-table" style="width: 50%; padding: 0 20px;">Hasil Karakter</td>
                                                        <td class="middle-table" style="width: 5%">:</td>
                                                        <td style="width: 45%">{{ $result4['value_string'] }}</td>
                                                    </tr>
                                                </table>
                                                <table style="width: 50% ;margin-left: 80px;">
                                                    <tr>
                                                        <td class="first-table" style="width: 50%; padding: 0 20px;">Hasil Memo</td>
                                                        <td class="middle-table" style="width: 5%">:</td>
                                                        <td style="width: 45%">{{ $result4['value_memo'] }}</td>
                                                    </tr>
                                                </table>
                                            @endforeach
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach
                    @endforeach
                @else 
                    <p class="tbr_font-bold">{{ $resultLab['metaData']['message'] }}</p>
                @endif
            @endif
        @endif
    </div>
</body>
</html>