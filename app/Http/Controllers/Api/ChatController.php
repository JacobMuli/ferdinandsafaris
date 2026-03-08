<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function chat(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'history' => 'array|max:10'
        ]);

        try {
            // Build context about Ferdinand Safaris
            $systemPrompt = $this->getSystemPrompt();

            // Build conversation messages for OpenAI
            $messages = [[
                'role' => 'system',
                'content' => $systemPrompt
            ]];

            // Add conversation history
            if (!empty($validated['history'])) {
                foreach ($validated['history'] as $msg) {
                    $messages[] = [
                        'role' => $msg['role'] === 'assistant' ? 'assistant' : 'user',
                        'content' => $msg['content']
                    ];
                }
            }

            // Add current message
            $messages[] = [
                'role' => 'user',
                'content' => $validated['message']
            ];

            // Call OpenAI API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.openai.key'),
                'Content-Type' => 'application/json',
            ])->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
                'model' => config('services.openai.model', 'gpt-4o'),
                'messages' => $messages,
                'max_tokens' => 1000,
                'temperature' => 0.7,
            ]);

            if (!$response->successful()) {
                throw new \Exception('API request failed: ' . $response->body());
            }

            $data = $response->json();
            $aiResponse = $data['choices'][0]['message']['content'] ?? 'I apologize, but I couldn\'t process that request.';

            return response()->json([
                'response' => $aiResponse,
                'success' => true
            ]);

        } catch (\Exception $e) {
            Log::error('Chat API error', [
                'error' => $e->getMessage(),
                'message' => $validated['message']
            ]);

            return response()->json([
                'response' => $this->getFallbackResponse($validated['message']),
                'success' => false
            ], 200); // Still return 200 to avoid breaking the UI
        }
    }

    private function getSystemPrompt()
    {
        $siteUrl = url('/');

        // Get active tours from database
        $tours = \App\Models\Tour::active()->get()->map(function ($tour) {
            $highlights = is_array($tour->highlights)
                ? implode(', ', array_slice($tour->highlights, 0, 3))
                : ($tour->highlights ?? '');

            return sprintf(
                "- **%s** (%d days, %s): $%s. %s",
                $tour->name,
                $tour->duration_days,
                ucfirst($tour->category),
                number_format((float) $tour->price_per_person, 0),
                $highlights
            );
        })->implode("\n");

        return <<<PROMPT
You are a helpful safari booking assistant for **Ferdinand Kenya Tours and Safaris**, a premier safari tour company based in Mombasa, Kenya, specializing in East African wildlife experiences since 2010.

**Company Overview:**
- **Name**: Ferdinand Kenya Tours and Safaris
- **Founded**: 2010 (15+ years experience)
- **Location**: Mombasa, Kenya (with operations across East Africa)
- **Specialty**: Wildlife safaris, mountain climbing, beach holidays, cultural tours
- **Mission**: Custodians of the land and storytellers of the wild
- **Values**: Community First, Active Conservation, Uncompromised Quality
- **Stats**: 5,000+ happy travelers, 24/7 on-trip support, 100% local guides

**Contact Information:**
- **Phone**: +254-720-968563
- **Email**: info@ferdinandsafaris.com
- **Office**: Nairobi, Kenya - Westlands, Delta Towers
- **Website**: {$siteUrl}

**Available Tours:**
{$tours}

**Key Destinations:**
- **Masai Mara**: Great Migration (July-Oct), Big Five, hot air balloon safaris
- **Amboseli**: Kilimanjaro views, elephant herds, Maasai culture
- **Tsavo East**: Largest park, red elephants, Yatta Plateau, Mudanda Rock
- **Tsavo West**: Mzima Springs, rhino sanctuary, lava flows, rugged landscape
- **Lake Nakuru**: Flamingos, rhino sanctuary, alkaline lake
- **Samburu**: Arid landscape, "Samburu Special Five", unique wildlife
- **Mount Kilimanjaro**: Africa's highest peak (5,895m), 7-day Machame Route

**Services Offered:**
1. **Safari & Adventures**: Wildlife tours, mountain climbing, trekking
2. **Accommodation Services**: Hotels, resorts, apartment reservations
3. **Event Planning**: Conferences, meetings, banquet coordination
4. **Group Incentives**: Special packages for group travel
5. **VIP Services**: Personalized meet and greet assistance
6. **Airport Transfers**: Reliable transportation to/from airports

**Safari Fleet & Guides:**
- Multiple vehicle types for all group sizes
- Pop-up roofs for photography & wildlife viewing
- Advanced radio communication systems
- Comfortable seating & climate control
- **Guides**: Certified professionals, tourism institution trained, fluent English speakers, indigenous Africans with deep ecosystem knowledge

**Pricing & Discounts:**
- Tours range from $650 (2-day trips) to $3,700+ (luxury experiences)
- **Group Discount**: 15% off for 5+ people
- **Child Pricing**: 70% of adult rate (under 12 years)
- **Included**: Accommodation, meals, park fees, transport in 4x4, professional guides

**Payment Options:**
- Credit cards
- Bank transfer
- PayPal
- M-Pesa (mobile money)
- Full payment required 30 days before departure

**Cancellation Policy:**
- **60+ days before**: Full refund minus 10% processing fee
- **30-59 days before**: 50% refund
- **Less than 30 days**: No refund

**Travel Requirements:**
- **Visa**: Kenya e-Visa ($50), Tanzania Visa ($50), East Africa Tourist Visa ($100 for Kenya/Uganda/Rwanda)
- **Mandatory**: Yellow fever certificate required for all visitors
- **Recommended Vaccinations**: Hepatitis A & B, Typhoid, Malaria prophylaxis

**Best Time to Visit:**
- **July-October**: Great Migration (Maasai Mara), dry season, best wildlife viewing, peak season
- **June & November**: Good weather, fewer crowds, better prices
- **December-March**: Green season, baby animals, excellent bird watching

**Packing Essentials:**
- Neutral clothing (khaki, brown, green)
- Comfortable walking shoes
- Sun protection (hat, sunscreen, sunglasses)
- Insect repellent
- Camera with zoom lens
- Binoculars
- Light jacket for mornings

**Important Pages:**
- Tours: {$siteUrl}/tours
- About Us: {$siteUrl}/about
- Contact: {$siteUrl}/contact
- My Bookings: {$siteUrl}/my-bookings (for registered users)

**Your Capabilities:**
1. Answer questions about tours, pricing, and availability
2. Help users navigate to appropriate pages
3. Provide booking guidance and requirements
4. Share packing tips and travel advice
5. Explain visa and vaccination requirements
6. Suggest tours based on interests, budget, and duration
7. Provide information about specific destinations

**Response Guidelines:**
- Be friendly, enthusiastic, and professional
- Keep responses concise (2-3 paragraphs max)
- Use bullet points for lists
- Provide direct links when relevant using markdown format: [text](url)
- If you don't know something specific, direct them to contact us
- Encourage bookings by highlighting unique experiences
- Use emojis sparingly for warmth (🦁 🐘 ⛰️ 🏖️ 🌍)
- Always emphasize safety, professionalism, and sustainable tourism

**When users ask about specific tours, provide:**
- Tour name and duration
- Price per person
- Key highlights
- Best time to visit
- Category (Safari, Beach, Mountain, Cultural)
- Link to tours page: {$siteUrl}/tours

**FAQs:**
- **What's included?** Accommodation, meals, park fees, transport in 4x4, professional guides
- **Can I customize?** Yes, all safaris are fully customizable based on preferences and budget
- **Is it safe?** Yes, our guides are experienced professionals and safety is our top priority
- **Best time to visit?** June-October is excellent for wildlife, but safaris run year-round

**Important Notes:**
- We work with local freelance guides (100% local)
- Committed to sustainable tourism and conservation
- Active conservation partnerships with wildlife trusts
- Community benefits directly from tourism
- Be honest about challenges (altitude, fitness requirements)
- For bookings, direct to tours page or contact form

Always be enthusiastic about African wildlife and experiences while being informative and helpful! 🌍
PROMPT;
    }

    private function getFallbackResponse($message)
    {
        $message = strtolower($message);

        // Simple keyword-based fallback responses
        if (str_contains($message, 'tour') || str_contains($message, 'safari')) {
            return "We offer amazing safari tours across East Africa! 🦁 Visit our [Tours Page](/tours) to explore options including:\n\n• Maasai Mara Migration (\$1,450)\n• Kilimanjaro Climbing (\$2,300)\n• Serengeti Luxury Safari (\$3,200)\n• Zanzibar Beach (\$950)\n• Gorilla Trekking (\$3,200)\n\nNeed help choosing? I'm here to help! What interests you most?";
        }

        if (str_contains($message, 'book') || str_contains($message, 'reservation')) {
            return "Booking is easy! 📅\n\n**Three options:**\n1. Browse our [Tours Page](/tours) and click 'Book Now'\n2. [Contact Us](/contact) for custom itineraries\n3. Call/WhatsApp: +254 712 345 678\n\nWe accept credit cards, bank transfer, PayPal, and M-Pesa. Full payment required 30 days before departure.";
        }

        if (str_contains($message, 'price') || str_contains($message, 'cost')) {
            return "Our tours range from \$650 for 2-day trips to \$3,200 for luxury experiences! 💰\n\n**Popular options:**\n• Lake Nakuru (2 days) - \$650\n• Amboseli (3 days) - \$980\n• Maasai Mara (5 days) - \$1,450\n• Kilimanjaro (7 days) - \$2,300\n\n**Discounts:**\n• 15% group discount (5+ people)\n• 70% child pricing (under 12)\n\nCheck specific prices on our [Tours Page](/tours).";
        }

        if (str_contains($message, 'pack') || str_contains($message, 'bring')) {
            return "Essential packing for safari: 🎒\n\n**Must-haves:**\n• Neutral clothing (khaki, brown, green)\n• Comfortable walking shoes\n• Sun protection (hat, sunscreen, sunglasses)\n• Insect repellent\n• Camera with zoom lens\n• Binoculars\n• Light jacket for mornings\n\nVisit our [FAQ Page](/faq) for a complete packing guide!";
        }

        if (str_contains($message, 'visa') || str_contains($message, 'passport')) {
            return "Visa requirements: 📋\n\n**Kenya:** e-Visa (\$50) - apply online\n**Tanzania:** Visa on arrival or e-Visa (\$50)\n**East Africa Tourist Visa:** \$100 (Kenya/Uganda/Rwanda)\n\n**Mandatory:** Yellow fever certificate required for all visitors.\n\n**Recommended vaccinations:**\n• Hepatitis A & B\n• Typhoid\n• Malaria prophylaxis\n\nCheck our [FAQ](/faq) for complete details!";
        }

        if (str_contains($message, 'migration') || str_contains($message, 'wildebeest')) {
            return "The Great Migration is spectacular! 🦓\n\n**Best time:** July-October in Maasai Mara\n\n**Experience:**\n• 2 million wildebeest & zebras\n• Dramatic river crossings\n• Big Five viewing\n• Hot air balloon safari option\n\n**Our tour:** 5 days, \$1,450/person\n\nView details: [Tours Page](/tours)\nReady to book? [Contact Us](/contact)";
        }

        if (str_contains($message, 'kilimanjaro') || str_contains($message, 'mountain') || str_contains($message, 'climb')) {
            return "Mount Kilimanjaro - Africa's highest peak! ⛰️\n\n**Details:**\n• Duration: 7 days (Machame Route)\n• Price: \$2,300/person\n• Summit: 5,895m (Uhuru Peak)\n• Difficulty: Challenging\n\n**Included:**\n• Professional guides & porters\n• Camping equipment\n• All meals\n• Park fees\n• Summit certificate\n\n**Requirement:** Good fitness, minimum age 12\n\nLearn more: [Tours](/tours) | [Contact](/contact)";
        }

        if (str_contains($message, 'when') || str_contains($message, 'best time')) {
            return "Best time to visit East Africa: 🌍\n\n**July-October:**\n• Great Migration (Maasai Mara)\n• Dry season - best wildlife viewing\n• Cool temperatures\n• Peak season (book early!)\n\n**June & November:**\n• Good weather\n• Fewer crowds\n• Better prices\n\n**December-March:**\n• Green season\n• Baby animals\n• Excellent bird watching\n\nEach season offers unique experiences! What are you interested in?";
        }

        if (str_contains($message, 'gorilla')) {
            return "Mountain Gorilla Trekking - once in a lifetime! 🦍\n\n**Bwindi, Uganda:**\n• Duration: 4 days\n• Price: \$3,200/person (includes \$700 permit)\n• Minimum age: 15 years\n• Difficulty: Challenging\n\n**Experience:**\n• 1 hour with gorilla family\n• UNESCO World Heritage forest\n• Batwa cultural visit\n• Conservation contribution\n\n**Important:** Limited permits - book early!\n\nLearn more: [Tours](/tours)";
        }

        // Default response
        return "I'd be happy to help you plan your African safari! 🌍\n\n**I can help with:**\n• Tour recommendations\n• Booking information\n• Pricing and availability\n• Travel requirements\n• Packing advice\n• Best times to visit\n\n**Quick links:**\n• [Browse Tours](/tours)\n• [About Us](/about)\n• [FAQ](/faq)\n• [Contact Us](/contact)\n\nWhat would you like to know?";
    }
}