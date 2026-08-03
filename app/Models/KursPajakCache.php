<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class KursPajakCache extends Model {
    protected $table = 'kurs_pajak_caches';
    protected $fillable = ['tanggal','periode_mulai','periode_akhir','raw_json','fetched_at'];
    protected $casts = ['tanggal'=>'date','periode_mulai'=>'date','periode_akhir'=>'date','raw_json'=>'array','fetched_at'=>'datetime'];

    public function getKursUsdAttribute() {
        $data = $this->raw_json ?? [];
        foreach ($data as $item) {
            if (isset($item['kode']) && strtoupper($item['kode']) === 'USD') {
                return $item['nilai_jual'] ?? $item['nilai_tengah'] ?? null;
            }
        }
        return null;
    }
}
