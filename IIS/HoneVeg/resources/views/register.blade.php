<x-default>
    <x-postHeaderContent>
        
            <div style="width:20%; margin:auto; padding:5% 0 2% 0">
                <form method="POST" action={{url("/register")}}> 
                    @csrf
                    <h1 >Join our market!</h1>
                    
                    <label>
                        <h2 style="text-align: left; margin:5% 0 0 20%;">Email</h2>
                        <input type="email" name="email" maxlength=64 required value="{{old('email')}}" style="padding: 5px; border-radius: 20px;">
                    </label>
                    
                    @error("email")
                        <h2 style="font-size: 12px; color:red; text-align:center;">{{$message}}</h2>
                    @else
                        <h2 style="font-size: 12px; color:red; text-align:center;visibility: hidden;">fantom error message</h2>
                    @enderror
                    
                    <label>
                        <h2 style="text-align: left; margin:5% 0 0 20%;">Name</h2>
                        <input type="text" name="name" maxlength=41 minlength=3 required value="{{old('name')}}" style="padding: 5px; border-radius: 20px;">
                      
                    </label>

                    @error("name")
                    <h2 style="font-size: 12px; color:red; text-align:center;">{{$message}}</h2>
                    @else
                        <h2 style="font-size: 12px; color:red; text-align:center;visibility: hidden;">fantom error message</h2>
                    @enderror

                    <label>
                        <h2 style="text-align: left; margin:5% 0 0 20%;">Password</h2>
                        <input type="password" maxlength=64 minlength=6 name="password" required style="padding: 5px; border-radius: 20px;">
                        
                    </label>
                    
                    @error("password")
                        <h2 style="font-size: 12px; color:red; text-align:center;">{{$message}}</h2>
                    @else
                        <h2 style="font-size: 12px; color:red; text-align:center; visibility: hidden;">fantom error message</h2>
                    @enderror

                    <x-defaultButton style="margin-top: 15%; color:white; background-color: var(--green-style);">Register</x-defaultButton>
                </form>
            </div>


    </x-postHeaderContent>
</x-default>
