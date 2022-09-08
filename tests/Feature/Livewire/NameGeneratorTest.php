<?php

namespace Tests\Feature\Livewire;

use App\Http\Livewire\NameGenerator;
use Livewire\Livewire;
use Mockery\MockInterface;
use Orhanerday\OpenAi\OpenAi;
use Tests\TestCase;

class NameGeneratorTest extends TestCase
{
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
        // Ensure the Laravel Service Container returns a mocked OpenAI client rather than the Client registered
        // in \App\Providers\AppServiceProvider::register().
        $this->app->singleton(OpenAi::class, function () {
            // Mock the completions response
            return $this->partialMock(OpenAi::class, function (MockInterface $mock) {
                $mock->shouldReceive('complete')
                     ->once()
                     ->andReturn('{"choices":[{"text":"Name 1"},{"text":"Name 2"}]}');
            });
        });

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
