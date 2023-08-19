<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Convertors;
use App\Http\Requests\V1\Register\TicketListRequest;
use App\Models\Invoice;
use App\Models\Ticket;
use App\Models\TicketLog;
use App\Models\TicketStatus;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\TicketListResource;

class TicketController extends Controller
{

    public function list(TicketListRequest $request): JsonResponse
    {

        $ticket = Ticket::where('user_id', Auth::id())
            ->when(
                isset($request->title),
                fn($q) => $q->where('tickets.title', 'Like', '%' . $request->title . '%')
            )
            ->when(
                isset($request->date),
                fn($q) => $q->where('tickets.created_at', '=', $request->date )
            )
            ->when(
                isset($request->status),
                fn($q) => $q->where('tickets.status_id', '=', $request->status )
            )
            ->latest()
            ->paginate(config('custom.paginate_count'));
        $status=TicketStatus::select('id','name')->get();
        $data = TicketListResource::collection($ticket);
        $data->put('status',$status);
        return $this->successResponse($data , '');
    }

    public function createpage(): JsonResponse
    {
        $invoice=Invoice::select('code')->where('user_id','=',Auth::user()->id)->get();
        return $this->successResponse($invoice , '');

    }

    public function insert(TicketListRequest $request)
    {
        $code = Convertors::datetocode();
        $invoice=Invoice::select('id')->where('code','=',$request->invoiceid)->get();
//        dd($invoice[0]['id']);
        $tiketid=Ticket::create([
            "title"         => $request->title,
            "user_id"       => Auth::user()->id,
            "status_id"     => 1,
            "department_id" => $request->department_id,
            "code"          => $code,
            "invoice_id"    => $invoice[0]['id']
        ]);
        TicketLog::create([
            "content" => $request->input('content'),
            "ticket_id" => $tiketid->id,
            "file_id" => empty($request->attachment_id) ? null : $request->attachment_id,
            "department_id" => $request->department_id,
            "status_id"     => 1
        ]);
        return $this->successResponse($tiketid->id , __('messages.ticket_saved_successfully'));
    }
}
