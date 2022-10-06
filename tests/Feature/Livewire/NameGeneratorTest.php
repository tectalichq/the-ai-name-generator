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
                    'industry' => 'Software Developers',
                    'concept'  => 'Humorous',
                ])
                ->call('generateNames')
                ->assertHasNoErrors()
                ->assertSet('names', [
                    'Humorous Software Developers Name 1',
                    'Humorous Software Developers Name 2',
                ])
                ->assertSee('Humorous Software Developers Name 1')
                ->assertSee('Humorous Software Developers Name 2');
    }
}
