<nav class="headerStyle" id="headView">
    <a href="{{url('/home')}}"><h1 class="logo">HoneVeg</h1></a>
    
    <div class="navButtons">
        @php
            $notChosen = "notChosenButton";
            $chosen = "chosenButton";
            $visible = "visibleElement";
            $hidden = "hiddenElement";

            $homeBtn = $catBtn = $evBtn = $regBtn = $signinBtn = $notChosen;
        @endphp
        @if (Route::currentRouteNamed("home.index")) @php $homeBtn = $chosen; @endphp @endif
        @if (Route::currentRouteNamed(["categories.index", "categories.show"])) @php $catBtn = $chosen; @endphp @endif
        @if (Route::currentRouteNamed("events.index")) @php $evBtn = $chosen; @endphp @endif
        @if (Route::currentRouteNamed("register.index")) @php  $regBtn =  $chosen;  @endphp @endif 
        @if (Route::currentRouteNamed("login")) @php $signinBtn = $chosen; @endphp @endif 

        <a href={{ url("/home") }}><x-navButton class={{$homeBtn}}>Home</x-navButton></a>
        <a href="{{ url("/categories") }}"><x-navButton class={{$catBtn}}>Categories</x-navButton></a>
        <a href="{{ url("/events") }}"><x-navButton class={{$evBtn}}>Events</x-navButton></a>
    </div>
    <div class="regButtons">
        
        @guest
            <a href="{{ url("/register") }}"><x-defaultButton class={{$regBtn}}>Register</x-defaultButton></a>
            <a href="{{ url("/signin") }}"><x-defaultButton class={{$signinBtn}}>Sign In</x-defaultButton></a>
        @endguest

        @auth
            <form id="lout" method="POST" action={{url("/signin")}} style="width: 0;height: 0;overflow: hidden;padding: 0;margin: 0;border: 0;">
                @csrf
                @method('PATCH')
            </form>
            <a href="{{ url('/profile/'. Auth::user()->user_id) }}"><img src="{{ asset('/web/icons/user.png') }}" class="profileButton"  width=40 heigh=40 style="background-color: var(--background-style); border-radius: 25px;"></a>
            <a href="{{ url("/home") }}" style="display:flex; align-items:center; text-decoration: none;"><x-defaultButton type="submit" form="lout" class={{$signinBtn}}>Log out</x-defaultButton></a>
        @endauth
    </div>
</nav>