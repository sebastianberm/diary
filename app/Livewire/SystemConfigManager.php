<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\Setting;
use App\Services\SystemConfig;
use Illuminate\Support\Facades\Http;

class SystemConfigManager extends Component
{
    public $settings;
    public $envValues = [];
    public $testResult = null;

    protected $rules = [
        'settings.*.value' => 'nullable|string',
    ];

    public function mount()
    {
        $this->seedDefaults();
        $this->loadSettings();
    }

    public function seedDefaults()
    {
        $defaults = [
            'immich_url' => ['group' => 'immich', 'type' => 'string', 'desc' => 'API URL (e.g. http://192.168.1.10:2283)'],
            'immich_key' => ['group' => 'immich', 'type' => 'password', 'desc' => 'API Key'],
            'immich_copy_photos' => ['group' => 'immich', 'type' => 'boolean', 'desc' => 'Copy photos locally (Backup)'],

            'llm_endpoint' => ['group' => 'ai', 'type' => 'string', 'desc' => 'LLM API URL (e.g. https://api.openai.com/v1)'],
            'llm_key' => ['group' => 'ai', 'type' => 'password', 'desc' => 'LLM API Key'],
            'llm_model' => ['group' => 'ai', 'type' => 'string', 'desc' => 'Model Name (e.g. gpt-4 or local-model)'],
            'llm_enabled' => ['group' => 'ai', 'type' => 'boolean', 'desc' => 'Enable AI Features'],
            'scan_delay' => ['group' => 'ai', 'type' => 'number', 'desc' => 'Auto-scan Delay (ms)'],

            'reminder_enabled' => ['group' => 'notifications', 'type' => 'boolean', 'desc' => 'Enable Inactivity Reminders'],
            'reminder_days' => ['group' => 'notifications', 'type' => 'number', 'desc' => 'Days inactive before email'],

            'app_name' => ['group' => 'branding', 'type' => 'string', 'desc' => 'Application Name'],
        ];

        foreach ($defaults as $key => $meta) {
            Setting::firstOrCreate(
                ['key' => $key],
                [
                    'group' => $meta['group'],
                    'type' => $meta['type'],
                    'description' => $meta['desc']
                ]
            );
        }
    }

    public function loadSettings()
    {
        $this->settings = Setting::all();

        foreach ($this->settings as $setting) {
            $envKey = strtoupper($setting->key);
            $this->envValues[$setting->key] = env($envKey);
        }
    }

    public function save()
    {
        foreach ($this->settings as $setting) {
            // Only update if not strictly controlled by ENV (though we allow editing DB value even if ENV overrides it, 
            // the UI will show ENV takes precedence).
            $setting->save();
        }

        // Clear cache via Service
        // We can't easily iterate all keys in service, so we rely on individual lookups or flush everything
        // For now, simpler:
        cache()->flush();

        session()->flash('message', 'Settings saved!');
    }

    public function testImmich()
    {
        $url = SystemConfig::get('immich_url');
        $key = SystemConfig::get('immich_key');

        if (!$url || !$key) {
            $this->testResult = ['ok' => false, 'msg' => 'Missing URL or Key'];
            return;
        }

        try {
            $response = Http::withHeaders(['x-api-key' => $key])
                ->timeout(5)
                ->get(rtrim($url, '/') . '/api/server-info/ping');

            if ($response->successful()) {
                $this->testResult = ['ok' => true, 'msg' => 'Connection Successful!'];
            } else {
                $this->testResult = ['ok' => false, 'msg' => 'Error: ' . $response->status()];
            }
        } catch (\Exception $e) {
            $this->testResult = ['ok' => false, 'msg' => 'Exception: ' . $e->getMessage()];
        }
    }

    public function render()
    {
        return view('livewire.system-config-manager');
    }
}
