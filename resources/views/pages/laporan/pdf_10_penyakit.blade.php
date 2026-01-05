<!DOCTYPE html>
<html>

<head>
    <title>Laporan 10 Besar Penyakit</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .text-center {
            text-align: center;
        }

        .info {
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>LAPORAN 20 BESAR PENYAKIT</h2>
    </div>

    <div class="info">
        Ruangan : {{ $filter['ruangan'] }} <br>
        Periode : {{ $filter['periode'] }}
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Diagnosa</th>
                <th width="20%">Kode ICD-10</th>
                <th width="15%">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($diagnosas as $index => $d)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $d->diagnosa }}</td>
                    <td>{{ $d->kode_icd }}</td>
                    <td class="text-center">{{ $d->jumlah }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>