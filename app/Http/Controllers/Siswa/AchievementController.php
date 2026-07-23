<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAchievementRequest;
use App\Models\Achievement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AchievementController extends Controller
{
    public function index(Request $request): View
    {
        $base = Auth::user()->achievements();

        $counts = [
            'all' => (clone $base)->count(),
            'pending' => (clone $base)->where('status', 'pending')->count(),
            'approved' => (clone $base)->where('status', 'approved')->count(),
            'revision' => (clone $base)->where('status', 'revision')->count(),
        ];

        $query = Auth::user()->achievements()->latest('event_date');

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $achievements = $query->paginate(10)->withQueryString();

        return view('siswa.achievements.index', compact('achievements', 'counts'));
    }

    public function create(): View
    {
        return view('siswa.achievements.create');
    }

    public function store(StoreAchievementRequest $request): RedirectResponse
    {
        $achievement = Auth::user()->achievements()->create($request->safe()->except('files'));

        foreach ($request->file('files', []) as $file) {
            $path = $file->store('bukti-prestasi', 'public');

            $achievement->files()->create([
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
            ]);
        }

        return redirect()->route('siswa.achievements.index')
            ->with('status', 'Prestasi berhasil diajukan dan menunggu verifikasi guru.');
    }

    public function show(Achievement $achievement): View
    {
        abort_unless($achievement->student_id === Auth::id(), 403);

        return view('siswa.achievements.show', compact('achievement'));
    }
}
