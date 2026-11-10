<!doctype html>
<html>
<body style="font-family:Arial,Helvetica,sans-serif;color:#22262A;line-height:1.5">
<h2 style="margin:0 0 12px">New enquiry #{{ $enquiry->id }}</h2>
<p style="margin:0 0 16px">Received {{ $enquiry->created_at->format('j M Y H:i') }} via <strong>{{ $enquiry->form_name }}</strong>.</p>
<table cellpadding="6" style="border-collapse:collapse;font-size:14px">
    @foreach ([
        'Name' => $enquiry->fullName(),
        'Email' => $enquiry->email,
        'Phone' => $enquiry->phone_e164,
        'Package' => $enquiry->package_name,
        'Tour type' => $enquiry->tour_type,
        'Preferred date' => $enquiry->preferred_date,
        'Time of year' => $enquiry->time_of_year,
        'Group size' => $enquiry->group_size,
        'Nights' => $enquiry->nights,
        'Second quote' => $enquiry->second_quote,
        'Special request' => $enquiry->special_request,
        'Source' => $enquiry->source,
    ] as $label => $value)
        @if ($value)
            <tr><td style="color:#666;vertical-align:top">{{ $label }}</td><td>{{ $value }}</td></tr>
        @endif
    @endforeach
    @foreach ($enquiry->quiz_answers ?? [] as $a)
        <tr><td style="color:#666;vertical-align:top">{{ $a['label'] }}</td><td>{{ $a['answer'] }}</td></tr>
    @endforeach
</table>
<p style="margin-top:20px"><a href="{{ route('admin.enquiries.show', $enquiry) }}">Open in admin</a></p>
</body>
</html>
