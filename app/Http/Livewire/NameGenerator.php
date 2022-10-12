<?php

namespace App\Http\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use Livewire\Component;
use Tectalic\OpenAi\Client;
use Tectalic\OpenAi\ClientException;
use Tectalic\OpenAi\Models\Completions\CreateRequest;
use Tectalic\OpenAi\Models\Completions\CreateResponse;
use Tectalic\OpenAi\Models\Completions\CreateResponseItem;

class NameGenerator extends Component
{
    /**
     * List of Angles/Concepts.
     *
     * @var array<int, string>
     */
    public array $concepts = [
        "Accomplished",
        "Adaptable",
        "Adventurous",
        "Affectionate",
        "Agreeable",
        "Alive",
        "Amazing",
        "Ambitious",
        "Amusing",
        "Approachable",
        "Articulate",
        "Awesome",
        "Blazing",
        "Brave",
        "Breath-taking",
        "Bright",
        "Brilliant",
        "Broad-minded",
        "Bustling",
        "Calm",
        "Capable",
        "Captivating",
        "Charismatic",
        "Charming",
        "Chatty",
        "Cheerful",
        "Compassionate",
        "Competitive",
        "Confident",
        "Considerate",
        "Courageous",
        "Courteous",
        "Creative",
        "Dependable",
        "Determined",
        "Devoted",
        "Discreet",
        "Dramatic",
        "Dynamic",
        "Easy-going",
        "Educated",
        "Efficient",
        "Elegant",
        "Energetic",
        "Engaging",
        "Enthusiastic",
        "Excellent",
        "Expert",
        "Fabulous",
        "Fantastic",
        "Fascinating",
        "Fearless",
        "Flexible",
        "Focused",
        "Fresh",
        "Friendly",
        "Funny",
        "Generous",
        "Gentle",
        "Giving",
        "Good",
        "Gorgeous",
        "Hard-working",
        "Harmonious",
        "Helpful",
        "Hilarious",
        "Honest",
        "Humorous",
        "Imaginative",
        "Immaculate",
        "Imposing",
        "Impressive",
        "Incredible",
        "Independent",
        "Inquisitive",
        "Insightful",
        "Inspiring",
        "Intuitive",
        "Inventive",
        "Kind",
        "Knowledgeable",
        "Laid-back",
        "Likable",
        "Lively",
        "Loyal",
        "Luxurious",
        "Magnificent",
        "Marvelous",
        "Modest",
        "Mysterious",
        "Nice",
        "Observant",
        "Open-minded",
        "Optimistic",
        "Organized",
        "Outstanding",
        "Passionate",
        "Patient",
        "Peaceful",
        "Perfect",
        "Pioneering",
        "Pleasant",
        "Polite",
        "Positive",
        "Powerful",
        "Proactive",
        "Productive",
        "Proficient",
        "Quick-witted",
        "Quiet",
        "Rational",
        "Relaxed",
        "Reliable",
        "Remarkable",
        "Reserved",
        "Resourceful",
        "Responsible",
        "Self-confident",
        "Self-disciplined",
        "Sensational",
        "Sensible",
        "Spectacular",
        "Splendid",
        "Stellar",
        "Straightforward",
        "Stunning",
        "Superb",
        "Technological",
        "Terrific",
        "Thoughtful",
        "Tidy",
        "Trustworthy",
        "Understanding",
        "Upbeat",
        "Versatile",
        "Vibrant",
        "Witty",
    ];

