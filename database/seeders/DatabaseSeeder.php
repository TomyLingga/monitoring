<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Supplier;
use App\Models\Buyer;
use App\Models\Storage;
use App\Models\MasterProduk;
use App\Models\KontrakCpo;
use App\Models\IncomingCpo;
use App\Models\PembayaranCpo;
use App\Models\StokProduk;
use App\Models\SaldoBankHarian;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. User
        User::updateOrCreate(
            ['email' => 'admin@cpo.com'],
            [
                'name' => 'Administrator CPO',
                'password' => Hash::make('admin123'),
            ]
        );

        // 2. 5 Suppliers (Simple: hanya nama)
        $suppliers = [
            ['nama' => 'PT Perkebunan Nusantara IV'],
            ['nama' => 'PT Sawit Sumbermas Sarana'],
            ['nama' => 'PT Astra Agro Lestari'],
            ['nama' => 'PT Smart Tbk'],
            ['nama' => 'PT Dharma Satya Nusantara']
        ];
        $supplierModels = [];
        foreach ($suppliers as $sup) {
            $supplierModels[] = Supplier::create($sup);
        }

        // 3. 12 Products
        $productsData = [
            ['nama_produk' => 'Crude Palm Oil', 'kode_produk' => 'CPO', 'satuan' => 'Kg'],
            ['nama_produk' => 'RBD Palm Oil', 'kode_produk' => 'RBDPO', 'satuan' => 'Kg'],
            ['nama_produk' => 'Palm Fatty Acid Distillate', 'kode_produk' => 'PFAD', 'satuan' => 'Kg'],
            ['nama_produk' => 'RBD Palm Stearin', 'kode_produk' => 'Stearin', 'satuan' => 'Kg'],
            ['nama_produk' => 'RBD Palm Olein IV56', 'kode_produk' => 'OL-IV56', 'satuan' => 'Kg'],
            ['nama_produk' => 'RBD Palm Olein IV57', 'kode_produk' => 'OL-IV57', 'satuan' => 'Kg'],
            ['nama_produk' => 'RBD Palm Olein IV58', 'kode_produk' => 'OL-IV58', 'satuan' => 'Kg'],
            ['nama_produk' => 'RBD Palm Olein IV60', 'kode_produk' => 'OL-IV60', 'satuan' => 'Kg'],
            ['nama_produk' => 'Kemasan Minyakita', 'kode_produk' => 'K-MINYAKITA', 'satuan' => 'Kg'],
            ['nama_produk' => 'Kemasan Salvaco', 'kode_produk' => 'K-SALVACO', 'satuan' => 'Kg'],
            ['nama_produk' => 'Kemasan Nusakita', 'kode_produk' => 'K-NUSAKITA', 'satuan' => 'Kg'],
            ['nama_produk' => 'Kemasan INL', 'kode_produk' => 'K-INL', 'satuan' => 'Kg'],
        ];
        $productModels = [];
        foreach ($productsData as $prod) {
            $productModels[$prod['kode_produk']] = MasterProduk::create($prod);
        }

        // 4. Storages: 5 CPO Tanks + 3 Other Tanks + 2 Warehouses
        $storagesData = [
            // CPO Tanks (5 units)
            ['nama' => 'Tangki CPO 01', 'lokasi' => 'Zona Utara', 'kapasitas' => 5000000.00, 'jenis' => 'tangki'],
            ['nama' => 'Tangki CPO 02', 'lokasi' => 'Zona Utara', 'kapasitas' => 3000000.00, 'jenis' => 'tangki'],
            ['nama' => 'Tangki CPO 03', 'lokasi' => 'Zona Utara', 'kapasitas' => 4000000.00, 'jenis' => 'tangki'],
            ['nama' => 'Tangki CPO 04', 'lokasi' => 'Zona Utara', 'kapasitas' => 2500000.00, 'jenis' => 'tangki'],
            ['nama' => 'Tangki CPO 05', 'lokasi' => 'Zona Utara', 'kapasitas' => 3500000.00, 'jenis' => 'tangki'],
            
            // Other Tanks (3 units)
            ['nama' => 'Tangki RBDPO 01', 'lokasi' => 'Zona Timur', 'kapasitas' => 2000000.00, 'jenis' => 'tangki'],
            ['nama' => 'Tangki Olein IV56 01', 'lokasi' => 'Zona Barat', 'kapasitas' => 4000000.00, 'jenis' => 'tangki'],
            ['nama' => 'Tangki Stearin 01', 'lokasi' => 'Zona Timur', 'kapasitas' => 3000000.00, 'jenis' => 'tangki'],
            
            // Warehouses (2 units)
            ['nama' => 'Gudang Kemasan Retail', 'lokasi' => 'Blok B', 'kapasitas' => 1500000.00, 'jenis' => 'gudang'],
            ['nama' => 'Gudang Samping PFAD', 'lokasi' => 'Blok C', 'kapasitas' => 1000000.00, 'jenis' => 'gudang'],
        ];
        $storageModels = [];
        foreach ($storagesData as $store) {
            $storageModels[] = Storage::create($store);
        }

        // 5. CPO Contracts (Procurement)
        // Contract 1: PTPN IV
        $k1 = KontrakCpo::create([
            'supplier_id' => $supplierModels[0]->id,
            'nomor_kontrak' => 'CTR-CPO-001',
            'qty' => 1500000.00, 
            'harga_per_kg' => 15500.00, 
            'cbd_cad' => 'CAD',
            'tgl_kontrak' => '2026-06-15',
            'tgl_jatuh_tempo' => '2026-07-20',
            'status' => 'aktif',
            'is_closed' => false,
        ]);

        // Contract 2: Sawit Sumbermas
        $k2 = KontrakCpo::create([
            'supplier_id' => $supplierModels[1]->id,
            'nomor_kontrak' => 'CTR-CPO-002',
            'qty' => 500000.00, 
            'harga_per_kg' => 15600.00,
            'cbd_cad' => 'CBD',
            'tgl_kontrak' => '2026-07-01',
            'tgl_jatuh_tempo' => '2026-07-31',
            'status' => 'aktif',
            'is_closed' => false,
        ]);

        // Contract 3: Astra Agro
        $k3 = KontrakCpo::create([
            'supplier_id' => $supplierModels[2]->id,
            'nomor_kontrak' => 'CTR-CPO-003',
            'qty' => 1000000.00, 
            'harga_per_kg' => 15400.00,
            'cbd_cad' => 'CAD',
            'tgl_kontrak' => '2026-06-20',
            'tgl_jatuh_tempo' => '2026-08-10',
            'status' => 'aktif',
            'is_closed' => false,
        ]);

        // Contract 4: PT Smart
        $k4 = KontrakCpo::create([
            'supplier_id' => $supplierModels[3]->id,
            'nomor_kontrak' => 'CTR-CPO-004',
            'qty' => 2000000.00, 
            'harga_per_kg' => 15450.00,
            'cbd_cad' => 'CAD',
            'tgl_kontrak' => '2026-06-25',
            'tgl_jatuh_tempo' => '2026-08-20',
            'status' => 'aktif',
            'is_closed' => false,
        ]);

        // Contract 5: Dharma Satya
        $k5 = KontrakCpo::create([
            'supplier_id' => $supplierModels[4]->id,
            'nomor_kontrak' => 'CTR-CPO-005',
            'qty' => 1000000.00, 
            'harga_per_kg' => 15550.00,
            'cbd_cad' => 'CBD',
            'tgl_kontrak' => '2026-07-02',
            'tgl_jatuh_tempo' => '2026-08-15',
            'status' => 'aktif',
            'is_closed' => false,
        ]);

        // Incoming logs mapping to the CPO Tanks
        // Tank 1 receives CPO from Contract 1
        IncomingCpo::create([
            'kontrak_cpo_id' => $k1->id,
            'storage_id' => $storageModels[0]->id, // Tangki CPO 01
            'qty_kirim' => 400000.00,
            'qty_terima' => 398500.00,
            'selisih_qty' => 1500.00,
            'tgl' => '2026-06-25',
            'note' => 'Pengiriman CPO perdana, susut normal',
        ]);

        IncomingCpo::create([
            'kontrak_cpo_id' => $k1->id,
            'storage_id' => $storageModels[0]->id, // Tangki CPO 01
            'qty_kirim' => 300000.00,
            'qty_terima' => 299200.00,
            'selisih_qty' => 800.00,
            'tgl' => '2026-06-29',
            'note' => 'Kualitas CPO baik, kadar air 0.15%',
        ]);

        // Tank 2 receives CPO from Contract 1 AND Contract 2
        IncomingCpo::create([
            'kontrak_cpo_id' => $k1->id,
            'storage_id' => $storageModels[1]->id, // Tangki CPO 02
            'qty_kirim' => 810000.00,
            'qty_terima' => 802300.00,
            'selisih_qty' => 7700.00,
            'tgl' => '2026-06-30',
            'note' => 'Pengisian Tangki 2',
        ]);

        IncomingCpo::create([
            'kontrak_cpo_id' => $k2->id,
            'storage_id' => $storageModels[1]->id, // Tangki CPO 02
            'qty_kirim' => 400000.00,
            'qty_terima' => 397700.00,
            'selisih_qty' => 2300.00,
            'tgl' => '2026-07-02',
            'note' => 'CPO Sawit Sumbermas',
        ]);

        // Tank 3 receives CPO from Contract 3
        IncomingCpo::create([
            'kontrak_cpo_id' => $k3->id,
            'storage_id' => $storageModels[2]->id, // Tangki CPO 03
            'qty_kirim' => 860000.00,
            'qty_terima' => 850000.00,
            'selisih_qty' => 10000.00,
            'tgl' => '2026-06-28',
            'note' => 'CPO Astra Agro',
        ]);

        // Tank 4 receives CPO from Contract 4
        IncomingCpo::create([
            'kontrak_cpo_id' => $k4->id,
            'storage_id' => $storageModels[3]->id, // Tangki CPO 04
            'qty_kirim' => 1820000.00,
            'qty_terima' => 1800000.00,
            'selisih_qty' => 20000.00,
            'tgl' => '2026-07-03',
            'note' => 'CPO PT Smart',
        ]);

        // Tank 5 receives CPO from Contract 5
        IncomingCpo::create([
            'kontrak_cpo_id' => $k5->id,
            'storage_id' => $storageModels[4]->id, // Tangki CPO 05
            'qty_kirim' => 510000.00,
            'qty_terima' => 500000.00,
            'selisih_qty' => 10000.00,
            'tgl' => '2026-07-04',
            'note' => 'CPO Dharma Satya',
        ]);

        // Payments for Contract 1
        PembayaranCpo::create([
            'kontrak_cpo_id' => $k1->id,
            'nominal' => 8000000000.00,
            'tgl_bayar' => '2026-06-18',
            'metode_bayar' => 'transfer',
            'bukti_bayar' => 'tf_cpo_001.pdf',
            'catatan' => 'Uang muka pengadaan CPO'
        ]);

        // Seed stock levels
        // Seed CPO Stock in 5 CPO Tanks
        // Tangki CPO 01
        StokProduk::create([
            'produk_id' => $productModels['CPO']->id,
            'storage_id' => $storageModels[0]->id,
            'qty' => 697700.00,
        ]);

        // Tangki CPO 02
        StokProduk::create([
            'produk_id' => $productModels['CPO']->id,
            'storage_id' => $storageModels[1]->id,
            'qty' => 1200000.00,
        ]);

        // Tangki CPO 03
        StokProduk::create([
            'produk_id' => $productModels['CPO']->id,
            'storage_id' => $storageModels[2]->id,
            'qty' => 850000.00,
        ]);

        // Tangki CPO 04
        StokProduk::create([
            'produk_id' => $productModels['CPO']->id,
            'storage_id' => $storageModels[3]->id,
            'qty' => 1800000.00,
        ]);

        // Tangki CPO 05
        StokProduk::create([
            'produk_id' => $productModels['CPO']->id,
            'storage_id' => $storageModels[4]->id,
            'qty' => 500000.00,
        ]);

        // Tangki RBDPO 01 (Index 5)
        StokProduk::create([
            'produk_id' => $productModels['RBDPO']->id,
            'storage_id' => $storageModels[5]->id,
            'qty' => 950000.00,
        ]);

        // Tangki Olein IV56 01 (Index 6)
        StokProduk::create([
            'produk_id' => $productModels['OL-IV56']->id,
            'storage_id' => $storageModels[6]->id,
            'qty' => 1500000.00,
        ]);

        // Warehouse (Gudang Kemasan Retail, Index 8)
        StokProduk::create([
            'produk_id' => $productModels['K-MINYAKITA']->id,
            'storage_id' => $storageModels[8]->id,
            'qty' => 250000.00,
        ]);
        StokProduk::create([
            'produk_id' => $productModels['K-SALVACO']->id,
            'storage_id' => $storageModels[8]->id,
            'qty' => 180000.00,
        ]);

        // Warehouse (Gudang Samping PFAD, Index 9)
        StokProduk::create([
            'produk_id' => $productModels['PFAD']->id,
            'storage_id' => $storageModels[9]->id,
            'qty' => 420000.00,
        ]);

        // 6. Buyers
        Buyer::create([
            'nama' => 'PT Salim Ivomas Pratama',
            'alamat' => 'Jl. Sudirman Kav 34, Jakarta',
            'telepon' => '021-579588',
            'email' => 'sales@salimivomas.com',
            'pic' => 'Ahmad Subagyo'
        ]);

        // 7. Bank Balance
        SaldoBankHarian::create([
            'saldo' => 25000000000.00,
            'tgl' => '2026-07-06',
            'no_rek' => '102-00-112233-4',
            'nama_bank' => 'Bank Mandiri',
            'kurs' => 1.00,
        ]);
    }
}
