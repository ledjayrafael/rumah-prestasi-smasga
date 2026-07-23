<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCompetitionRequest;
use App\Models\Competition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CompetitionController extends Controller
{
    public function index(): View
    {
        $competitions = Competition::orderByDesc('registration_deadline')->paginate(15);

        return view('admin.competitions.index', compact('competitions'));
    }

    public function create(): View
    {
        return view('admin.competitions.create');
    }

    public function store(StoreCompetitionRequest $request): RedirectResponse
    {
        Competition::create([...$request->validated(), 'created_by' => Auth::id()]);

        return redirect()->route('admin.competitions.index')->with('status', 'Info lomba berhasil ditambahkan.');
    }

    public function edit(Competition $competition): View
    {
        return view('admin.competitions.edit', compact('competition'));
    }

    public function update(StoreCompetitionRequest $request, Competition $competition): RedirectResponse
    {
        $competition->update($request->validated());

        return redirect()->route('admin.competitions.index')->with('status', 'Info lomba berhasil diperbarui.');
    }

    public function destroy(Competition $competition): RedirectResponse
    {
        $competition->delete();

        return back()->with('status', 'Info lomba berhasil dihapus.');
    }
}
