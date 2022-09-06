<?php

namespace Tests\Feature\Livewire;

use App\Http\Livewire\NameGenerator;
use Livewire\Livewire;
use Nyholm\Psr7\Response;
use Tectalic\OpenAi\Authentication;
use Tectalic\OpenAi\Client;
use Tectalic\OpenAi\Manager;
use Tests\MockHttpClient;
use Tests\TestCase;

class NameGeneratorTest extends TestCase
{
    protected MockHttpClient $mockClient;

    public function setUp(): void
    {
        parent::setUp();

        // TODO: this is an autoload-dev requirement in our package, so it can't be autoloaded?
        require_once('vendor/tectalic/openai/tests/MockHttpClient.php');

        // Mock the response from OpenAI.
        $this->mockClient = new MockHttpClient();

        // Ensure the Laravel Service Container returns an OpenAI client with the Mocked HTTP Client,
        // rather than the Client registered in \App\Providers\AppServiceProvider::register().
        $this->app->singleton(Client::class, function () {
            return new Client(
                $this->mockClient,
                new Authentication('token'),
                Manager::BASE_URI
            );
        });
    }

    public function test_render(): void
    {
        Livewire::test(NameGenerator::class)
                ->assertStatus(200);
    }

    public function test_submit_validates_required_fields(): void
    {
        Livewire::test(NameGenerator::class)
                ->call('generateNames')
                ->assertHasErrors(['industry', 'concept']);
    }

    public function test_submit(): void
    {
        // Mock the OpenAI API response.
        $this->mockClient->makeResponse(
            new Response(200, ['Content-Type' => 'application/json'], (string) json_encode([
                'choices' => [
                    [
                        'text' => 'Name 1',
                    ],
                    [
                        'text' => 'Name 2',
                    ],
                ],
            ]))
        );

        Livewire::test(NameGenerator::class)
                ->fill([
                    'industry' => 'Computer Software',
                    'concept'  => 'Humorous',
                ])
                ->call('generateNames')
                ->assertHasNoErrors()
                ->assertSet('names', [
                    'Name 1',
                    'Name 2',
                ])
        ->assertSee('Name 1')
        ->assertSee('Name 2');
    }
}
