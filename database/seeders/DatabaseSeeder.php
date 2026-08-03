<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\{User, Supplier, Buyer, Storage, MasterProduk, KontrakCpo, KontrakPenjualan, JadwalKapal, PengirimanPenjualan, StokProduk, IncomingCpo, AlokasiStok, KebutuhanProduksi, BankAccount};

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─── USERS ────────────────────────────────────────────────────────────
        $users = [
            ['name'=>'Admin','email'=>'admin@cpo.com','password'=>Hash::make('admin123'),'role'=>'admin'],
            ['name'=>'Marketing','email'=>'marketing@cpo.com','password'=>Hash::make('pass123'),'role'=>'marketing'],
            ['name'=>'PnD/SCM','email'=>'pnd@cpo.com','password'=>Hash::make('pass123'),'role'=>'pnd'],
            ['name'=>'Sourcing','email'=>'sourcing@cpo.com','password'=>Hash::make('pass123'),'role'=>'sourcing'],
            ['name'=>'Produksi','email'=>'produksi@cpo.com','password'=>Hash::make('pass123'),'role'=>'produksi'],
            ['name'=>'Keuangan','email'=>'keuangan@cpo.com','password'=>Hash::make('pass123'),'role'=>'keuangan'],
        ];
        foreach ($users as $u) User::firstOrCreate(['email'=>$u['email']], $u);

        // ─── MASTER PRODUK ────────────────────────────────────────────────────
        $produks = [
            ['nama_produk'=>'CPO (Crude Palm Oil)','kode_produk'=>'CPO','kategori'=>'CPO','satuan'=>'MT','yield_dari_cpo'=>1.0],
            ['nama_produk'=>'RBDPO (Refined Bleached Deodorized Palm Oil)','kode_produk'=>'RBDPO','kategori'=>'RBDPO','satuan'=>'MT','yield_dari_cpo'=>0.94],
            ['nama_produk'=>'Olein IV 56','kode_produk'=>'OL56','kategori'=>'Olein','satuan'=>'MT','yield_dari_cpo'=>0.77],
            ['nama_produk'=>'Olein IV 57','kode_produk'=>'OL57','kategori'=>'Olein','satuan'=>'MT','yield_dari_cpo'=>0.78],
            ['nama_produk'=>'Olein IV 58','kode_produk'=>'OL58','kategori'=>'Olein','satuan'=>'MT','yield_dari_cpo'=>0.71],
            ['nama_produk'=>'Olein CIQ','kode_produk'=>'OLCIQ','kategori'=>'Olein','satuan'=>'MT','yield_dari_cpo'=>0.77],
            ['nama_produk'=>'Stearin','kode_produk'=>'STE','kategori'=>'Stearin','satuan'=>'MT','yield_dari_cpo'=>0.23],
            ['nama_produk'=>'PFAD (Palm Fatty Acid Distillate)','kode_produk'=>'PFAD','kategori'=>'PFAD','satuan'=>'MT','yield_dari_cpo'=>0.04],
            ['nama_produk'=>'PKO (Palm Kernel Oil)','kode_produk'=>'PKO','kategori'=>'PKO','satuan'=>'MT','yield_dari_cpo'=>0],
        ];
        foreach ($produks as $p) MasterProduk::firstOrCreate(['kode_produk'=>$p['kode_produk']], $p);

        // ─── SUPPLIERS ────────────────────────────────────────────────────────
        $suppliers = [
            ['nama'=>'PALMCO','kode'=>'PALMCO','alamat'=>'Jakarta','keterangan'=>'Supplier CPO utama'],
            ['nama'=>'AGRINAS','kode'=>'AGRINAS','alamat'=>'Kalimantan','keterangan'=>'Supplier CPO lokal'],
            ['nama'=>'PT. Swasta Palm','kode'=>'SWASTA','alamat'=>'Sumatera','keterangan'=>'Supplier CPO swasta'],
        ];
        foreach ($suppliers as $s) Supplier::firstOrCreate(['kode'=>$s['kode']], $s);

        // ─── BUYERS ───────────────────────────────────────────────────────────
        $buyers = [
            ['nama'=>'OLAM International','kode'=>'OLAM','negara'=>'Singapore','keterangan'=>'Buyer ekspor utama'],
            ['nama'=>'Adesa Commodities','kode'=>'ADESA','negara'=>'Malaysia','keterangan'=>'Buyer ekspor Olein IV58'],
            ['nama'=>'EOP (Export Oriented)','kode'=>'EOP','negara'=>'India','keterangan'=>'Buyer ekspor'],
            ['nama'=>'LJIM (Lokal)','kode'=>'LJIM','negara'=>'Indonesia','keterangan'=>'Pembeli lokal Olein IV57'],
            ['nama'=>'DMO (Lokal)','kode'=>'DMO','negara'=>'Indonesia','keterangan'=>'Domestic Market Obligation'],
            ['nama'=>'MANN (Lokal)','kode'=>'MANN','negara'=>'Indonesia','keterangan'=>'Buyer lokal'],
            ['nama'=>'RUP (Lokal)','kode'=>'RUP','negara'=>'Indonesia','keterangan'=>'Buyer lokal'],
            ['nama'=>'Socimas','kode'=>'SOCIMAS','negara'=>'Spain','keterangan'=>'Buyer Stearin ekspor'],
            ['nama'=>'IndoChina Trading','kode'=>'INDOCHINA','negara'=>'Vietnam','keterangan'=>'Buyer RBDPO'],
        ];
        foreach ($buyers as $b) Buyer::firstOrCreate(['kode'=>$b['kode']], $b);

        // ─── STORAGES ─────────────────────────────────────────────────────────
        $storages = [
            ['nama'=>'Tangki CPO 01','kode'=>'TK-CPO-01','tipe'=>'tangki','kapasitas'=>5000,'lokasi'=>'Plant A'],
            ['nama'=>'Tangki CPO 02','kode'=>'TK-CPO-02','tipe'=>'tangki','kapasitas'=>5000,'lokasi'=>'Plant A'],
            ['nama'=>'Tangki Olein 01','kode'=>'TK-OL-01','tipe'=>'tangki','kapasitas'=>8000,'lokasi'=>'Plant B'],
            ['nama'=>'Tangki Stearin 01','kode'=>'TK-STE-01','tipe'=>'tangki','kapasitas'=>3000,'lokasi'=>'Plant B'],
            ['nama'=>'Tangki PFAD 01','kode'=>'TK-PFAD-01','tipe'=>'tangki','kapasitas'=>2000,'lokasi'=>'Plant B'],
            ['nama'=>'Tangki RBDPO 01','kode'=>'TK-RBDPO-01','tipe'=>'tangki','kapasitas'=>5000,'lokasi'=>'Plant A'],
        ];
        foreach ($storages as $s) Storage::firstOrCreate(['kode'=>$s['kode']], $s);

        // ─── STOK AWAL ────────────────────────────────────────────────────────
        $cpoProduk   = MasterProduk::where('kode_produk','CPO')->first();
        $oleinProduk = MasterProduk::where('kode_produk','OL56')->first();
        $steinProduk = MasterProduk::where('kode_produk','STE')->first();
        $tkCpo1      = Storage::where('kode','TK-CPO-01')->first();
        $tkOl1       = Storage::where('kode','TK-OL-01')->first();
        $tkSte1      = Storage::where('kode','TK-STE-01')->first();

        if ($cpoProduk && $tkCpo1)
            StokProduk::firstOrCreate(['storage_id'=>$tkCpo1->id,'produk_id'=>$cpoProduk->id], ['qty'=>8247,'harga_satuan'=>12500,'mata_uang'=>'IDR','tgl_update'=>now()]);
        if ($oleinProduk && $tkOl1)
            StokProduk::firstOrCreate(['storage_id'=>$tkOl1->id,'produk_id'=>$oleinProduk->id], ['qty'=>4194,'harga_satuan'=>900,'mata_uang'=>'USD','tgl_update'=>now()]);
        if ($steinProduk && $tkSte1)
            StokProduk::firstOrCreate(['storage_id'=>$tkSte1->id,'produk_id'=>$steinProduk->id], ['qty'=>15606,'harga_satuan'=>750,'mata_uang'=>'USD','tgl_update'=>now()]);

        // ─── KONTRAK CPO ──────────────────────────────────────────────────────
        $palmco = Supplier::where('kode','PALMCO')->first();
        $agrinas = Supplier::where('kode','AGRINAS')->first();
        if ($palmco) {
            KontrakCpo::firstOrCreate(['nomor_kontrak'=>'KC/PALMCO/2026/001'], ['supplier_id'=>$palmco->id,'jenis'=>'lokal','mata_uang'=>'IDR','qty'=>50000,'harga_per_kg'=>12500,'termin_pembayaran'=>'CAD','tgl_kontrak'=>'2026-01-01','tgl_jatuh_tempo'=>'2026-12-31','status'=>'aktif','keterangan'=>'Kontrak CPO utama 2026']);
            KontrakCpo::firstOrCreate(['nomor_kontrak'=>'KC/PALMCO/2026/002'], ['supplier_id'=>$palmco->id,'jenis'=>'lokal','mata_uang'=>'IDR','qty'=>19570,'harga_per_kg'=>12800,'termin_pembayaran'=>'CBD','tgl_kontrak'=>'2026-06-01','tgl_jatuh_tempo'=>'2026-09-30','status'=>'aktif']);
        }
        if ($agrinas)
            KontrakCpo::firstOrCreate(['nomor_kontrak'=>'KC/AGRINAS/2026/001'], ['supplier_id'=>$agrinas->id,'jenis'=>'lokal','mata_uang'=>'IDR','qty'=>3000,'harga_per_kg'=>12300,'termin_pembayaran'=>'CAD','tgl_kontrak'=>'2026-06-01','tgl_jatuh_tempo'=>'2026-08-31','status'=>'aktif']);

        // ─── KONTRAK PENJUALAN ────────────────────────────────────────────────
        $olam    = Buyer::where('kode','OLAM')->first();
        $adesa   = Buyer::where('kode','ADESA')->first();
        $dmo     = Buyer::where('kode','DMO')->first();
        $ol56    = MasterProduk::where('kode_produk','OL56')->first();
        $ol58    = MasterProduk::where('kode_produk','OL58')->first();

        if ($olam && $ol56) {
            KontrakPenjualan::firstOrCreate(['nomor_kontrak'=>'KP/OLAM/2026/001'], ['buyer_id'=>$olam->id,'produk_id'=>$ol56->id,'jenis'=>'ekspor','mata_uang'=>'USD','qty'=>24000,'harga_satuan'=>920,'kurs_konversi'=>16200,'incoterm'=>'FOB','levy_rate_usd'=>33,'termin_pembayaran'=>'CAD','metode_invoice'=>'invoice','tgl_kontrak'=>'2026-06-15','tgl_jatuh_tempo'=>'2026-08-31','status'=>'aktif','keterangan'=>'Olein IV56 untuk July shipment']);
        }
        if ($adesa && $ol58) {
            KontrakPenjualan::firstOrCreate(['nomor_kontrak'=>'KP/ADESA/2026/030'], ['buyer_id'=>$adesa->id,'produk_id'=>$ol58->id,'jenis'=>'ekspor','mata_uang'=>'USD','qty'=>5300,'harga_satuan'=>910,'kurs_konversi'=>16200,'incoterm'=>'CIF','levy_rate_usd'=>33,'termin_pembayaran'=>'CAD','metode_invoice'=>'invoice','tgl_kontrak'=>'2026-06-01','tgl_jatuh_tempo'=>'2026-07-31','status'=>'aktif']);
        }
        if ($dmo && $ol56) {
            KontrakPenjualan::firstOrCreate(['nomor_kontrak'=>'KP/DMO/2026/001'], ['buyer_id'=>$dmo->id,'produk_id'=>$ol56->id,'jenis'=>'lokal','mata_uang'=>'IDR','qty'=>2308,'harga_satuan'=>14500,'kurs_konversi'=>null,'incoterm'=>'LOCO','levy_rate_usd'=>null,'termin_pembayaran'=>'Net30','metode_invoice'=>'invoice','tgl_kontrak'=>'2026-06-01','tgl_jatuh_tempo'=>'2026-07-31','status'=>'aktif','keterangan'=>'DMO local obligation']);
        }

        // ─── JADWAL KAPAL ─────────────────────────────────────────────────────
        $kapal1 = JadwalKapal::firstOrCreate(['nama_kapal'=>'MT TORM ALICE'], ['nomor_voyage'=>'TA-2026-07','laycan_start'=>'2026-07-20','laycan_end'=>'2026-07-26','eta'=>'2026-07-22','etb'=>'2026-07-23','port_muat'=>'Belawan','port_bongkar'=>'Singapore','status'=>'scheduled']);
        $kapal2 = JadwalKapal::firstOrCreate(['nama_kapal'=>'MT TIRTA SARI'], ['nomor_voyage'=>'TS-2026-07','laycan_start'=>'2026-07-25','laycan_end'=>'2026-07-28','eta'=>'2026-07-26','port_muat'=>'Belawan','port_bongkar'=>'Port Klang','status'=>'scheduled']);

        // ─── PENGIRIMAN ───────────────────────────────────────────────────────
        $kpOlam = KontrakPenjualan::where('nomor_kontrak','KP/OLAM/2026/001')->first();
        $kpAdesa = KontrakPenjualan::where('nomor_kontrak','KP/ADESA/2026/030')->first();
        if ($kpOlam && $kapal1) {
            PengirimanPenjualan::firstOrCreate(['nomor_do'=>'DO/OLAM/2026/001'], ['kontrak_penjualan_id'=>$kpOlam->id,'jadwal_kapal_id'=>$kapal1->id,'qty_order'=>24000,'qty_alokasi'=>0,'tgl_rencana'=>'2026-07-22','laycan_start'=>'2026-07-20','laycan_end'=>'2026-07-26','via'=>'kapal','status'=>'draft','keterangan'=>'Shipment Olein IV56 ke OLAM']);
        }
        if ($kpAdesa && $kapal2) {
            PengirimanPenjualan::firstOrCreate(['nomor_do'=>'DO/ADESA/2026/030'], ['kontrak_penjualan_id'=>$kpAdesa->id,'jadwal_kapal_id'=>$kapal2->id,'qty_order'=>5300,'qty_alokasi'=>0,'tgl_rencana'=>'2026-07-26','laycan_start'=>'2026-07-25','laycan_end'=>'2026-07-28','via'=>'kapal','status'=>'draft']);
        }

        // ─── BANK ACCOUNTS ────────────────────────────────────────────────────
        BankAccount::firstOrCreate(['nomor_rekening'=>'001-234-5678'], ['nama_bank'=>'BCA','nama_rekening'=>'PT. CPO Trading IDR','mata_uang'=>'IDR','saldo_awal'=>5000000000,'keterangan'=>'Rekening operasional IDR']);
        BankAccount::firstOrCreate(['nomor_rekening'=>'USD-001-9999'], ['nama_bank'=>'BNI','nama_rekening'=>'PT. CPO Trading USD','mata_uang'=>'USD','saldo_awal'=>500000,'keterangan'=>'Rekening USD ekspor']);

        $this->command->info('✅ Seeder selesai! Users: admin@cpo.com (pass: admin123)');
    }
}
