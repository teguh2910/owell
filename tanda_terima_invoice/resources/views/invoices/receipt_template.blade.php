<!DOCTYPE html>
<html>
<head>
    <title>Tanda Terima Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
        }
        .container {
            width: 90%;
            margin: auto;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18pt;
        }
        .header p {
            margin: 0;
            font-size: 10pt;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 5px;
            vertical-align: top;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        .footer {
            margin-top: 50px;
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .signature {
            margin-top: 60px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>TANDA TERIMA INVOICE</h1>
            <p>Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d F Y') }}</p>
            <hr>
        </div>

        <table class="info-table">
            <tr>
                <td style="width: 20%;">Diterima dari</td>
                <td style="width: 2%;">:</td>
                <td>{{ $customerName }}</td>
            </tr>
            <tr>
                <td>Untuk Pembayaran</td>
                <td>:</td>
                <td>Invoice tanggal {{ \Carbon\Carbon::parse($invoiceDate)->format('d F Y') }}</td>
            </tr>
        </table>

        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No.</th>
                    <th>Nomor Invoice</th>
                    <th>Tanggal Invoice</th>
                    <th>Nomor Faktur Pajak</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoicesToPrint as $index => $invoice)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $invoice->invoice_number }}</td>
                        <td>{{ $invoice->invoice_date->format('d-m-Y') }}</td>
                        <td>{{ $invoice->tax_invoice_number ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            <p>Bekasi, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            <div class="signature">
                <p>Hormat Kami,</p>
                <br><br><br>
                <p>(............................................)</p>
            </div>
        </div>
    </div>
</body>
</html>