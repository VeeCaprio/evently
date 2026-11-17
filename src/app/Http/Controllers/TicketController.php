<?php

namespace App\Http\Controllers;

use App\Models\EventRegistration;
use Illuminate\Contracts\View\View;

class TicketController extends Controller
{
    public function show(string $ticketCode): View
    {
        $registration = EventRegistration::query()
            ->with(['event.category', 'attendance'])
            ->where('ticket_code', strtoupper(trim($ticketCode)))
            ->firstOrFail();

        return view('tickets.show', compact('registration'));
    }
}
