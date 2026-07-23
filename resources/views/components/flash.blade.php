@if (session('status'))
    <div class="mb-4 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm font-medium px-4 py-3">
        {{ session('status') }}
    </div>
@endif

@if (session('credentials'))
    <div class="mb-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm font-medium px-4 py-3">
        {{ session('credentials') }}
    </div>
@endif

@if ($errors->any())
    <div class="mb-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm font-medium px-4 py-3">
        <ul class="list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
