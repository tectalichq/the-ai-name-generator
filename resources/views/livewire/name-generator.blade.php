<form wire:submit.prevent="generateNames" class="mt-4 py-4 sm:border-t sm:border-gray-200 sm:pt-5">
    @csrf
    <p>
        <label for="industry" class="font-semibold block py-2">{{ __('What industry is your business in?') }}</label>
        @error('industry') <span class="text-red-400 font-semibold">{{ $message }}</span> @enderror
        <select name="industry" wire:model="industry" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            <option value="">{{ __('Please Select') }}</option>
            @foreach($industries as $i)
                <option @selected($industry === $i) value="{{ $i }}">{{ $i }}</option>
            @endforeach
        </select>
    </p>
    <p class="py-4">
        <label for="concept" class="font-semibold block py-2">{{ __('An angle or concept would you like to emphasise?') }}</label>
        @error('concept') <span class="text-red-400 font-semibold">{{ $message }}</span> @enderror
        <select name="concept" wire:model="concept" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            <option value="">{{ __('Please Select') }}</option>
            @foreach( $concepts as $c)
                <option @selected($concept === $c) value="{{ $c }}">{{ $c }}</option>
            @endforeach
        </select>
    </p>

    @error('results') <span class="text-red-400 font-semibold">{{ $message }}</span> @enderror

    <p class="py-2">
        <button wire:click="generateNames" wire:loading.attr="disabled" class="inline-flex justify-center rounded-lg text-md font-semibold py-3 px-4 bg-slate-900 text-white hover:bg-slate-700 disabled:opacity-30 disabled:cursor-not-allowed">{{ __('Generate Your Names') }}</button>
        <button wire:click="clear" type="button"  class="inline-flex justify-center rounded-lg text-md font-semibold py-3 px-4 bg-white/0 text-slate-900 ring-1 ring-slate-900/10 hover:bg-white/25 hover:ring-slate-900/15 ">{{ __('Reset') }}</button>
    </p>

    <div wire:loading.delay wire:loading.block wire:target="generateNames" class="mt-4">
            {{ __('Please wait, generating your names and taglines ...') }}
    </div>

    <div wire:loading.remove class="mt-4">
        @if (!empty($names))
            <h2 class="text-lg font-bold">{{ ('Your Results') }}</h2>
            <ul class="mt-4 ml-6 list-disc">
                @foreach( $names as $name)
                    <li class="p-1">{{ $name }}</li>
                @endforeach
            </ul>
        @endif
    </div>

</form>


