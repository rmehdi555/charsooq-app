<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Invoice\InvoiceIndexRequest;
use App\Http\Resources\InvoiceItemResource;
use App\Http\Resources\InvoiceOthercostResource;
use App\Http\Resources\TransactionResource;
use App\Models\Invoice;
use App\Models\InvoicesOthercosts;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
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

    public function show($code): JsonResponse
    {
        $invoice = Invoice::where('code', $code)->first();
        if (!filled($invoice) or $invoice->user_id != Auth::id())
            return $this->errorResponse(__('messages.item_not_found'), 404);
        $data = [];
        $data['code'] = $code;
        $data['invoice_status'] = $invoice->status;
        $data['items'] = InvoiceItemResource::collection($invoice->invoiceItems);
        $data['othercost'] = $invoice->othercost;
        $data['othercost_list'] = InvoicesOthercosts::where('invoice_id', $invoice->id)->get();
        $data['othercost_list'] = InvoiceOthercostResource::collection($data['othercost_list']);
        $data['totaltransportprice'] = $invoice->totaltransportprice;
        $data['totalitemprice'] = $invoice->totalitemprice;
        $data['brokerwageprice'] = $invoice->brokerwageprice;
        $data['transaction_list'] = Transaction::where('invoice_id', $invoice->id)->get();
        $data['transaction_list'] = TransactionResource::collection($data['transaction_list']);

        $price = 0;
        foreach ($invoice->invoiceItems as $item) {
            if ($item->isapproved == 1)
                $price = $price + ($item->singleitemfullprice * $item->count);
        }
        $data['price'] = (int)$price;
        $data['sum_pay'] = Transaction::where('invoice_id', $invoice->id)->sum('amount');
        $data['price_for_pay'] = (int)$price + (int)$invoice->invoiceOthercosts->sum('OtherCostPrice') - (int)$data['sum_pay'];

        return $this->successResponse($data, '');
    }
}
