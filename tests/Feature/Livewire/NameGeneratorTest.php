<?php

namespace Tests\Feature\Livewire;

use App\Http\Livewire\NameGenerator;
use Livewire\Livewire;
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
        Livewire::test(NameGenerator::class)
                ->fill([
                    'industry' => 'Computer Software',
                    'concept'  => 'Humorous',
                ])
                ->call('generateNames')
                ->assertHasNoErrors()
                ->assertSet('names', [
                    'Humorous Name 1',
                    'Humorous Name 2',
                ])
        ->assertSee('Humorous Name 1')
        ->assertSee('Humorous Name 2');
    }
}
