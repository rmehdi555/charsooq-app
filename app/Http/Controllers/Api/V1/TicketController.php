<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\V1\Register\TicketListRequest;
use App\Models\Department;
use App\Models\Ticket;
use App\Models\TicketLog;
use App\Models\TicketStatus;
use Illuminate\Http\Request;
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

    public function new(TicketListRequest $request)
    {
        $code = Convertors::datetocode();
        Ticket::create([
            "title"         => $request->title,
            "user_id"       => Auth::user()->id,
            "status_id"     => 2,
            "department_id" => $request->department_id,
            "invoice_id"    => empty($request->invoice_id) ? null : $request->invoice_id,
            "code"          => $code,
        ]);
        TicketLog::create([
            "content"       => $request->input('content'),
            "attachment_id" => empty($request->attachment_id) ? null : $request->attachment_id,
        ]);

        return redirect('tickets');
    }
}
