<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logbook - {{ $logbook->judul }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff;
            margin: 0;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }

        @media print {
            .print-button, .close-button, .navbar-header, .no-print {
                display: none !important;
            }
            body {
                margin: 0 !important;
                font-size: 12px;
            }
            table {
                width: 100%;
            }
            td, th {
                page-break-inside: avoid;
            }
            @page {
                size: auto;
                margin: 10mm;
            }
        }

        .navbar {
            background-color: #333;
            color: #fff;
            padding: 10px 0;
        }

        .navbar-header {
            padding: 0 20px;
        }

        .close-button, .print-button {
            background: #007bff;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            margin: 0 5px;
        }

        .close-button {
            background: #dc3545;
        }

        .close-button:hover {
            background: #c82333;
        }

        .print-button:hover {
            background: #0056b3;
        }

        header {
            background-color: #fff;
            padding: 10px 0;
            display: flex;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .logo {
            width: 70px;
            height: 70px;
            background-color: #ccc;
            border-radius: 50%;
            margin-left: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .logo img {
            width: 70px;
            height: 70px;
            object-fit: cover;
        }

        .title-container {
            flex-grow: 1;
            text-align: center;
        }

        .title-container h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }

        .title-container h4 {
            margin: 5px 0 0 0;
            font-size: 16px;
            font-weight: normal;
        }

        .info-section {
            margin: 0 auto;
            width: 95%;
        }

        .section-container {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }

        .left-column {
            flex: 7;
        }

        .right-column {
            flex: 3;
        }

        .info-table {
            border-collapse: collapse;
            width: 100%;
            max-width: 100%;
            background-color: rgba(255, 255, 255, 0.8);
        }

        .info-table td {
            padding: 8px;
            border: none;
            vertical-align: top;
            background-color: rgba(255, 255, 255, 0.8);
        }

        .info-left {
            text-align: left;
            width: 20%;
            font-weight: bold;
        }

        .info-center {
            text-align: left;
            width: 40%;
        }

        .info-right {
            text-align: right;
            width: 40%;
            padding: 8px;
        }

        .teknisi-container {
            display: inline-block;
            text-align: left;
            margin-right: 50px;
            vertical-align: bottom;
        }

        .underline {
            display: inline-block;
            border-bottom: 1px solid black;
            width: 150px;
            vertical-align: bottom;
            padding-bottom: 2px;
        }

        .logbook-table {
            width: 95%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: rgba(255, 255, 255, 0.8);
        }

        .logbook-table th,
        .logbook-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        .logbook-table th {
            background-color: #f0f0f0;
            text-align: center;
            font-weight: bold;
        }

        .logbook-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .signature-section {
            width: 95%;
            margin: 20px;
            text-align: right;
        }

        .signature-container {
            display: inline-block;
            margin-top: 50px;
            text-align: center;
        }

        .signature-container img {
            width: 200px;
            height: auto;
            margin-top: 10px;
        }

        .shift-checkbox {
            margin: 0 5px;
        }

        .no-col {
            width: 40px;
            text-align: center;
        }

        .time-col {
            width: 120px;
            text-align: center;
        }

        .duration-col {
            width: 80px;
            text-align: center;
        }
		
		.signature-col {
            width: 95px;
            text-align: center !important; 
        }
        
        .signature-col img {
            /* Membuat gambar Paraf di kolom tabel berada di tengah */
            display: block;
            margin: 0 auto; 
            width: 50px;
            height: 25px;
        }

        .shift-checkbox {
            margin: 0 5px;
        }
    </style>
</head>
<body>
    <nav class="navbar no-print">
        <div class="navbar-header">
            <button class="close-button" onclick="window.history.back()">← Kembali</button>
            Logbook - {{ $logbook->judul }}
            <button class="print-button" onclick="window.print()">🖨️ Print Logbook</button>
        </div>
    </nav>

    <div id="view_pdf">
        <header>
            <div class="logo">
                <img src="data:image/png;base64,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" alt="Logo" style="width: 70px; height: 70px;" onerror="this.style.display='none'">
            </div>
            <div class="title-container">
                <h1>BUKU CATATAN FASILITAS DAN KEGIATAN</h1>
                <h4>(FACILITY LOG BOOK)</h4>
            </div>
        </header>

        <div class="info-section">
            <div class="section-container">
                <div class="left-column">
                    <table class="info-table">
                        <tr>
                            <td class="info-left">PENYELENGGARA PELAYANAN:</td>
                            <td class="info-center">PERUM LPPNPI CABANG JATSC</td>
                        </tr>
                        <tr>
                            <td class="info-left">KELOMPOK FASILITAS:</td>
                            <td class="info-center">{{ strtoupper($logbook->unit->nama ?? 'UNIT KERJA') }}</td>
                        </tr>
                        <tr>
                            <td class="info-left">NAMA PERALATAN:</td>
                            <td class="info-center">
                                @php
                                    $unique_tools = $logbookItems->pluck('tools')->unique()->filter()->toArray();
                                    $tool_count = count($unique_tools);
                                    $font_size = max(12, 18 - ($tool_count * 1.2));
                                @endphp
                                @foreach($unique_tools as $index => $tool)
                                    <span style="font-size: {{ $font_size }}px;">{{ $tool }}</span>
                                    @if($index < count($unique_tools) - 1) / @endif
                                @endforeach
                            </td>
                        </tr>
                        <tr>
                            <td class="info-left">TANGGAL:</td>
                            <td class="info-center">{{ strtoupper(\Carbon\Carbon::parse($logbook->date)->translatedFormat('d F Y')) }}</td>
                        </tr>
                        <tr>
                            <td class="info-left">SHIFT:</td>
                            <td class="info-center">
                                <input type="checkbox" {{ $logbook->shift == '1' ? 'checked' : '' }} disabled class="shift-checkbox"> P
                                <input type="checkbox" {{ $logbook->shift == '2' ? 'checked' : '' }} disabled class="shift-checkbox"> S
                                <input type="checkbox" {{ $logbook->shift == '3' ? 'checked' : '' }} disabled class="shift-checkbox"> M
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="right-column">
                    <div class="info-right">
                        @php
                            // Mengubah ke fullname
                            $unique_teknisi = $logbookItems->pluck('teknisi_user.fullname')->unique()->filter()->values()->toArray();
                        @endphp
                        @for($i = 0; $i < 5; $i++)
                            <p>
                                <span class="teknisi-container">
                                    <span class="underline">
                                        {{ $i + 1 }}.
                                        @if(isset($unique_teknisi[$i]))
                                            {{ $unique_teknisi[$i] }}
                                        @endif
                                    </span>
                                </span>
                            </p>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
        <table class="logbook-table">
            <thead>
                <tr>
                    <th class="no-col">NO.</th>
                    <th class="time-col">TANGGAL/JAM</th>
                    <th class="duration-col">DURASI</th>
                    <th>CATATAN/TINDAKAN</th>
                    <th>NAMA TEKNISI</th>
                    <th class="signature-col">PARAF</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logbookItems as $index => $item)
                <tr>
                    <td class="no-col">{{ $index + 1 }}</td>
                    <td class="time-col">{{ $item->mulai ? \Carbon\Carbon::parse($item->mulai)->format('d/m/Y H:i') : '-' }}</td>
                    <td class="duration-col">
                        @if($item->mulai && $item->selesai)
                            @php
                                $mulai = \Carbon\Carbon::parse($item->mulai);
                                $selesai = \Carbon\Carbon::parse($item->selesai);
                                $diff = $mulai->diff($selesai);
                                $durasi = '';
                                if($diff->d > 0) $durasi .= $diff->d . ' hari ';
                                if($diff->h > 0) $durasi .= $diff->h . ' jam ';
                                if($diff->i > 0) $durasi .= $diff->i . ' menit ';
                                if($diff->s > 0 || empty(trim($durasi))) $durasi .= $diff->s . ' detik';
                                echo trim($durasi);
                            @endphp
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $item->catatan ?? '-' }}</td>
                    {{-- Mengubah ke fullname --}}
                    <td>{{ $item->teknisi_user->fullname ?? 'Unknown' }}</td> 
                    <td class="signature-col">
                        @if($item->teknisi_user && $item->teknisi_user->signature)
                            <img src="{{ $item->teknisi_user->signature }}" alt="Paraf Teknisi" style="width: 50px; height: 25px;">
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @empty
                @for($i = 1; $i <= 10; $i++)
                <tr style="height: 40px;">
                    <td class="no-col">{{ $i }}</td>
                    <td class="time-col"></td>
                    <td class="duration-col"></td>
                    <td></td>
                    <td></td>
                    <td class="signature-col"></td>
                </tr>
                @endfor
                @endforelse

                @if(count($logbookItems) > 0 && count($logbookItems) < 10)
                    @for($i = count($logbookItems) + 1; $i <= 10; $i++)
                    <tr style="height: 40px;">
                        <td class="no-col">{{ $i }}</td>
                        <td class="time-col"></td>
                        <td class="duration-col"></td>
                        <td></td>
                        <td></td>
                        <td class="signature-col"></td>
                    </tr>
                    @endfor
                @endif
            </tbody>
        </table>

        <div class="signature-section">
            @if($logbook->is_approved && $logbook->approvedBy)
                <div class="signature-container">
                    Jakarta, {{ \Carbon\Carbon::parse($logbook->date)->translatedFormat('d F Y') }}<br>
                    {{ $logbook->approvedBy->position ?? 'Atasan' }}<br>
                    @if($logbook->approvedBy->signature)
                        <img src="{{ $logbook->approvedBy->signature }}" alt="Tanda Tangan" style="width: 200px; height: auto; margin-top: 10px;"><br>
                    @else
                        <div style="height: 80px; margin-top: 10px;"></div>
                    @endif
                    {{-- Mengubah ke fullname --}}
                    {{ $logbook->approvedBy->fullname }}{{ $logbook->approvedBy->gelar ? ', ' . $logbook->approvedBy->gelar : '' }}
                </div>
            @else
                <div class="signature-container" style="filter: blur(5px); transition: filter 0.3s ease-in-out; user-select: none; pointer-events: none;">
                    Jakarta, {{ \Carbon\Carbon::parse($logbook->date)->translatedFormat('d F Y') }}<br>
                    (Jabatan)<br>
                    <div style="height: 80px; margin-top: 10px; border: 1px dashed #ccc; display: flex; align-items: center; justify-content: center; color: #999;">
                        (Tanda Tangan)
                    </div>
                    (Nama TTD)
                </div>
            @endif
        </div>
    </div>

    <script>
        
    </script>
</body>
</html>