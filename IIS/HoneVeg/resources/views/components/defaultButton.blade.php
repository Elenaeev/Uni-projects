<button {{ 
    $attributes->merge(["class" => "defaultButton"])
}}>
    {{$slot}}
</button>