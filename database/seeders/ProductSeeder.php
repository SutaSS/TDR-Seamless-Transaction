<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // ── TDR Racing Camshaft Series ─────────────────────────────────
            [
                'sku'             => 'TDR-CAM-BEAT-01',
                'name'            => 'TDR Camshaft Racing Beat/Vario 110',
                'description'     => 'Noken as racing TDR untuk Honda Beat FI dan Vario 110. Meningkatkan performa mesin hingga 15%, cocok untuk harian &amp; balap. Material baja tempa berkualitas tinggi.',
                'price'           => 285000,
                'commission_rate' => 10.00,
                'stock'           => 50,
            ],
            [
                'sku'             => 'TDR-CAM-MIO-01',
                'name'            => 'TDR Camshaft Racing Mio/Nouvo',
                'description'     => 'Noken as racing TDR khusus Yamaha Mio series dan Nouvo. Duration lebih panjang untuk akselerasi maksimal. Lift lebih tinggi untuk tenaga optimal.',
                'price'           => 265000,
                'commission_rate' => 10.00,
                'stock'           => 35,
            ],
            [
                'sku'             => 'TDR-CAM-SUPRA-01',
                'name'            => 'TDR Camshaft Racing Supra X 125',
                'description'     => 'Noken as TDR untuk Honda Supra X 125 dan Karisma. Performa lebih responsif dan tenaga meningkat signifikan. Proses pengerjaan presisi CNC.',
                'price'           => 295000,
                'commission_rate' => 10.00,
                'stock'           => 28,
            ],

            // ── TDR CVT Package ────────────────────────────────────────────
            [
                'sku'             => 'TDR-CVT-BEAT-01',
                'name'            => 'TDR Package Zozz for Beat/Scoopy FI ESP with CVT Belt Green Line',
                'description'     => 'Paket lengkap CVT TDR untuk Honda Beat FI, Scoopy FI & ESP. Termasuk: roller racing, per CVT, v-belt hijau, dan gasket. Akselerasi lebih dahsyat dan responsif.',
                'price'           => 1300000,
                'commission_rate' => 10.00,
                'stock'           => 20,
            ],
            [
                'sku'             => 'TDR-CVT-MIO-01',
                'name'            => 'TDR Package Zozz for Mio M3/Soul GT with CVT Belt',
                'description'     => 'Paket CVT racing TDR untuk Yamaha Mio M3, Soul GT, dan Fino. Roller set + per CVT + v-belt TDR. Meningkatkan akselerasi awal dan top speed.',
                'price'           => 1250000,
                'commission_rate' => 10.00,
                'stock'           => 18,
            ],
            [
                'sku'             => 'TDR-CVT-VARIO-01',
                'name'            => 'TDR Package Zozz for Vario 125/150 with CVT Belt',
                'description'     => 'Paket CVT TDR untuk Honda Vario 125 dan Vario 150. Cocok untuk penggunaan harian maupun touring. Set lengkap termasuk roller dan per variasi.',
                'price'           => 1350000,
                'commission_rate' => 10.00,
                'stock'           => 15,
            ],

            // ── TDR Big Bore Kit ───────────────────────────────────────────
            [
                'sku'             => 'TDR-BBK-BEAT-57',
                'name'            => 'TDR Big Bore 57mm Kit Beat/Vario 110',
                'description'     => 'Kit bore up TDR 57mm untuk Honda Beat dan Vario 110. Kapasitas mesin meningkat menjadi ~150cc. Material piston aircraft aluminium, ring set, dan paking head sudah termasuk.',
                'price'           => 850000,
                'commission_rate' => 10.00,
                'stock'           => 12,
            ],
            [
                'sku'             => 'TDR-BBK-MIO-58',
                'name'            => 'TDR Big Bore 58mm Kit Mio/Soul GT',
                'description'     => 'Kit bore up 58mm untuk Yamaha Mio series. Piston forged aluminium presisi tinggi, ring oil control, dan paking set lengkap. Kapasitas jadi ~155cc.',
                'price'           => 820000,
                'commission_rate' => 10.00,
                'stock'           => 10,
            ],

            // ── TDR Roller Racing ──────────────────────────────────────────
            [
                'sku'             => 'TDR-ROLL-BEAT-9G',
                'name'            => 'TDR Roller Racing 9gr Beat/Vario (Set isi 6)',
                'description'     => 'Roller racing TDR 9 gram untuk Honda Beat FI, Vario 110/125. Set isi 6 buah. Material kevlar reinforced, anti selip, tahan panas tinggi. Akselerasi awal lebih responsif.',
                'price'           => 55000,
                'commission_rate' => 10.00,
                'stock'           => 200,
            ],
            [
                'sku'             => 'TDR-ROLL-MIO-10G',
                'name'            => 'TDR Roller Racing 10gr Mio/Fino (Set isi 6)',
                'description'     => 'Roller TDR 10 gram Yamaha Mio, Fino, Soul GT. Set 6 buah, material polymer berkualitas. Cocok untuk harian dengan akselerasi lebih baik.',
                'price'           => 52000,
                'commission_rate' => 10.00,
                'stock'           => 180,
            ],
            [
                'sku'             => 'TDR-ROLL-AEROX-11G',
                'name'            => 'TDR Roller 11gr Yamaha AEROX/NMAX (Set isi 6)',
                'description'     => 'Roller TDR 11 gram untuk Yamaha AEROX 155 dan NMAX 155. Presisi tinggi, toleransi sempit. Perpindahan tenaga lebih halus dan akselerasi optimal.',
                'price'           => 75000,
                'commission_rate' => 10.00,
                'stock'           => 90,
            ],

            // ── HPZ Variasi Parts ──────────────────────────────────────────
            [
                'sku'             => 'HPZ-SPRING-CVT-01',
                'name'            => 'HPZ Per CVT Torsi Keras Beat FI / Vario 110',
                'description'     => 'Per CVT HPZ kekerasan tinggi untuk Beat FI dan Vario 110. Meningkatkan respons gas dan akselerasi dari putaran rendah. Tahan lama, material spring steel premium.',
                'price'           => 35000,
                'commission_rate' => 10.00,
                'stock'           => 150,
            ],
            [
                'sku'             => 'HPZ-VBELT-BEAT-01',
                'name'            => 'HPZ V-Belt Green Line Beat FI / Scoopy',
                'description'     => 'V-Belt HPZ Green Line khusus Honda Beat FI dan Scoopy. Material rubber compound pilihan, anti slip, tahan temperatur hingga 120°C. Garansi 6 bulan.',
                'price'           => 85000,
                'commission_rate' => 10.00,
                'stock'           => 80,
            ],
            [
                'sku'             => 'HPZ-VBELT-MIO-01',
                'name'            => 'HPZ V-Belt Blue Line Mio M3 / Soul GT',
                'description'     => 'V-Belt HPZ Blue Line Yamaha Mio M3 dan Soul GT. Kekuatan tarik lebih tinggi dari OEM, serat karbon reinforced. Lebih awet 2x dari belt biasa.',
                'price'           => 80000,
                'commission_rate' => 10.00,
                'stock'           => 75,
            ],

            // ── Rantai Keteng ──────────────────────────────────────────────
            [
                'sku'             => 'TDR-CHAIN-BEAT-01',
                'name'            => 'TDR Rantai Keteng Honda Beat / Vario ESP',
                'description'     => 'Rantai keteng (timing chain) TDR untuk Beat FI ESP, Vario 125/150 ESP. Material baja hardened, presisi tinggi, anti memanjang. Pengganti OEM berkualitas.',
                'price'           => 95000,
                'commission_rate' => 10.00,
                'stock'           => 60,
            ],
            [
                'sku'             => 'TDR-CHAIN-SCOOPY-01',
                'name'            => 'TDR Rantai Keteng Scoopy FI / PCX 150',
                'description'     => 'Rantai keteng TDR untuk Honda Scoopy FI dan PCX 150. Cocok untuk penggantian rantai keteng yang aus atau bunyi. Pemasangan mudah.',
                'price'           => 98000,
                'commission_rate' => 10.00,
                'stock'           => 45,
            ],
        ];

        foreach ($products as $data) {
            Product::firstOrCreate(
                ['sku' => $data['sku']],
                array_merge($data, ['is_active' => true])
            );
        }

        $this->command->info('✅ ProductSeeder: ' . count($products) . ' produk berhasil di-seed.');
    }
}
