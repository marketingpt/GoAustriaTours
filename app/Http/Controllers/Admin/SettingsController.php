<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    private function fields(): array
    {
        return collect(config('site.fields'))->collapse()->all();
    }

    public function edit()
    {
        $token = (string) config('services.ghl.token');

        return view('admin.settings.edit', [
            'groups' => config('site.fields'),
            'ghl' => [
                'on' => ghl_enabled(),
                'token' => filled($token) ? 'Set (hidden)' : 'Not set',
                'location' => filled(config('services.ghl.location_id')) ? '…'.substr((string) config('services.ghl.location_id'), -4) : 'Not set',
            ],
            'secretSet' => filled(Setting::get('recaptcha_secret_key')),
        ]);
    }

    public function update(Request $request)
    {
        $rules = [];
        foreach ($this->fields() as $key => [$label, $type]) {
            $rules[$key] = match (true) {
                $type === 'media' => ['nullable', 'exists:media,id'],
                $key === 'gtm_id' => ['nullable', 'regex:/^GTM-[A-Z0-9]+$/'],
                $key === 'email' => ['nullable', 'email', 'max:190'],
                str_starts_with($key, 'social_') => ['nullable', 'url', 'max:300'],
                $key === 'terms_version' => ['required', 'string', 'max:40'],
                $key === 'legal_name', $key === 'brand_name' => ['required', 'string', 'max:120'],
                default => ['nullable', 'string', 'max:2000'],
            };
        }
        $data = $request->validate($rules, ['gtm_id.regex' => 'The GTM ID must look like GTM-XXXXXXX.']);

        // Notification emails: every entry must be a valid address.
        $emails = collect(explode(',', (string) ($data['notification_emails'] ?? '')))->map('trim')->filter();
        foreach ($emails as $e) {
            if (! filter_var($e, FILTER_VALIDATE_EMAIL)) {
                return back()->withInput()->withErrors(['notification_emails' => "\"{$e}\" is not a valid email address."]);
            }
        }
        $data['notification_emails'] = $emails->implode(', ');

        foreach ($this->fields() as $key => [$label, $type]) {
            if ($type === 'password' && blank($data[$key] ?? null)) {
                continue; // keep the existing secret
            }
            Setting::put($key, isset($data[$key]) ? strip_tags((string) $data[$key]) : null);
        }

        return back()->with('status', 'Settings saved.');
    }
}
