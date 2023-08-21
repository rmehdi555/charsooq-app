<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Invoice\InvoiceIndexRequest;
use App\Http\Resources\InvoiceListResource;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index(InvoiceIndexRequest $request): JsonResponse
    {

        $invoice = Invoice::select('invoices.code', 'invoices.status', 'invoices.invoicedate', DB::raw('SUM(invoice_items.count) as sum_count'))
            ->where('user_id', Auth::id())
            ->leftjoin('invoice_items', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->when(
                isset($request->code),
                fn($q) => $q->where('invoices.code', '=', $request->code)
            )
            ->when(
                isset($request->date),
                fn($q) => $q->where('invoices.invoicedate', 'Like', '%' . $request->date . '%')
            )
            ->when(
                isset($request->order_level),
                fn($q) => $q->where('invoices.orderlevel', '=', $request->order_level)
            )
            ->groupBy('invoices.code')
            ->orderBy('invoices.created_at', 'desc')
            ->paginate(config('custom.paginate_count'));
        $order_level = ['درخواست', 'فاکتور', 'سفارش', 'تسویه', 'ارسال', 'تحویل', 'نامشخص', 'آماده برای پرداخت', 'پرداخت شده', 'ارسال به مشتری', 'تحویل مشتری'];
        $invoice->put('orderlevel', $order_level);
        return $this->successResponse($invoice, '');
    }
}
