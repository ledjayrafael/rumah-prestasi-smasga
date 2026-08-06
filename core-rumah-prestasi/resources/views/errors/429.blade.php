@extends('layouts.guest')

@section('title', '429 — Terlalu Banyak Permintaan')

@section('content')
    <x-error-card title="Terlalu Banyak Permintaan"
        message="Kamu mengirim permintaan terlalu cepat. Coba lagi dalam beberapa saat." />
@endsection
