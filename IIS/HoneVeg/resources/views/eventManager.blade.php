@props(['event', 'soldProducts', 'create']) 
 
 <x-default>
    <x-header></x-header>

    <div class="eventEditErrMsg">
      @php 
          $style1 = "font-size: 16px; color:red; text-align:center;";
          $style2 = "font-size: 16px; color:green; text-align:center;";
      @endphp
      @if(!$errors->any() && !session()->has('message'))
          <h2 style="{{$style1}}  visibility:hidden;">fantom error message</h2>
      @else
          @foreach($errors->all() as $message)
              <h2 style="{{$style1}}">{{$message}}</h2>     
          @endforeach

          @if(session()->has('message'))
              <h2 style="{{$style2}}">{{session('message')}}</h2>
          @endif
      @endif
  </div>


  <div class="eventEditor">
   @if($create)
       <h1>Event create</h1>
   @else
       <h1>Event edit</h1>
   @endif
   @if($create)
       <form id="eventeditform" method="POST" action="{{url('/event/create/')}}"  enctype="multipart/form-data">
   @else
       <form id="eventeditform" method="POST" action="{{url('/event/'.$event->event_id.'/edit')}}"  enctype="multipart/form-data">    
   @endif
       @csrf
       <label>
           <h2>Event image</h2>
           <h5>(size 300x250)</h5>
           <input type="file" name="image" style="margin: 2% 0 0 11%;"> 
       </label> 
       <label>
           <h2>Event name</h2>
           <input required type="text" name="ename" minlength=2 maxlength=64 value="{{ $create ? old('ename') : $event->name}}">
       </label>

       <label>
           <h2>Description</h2>
           <textarea id="eventDescr" maxlength=300 name="edescr" style="width: 300px; height: 200px;">{{$create ? old('edescr') : $event->description}}</textarea>
           <script>
               const textarea = document.getElementById("eventDescr");
               textarea.focus();
               textarea.setSelectionRange(0, 0);
           </script>
       </label>

       <label>
           <h2>Event start date</h2>
           <input required type="datetime-local" name="evStart" value="{{$create ? old('evStart') : $event->start_date}}">
       </label>

       <label>
           <h2>Event end date</h2>
           <input required type="datetime-local" name="evEnd" value="{{$create ? old('evEnd') : $event->end_date}}">
       </label>

       <label>
            <h2>Event address</h2>
            <input required type="text" name="evPlace" value="{{$create ? old('evPlace') : $event->address}}">
       </label>

       <h2>Choose which products are sold on event</h2>
       @if(!$soldProducts->isEmpty()) 
            @foreach($soldProducts as $prod)
                <label>
                    <h4>{{$prod->name}}</h4>
                    <input type="checkbox" name="evProds[]"  value="{{$prod->product_id}}" {{ in_array($prod->product_id, old('evProds', [])) ? 'checked' : '' }}>
                </label>
            @endforeach
       @else
            <h4>You don't have any products</h4>
       @endif
        

   </form>
   <x-defaultButton form="eventeditform" type="submit">Done</x-defaultButton>
</div>

 </x-default>