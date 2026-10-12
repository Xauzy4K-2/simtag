@props(['name', 'bag' => 'default'])

@error($name, $bag)
    <p class="field-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>
@enderror
