{{-- resources/views/affiliate/register.blade.php --}}
{{-- TODO [PHASE 2 - Ghufron]: Implementasi form registrasi affiliate sesuai TASK G1 --}}

{{--
    Requirements:
    - Form input: name, telegram_id, payout_method
    - Submit ke POST /affiliate/register
    - Tampilkan referral link hasil registrasi
    - Style: bebas (bisa Tailwind atau Bootstrap)
--}}

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Affiliate Register - TDR HPZ</title>
    {{-- TODO [PHASE 2 - Ghufron]: Tambahkan CSS/asset --}}
</head>
<body>

    <h1>Daftar Affiliate</h1>

    {{-- TODO [PHASE 2 - Ghufron]: Form registrasi affiliate --}}
    <form method="POST" action="{{ route('affiliate.register') }}">
        @csrf
        {{-- TODO: input name --}}
        {{-- TODO: input telegram_id --}}
        {{-- TODO: input payout_method --}}
        {{-- TODO: submit button --}}
    </form>

    {{-- TODO [PHASE 2 - Ghufron]: Tampilkan referral link jika session success tersedia --}}

</body>
</html>
