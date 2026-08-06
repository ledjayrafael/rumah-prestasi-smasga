@extends('layouts.guest')

@section('title', '500 — Terjadi Kesalahan Server')

@section('content')
    <x-error-card title="Terjadi Kesalahan Server"
        message="Maaf, terjadi kesalahan pada sistem. Tim kami sudah diberi tahu." />
@endsection