    /**
     * List of Industries.
     *
     * @link https://www.opensecrets.org/industries/alphalist.php
     *
     * @var string[]
     */
    public array $industries = [
        "Accountants",
        "Advertising",
        "Aerospace",
        "Agriculture",
        "Airlines",
        "Alternative Energy Production & Services",
        "Architectural Services",
        "Auto Dealers",
        "Auto Manufacturers",
        "Automotive",
        "Bars & Restaurants",
        "Beer, Wine & Liquor",
        "Books, Magazines & Newspapers",
        "Broadcasters, Radio/TV",
        "Builders",
        "Building Materials & Equipment",
        "Business Associations",
        "Business Services",
        "Cable & Satellite TV Production & Distribution",
        "Car Dealers",
        "Car Manufacturers",
        "Cattle Ranchers",
        "Clothing Manufacturing",
        "Colleges, Universities & Schools",
        "Comic Publisher",
        "Commercial Banks",
        "Commercial TV & Radio Stations",
        "Communications/Electronics",
        "Construction",
        "Cruise Lines",
        "Defense",
        "Dentists",
        "Doctors & Other Health Professionals",
        "Education",
        "Electric Utilities",
        "Electronics",
        "Energy & Natural Resources",
        "Entertainment Industry",
        "Environment",
        "Farm Bureaus",
        "Farming",
        "Finance",
        "Food & Beverage",
        "Food Stores",
        "Forestry & Forest Products",
        "Foundations, Philanthropists & Non-Profits",
        "Gaming",
        "Garbage Collection",
        "Health",
        "Hedge Funds",
        "Hospitals & Nursing Homes",
        "Hotels, Motels & Tourism",
        "Insurance",
        "Internet",
        "Labor",
        "Leadership PACs",
        "Livestock",
        "Manufacturing",
        "Marine Transport",
        "Mining",
        "Music Production",
        "Newspaper, Magazine & Book Publishing",
        "Phone Companies",
        "Physicians & Other Health Professionals",
        "Printing & Publishing",
        "Private Equity & Investment Firms",
        "Professional Sports",
        "Radio/TV Stations",
        "Real Estate",
        "Retail Sales",
        "Retired",
        "Schools/Education",
        "Securities & Investment",
        "Software Developers",
        "Software Startups",
        "Sports",
        "Stock Brokers/Investment Industry",
        "TV / Movies / Music",
        "TV Production",
        "Teachers/Education",
        "Telephone Utilities",
        "Textiles",
        "Transportation",
        "Trucking",
        "Type Foundry",
        "Universities, Colleges & Schools",
        "Venture Capital",
        "Waste Management",
    ];

    /**
     * @var string The currently selected Industry.
     */
    public string $industry = '';

    /**
     * @var string The currently selected Concept.
     */
    public string $concept = '';

    /**
     * @var string[] The current results.
     */
    public array $names = [];

    public function render(): View
    {
        return view('livewire.name-generator');
    }

    /**
     * When the Reset button is clicked.
     */
    public function clear(): void
    {
        $this->reset(['industry', 'concept', 'names']);
        $this->resetErrorBag();
    }

    /**
     * The form validation rules.
     *
     * @return array<string, array<int, In|string>>
     */
    protected function rules(): array
    {
        return [
            'industry' => ['required', Rule::in($this->industries)],
            'concept'  => ['required', Rule::in($this->concepts)],
        ];
    }

    /**
     * When the form is submitted.
     */
    public function generateNames(Client $client): void
    {
        $validated = $this->validate();

        $this->clearValidation();

        $prompt = sprintf(
            'Give me a business name and tagline for a company in the %s industry with a %s concept',
            $validated['industry'],
            $validated['concept'],
        );

        $request = $client->completions()->create(
            new CreateRequest([
                'model'      => 'text-davinci-002',
                'prompt'     => $prompt,
                'max_tokens' => 2048,
                'n'          => 5 // 5 completions
            ])
        );

        try {
            /** @var CreateResponse $result */
            $result = $request->toModel();
            // Transform the result, as we only need to use the text from each completion choice.
            $this->names = Arr::map($result->choices, function (CreateResponseItem $item) {
                return $item->text;
            });
        } catch (ClientException $e) {
            // Error querying OpenAI.
            // Clear any existing results and display an error message.
            $this->reset(['names']);
            $this->addError('results', __('Results are temporarily unavailable. Please try again later.'));
        }
    }
}
