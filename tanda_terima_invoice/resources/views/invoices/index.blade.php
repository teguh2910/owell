<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Invoice</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Daftar Invoice</h2>
            <div>
                <a href="{{ route('invoices.upload.form') }}" class="btn btn-success">Upload Excel</a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-header">
                Filter Invoice & Cetak Tanda Terima
            </div>
            <div class="card-body">
                <form action="{{ route('invoices.index') }}" method="GET" class="mb-3">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="invoice_date" class="form-label">Tanggal Invoice:</label>
                            <input type="date" class="form-control" id="invoice_date" name="invoice_date" value="{{ request('invoice_date') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="customer_name" class="form-label">Customer:</label>
                            <select class="form-select" id="customer_name" name="customer_name">
                                <option value="">-- Pilih Customer --</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer }}" {{ request('customer_name') == $customer ? 'selected' : '' }}>
                                        {{ $customer }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="{{ route('invoices.index') }}" class="btn btn-secondary">Reset Filter</a>
                        </div>
                    </div>
                </form>

                <hr>

                <h5>Cetak Tanda Terima</h5>
                <form action="{{ route('invoices.print') }}" method="GET">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="print_invoice_date" class="form-label">Tanggal Invoice (untuk cetak):</label>
                            <input type="date" class="form-control" id="print_invoice_date" name="invoice_date" value="{{ request('invoice_date') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label for="print_customer_name" class="form-label">Customer (untuk cetak):</label>
                            <select class="form-select" id="print_customer_name" name="customer_name" required>
                                <option value="">-- Pilih Customer --</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer }}" {{ request('customer_name') == $customer ? 'selected' : '' }}>
                                        {{ $customer }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-info text-white">
                                <i class="fas fa-print"></i> Cetak Tanda Terima
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>


        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No. Invoice</th>
                        <th>Tgl. Invoice</th>
                        <th>Customer</th>
                        <th>No. Faktur Pajak</th>
                        <th>Tgl. Cetak Terakhir</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($invoices as $invoice)
                        <tr>
                            <td>{{ $invoice->invoice_number }}</td>
                            <td>{{ $invoice->invoice_date->format('d-m-Y') }}</td>
                            <td>{{ $invoice->customer_name }}</td>
                            <td>{{ $invoice->tax_invoice_number ?? '-' }}</td>
                            <td>
                                @if ($invoice->receipts->isNotEmpty())
                                    {{ $invoice->receipts->last()->print_date->format('d-m-Y') }}
                                @else
                                    Belum pernah dicetak
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada data invoice.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $invoices->appends(request()->input())->links() }}
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> </body>
</html>