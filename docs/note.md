```php
$factory = new ConditionFactory();

var_dump(
    // 1. SIMPLE (Equality dan Comparison)
    $factory->create('=', ['name', 'Budi']),    // SQL: name = 'Budi'
    $factory->create('>', ['price', 100]),      // SQL: price > 100
    $factory->create('!==', ['id', 50]),        // SQL: id <> 50

    // 2. NULL / IS (Konversi Otomatis)
    $factory->create('=', ['status', null]),    // SQL: status IS NULL (Konversi dari =)
    $factory->create('!==', ['age', null]),     // SQL: age IS NOT NULL (Konversi dari !==)
    $factory->create('IS NOT NULL', ['email']), // SQL: email IS NOT NULL (Eksplisit)

    // 3. IN / NOT IN
    $factory->create('IN', ['category', [1, 5, 9]]), // SQL: category IN (1, 5, 9)

    // 4. BETWEEN / NOT BETWEEN
    $factory->create('BETWEEN', ['tanggal', '2025-01-01', '2025-12-31']),
    // SQL: tanggal BETWEEN '2025-01-01' AND '2025-12-31'

    // 5. LIKE / NOT LIKE
    $factory->create('LIKE', ['description', '%test%']),
    // SQL: description LIKE '%test%'

    // 6. COMPOSITE (Hanya bisa dibuat jika operan adalah objek kondisi lain,
    //    tapi kita simulasikan untuk validasi operator)
    $factory->create('AND', [
        $factory->create('>', ['qty', 10]),
        $factory->create('=', ['active', 1])
    ]),
    // SQL: (qty > 10) AND (active = 1)
);

```

```php

$query = new Query();
$query
    ->select([
        // '*'
        'nm.label AS namaMobil',
        'km.label AS tipeMobil',
        'fm.filename AS imageUrl',
        's.harga',
        'iph.label AS paketSewa'
    ])
    ->from('sewa_dalam_kota AS s')
    ->leftJoin('nama_mobil AS nm', 'nm.idnama_mobil', 's.nama_mobil_idnama_mobil')
    ->leftJoin('kategori_mobil AS km', 'km.idkategori_mobil', 'nm.kategori_mobil_idkategori_mobil')
    ->leftJoin('file_mobil AS fm', 'fm.nama_mobil_idnama_mobil', 'nm.idnama_mobil')
    ->leftJoin('item_paket_harga AS iph', 'iph.iditem_paket_harga', 's.item_paket_harga_iditem_paket_harga')
    ->leftJoin('item_paket_keterangan AS ipk', 'ipk.iditem_paket_keterangan', 's.item_paket_keterangan_iditem_paket_keterangan')
    ->where(['iph.nama' => 'DROPOFF'])
    ->andWhere(['fm.filename', '!==', null])
    ->limit(20)
```
