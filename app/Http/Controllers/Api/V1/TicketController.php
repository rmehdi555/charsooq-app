<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\V1\Register\TicketListRequest;
use App\Models\Ticket;
use App\Models\TicketLog;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\TicketListResource;

class TicketController extends Controller
{

    public function list(): JsonResponse
    {

        $ticket = Ticket::where('user_id', Auth::id())
            ->latest()
            ->paginate(config('custom.paginate_count'));
        $data = TicketListResource::collection($ticket);
        return $this->successResponse($data , '');
    }

    public function filter($request):JsonResponse
    {
        $ticket = Ticket::where('user_id', Auth::id())
            ->where('title',$request->title)
            ->where('created_at',$request->created_at)
            ->where('status_id',$request->status_id)
            ->latest()
        ->paginate(config('custom.paginate_count'));
        $data = TicketListResource::collection($ticket);
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
