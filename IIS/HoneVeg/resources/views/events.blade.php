@props(['events'])


<x-default>
    <x-eventsPanel></x-eventsPanel>
    <x-header></x-header>
        <div class="eventsCatalog">
            @foreach($events as $ev)
                @php 
                    $avatarPath = 'images/events/'.$ev->event_id.'.jpg';
                    $checkPath = public_path('web/'.$avatarPath) 
                @endphp
                <div class="ecatItem">
                    <a href="{{url('/event/'.$ev->event_id)}}">
                        <img width=300 height=250 src="{{asset('web/'.$avatarPath)}}" style="border-radius: 5%;">
                        <h2>{{$ev->name}}</h2>
                        <h4>{{$ev->start_date}} - {{$ev->end_date}}<h4>
                    </a>
                </div>
            @endforeach
        </div>
</x-default>