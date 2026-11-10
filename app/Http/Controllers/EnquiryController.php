<?php

namespace App\Http\Controllers;

use App\Http\Requests\EnquiryRequest;
use App\Services\EnquiryService;

class EnquiryController extends Controller
{
    /** The single endpoint behind every enquiry form (quiz, modal, package, question, contact). */
    public function store(EnquiryRequest $request, EnquiryService $service)
    {
        $enquiry = $service->create($request);

        // Read once by the Thank You page to fire the conversion event (not on reloads).
        $request->session()->flash('enquiry', ['id' => $enquiry->id, 'form_name' => $enquiry->form_name]);

        $redirect = route('thank-you');

        return $request->expectsJson()
            ? response()->json(['ok' => true, 'redirect' => $redirect])
            : redirect($redirect);
    }
}
