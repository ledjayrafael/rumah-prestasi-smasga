<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompetitionController extends Controller
{
    public function index(Request $request): View
    {
        $query = Competition::query()
            ->where('registration_deadline', '>=', today())
            ->orderBy('registration_deadline');

        if ($category = $request->query('category')) {
            $query->where('category', $category);
        }

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('organizer', 'like', "%{$search}%");
            });
        }

        $competitions = $query->get();
        $featured = $request->query('category') || $request->query('q') ? null : $competitions->shift();

        return view('siswa.competitions.index', compact('competitions', 'featured'));
    }
}
