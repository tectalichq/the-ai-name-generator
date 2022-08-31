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
        "Abundant",
        "Accomplished",
        "Adaptable",
        "Adept",
        "Adventurous",
        "Affectionate",
        "Agreeable",
        "Alive",
        "Alluring",
        "Amazing",
        "Ambitious",
        "Amiable",
        "Ample",
        "Amusing",
        "Approachable",
        "Articulate",
        "Attractive",
        "Awesome",
        "Beautiful",
        "Blazing",
        "Boundless",
        "Bountiful",
        "Brave",
        "Breath-taking",
        "Bright",
        "Brilliant",
        "Broad-minded",
        "Bustling",
        "Calm",
        "Capable",
        "Captivating",
        "Careful",
        "Charismatic",
        "Charming",
        "Chatty",
        "Cheerful",
        "Colorful",
        "Colossal",
        "Communicative",
        "Compassionate",
        "Competitive",
        "Confident",
        "Conscientious",
        "Considerate",
        "Convivial",
        "Cosmopolitan",
        "Courageous",
        "Courteous",
        "Creative",
        "Dazzling",
        "Decisive",
        "Dependable",
        "Determined",
        "Devoted",
        "Diligent",
        "Diplomatic",
        "Discreet",
        "Dramatic",
        "Dusky",
        "Dynamic",
        "Easy-going",
        "Educated",
        "Efficient",
        "Elegant",
        "Emotional",
        "Enchanted",
        "Enchanting",
        "Energetic",
        "Engaging",
        "Enlightened",
        "Enthusiastic",
        "Excellent",
        "Expert",
        "Extensive",
        "Exuberant",
        "Fabulous",
        "Fair-minded",
        "Fairy-tale-like",
        "Faithful",
        "Fantastic",
        "Far-flung",
        "Fascinating",
        "Favorable",
        "Fearless",
        "Fertile",
        "Flexible",
        "Focused",
        "Forceful",
        "Frank",
        "Fresh",
        "Friendly",
        "Funny",
        "Generous",
        "Gentle",
        "Giving",
        "Gleaming",
        "Glimmering",
        "Glistening",
        "Glittering",
        "Glowing",
        "Good",
        "Gorgeous",
        "Gregarious",
        "Hard-working",
        "Harmonious",
        "Helpful",
        "Hilarious",
        "Historic",
        "Homey",
        "Honest",
        "Humorous",
        "Imaginative",
        "Immaculate",
        "Immeasurable",
        "Immense",
        "Impartial",
        "Imposing",
        "Impressive",
        "Incredible",
        "Independent",
        "Indescribable",
        "Inquisitive",
        "Insightful",
        "Inspiring",
        "Intellectual",
        "Intelligent",
        "Intuitive",
        "Inventive",
        "Kind",
        "Knowledgeable",
        "Kooky",
        "Laid-back",
        "Likable",
        "Lively",
        "Lovely",
        "Loving",
        "Loyal",
        "Lush",
        "Lustrous",
        "Luxurious",
        "Magical",
        "Magnificent",
        "Majestic",
        "Marvelous",
        "Massive",
        "Meandering",
        "Mirthful",
        "Modest",
        "Monumental",
        "Mountainous",
        "Mysterious",
        "Mystical",
        "Neat",
        "Nice",
        "Nostalgic",
        "Observant",
        "Open-minded",
        "Optimistic",
        "Organized",
        "Outstanding",
        "Palatial",
        "Passionate",
        "Pastoral",
        "Patient",
        "Peaceful",
        "Perfect",
        "Persistent",
        "Personable",
        "Philosophical",
        "Picturesque",
        "Pioneering",
        "Placid",
        "Pleasant",
        "Plucky",
        "Polite",
        "Positive",
        "Powerful",
        "Practical",
        "Proactive",
        "Productive",
        "Proficient",
        "Propitious",
        "Prosperous",
        "Qualified",
        "Quick-witted",
        "Quiet",
        "Rational",
        "Ravishing",
        "Relaxed",
        "Reliable",
        "Remarkable",
        "Reserved",
        "Resourceful",
        "Responsible",
        "Romantic",
        "Self-confident",
        "Self-disciplined",
        "Sensational",
        "Sensible",
        "Sensitive",
        "Serene",
        "Shiny",
        "Sincere",
        "Sleek",
        "Sociable",
        "Spacious",
        "Spectacular",
        "Splendid",
        "Stellar",
        "Straightforward",
        "Stunning",
        "Stupendous",
        "Super",
        "Superb",
        "Sympathetic",
        "Technological",
        "Terrific",
        "Thoughtful",
        "Tidy",
        "Tough",
        "Towering",
        "Tranquil",
        "Trustworthy",
        "Unassuming",
        "Understanding",
        "Unique",
        "Unspoiled",
        "Upbeat",
        "Vast",
        "Versatile",
        "Vibrant",
        "Vivacious",
        "Vivid",
        "Warm-hearted",
        "Wondrous",
        "Witty",
    ];

    /**
     * List of Industries.
     *
     *  https://www.opensecrets.org/industries/alphalist.php
     *
     * @var string[]
     */
    public array $industries = [
        "Accountants",
        "Advertising/Public Relations",
        "Aerospace, Defense Contractors",
        "Agribusiness",
        "Agricultural Services & Products",
        "Agriculture",
        "Air Transport",
        "Air Transport Unions",
        "Airlines",
        "Alcoholic Beverages",
        "Alternative Energy Production & Services",
        "Architectural Services",
        "Attorneys/Law Firms",
        "Auto Dealers",
        "Auto Dealers, Japanese",
        "Auto Manufacturers",
        "Automotive",
        "Banking, Mortgage",
        "Banks, Commercial",
        "Banks, Savings & Loans",
        "Bars & Restaurants",
        "Beer, Wine & Liquor",
        "Books, Magazines & Newspapers",
        "Broadcasters, Radio/TV",
        "Builders/General Contractors",
        "Builders/Residential",
        "Building Materials & Equipment",
        "Building Trade Unions",
        "Business Associations",
        "Business Services",
        "Cable & Satellite TV Production & Distribution",
        "Candidate Committees",
        "Car Dealers",
        "Car Dealers, Imports",
        "Car Manufacturers",
        "Cattle Ranchers/Livestock",
        "Chemical & Related Manufacturing",
        "Chiropractors",
        "Civil Servants/Public Officials",
        "Clergy & Religious Organizations",
        "Clothing Manufacturing",
        "Coal Mining",
        "Colleges, Universities & Schools",
        "Commercial Banks",
        "Commercial TV & Radio Stations",
        "Communications/Electronics",
        "Computer Software",
        "Conservative/Republican",
        "Construction",
        "Construction Services",
        "Construction Unions",
        "Credit Unions",
        "Crop Production & Basic Processing",
        "Cruise Lines",
        "Cruise Ships & Lines",
        "Dairy",
        "Defense",
        "Defense Aerospace",
        "Defense Electronics",
        "Defense/Foreign Policy Advocates",
        "Democratic Candidate Committees",
        "Democratic Leadership PACs",
        "Democratic/Liberal",
        "Dentists",
        "Doctors & Other Health Professionals",
        "Education",
        "Electric Utilities",
        "Electronics Manufacturing & Equipment",
        "Electronics, Defense Contractors",
        "Energy & Natural Resources",
        "Entertainment Industry",
        "Environment",
        "Farm Bureaus",
        "Farming",
        "Finance / Credit Companies",
        "Finance, Insurance & Real Estate",
        "Food & Beverage",
        "Food Processing & Sales",
        "Food Products Manufacturing",
        "Food Stores",
        "For-profit Education",
        "Foreign & Defense Policy",
        "Forestry & Forest Products",
        "Foundations, Philanthropists & Non-Profits",
        "Funeral Services",
        "Garbage Collection/Waste Management",
        "Gas & Oil",
        "General Contractors",
        "Government Employee Unions",
        "Government Employees",
        "Health",
        "Health Professionals",
        "Health Services/HMOs",
        "Hedge Funds",
        "HMOs & Health Care Services",
        "Home Builders",
        "Hospitals & Nursing Homes",
        "Hotels, Motels & Tourism",
        "Human Rights",
        "Ideological/Single-Issue",
        "Industrial Unions",
        "Insurance",
        "Internet",
        "Labor",
        "Lawyers & Lobbyists",
        "Lawyers / Law Firms",
        "Leadership PACs",
        "LGBTQIA Rights & Issues",
        "Liberal/Democratic",
        "Liquor, Wine & Beer",
        "Livestock",
        "Lobbyists",
        "Lodging / Tourism",
        "Logging, Timber & Paper Mills",
        "Manufacturing, Misc",
        "Marine Transport",
        "Meat processing & products",
        "Medical Supplies",
        "Mining",
        "Misc Business",
        "Misc Finance",
        "Misc Manufacturing & Distributing",
        "Misc Unions",
        "Miscellaneous Defense",
        "Miscellaneous Services",
        "Mortgage Bankers & Brokers",
        "Motion Picture Production & Distribution",
        "Music Production",
        "Natural Gas Pipelines",
        "Newspaper, Magazine & Book Publishing",
        "Non-profits, Foundations & Philanthropists",
        "Nurses",
        "Nursing Homes/Hospitals",
        "Nutritional & Dietary Supplements",
        "Oil & Gas",
        "Other",
        "Phone Companies",
        "Physicians & Other Health Professionals",
        "Postal Unions",
        "Poultry & Eggs",
        "Power Utilities",
        "Printing & Publishing",
        "Private Equity & Investment Firms",
        "Professional Sports",
        "Progressive/Democratic",
        "Public Employees",
        "Public Sector Unions",
        "Publishing & Printing",
        "Radio/TV Stations",
        "Railroads",
        "Real Estate",
        "Record Companies/Singers",
        "Recorded Music & Music Production",
        "Recreation / Live Entertainment",
        "Religious Organizations/Clergy",
        "Republican Candidate Committees",
        "Republican Leadership PACs",
        "Republican/Conservative",
        "Residential Construction",
        "Restaurants & Drinking Establishments",
        "Retail Sales",
        "Retired",
        "Savings & Loans",
        "Schools/Education",
        "Sea Transport",
        "Securities & Investment",
        "Special Trade Contractors",
        "Sports, Professional",
        "Steel Production",
        "Stock Brokers/Investment Industry",
        "Student Loan Companies",
        "Sugar Cane & Sugar Beets",
        "Teachers Unions",
        "Teachers/Education",
        "Telecom Services & Equipment",
        "Telephone Utilities",
        "Textiles",
        "Timber, Logging & Paper Mills",
        "Tobacco",
        "Transportation",
        "Transportation Unions",
        "Trash Collection/Waste Management",
        "Trucking",
        "TV / Movies / Music",
        "TV Production",
        "Unions",
        "Unions, Airline",
        "Unions, Building Trades",
        "Unions, Industrial",
        "Unions, Misc",
        "Unions, Public Sector",
        "Unions, Teacher",
        "Unions, Transportation",
        "Universities, Colleges & Schools",
        "Vegetables & Fruits",
        "Venture Capital",
        "Waste Management",
        "Wine, Beer & Liquor",
        "Women's Issues",
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

        try {
            // Query OpenAI for the results.

            /** @var CreateResponse $result */
            $result = $client->completions()->create(
                new CreateRequest([
                    'model'      => 'text-davinci-002',
                    'prompt'     => $prompt,
                    'max_tokens' => 2048,
                    'n'          => 5, // 5 completions
                ])
            )->toModel();

            // Use only the text from each Completion.
            $this->names = Arr::map($result->choices, function (CreateResponseItem $item) {
                return $item->text;
            });
        } catch (ClientException $exception) {
            // Error querying OpenAI.

            // Clear any existing results and display an error message.
            $this->reset(['names']);
            $this->addError('results', __('Results are temporarily unavailable. Please try again later.'));
        }
    }
}
