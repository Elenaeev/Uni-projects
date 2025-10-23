<button {{ 
    $attributes->merge(["class" => "navButton"])
}}>
    {{$slot}}
</button>  