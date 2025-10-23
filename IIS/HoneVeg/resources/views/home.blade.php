@props(['products', 'labels'])

<x-default>
    <x-header></x-header>

    <x-catalog :products="$products" :labels="$labels"></x-catalog>
    

</x-default>