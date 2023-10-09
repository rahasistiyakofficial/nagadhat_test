<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Keyword;
use Illuminate\Http\Request;
use App\Models\UserSearchHistory;

class SearchHistoryController extends Controller
{
    public function index(Request $request)
    {
        // dd($request->all());
        $selectedKeywords = $request->input('keywords', []);
        $selectedUsers = $request->input('users', []);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $yesterday = $request->input('yesterday');
        $lastWeek = $request->input('last_week');
        $lastMonth = $request->input('last_month');

        $query = UserSearchHistory::query();
// dd($selectedKeywords);
        $query->when(!empty($selectedKeywords), function ($query) use ($selectedKeywords) {
            $query->whereIn('search_keyword_id', is_array($selectedKeywords) ? $selectedKeywords : [$selectedKeywords]);
        });

        $query->when(!empty($selectedUsers), function ($query) use ($selectedUsers) {
            $query->whereIn('user_id', is_array($selectedUsers) ? $selectedUsers : [$selectedUsers]);
        });

        $query->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
            $query->whereBetween('search_time', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ]);
        });

        $query->where(function ($query) use ($yesterday, $lastWeek, $lastMonth) {
            $query->when($yesterday, function ($query) {
                $query->orWhereDate('search_time', Carbon::yesterday());
            });
        
            $query->when($lastWeek, function ($query) {
                $query->orWhere(function ($query) {
                    $query->whereDate('search_time', '>=', Carbon::now()->subWeek()->startOfWeek())
                        ->whereDate('search_time', '<=', Carbon::now()->subWeek()->endOfWeek());
                });
            });
        
            $query->when($lastMonth, function ($query) {
                $query->orWhere(function ($query) {
                    $query->whereDate('search_time', '>=', Carbon::now()->subMonth()->startOfMonth())
                        ->whereDate('search_time', '<=', Carbon::now()->subMonth()->endOfMonth());
                });
            });
        });

        $searchHistory = $query->get();

        $searchHistory = $this->countKeywordOccurrences($searchHistory);
        if ($request->ajax()) {
            return response()->json(['searchHistory' => $searchHistory]);
        } else {
            return view('search-history.index', [
                'searchHistory' => $searchHistory,
                'keywords' => $this->getAllKeywords(),
                'users' => $this->getAllUsers(),
            ]);
        }
    }

    private function getAllKeywords()
    {
        $keywords = Keyword::all();

        $keywords = $this->countKeywordOccurrences($keywords);

        return $keywords;
    }

    private function getAllUsers()
    {
        return User::all();
    }
    private function countKeywordOccurrences($collection)
    {
        $keywordCounts = [];

        foreach ($collection as $item) {
            $keyword = $item->search_keyword;

            if (array_key_exists($keyword, $keywordCounts)) {
                $keywordCounts[$keyword]++;
            } else {
                $keywordCounts[$keyword] = 1;
            }

            $item->count = $keywordCounts[$keyword];
        }

        return $collection;
    }
}
