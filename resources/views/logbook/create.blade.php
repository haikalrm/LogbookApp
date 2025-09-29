@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create Logbook for Unit {{ strtoupper($unit->name) }}</h1>

    <form action="{{ route('logbook.store', $unit->id) }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="nameWithTitle">Judul</label>
            <input type="text" name="nameWithTitle" id="nameWithTitle" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="shift">Shift</label>
            <select name="shift" id="shift" class="form-control" required>
                <option value="0">Pagi</option>
                <option value="1">Siang</option>
                <option value="2">Malam</option>
            </select>
        </div>

        <div class="form-group">
            <label for="dateWithTitle">Tanggal Kegiatan</label>
            <input type="date" name="dateWithTitle" id="dateWithTitle" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Submit</button>
    </form>
</div>
@endsection
