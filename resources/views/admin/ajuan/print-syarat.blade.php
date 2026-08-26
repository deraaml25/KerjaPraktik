<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Verifikasi Syarat - {{ $ajuan->no_registrasi }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11px;
            color: #000;
            line-height: 1.2;
            margin: 0;
            padding: 10px 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .header h3 {
            margin: 0;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .header p {
            margin: 2px 0 0 0;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #000;
            padding: 3px 5px;
            vertical-align: middle;
        }
        th {
            text-align: center;
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .title-row th {
            font-size: 12px;
            text-align: center;
            background-color: #fff;
        }
        .header-row th {
            font-size: 11px;
        }
        .col-no {
            width: 30px;
            text-align: center;
        }
        .col-ket {
            width: 60px;
            text-align: center;
        }
        .footer-ttd {
            width: 100%;
            margin-top: 10px;
        }
        .ttd-box {
            float: right;
            width: 200px;
            text-align: center;
            font-size: 11px;
        }
        .ttd-box p {
            margin: 0;
        }
        .ttd-name {
            margin-top: 50px !important;
            font-weight: bold;
            text-decoration: underline;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        @page {
            size: A4 portrait; /* Menggunakan A4 karena barisnya banyak, agar muat 1 halaman */
            margin: 10mm 15mm;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                padding: 0;
            }
        }
        .print-btn {
            padding: 8px 16px;
            background: #0f3c65;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-bottom: 15px;
            font-weight: bold;
            font-size: 12px;
        }
    </style>
</head>
<body>

    <div class="no-print text-center" style="text-align: center;">
        <button class="print-btn" onclick="window.print()">Print Halaman Ini</button>
    </div>

    <div class="header">
        <h3>VERIFIKASI SYARAT DOKUMEN</h3>
        <p>No Registrasi: {{ $ajuan->no_registrasi }}</p>
    </div>

    <table>
        <thead>
            <tr class="title-row">
                <th colspan="3">Dokumen {{ $ajuan->jenisLayanan->nama }} (Desa {{ $ajuan->desa->nama_desa }})</th>
            </tr>
            <tr class="header-row">
                <th class="col-no">NO</th>
                <th>BERKAS</th>
                <th class="col-ket">KET</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dokumenList as $index => $item)
            <tr>
                <td class="col-no">{{ $item->templateChecklist->urutan }}</td>
                <td>
                    {{ $item->templateChecklist->nama_dokumen }}

                </td>
                <td class="col-ket">
                    @if($item->status == 'valid' || $item->status == 'lengkap')
                        &#10003;
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>



    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        }
    </script>
</body>
</html>

