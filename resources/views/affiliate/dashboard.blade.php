{{-- resources/views/affiliate/dashboard.blade.php --}}
{{-- TODO [PHASE 2 - Ghufron]: Implementasi dashboard affiliate sesuai TASK G3 --}}

{{--
    Required displays:
    - Total clicks
    - Total conversions
    - Conversion rate  (conversions / clicks * 100)
    - Total commission (IDR)
    - Recent orders table
    - Chart.js graph (klik & konversi over time)

    Commission formula: commission = total_amount * 0.10
--}}

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Affiliate Dashboard - TDR HPZ</title>
    {{-- TODO [PHASE 2 - Ghufron]: Tambahkan CSS/asset --}}
    {{-- TODO [PHASE 2 - Ghufron]: Tambahkan Chart.js CDN --}}
</head>
<body>

    <h1>Dashboard Affiliate</h1>

    {{-- TODO [PHASE 2 - Ghufron]: Summary stats cards --}}
    {{-- Total Clicks: $stats['total_clicks'] --}}
    {{-- Total Conversions: $stats['total_conversions'] --}}
    {{-- Conversion Rate: $stats['conversion_rate'] --}}
    {{-- Total Commission: $stats['total_commission'] --}}

    {{-- TODO [PHASE 2 - Ghufron]: Chart.js canvas --}}
    <canvas id="referralChart"></canvas>

    {{-- TODO [PHASE 2 - Ghufron]: Recent orders table --}}

    {{-- TODO [PHASE 2 - Ghufron]: Script Chart.js inisialisasi --}}
    <script>
        // TODO [PHASE 2 - Ghufron]: Inisialisasi Chart.js dengan data dari controller
    </script>

</body>
</html>
