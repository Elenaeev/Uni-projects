@props(['creationCategory', 'create', 'design', 'dlabels']) 

<x-default>
    <x-header></x-header>
    <div style="margin: 5% 0 -8% 0;">
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
    <div class="catDesignManager">

        <span><h2>subcategory design of</h2><h1>{{$creationCategory->name}}</h1></span>
        <hr style="orientation: vertical; width: 100%;">
        @if($create)
            <form  method="POST" id="createDesignFrom" action="{{url('/design/create-in/'.$creationCategory->category_id)}}">
                @csrf
                <label>
                    <div>
                        <h2>New subcategory name</h2>
                        <input name="designSubcatName" type="text" minlength="3" maxlength="20" value="{{old('designSubcatName')}}">
                    </div>
                </label>

                <label>
                    <div>
                        <h2>Shortly describe why we should add this subcategory</h2>
                        <textarea id="designDescr" name="designDescription" minlength="0" maxlength="500", style="height: 200px; width:300px;">{{old('designDescription')}}</textarea>
                        <script>
                            const textarea = document.getElementById("designDescr");
                            textarea.focus();
                            textarea.setSelectionRange(0, 0);
                        </script>
                    </div>
                </label>

                <div>
                    <div>
                        <x-defaultButton id="addTextLabelButton" type="button" onclick="addLabel('text')">Add text</x-defaultButton>
                        <x-defaultButton id="addIntLabelButton" type="button" onclick="addLabel('number')">Add numeric</x-defaultButton>
                    </div>
                    <x-defaultButton id="removeLabelButton" type="button" onclick="removeLabel()">Remove label</x-defaultButton>
                </div>

                <div id="labelsList" class="labelsList">

                </div>
                
            </form>

            <script>
                function addLabel(type)
                {
                    list = document.getElementById('labelsList');

                    lblnameLbl = document.createElement('label');
                    lblnameLbl.innerHTML = '<h2>Label name (' + type + ')</h2>';

                    lblnameInp = document.createElement('input');
                    lblnameInp.type = 'text';
                    lblnameInp.name = 'lblnames[]';
                    lblnameInp.max = 20;
                    lblnameInp.min = 3;
                    lblnameInp.required = true;

                    lbltypeInp = document.createElement('input');
                    lbltypeInp.style.visibility = 'hidden';
                    lbltypeInp.name = 'lbltypes[]';
                    lbltypeInp.type = 'text';
                    lbltypeInp.value = type;

                    lblnameLbl.appendChild(lblnameInp);
                    lblnameLbl.appendChild(lbltypeInp);

                    div = document.createElement('div');
                    div.appendChild(lblnameLbl);

                    list.insertBefore(div, list.firstChild);
                }

                function removeLabel()
                {
                    list = document.getElementById('labelsList');
                    if(list.children.length > 0)
                    {
                        list.removeChild(list.firstChild);
                    }
                }

            </script>
        @else
            @php $s = ["created" => "#e0bc00", "approved" => "green", "declined" => "red", ]@endphp
            <h1 style="color:{{$s[$design->status]}}">{{ucfirst($design->status)}}</h1>
            <div>
                <h2>New subcategory name</h2>
                <h3>{{$design->name}}</h3>
            </div>

            <div>
                <h2>Description</h2>
                <textarea readonly rows="10" cols="30" minlength="3" maxlength="500">{{$design->description}}</textarea>
            </div>

            <h2>Labels</h2>
            <div class="labelsList2">
                @foreach($dlabels as $lbl)
                    <h3>{{$lbl->name}} ({{$lbl->type}})</h3>
                @endforeach
            </div>
        @endif

        @if($create)
            <x-defaultButton form="createDesignFrom">Create</x-defaultButton>
        @else
            @can("be-design-author", $design->design_id)
                <form method="POST" action="{{url('/design/'.$design->design_id)}}">
                    @csrf
                    @method("PATCH")
                    <x-defaultButton>Delete</x-defaultButton>
                </form>
            @endcan
            <div>
                @can("be-moder")
                    @if($design->status == "created")
                        <br>
                        <form method="POST" action="{{url('/design/'.$design->design_id.'/accept')}}">
                            @csrf
                            <x-defaultButton style="color: green;">Accept</x-defaultButton>
                        </form>
                        <br>
                        <form method="POST" action="{{url('/design/'.$design->design_id.'/decline')}}">
                            @csrf
                            <x-defaultButton style="color: red;">Decline</x-defaultButton>
                        </form>
                    @endif
                @endcan
            <div>
        @endif
    </div>
 </x-default>