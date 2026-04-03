<?php

namespace App\Http\Controllers;

use App\Models\AIContent;
use App\Models\Content;
use App\Models\ContentAnalytics;
use App\Models\ContentEdit;
use App\Models\ContentInsight;
use App\Models\Translation;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalGeneratedContents = AIContent::count();
        $totalEditedContents = ContentEdit::count();
        $totalTranslations = Translation::count();
        $totalInsights = ContentInsight::count();

        $draftCount = Content::where('status', 'draft')->count();
        $publishedCount = Content::where('status', 'published')->count();

        $analytics = ContentAnalytics::get();
        $totalViews = $analytics->sum(fn($item) => (int) $item->views);
        $totalLikes = $analytics->sum(fn($item) => (int) $item->likes);
        $totalShares = $analytics->sum(fn($item) => (int) $item->shares);
        $engagementRate = $totalViews > 0 ? round((($totalLikes + $totalShares) / $totalViews) * 100, 2) : 0;

        $recentContents = Content::latest()->take(5)->get();
        $recentInsights = ContentInsight::latest()->take(4)->get();
        $recentTranslations = Translation::latest()->take(4)->get();

        $moduleStats = [
            ['label' => 'Generated', 'value' => $totalGeneratedContents, 'color' => '#2563eb'],
            ['label' => 'Edited', 'value' => $totalEditedContents, 'color' => '#0f766e'],
            ['label' => 'Translated', 'value' => $totalTranslations, 'color' => '#7c3aed'],
            ['label' => 'Insights', 'value' => $totalInsights, 'color' => '#ea580c'],
        ];

        return view('admin.Dashboard.dashboard', compact(
            'totalUsers',
            'totalGeneratedContents',
            'totalEditedContents',
            'totalTranslations',
            'totalInsights',
            'draftCount',
            'publishedCount',
            'totalViews',
            'totalLikes',
            'totalShares',
            'engagementRate',
            'recentContents',
            'recentInsights',
            'recentTranslations',
            'moduleStats'
        ));
    }
}
