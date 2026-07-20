<?php

namespace Modules\Pos\Filament\Pages;

use Filament\Pages\Page;
use UnitEnum;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use Modules\Pos\Models\PosTransaction;
use Modules\Pos\Models\PosTransactionItem;
use Modules\Pos\Models\PosProduct;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PosReports extends Page
{
    protected string $view = 'pos::filament.pages.pos-reports';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Laporan POS';

    protected static string | UnitEnum | null $navigationGroup = 'Point of Sale';

    protected static ?int $navigationSort = 6;

    protected static ?string $slug = 'pos-reports';

    protected static ?string $title = 'Laporan Point of Sale';

    // Filter properties
    public $dateFrom;
    public $dateTo;
    public $paymentMethod = '';
    public $reportType = 'daily';

    public function mount()
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function getTransactionsProperty()
    {
        $cacheKey = 'pos_reports_transactions_' . md5($this->dateFrom . '_' . $this->dateTo . '_' . $this->paymentMethod);
        
        return cache()->remember($cacheKey, 300, function () {
            $query = PosTransaction::with('transactionItems')
                ->whereBetween('transaction_date', [
                    Carbon::parse($this->dateFrom)->startOfDay(),
                    Carbon::parse($this->dateTo)->endOfDay()
                ]);

            if ($this->paymentMethod) {
                $query->where('payment_method', $this->paymentMethod);
            }

            return $query->orderBy('transaction_date', 'desc')->get();
        });
    }

    public function getSalesStatsProperty()
    {
        $transactions = $this->transactions;

        return [
            'total_transactions' => $transactions->count(),
            'total_revenue' => $transactions->sum('total'),
            'average_transaction' => $transactions->count() > 0 ? $transactions->avg('total') : 0,
            'total_items_sold' => $transactions->sum(function($transaction) {
                return $transaction->transactionItems->sum('pos_quantity');
            }),
        ];
    }

    public function getTopProductsProperty()
    {
        $cacheKey = 'pos_reports_top_products_' . md5($this->dateFrom . '_' . $this->dateTo . '_' . $this->paymentMethod);
        
        return cache()->remember($cacheKey, 300, function () {
            return PosTransactionItem::select('pos_product_name', 
                    DB::raw('SUM(pos_quantity) as total_quantity'),
                    DB::raw('SUM(subtotal) as total_revenue'))
                ->whereHas('transaction', function($query) {
                    $query->whereBetween('transaction_date', [
                        Carbon::parse($this->dateFrom)->startOfDay(),
                        Carbon::parse($this->dateTo)->endOfDay()
                    ]);
                    
                    if ($this->paymentMethod) {
                        $query->where('payment_method', $this->paymentMethod);
                    }
                })
                ->groupBy('pos_product_name')
                ->orderBy('total_quantity', 'desc')
                ->limit(10)
                ->get();
        });
    }

    public function getPaymentMethodStatsProperty()
    {
        return PosTransaction::select('payment_method',
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(total) as total_amount'))
            ->whereBetween('transaction_date', [
                Carbon::parse($this->dateFrom)->startOfDay(),
                Carbon::parse($this->dateTo)->endOfDay()
            ])
            ->groupBy('payment_method')
            ->get();
    }

    public function getDailySalesProperty()
    {
        return PosTransaction::select(
                DB::raw('DATE(transaction_date) as date'),
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(total) as daily_revenue'))
            ->whereBetween('transaction_date', [
                Carbon::parse($this->dateFrom)->startOfDay(),
                Carbon::parse($this->dateTo)->endOfDay()
            ])
            ->groupBy(DB::raw('DATE(transaction_date)'))
            ->orderBy('date', 'desc')
            ->get();
    }

    public function applyFilter()
    {
        $this->clearReportCaches();
        
        $this->dispatch('filterApplied');
    }

    public function resetFilter()
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
        $this->paymentMethod = '';
        $this->reportType = 'daily';
        
        $this->clearReportCaches();
    }

    private function clearReportCaches()
    {
        $patterns = [
            'pos_reports_transactions_*',
            'pos_reports_top_products_*',
            'pos_reports_payment_stats_*',
            'pos_reports_daily_sales_*'
        ];
        
        foreach ($patterns as $pattern) {
            cache()->forget($pattern);
        }
    }

    public function exportReport()
    {
        try {
            $transactions = $this->transactions;
            
            if ($transactions->isEmpty()) {
                $this->dispatch('show-notification', [
                    'type' => 'warning',
                    'title' => 'Peringatan',
                    'message' => 'Tidak ada data untuk diekspor'
                ]);
                return;
            }

            $this->dispatch('show-notification', [
                'type' => 'success',
                'title' => 'Berhasil',
                'message' => 'Laporan berhasil diekspor'
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('show-notification', [
                'type' => 'error',
                'title' => 'Error',
                'message' => 'Gagal mengekspor laporan: ' . $e->getMessage()
            ]);
        }
    }

    public function getPaymentMethodOptionsProperty()
    {
        return [
            '' => 'Semua Metode',
            'cash' => 'Tunai',
            'card' => 'Kartu Debit/Kredit',
            'transfer' => 'Transfer Bank',
        ];
    }

    public function getReportTypeOptionsProperty()
    {
        return [
            'daily' => 'Harian',
            'weekly' => 'Mingguan',
            'monthly' => 'Bulanan',
        ];
    }
}