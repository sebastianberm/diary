<?php

namespace App\Services;

use App\Models\Person;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ContextService
{
    /**
     * Detect mentions of people in the given text.
     * Uses hybrid approach: Keyword matching + Optional LLM.
     *
     * @param string $text
     * @return array Array of Person IDs
     */
    public function detectPeople(string $text): array
    {
        if (empty(trim($text))) {
            return [];
        }

        $detectedIds = [];

        // 1. Keyword Matching (Fast, Local, Deterministic)
        // We fetch all people and check if their name or keywords appear in the text
        $people = Person::all();

        foreach ($people as $person) {
            // Check Name
            if (stripos($text, $person->name) !== false) {
                $detectedIds[] = $person->id;
                continue;
            }

            // Check Keywords (Aliases)
            if ($person->keywords) {
                // assume keywords are comma separated or array
                $keywords = is_array($person->keywords) ? $person->keywords : explode(',', $person->keywords);
                foreach ($keywords as $keyword) {
                    if (stripos($text, trim($keyword)) !== false) {
                        $detectedIds[] = $person->id;
                        break; // Found this person
                    }
                }
            }
        }

        // 2. LLM Detection (Smarter, Context aware, can detect "my son" if context implies)
        if (SystemConfig::get('llm_enabled', false)) {
            $llmIds = $this->detectViaLLM($text, $people);
            $detectedIds = array_merge($detectedIds, $llmIds);
        }

        return array_unique($detectedIds);
    }

    protected function detectViaLLM($text, $people)
    {
        $url = SystemConfig::get('llm_endpoint');
        $key = SystemConfig::get('llm_key');
        $model = SystemConfig::get('llm_model', 'gpt-3.5-turbo');

        if (!$url || !$key)
            return [];

        // Simple Prompt
        // We provide a list of known people: [ID] Name
        $peopleList = $people->map(fn($p) => "[{$p->id}] {$p->name}")->join(", ");

        $prompt = "Analyze the following diary entry and identify which people from the list are mentioned or referred to.
        
        Known People List: {$peopleList}
        
        Diary Entry: \"{$text}\"
        
        Return ONLY a JSON array of the IDs of the identified people. Example: [1, 5]. If no one is mentioned, return [].";

        try {
            // Check if it's OpenAI compatible format
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $key,
                'Content-Type' => 'application/json',
            ])->post(rtrim($url, '/') . '/chat/completions', [
                        'model' => $model,
                        'messages' => [
                            ['role' => 'system', 'content' => 'You are a helpful assistant that extracts entities from text.'],
                            ['role' => 'user', 'content' => $prompt],
                        ],
                        'temperature' => 0.1, // Low temp for deterministic results
                    ]);

            if ($response->successful()) {
                $content = $response->json()['choices'][0]['message']['content'] ?? '[]';
                // Clean markdown code blocks if any
                $content = str_replace(['```json', '```'], '', $content);
                $ids = json_decode($content, true);

                return is_array($ids) ? $ids : [];
            } else {
                Log::error('LLM Context Scan Failed: ' . $response->body());
            }

        } catch (\Exception $e) {
            Log::error('LLM Scan Exception: ' . $e->getMessage());
        }

        return [];
    }
}
