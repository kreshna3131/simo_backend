<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Hasil Radiologi - {{ $radiology->unique_id }}</title>
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
        <table style="width: 100%">
            <tr>
                <td style="width: 80%">
                    <table class="tbr_table--profile" style="width: 100%">
                        <tr>
                            <td class="first-table">No. RM</td>
                            <td class="middle-table">:</td>
                            <td>{{ $visits ? $visits['norm'] : '-' }}</td>
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
                            <td>Kunjungan ke {{ $radiology->visit_number }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <div class="tbr_body mt-2">
        @foreach ($actions as $key => $action)
            @if ($action->result)
                <table style="width: 100%; {{ $key == 0 ? '' : 'margin-top: 20px' }}">
                    <tr>
                        <th>
                            <td class="tbr_font-bold" align="left" style="background-color: #0D9A89; color: #FFFFFF; padding: 6px 20px;">RADIOLOGI - {{ strtoupper($action->name) }}</td>
                            <td class="tbr_font-bold" align="right" style="background-color: #0D9A89; color: #FFFFFF; padding: 6px 20px;">{{ $radiology->unique_id }}</td>
                        </th>
                    </tr>
                </table>
                <table style="width: 100%">
                    <tr>
                        {!! $action->result !!}
                    </tr>
                </table>
                <table style="width: 100%">
                    <tr>
                        <th>
                            <td class="tbr_font-bold" align="left" style="background-color: #0C96C5; color: #FFFFFF; padding: 6px 20px;">LAMPIRAN : {{ $action->attachment_count ? 'Lampiran('.$action->attachment_count.')' : '-' }}</td>
                        </th>
                    </tr>
                </table>
            @endif
        @endforeach
        <div class="page-break"></div>
        @foreach ($actions as $keyAction => $action)
            @foreach ($action->actionRadAttchs as $key => $attachment)
                <table style="width: 100%; margin-top: 20px;">
                    <tr>
                        <th>
                            <td class="tbr_font-bold" align="left" style="background-color: #0C96C5; color: #FFFFFF; padding: 6px 20px;">RADIOLOGI - {{ strtoupper($action->name) }}</td>
                            <td class="tbr_font-bold" align="right" style="background-color: #0C96C5; color: #FFFFFF; padding: 6px 20px;">{{ $radiology->unique_id }}</td>
                        </th>
                    </tr>
                </table>
                <table style="width: 100%">
                    <tr>
                        <td align="left" style="background-color: rgba(12, 150, 189, 0.08); color: #000000; padding: 6px 20px;">Nama Lampiran : {{ $attachment->photo_name }}</td>
                    </tr>
                </table>
                <div style="margin-top: 20px; page-break-after: always;">
                    <img style="width:100%; max-height: 948px;" src="{{ storage_path('app/public/'.$attachment->photo_path) }}" alt="">
                </div>
            @endforeach
        @endforeach
    </div>
</body>
</html>