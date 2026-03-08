<?php

namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;

class AiAnalyticsService
{
    /**
     * Generate sophisticated insights from analytics data using OpenAI.
     *
     * @param array $data
     * @return array
     */
    public function generateInsights(array $data): array
    {
        try {
            // Simplify data for the prompt to reduce tokens
            $context = [
                'metrics' => $data['metrics'] ?? [],
                'top_tours' => collect($data['top_tours'] ?? [])->map->only(['name', 'revenue', 'bookings_count']),
                'trends' => $data['monthlyTrends'] ?? [],
            ];

            $prompt = "As an expert business analyst for a safari tour agency, analyze the following performance data:\n\n" .
                      json_encode($context) .
                      "\n\nProvide a structured analysis with the following sections:\n" .
                      "1. **Strategic Recommendations**: 3 concise, actionable strategies to grow the business.\n" .
                      "2. **Key Insights**: 3 deep observations about customer behavior or product performance.\n" .
                      "3. **Warnings**: Any potential risks or negative trends observed.\n\n" .
                      "Format the response as a JSON object with keys: 'strategies', 'insights', 'warnings'. Each should be an array of strings.";

            $result = OpenAI::chat()->create([
                'model' => 'gpt-4o', // Or gpt-3.5-turbo if cost is a concern, but user asked for "deep dive"
                'messages' => [
                    ['role' => 'system', 'content' => 'You are an advanced data analyst AI specializing in travel and tourism.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'response_format' => ['type' => 'json_object'],
            ]);

            return json_decode($result->choices[0]->message->content, true);

        } catch (\Exception $e) {
            Log::error('AI Analytics Error: ' . $e->getMessage());

            // Fallback content if AI fails or key is missing
            return [
                'strategies' => ['Focus on high-performing tours', 'Optimize marketing for top customer demographics', 'Review pricing for low-conversion tours'],
                'insights' => ['Data indicates seasonal trends', 'Customer retention could be improved', 'Mobile traffic conversion is key'],
                'warnings' => ['Ensure API keys are configured for better insights'],
            ];
        }
    }
}
