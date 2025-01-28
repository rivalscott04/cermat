<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalIncome = Subscription::where('payment_status', 'paid')
            ->sum('amount_paid');

        $pendingTransactions = Subscription::where('payment_status', 'pending')
            ->sum('amount_paid');

        $totalUsers = User::count();

        $activeSubscribers = User::whereHas('subscriptions', function ($query) {
            $query->where('payment_status', 'paid')
                ->where('end_date', '>', Carbon::now());
        })->count();

        $previousMonthIncome = Subscription::where('payment_status', 'paid')
            ->whereMonth('created_at', '=', Carbon::now()->subMonth()->month)
            ->sum('amount_paid');

        $incomeGrowth = $previousMonthIncome != 0
            ? round((($totalIncome - $previousMonthIncome) / $previousMonthIncome) * 100)
            : 0;

        $previousMonthUsers = User::whereMonth('created_at', '=', Carbon::now()->subMonth()->month)
            ->count();

        $userGrowth = $previousMonthUsers != 0
            ? round((($totalUsers - $previousMonthUsers) / $previousMonthUsers) * 100)
            : 0;

        // Add new statistics
        $totalOrders = Subscription::where('payment_status', 'paid')->count();

        $pendingOrders = Subscription::where('payment_status', 'pending')->count();

        // Get current month's income
        $currentMonthIncome = Subscription::where('payment_status', 'paid')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount_paid');

        // Calculate percentages
        $totalOrdersPercent = $totalOrders > 0 ?
            round(($totalOrders / ($totalOrders + $pendingOrders)) * 100) : 0;

        $pendingOrdersPercent = ($totalOrders + $pendingOrders) > 0 ?
            round(($pendingOrders / ($totalOrders + $pendingOrders)) * 100) : 0;

        // Calculate month-over-month growth for income
        $lastMonthIncome = Subscription::where('payment_status', 'paid')
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->sum('amount_paid');

        $monthlyIncomeGrowth = $lastMonthIncome > 0 ?
            round((($currentMonthIncome - $lastMonthIncome) / $lastMonthIncome) * 100) : 0;

        // Get data for the last 31 days
        $startDate = Carbon::now()->subDays(30)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        // Get daily order counts (number of subscriptions)
        $dailyOrders = Subscription::where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->get()
            ->mapWithKeys(function ($item) {
                return [Carbon::parse($item->date)->timestamp * 1000 => $item->count];
            })
            ->toArray();

        // Get daily payment amounts
        $dailyPayments = Subscription::where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(amount_paid) as total'))
            ->get()
            ->mapWithKeys(function ($item) {
                return [Carbon::parse($item->date)->timestamp * 1000 => $item->total];
            })
            ->toArray();

        // Fill in missing dates with zero values
        $chartData = [];
        for ($date = clone $startDate; $date <= $endDate; $date->addDay()) {
            $timestamp = $date->timestamp * 1000;
            $chartData['orders'][] = [$timestamp, $dailyOrders[$timestamp] ?? 0];
            $chartData['payments'][] = [$timestamp, $dailyPayments[$timestamp] ?? 0];
        }

        return view('admin.dashboard', compact(
            'totalIncome',
            'pendingTransactions',
            'totalUsers',
            'activeSubscribers',
            'incomeGrowth',
            'userGrowth',
            'chartData',
            'totalOrders',
            'pendingOrders',
            'currentMonthIncome',
            'totalOrdersPercent',
            'pendingOrdersPercent',
            'monthlyIncomeGrowth'
        ));
    }
}
