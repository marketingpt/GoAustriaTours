<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SyncEnquiryToGhl;
use App\Models\Enquiry;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EnquiryController extends Controller
{
    private function filtered(Request $r)
    {
        return Enquiry::query()
            ->when($r->filled('q'), function ($q) use ($r) {
                $term = '%'.trim($r->input('q')).'%';
                $q->where(fn ($w) => $w->where('first_name', 'like', $term)->orWhere('last_name', 'like', $term)->orWhere('email', 'like', $term)->orWhere('phone_e164', 'like', $term)->orWhere('package_name', 'like', $term));
            })
            ->when($r->filled('status'), fn ($q) => $q->where('status', $r->input('status')))
            ->when($r->filled('source'), fn ($q) => $q->where('source', $r->input('source')))
            ->when($r->filled('form'), fn ($q) => $q->where('form_name', $r->input('form')))
            ->when($r->filled('ghl'), fn ($q) => $q->where('ghl_status', $r->input('ghl')))
            ->when($r->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $r->input('from')))
            ->when($r->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $r->input('to')))
            ->latest();
    }

    public function index(Request $request)
    {
        return view('admin.enquiries.index', [
            'enquiries' => $this->filtered($request)->paginate(25)->withQueryString(),
            'sources' => Enquiry::query()->whereNotNull('source')->distinct()->orderBy('source')->pluck('source'),
            'forms' => Enquiry::query()->distinct()->orderBy('form_name')->pluck('form_name'),
        ]);
    }

    public function show(Enquiry $enquiry)
    {
        return view('admin.enquiries.show', ['e' => $enquiry]);
    }

    public function update(Request $request, Enquiry $enquiry)
    {
        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', array_keys(Enquiry::STATUSES))],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);
        $enquiry->update($data);

        return back()->with('status', 'Enquiry updated.');
    }

    public function destroy(Enquiry $enquiry)
    {
        $enquiry->delete();

        return redirect()->route('admin.enquiries.index')->with('status', 'Enquiry deleted.');
    }

    public function retryGhl(Enquiry $enquiry)
    {
        if (! ghl_enabled()) {
            return back()->withErrors(['ghl' => 'GoHighLevel is not configured (GHL_API_TOKEN / GHL_LOCATION_ID in .env).']);
        }
        $enquiry->update(['ghl_status' => 'pending', 'ghl_error' => null]);
        SyncEnquiryToGhl::dispatchSync($enquiry->id);
        $enquiry->refresh();

        return back()->with('status', $enquiry->ghl_status === 'synced' ? 'Synced to GoHighLevel.' : 'Sync failed: '.$enquiry->ghl_error);
    }

    public function export(Request $request): StreamedResponse
    {
        $query = $this->filtered($request);
        $headers = ['ID', 'Received', 'Form', 'Status', 'First name', 'Last name', 'Email', 'Phone (E.164)', 'Package', 'Tour type', 'Preferred date', 'Time of year', 'Group size', 'Nights', 'Second quote', 'Special request', 'Quiz answers', 'Source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'gclid', 'Consent', 'Consent at', 'Terms version', 'IP', 'Page URL', 'GHL status', 'GHL contact ID'];

        // Cells starting with = + - @ would be run as formulas by Excel: neutralise them.
        $safe = fn ($v) => is_string($v) && $v !== '' && in_array($v[0], ['=', '+', '-', '@', "\t", "\r"], true) ? "'".$v : $v;

        return response()->streamDownload(function () use ($query, $headers, $safe) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, $headers);
            $query->chunk(200, function ($rows) use ($out, $safe) {
                foreach ($rows as $e) {
                    $t = $e->tracking_first ?? [];
                    fputcsv($out, array_map($safe, [
                        $e->id, $e->created_at->toDateTimeString(), $e->form_name, $e->status, $e->first_name, $e->last_name, $e->email, $e->phone_e164,
                        $e->package_name, $e->tour_type, $e->preferred_date, $e->time_of_year, $e->group_size, $e->nights, $e->second_quote, $e->special_request,
                        collect($e->quiz_answers ?? [])->map(fn ($a) => "{$a['label']}: {$a['answer']}")->implode(' | '),
                        $e->source, $t['utm_medium'] ?? '', $t['utm_campaign'] ?? '', $t['utm_term'] ?? '', $t['utm_content'] ?? '', $t['gclid'] ?? '',
                        $e->consent_given ? 'Yes' : 'No', $e->consent_at?->toDateTimeString(), $e->terms_version, $e->ip_address, $e->page_url, $e->ghl_status, $e->ghl_contact_id,
                    ]));
                }
            });
            fclose($out);
        }, 'enquiries-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
