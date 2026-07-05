<?php

namespace App\Http\Controllers;

use App\Models\Diploma;
use App\Models\DiplomaTemplate;
use App\Models\Student;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'students' => Student::count(),
            'diplomas' => Diploma::count(),
            'templates' => DiplomaTemplate::where('is_active', true)->count(),
            'users' => User::count(),
        ];

        $recentDiplomas = Diploma::with(['student', 'template'])
            ->latest()
            ->take(8)
            ->get();

        return view('dashboard', compact('stats', 'recentDiplomas'));
    }
}
