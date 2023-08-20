<?php

namespace App\Http\Controllers\Api\V1;

use App\Classes\FileUpload;
use App\Enum\FileCategory;
use App\Helpers\Convertors;
use App\Http\Requests\V1\Ticket\TicketIndexRequest;
use App\Http\Requests\V1\Ticket\TicketStoreRequest;
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

    public function index(TicketIndexRequest $request): JsonResponse
    {

        $ticket = Ticket::where('user_id', Auth::id())
            ->when(
                isset($request->title),
                fn($q) => $q->where('tickets.title', 'Like', '%' . $request->title . '%')
            )
            ->when(
                isset($request->date),
                fn($q) => $q->where('tickets.created_at', 'Like', '%' . $request->date . '%')
            )
            ->when(
                isset($request->status_id),
                fn($q) => $q->where('tickets.status_id', '=', $request->status_id)
            )
            ->latest()
            ->paginate(config('custom.paginate_count'));
        $status = TicketStatus::select('id', 'name')->get();
        $data = TicketListResource::collection($ticket);
        $data->put('status', $status);
        return $this->successResponse($data, '');
    }

    public function create(): JsonResponse
    {
        $invoice = Invoice::select('code')->where('user_id', '=', Auth::user()->id)->get();
        return $this->successResponse($invoice, '');

    }

    public function store(TicketStoreRequest $request, FileUpload $fileUpload)
    {
        $code = Convertors::datetocode();
        $invoice = Invoice::select('id')->where('code', $request->invoice_id)->get();
        $ticket = Ticket::create([
            "title" => $request->title,
            "user_id" => Auth::user()->id,
            "status_id" => 1,
            "department_id" => $request->department_id,
            "code" => $code,
            "invoice_id" => $invoice[0]['id']
        ]);
        if (isset($request->file))
            $file = $fileUpload->setKey('file')
                ->setRequest($request)
                ->setCaption('user image ticket | user_id: ' . Auth::id())
                ->setCategory(FileCategory::tickets)
                ->save();
        $file_id = isset($file) ? $file->id : null;
        TicketLog::create([
            "content" => $request->body,
            "ticket_id" => $ticket->id,
            "file_id" => $file_id,
            "department_id" => $request->department_id,
            "status_id" => 1
        ]);
        return $this->successResponse($ticket->id, __('messages.ticket_saved_successfully'));
    }
}
