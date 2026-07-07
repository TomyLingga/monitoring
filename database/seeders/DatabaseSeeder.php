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

        // 3. Products & Materials
        $productsData = [
            // Produk CPO & Turunannya
            ['nama_produk' => 'Crude Palm Oil', 'kode_produk' => 'CPO', 'satuan' => 'Kg'],
            ['nama_produk' => 'RBD Palm Oil', 'kode_produk' => 'RBDPO', 'satuan' => 'Kg'],
            ['nama_produk' => 'Palm Fatty Acid Distillate', 'kode_produk' => 'PFAD', 'satuan' => 'Kg'],
            ['nama_produk' => 'RBD Palm Stearin', 'kode_produk' => 'Stearin', 'satuan' => 'Kg'],
            ['nama_produk' => 'RBD Palm Olein IV56', 'kode_produk' => 'OL-IV56', 'satuan' => 'Kg'],
            ['nama_produk' => 'RBD Palm Olein IV57', 'kode_produk' => 'OL-IV57', 'satuan' => 'Kg'],
            ['nama_produk' => 'RBD Palm Olein IV58', 'kode_produk' => 'OL-IV58', 'satuan' => 'Kg'],
            ['nama_produk' => 'RBD Palm Olein IV60', 'kode_produk' => 'OL-IV60', 'satuan' => 'Kg'],
            // Kemasan Retail
            ['nama_produk' => 'Kemasan Minyakita', 'kode_produk' => 'K-MINYAKITA', 'satuan' => 'Box'],
            ['nama_produk' => 'Kemasan Salvaco', 'kode_produk' => 'K-SALVACO', 'satuan' => 'Box'],
            ['nama_produk' => 'Kemasan Nusakita', 'kode_produk' => 'K-NUSAKITA', 'satuan' => 'Box'],
            ['nama_produk' => 'Kemasan INL', 'kode_produk' => 'K-INL', 'satuan' => 'Box'],
            // Material / Bahan Tambahan Produksi
            ['nama_produk' => 'Bleaching Earth', 'kode_produk' => 'BE', 'satuan' => 'Kg'],
            ['nama_produk' => 'Phosphoric Acid', 'kode_produk' => 'PA', 'satuan' => 'Kg'],
            ['nama_produk' => 'Vitamin A (Fortifikan)', 'kode_produk' => 'VIT-A', 'satuan' => 'Kg'],
            ['nama_produk' => 'Karton', 'kode_produk' => 'KARTON', 'satuan' => 'Pcs'],
            ['nama_produk' => 'Pouch', 'kode_produk' => 'POUCH', 'satuan' => 'Pcs'],
        ];
        $productModels = [];
        foreach ($productsData as $prod) {
            $productModels[$prod['kode_produk']] = MasterProduk::updateOrCreate(
                ['kode_produk' => $prod['kode_produk']],
                $prod
            );
        }


        // 4. Storages: CPO Tanks, Processed Tanks, and Gudangs (including material gudangs)
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

            // New Storages for Materials (5 units)
            ['nama' => 'Penyimpanan BE', 'lokasi' => 'Zona Timur', 'kapasitas' => 500000.00, 'jenis' => 'gudang'],
            ['nama' => 'Penyimpanan PA', 'lokasi' => 'Zona Timur', 'kapasitas' => 200000.00, 'jenis' => 'gudang'],
            ['nama' => 'Penyimpanan Karton', 'lokasi' => 'Blok C', 'kapasitas' => 100000.00, 'jenis' => 'gudang'],
            ['nama' => 'Penyimpanan Pouch', 'lokasi' => 'Blok C', 'kapasitas' => 100000.00, 'jenis' => 'gudang'],
            ['nama' => 'Penyimpanan Vit A', 'lokasi' => 'Zona Barat', 'kapasitas' => 50000.00, 'jenis' => 'gudang'],
        ];
        $storageModels = [];
        foreach ($storagesData as $store) {
            $storageModels[$store['nama']] = Storage::updateOrCreate(
                ['nama' => $store['nama']],
                $store
            );
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
            'storage_id' => $storageModels['Tangki CPO 01']->id,
            'qty_kirim' => 400000.00,
            'qty_terima' => 398500.00,
            'selisih_qty' => 1500.00,
            'tgl' => '2026-06-25',
            'note' => 'Pengiriman CPO perdana, susut normal',
        ]);

        IncomingCpo::create([
            'kontrak_cpo_id' => $k1->id,
            'storage_id' => $storageModels['Tangki CPO 01']->id,
            'qty_kirim' => 300000.00,
            'qty_terima' => 299200.00,
            'selisih_qty' => 800.00,
            'tgl' => '2026-06-29',
            'note' => 'Kualitas CPO baik, kadar air 0.15%',
        ]);

        // Tank 2 receives CPO from Contract 1 AND Contract 2
        IncomingCpo::create([
            'kontrak_cpo_id' => $k1->id,
            'storage_id' => $storageModels['Tangki CPO 02']->id,
            'qty_kirim' => 810000.00,
            'qty_terima' => 802300.00,
            'selisih_qty' => 7700.00,
            'tgl' => '2026-06-30',
            'note' => 'Pengisian Tangki 2',
        ]);

        IncomingCpo::create([
            'kontrak_cpo_id' => $k2->id,
            'storage_id' => $storageModels['Tangki CPO 02']->id,
            'qty_kirim' => 400000.00,
            'qty_terima' => 397700.00,
            'selisih_qty' => 2300.00,
            'tgl' => '2026-07-02',
            'note' => 'CPO Sawit Sumbermas',
        ]);

        // Tank 3 receives CPO from Contract 3
        IncomingCpo::create([
            'kontrak_cpo_id' => $k3->id,
            'storage_id' => $storageModels['Tangki CPO 03']->id,
            'qty_kirim' => 860000.00,
            'qty_terima' => 850000.00,
            'selisih_qty' => 10000.00,
            'tgl' => '2026-06-28',
            'note' => 'CPO Astra Agro',
        ]);

        // Tank 4 receives CPO from Contract 4
        IncomingCpo::create([
            'kontrak_cpo_id' => $k4->id,
            'storage_id' => $storageModels['Tangki CPO 04']->id,
            'qty_kirim' => 1820000.00,
            'qty_terima' => 1800000.00,
            'selisih_qty' => 20000.00,
            'tgl' => '2026-07-03',
            'note' => 'CPO PT Smart',
        ]);

        // Tank 5 receives CPO from Contract 5
        IncomingCpo::create([
            'kontrak_cpo_id' => $k5->id,
            'storage_id' => $storageModels['Tangki CPO 05']->id,
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
        $initialStocks = [
            // CPO in CPO Tanks
            ['produk_kode' => 'CPO', 'storage_nama' => 'Tangki CPO 01', 'qty' => 697700.00],
            ['produk_kode' => 'CPO', 'storage_nama' => 'Tangki CPO 02', 'qty' => 1200000.00],
            ['produk_kode' => 'CPO', 'storage_nama' => 'Tangki CPO 03', 'qty' => 850000.00],
            ['produk_kode' => 'CPO', 'storage_nama' => 'Tangki CPO 04', 'qty' => 1800000.00],
            ['produk_kode' => 'CPO', 'storage_nama' => 'Tangki CPO 05', 'qty' => 500000.00],
            
            // RBDPO
            ['produk_kode' => 'RBDPO', 'storage_nama' => 'Tangki RBDPO 01', 'qty' => 950000.00],
            
            // Olein
            ['produk_kode' => 'OL-IV56', 'storage_nama' => 'Tangki Olein IV56 01', 'qty' => 1500000.00],
            
            // Warehouse (Gudang Kemasan Retail)
            ['produk_kode' => 'K-MINYAKITA', 'storage_nama' => 'Gudang Kemasan Retail', 'qty' => 250000.00],
            ['produk_kode' => 'K-SALVACO', 'storage_nama' => 'Gudang Kemasan Retail', 'qty' => 180000.00],
            
            // PFAD
            ['produk_kode' => 'PFAD', 'storage_nama' => 'Gudang Samping PFAD', 'qty' => 420000.00],

            // Materials
            ['produk_kode' => 'BE', 'storage_nama' => 'Penyimpanan BE', 'qty' => 45000.00],
            ['produk_kode' => 'PA', 'storage_nama' => 'Penyimpanan PA', 'qty' => 12000.00],
            ['produk_kode' => 'KARTON', 'storage_nama' => 'Penyimpanan Karton', 'qty' => 8000.00],
            ['produk_kode' => 'POUCH', 'storage_nama' => 'Penyimpanan Pouch', 'qty' => 15000.00],
            ['produk_kode' => 'VIT-A', 'storage_nama' => 'Penyimpanan Vit A', 'qty' => 2500.00],
        ];

        foreach ($initialStocks as $st) {
            $pId = $productModels[$st['produk_kode']]->id ?? null;
            $sId = $storageModels[$st['storage_nama']]->id ?? null;
            if ($pId && $sId) {
                StokProduk::updateOrCreate(
                    ['produk_id' => $pId, 'storage_id' => $sId],
                    ['qty' => $st['qty']]
                );
            }
        }

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

        // 8. Daily Production Targets
        $targets = [
            ['tgl' => '2026-07-01', 'jenis' => 'refinery', 'target_qty' => 1500000.00],
            ['tgl' => '2026-07-01', 'jenis' => 'fraksinasi', 'target_qty' => 1000000.00],
            ['tgl' => '2026-07-01', 'jenis' => 'packaging', 'target_qty' => 15000.00],
            ['tgl' => '2026-07-02', 'jenis' => 'refinery', 'target_qty' => 1500000.00],
            ['tgl' => '2026-07-02', 'jenis' => 'fraksinasi', 'target_qty' => 1000000.00],
            ['tgl' => '2026-07-02', 'jenis' => 'packaging', 'target_qty' => 15000.00],
            ['tgl' => '2026-07-03', 'jenis' => 'refinery', 'target_qty' => 1500000.00],
            ['tgl' => '2026-07-03', 'jenis' => 'fraksinasi', 'target_qty' => 1000000.00],
            ['tgl' => '2026-07-03', 'jenis' => 'packaging', 'target_qty' => 15000.00],
            ['tgl' => '2026-07-04', 'jenis' => 'refinery', 'target_qty' => 1500000.00],
            ['tgl' => '2026-07-04', 'jenis' => 'fraksinasi', 'target_qty' => 1000000.00],
            ['tgl' => '2026-07-04', 'jenis' => 'packaging', 'target_qty' => 15000.00],
            ['tgl' => '2026-07-05', 'jenis' => 'refinery', 'target_qty' => 1500000.00],
            ['tgl' => '2026-07-05', 'jenis' => 'fraksinasi', 'target_qty' => 1000000.00],
            ['tgl' => '2026-07-05', 'jenis' => 'packaging', 'target_qty' => 15000.00],
            ['tgl' => '2026-07-06', 'jenis' => 'refinery', 'target_qty' => 1500000.00],
            ['tgl' => '2026-07-06', 'jenis' => 'fraksinasi', 'target_qty' => 1000000.00],
            ['tgl' => '2026-07-06', 'jenis' => 'packaging', 'target_qty' => 15000.00],
        ];
        foreach ($targets as $tar) {
            \App\Models\DailyProductionTarget::create($tar);
        }

        // 9. Production Processes (Refinery)
        $ref1 = \App\Models\ProsesRefinery::create([
            'tgl' => '2026-07-05',
            'shift' => '1',
            'catatan' => 'Refinery Batch 1'
        ]);
        \App\Models\BahanRefinery::create(['proses_refinery_id' => $ref1->id, 'produk_id' => $productModels['CPO']->id, 'storage_id' => $storageModels['Tangki CPO 01']->id, 'qty' => 120000.00]);
        \App\Models\BahanRefinery::create(['proses_refinery_id' => $ref1->id, 'produk_id' => $productModels['BE']->id, 'storage_id' => $storageModels['Penyimpanan BE']->id, 'qty' => 1200.00]);
        \App\Models\BahanRefinery::create(['proses_refinery_id' => $ref1->id, 'produk_id' => $productModels['PA']->id, 'storage_id' => $storageModels['Penyimpanan PA']->id, 'qty' => 600.00]);
        \App\Models\HasilRefinery::create(['proses_refinery_id' => $ref1->id, 'produk_id' => $productModels['RBDPO']->id, 'storage_id' => $storageModels['Tangki RBDPO 01']->id, 'qty' => 115000.00]);
        \App\Models\HasilRefinery::create(['proses_refinery_id' => $ref1->id, 'produk_id' => $productModels['PFAD']->id, 'storage_id' => $storageModels['Gudang Samping PFAD']->id, 'qty' => 3800.00]);

        $ref2 = \App\Models\ProsesRefinery::create([
            'tgl' => '2026-07-06',
            'shift' => '2',
            'catatan' => 'Refinery Batch 2'
        ]);
        \App\Models\BahanRefinery::create(['proses_refinery_id' => $ref2->id, 'produk_id' => $productModels['CPO']->id, 'storage_id' => $storageModels['Tangki CPO 02']->id, 'qty' => 150000.00]);
        \App\Models\BahanRefinery::create(['proses_refinery_id' => $ref2->id, 'produk_id' => $productModels['BE']->id, 'storage_id' => $storageModels['Penyimpanan BE']->id, 'qty' => 1500.00]);
        \App\Models\BahanRefinery::create(['proses_refinery_id' => $ref2->id, 'produk_id' => $productModels['PA']->id, 'storage_id' => $storageModels['Penyimpanan PA']->id, 'qty' => 750.00]);
        \App\Models\HasilRefinery::create(['proses_refinery_id' => $ref2->id, 'produk_id' => $productModels['RBDPO']->id, 'storage_id' => $storageModels['Tangki RBDPO 01']->id, 'qty' => 144000.00]);
        \App\Models\HasilRefinery::create(['proses_refinery_id' => $ref2->id, 'produk_id' => $productModels['PFAD']->id, 'storage_id' => $storageModels['Gudang Samping PFAD']->id, 'qty' => 4800.00]);

        // 10. Production Processes (Fraksinasi)
        $frak1 = \App\Models\ProsesFraksinasi::create([
            'tgl' => '2026-07-05',
            'shift' => '1',
            'catatan' => 'Fraksinasi Batch 1'
        ]);
        \App\Models\BahanFraksinasi::create(['proses_fraksinasi_id' => $frak1->id, 'produk_id' => $productModels['RBDPO']->id, 'storage_id' => $storageModels['Tangki RBDPO 01']->id, 'qty' => 100000.00]);
        \App\Models\HasilFraksinasi::create(['proses_fraksinasi_id' => $frak1->id, 'produk_id' => $productModels['OL-IV56']->id, 'storage_id' => $storageModels['Tangki Olein IV56 01']->id, 'qty' => 78000.00]);
        \App\Models\HasilFraksinasi::create(['proses_fraksinasi_id' => $frak1->id, 'produk_id' => $productModels['Stearin']->id, 'storage_id' => $storageModels['Tangki Stearin 01']->id, 'qty' => 21000.00]);

        $frak2 = \App\Models\ProsesFraksinasi::create([
            'tgl' => '2026-07-06',
            'shift' => '1',
            'catatan' => 'Fraksinasi Batch 2'
        ]);
        \App\Models\BahanFraksinasi::create(['proses_fraksinasi_id' => $frak2->id, 'produk_id' => $productModels['RBDPO']->id, 'storage_id' => $storageModels['Tangki RBDPO 01']->id, 'qty' => 120000.00]);
        \App\Models\HasilFraksinasi::create(['proses_fraksinasi_id' => $frak2->id, 'produk_id' => $productModels['OL-IV56']->id, 'storage_id' => $storageModels['Tangki Olein IV56 01']->id, 'qty' => 93000.00]);
        \App\Models\HasilFraksinasi::create(['proses_fraksinasi_id' => $frak2->id, 'produk_id' => $productModels['Stearin']->id, 'storage_id' => $storageModels['Tangki Stearin 01']->id, 'qty' => 25000.00]);

        // 11. Production Processes (Packaging)
        $pack1 = \App\Models\ProsesPackaging::create([
            'tgl' => '2026-07-05',
            'shift' => '1',
            'catatan' => 'Packaging Minyakita'
        ]);
        \App\Models\BahanPackaging::create(['proses_packaging_id' => $pack1->id, 'produk_id' => $productModels['OL-IV56']->id, 'storage_id' => $storageModels['Tangki Olein IV56 01']->id, 'qty' => 25000.00]);
        \App\Models\BahanPackaging::create(['proses_packaging_id' => $pack1->id, 'produk_id' => $productModels['KARTON']->id, 'storage_id' => $storageModels['Penyimpanan Karton']->id, 'qty' => 2500.00]);
        \App\Models\BahanPackaging::create(['proses_packaging_id' => $pack1->id, 'produk_id' => $productModels['POUCH']->id, 'storage_id' => $storageModels['Penyimpanan Pouch']->id, 'qty' => 25000.00]);
        \App\Models\HasilPackaging::create(['proses_packaging_id' => $pack1->id, 'produk_id' => $productModels['K-MINYAKITA']->id, 'storage_id' => $storageModels['Gudang Kemasan Retail']->id, 'qty' => 2500.00]);

        $pack2 = \App\Models\ProsesPackaging::create([
            'tgl' => '2026-07-06',
            'shift' => '2',
            'catatan' => 'Packaging Salvaco'
        ]);
        \App\Models\BahanPackaging::create(['proses_packaging_id' => $pack2->id, 'produk_id' => $productModels['OL-IV56']->id, 'storage_id' => $storageModels['Tangki Olein IV56 01']->id, 'qty' => 30000.00]);
        \App\Models\BahanPackaging::create(['proses_packaging_id' => $pack2->id, 'produk_id' => $productModels['KARTON']->id, 'storage_id' => $storageModels['Penyimpanan Karton']->id, 'qty' => 3000.00]);
        \App\Models\BahanPackaging::create(['proses_packaging_id' => $pack2->id, 'produk_id' => $productModels['POUCH']->id, 'storage_id' => $storageModels['Penyimpanan Pouch']->id, 'qty' => 30000.00]);
        \App\Models\HasilPackaging::create(['proses_packaging_id' => $pack2->id, 'produk_id' => $productModels['K-SALVACO']->id, 'storage_id' => $storageModels['Gudang Kemasan Retail']->id, 'qty' => 3000.00]);

        // 12. Sales Contracts & Shipments Seeders
        $salimIvomas = Buyer::first();

        // Contract 1: Salim Ivomas buys RBDPO (1,000,000 Kg @ Rp 16,500)
        $sc1 = \App\Models\KontrakPenjualan::create([
            'buyer_id' => $salimIvomas->id,
            'produk_id' => $productModels['RBDPO']->id,
            'nomor_kontrak' => 'SALES-2026-001',
            'qty' => 1000000.00,
            'harga_satuan' => 16500.00,
            'tgl_kontrak' => '2026-07-01',
            'tgl_jatuh_tempo' => '2026-08-01',
            'termin_pembayaran' => 'CAD',
            'status' => 'aktif',
        ]);

        // Contract 2: Wings buys Olein IV56 (500,000 Kg @ Rp 16,800)
        $wings = Buyer::create([
            'nama' => 'PT Wings Surya',
            'alamat' => 'Jl. Kalibutuh 189, Surabaya',
            'pic' => 'Hendra Wijaya',
            'telepon' => '031-5322300',
            'email' => 'hendra.wings@wingscorp.com',
        ]);

        $sc2 = \App\Models\KontrakPenjualan::create([
            'buyer_id' => $wings->id,
            'produk_id' => $productModels['OL-IV56']->id,
            'nomor_kontrak' => 'SALES-2026-002',
            'qty' => 500000.00,
            'harga_satuan' => 16800.00,
            'tgl_kontrak' => '2026-07-02',
            'tgl_jatuh_tempo' => '2026-08-02',
            'termin_pembayaran' => 'CBD',
            'status' => 'aktif',
        ]);

        // 13. Sales Payments
        \App\Models\PembayaranPenjualan::create([
            'kontrak_penjualan_id' => $sc1->id,
            'nominal' => 5000000000.00,
            'tgl_bayar' => '2026-07-03',
            'catatan' => 'Uang Muka Tahap 1',
        ]);

        \App\Models\PembayaranPenjualan::create([
            'kontrak_penjualan_id' => $sc2->id,
            'nominal' => 8400000000.00,
            'tgl_bayar' => '2026-07-02',
            'catatan' => 'Pelunasan CBD',
        ]);

        // 14. Sales Shipments (Pengiriman Penjualan)
        // Shipment 1: Send 250,000 Kg RBDPO from Tangki RBDPO 01
        $ship1 = \App\Models\PengirimanPenjualan::create([
            'kontrak_penjualan_id' => $sc1->id,
            'qty_kirim' => 250000.00,
            'qty_terima' => 250000.00,
            'via' => 'Kapal Tanker',
            'termin' => 'CAD',
            'status' => 'Selesai',
            'incoterm' => 'FRANCO',
            'tgl' => '2026-07-04',
            'storage_id' => $storageModels['Tangki RBDPO 01']->id,
        ]);

        $st1 = StokProduk::where('produk_id', $productModels['RBDPO']->id)
            ->where('storage_id', $storageModels['Tangki RBDPO 01']->id)
            ->first();
        if ($st1) {
            $st1->decrement('qty', 250000.00);
        }

        \App\Models\Invoice::create([
            'pengiriman_id' => $ship1->id,
            'nomor_invoice' => 'INV-SALES-001',
            'nilai' => 250000.00 * 16500.00,
            'tgl_invoice' => '2026-07-04',
            'tgl_jatuh_tempo' => '2026-08-04',
            'status' => 'terkirim',
        ]);

        // Shipment 2: Send 100,000 Kg Olein IV56 from Tangki Olein IV56 01
        $ship2 = \App\Models\PengirimanPenjualan::create([
            'kontrak_penjualan_id' => $sc2->id,
            'qty_kirim' => 100000.00,
            'qty_terima' => 100000.00,
            'via' => 'Truck Fuso',
            'termin' => 'CBD',
            'status' => 'Selesai',
            'incoterm' => 'LOCO',
            'tgl' => '2026-07-05',
            'storage_id' => $storageModels['Tangki Olein IV56 01']->id,
        ]);

        $st2 = StokProduk::where('produk_id', $productModels['OL-IV56']->id)
            ->where('storage_id', $storageModels['Tangki Olein IV56 01']->id)
            ->first();
        if ($st2) {
            $st2->decrement('qty', 100000.00);
        }

        \App\Models\Invoice::create([
            'pengiriman_id' => $ship2->id,
            'nomor_invoice' => 'INV-SALES-002',
            'nilai' => 100000.00 * 16800.00,
            'tgl_invoice' => '2026-07-05',
            'tgl_jatuh_tempo' => '2026-08-05',
            'status' => 'lunas',
        ]);

        // 15. Logistics & Finance Data
        $this->call(LogisticsFinanceSeeder::class);

        // 16. Sales Targets
        $this->call(SalesTargetSeeder::class);

        // 17. Levy Duty & Pembayaran
        $this->call(LevyDutySeeder::class);
    }
}
