<style>
    #map {
        height: 300px;
        width: 100%;
    }
</style>

@php
    $folderPath = 'uploads/absensi/';
    $waktu = '';
    $lokasi_user = '';
    $foto = '';
    $judul = '';

    if ($status == 'in') {
        $waktu = $presensi->jam_in;
        $lokasi_user = $presensi->lokasi_in;
        $foto = $presensi->foto_in;
        $judul = 'Jam Masuk';
        $folderPath = 'uploads/absensi/';
    } elseif ($status == 'out') {
        $waktu = $presensi->jam_out;
        $lokasi_user = $presensi->lokasi_out;
        $foto = $presensi->foto_out;
        $judul = 'Jam Pulang';
        $folderPath = 'uploads/absensi/';
    } elseif ($status == 'istirahat_in') {
        $waktu = $presensi->istirahat_in;
        $lokasi_user = $presensi->lokasi_istirahat_in;
        $foto = $presensi->foto_istirahat_in;
        $judul = 'Istirahat Mulai';
        $folderPath = 'uploads/istirahat/';
    } elseif ($status == 'istirahat_out') {
        $waktu = $presensi->istirahat_out;
        $lokasi_user = $presensi->lokasi_istirahat_out;
        $foto = $presensi->foto_istirahat_out;
        $judul = 'Istirahat Selesai';
        $folderPath = 'uploads/istirahat/';
    }
@endphp

<div class="row">
    <div class="col-4 text-center">
        @if (!empty($foto))
            @if (Storage::disk('public')->exists('/' . $folderPath . $foto))
                <img src="{{ url('/storage/' . $folderPath . $foto) }}" class="card-img rounded thumbnail" alt="">
            @else
                <i class="ti ti-fingerprint text-success" style="font-size: 10rem;"></i>
            @endif
        @else
            <i class="ti ti-fingerprint text-success" style="font-size: 10rem;"></i>
        @endif
    </div>
    <div class="col-8">
        <table class="table">
            <tr>
                <th>NPP</th>
                <td>{{ $presensi->nik }}</td>
            </tr>
            <tr>
                <th>Nama</th>
                <td>{{ $presensi->nama_karyawan }}</td>
            </tr>
            <tr>
                <th>Tanggal</th>
                <td>{{ DateToIndo($presensi->tanggal) }}</td>
            </tr>
            <tr>
                <th>{{ $judul }}</th>
                <td>{{ $waktu != null ? date('d-m-Y H:i', strtotime($waktu)) : 'Belum Absen' }}</td>
            </tr>
            <tr>
                <th>Jarak</th>
                <td>
                    @php
                        if (!empty($lokasi_user)) {
                            $lokasi_in = explode(',', $lokasi_user);
                            $latitude_in = $lokasi_in[0];
                            $longitude_in = $lokasi_in[1];
                            $jarak_in = HitungJarak($latitude, $longitude, $latitude_in, $longitude_in);
                        } else {
                            $jarak_in['meters'] = 0;
                        }

                    @endphp

                    {{ formatAngkaDesimal($jarak_in['meters']) }} Meter

                </td>
            </tr>
        </table>

    </div>
</div>
@if (!empty($lokasi_user))
    <div class="row mt-3">
        <div class="col">
            <div id="map"></div>
        </div>
    </div>
@endif

<script>
    var lokasi = "{{ $lokasi_user }}";
    var lok = lokasi.split(",");
    var latitude = lok[0];
    var longitude = lok[1];

    var latitude_kantor = "{{ $latitude }}";
    var longitude_kantor = "{{ $longitude }}";
    // console.log(latitude_kantor + "," + longitude_kantor);
    var rd = "{{ $cabang->radius_cabang }}";
    var map = L.map('map', {
        center: [latitude, longitude],
        zoom: 15
    });

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);
    var marker = L.marker([latitude, longitude]).addTo(map);
    var circle = L.circle([latitude_kantor, longitude_kantor], {
        color: 'red',
        fillColor: '#f03',
        fillOpacity: 0.5,
        radius: rd
    }).addTo(map);

    setInterval(function () {
        map.invalidateSize();
    }, 100);
</script>