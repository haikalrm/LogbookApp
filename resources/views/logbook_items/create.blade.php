@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2>Tambah Item Logbook - {{ $logbook->tanggal }} ({{ ucfirst($logbook->shift) }})</h2>

    <form method="POST" action="{{ route('logbook.item.store', $logbook->id) }}">
        @csrf
        <div class="mb-3">
            <label>Judul</label>
            <input type="text" name="judul" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Isi</label>
            <textarea name="isi" class="form-control" required></textarea>
        </div>
        <button type="submit" class="btn btn-success">Tambah</button>
    </form>
</div>
@endsection
