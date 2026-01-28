@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-[#ED72A6] focus:ring-[#ED72A6] rounded-md shadow-sm']) }}>